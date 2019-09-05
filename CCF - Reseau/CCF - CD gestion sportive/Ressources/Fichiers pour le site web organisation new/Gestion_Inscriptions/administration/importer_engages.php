<?php
//---------------------------------------------------------------------------
// Ce script importe une liste d'engagés à partir d'un fichier csv
// le format du fichier doit avoir au minimum 6 colonnes:
//
//
//
//
// Simier Philippe 12/11/2009
// version 3.0
// page autoréférente protégée
//---------------------------------------------------------------------------
   include "authentification/authcheck.php" ;
   require_once('utile_sql.php');

   // Vérification des droits pour cette page uniquement organisateurs
   if ($_SESSION['droits']<>'2') { header("Location: index.php");};
require_once('../definitions.inc.php');
@mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
@mysql_select_db(BASE) or die("Echec de selection de la base cdt");

// lecture de la configuration et définition des constantes ENABLE SAISON DATE DESIGNATION etc
      $sql = 'SELECT * FROM `cross_route_configuration`';
      $reponse = mysql_query($sql) or die(mysql_error());
      while ($conf = mysql_fetch_object ($reponse)){
       define($conf->conf_key, $conf->conf_value);
      }
// fin de la lecture de la configuration

if( !empty($_POST['envoyer'])){

     if(isset($_FILES['datacsv']))
      {
      $dossier = 'import/';
      $fichier = basename($_FILES['datacsv']['name']);
      if ($fichier==""){ echo 'Vous devez sélectionner un fichier !';
         exit;
      }
      //------  Test de l'extension ----------------------
      $infosfichier = pathinfo($_FILES['datacsv']['name']);
      $extension_upload = $infosfichier['extension'];
      $extensions_autorisees = array('csv','txt');
       if (in_array($extension_upload, $extensions_autorisees)) {
        // ----  L'extension est bonne  déplacement du fichier
        if(move_uploaded_file($_FILES['datacsv']['tmp_name'], $dossier . $fichier))
        //Si la fonction renvoie TRUE, c'est que ça a fonctionné...
        {
            @readfile('en_tete.html') or die('erreur fichier entete');
            echo'<div id="contenu" style="width:900px; margin-left:40px; "> <h2>Compte rendu de l\'importation</h2>';
            echo '<p><br />Fichier de données transféré<br /><br /></p>';
            echo '<table id="tableau"><tr><th>N° ligne</th><th>Nom Prénom</th><th>Résultat</th></tr>';
        }
        else { echo "echec de l'import </div>"; exit; }
       }




    //--------------- Init variables ----------------------------------------
    $fichier= $dossier.$fichier;
    $nbimport = 0;                      //initialisation du Nb d'engagés importés
    $no = 0;                            //initialisation du compteur de lignes
    
    switch ($_POST['separateur']){      // choix du séparateur
    case "PVIR":
          $separateur=";";
          break;
    case "VIR":
          $separateur=",";
          break;
    case "TAB":
          $separateur="\t";
          break;
    }

    //----------------Ouverture du fichier csv en lecture--------------------
    if (file_exists($fichier)) {
       $fp = fopen("$fichier", "r");
       }
       else {
       /*le fichier n'existe pas*/
       echo "Fichier introuvable !<br />Importation stoppée.";
       exit;
       }
    //---------lecture ligne par ligne---------------------------------------
      error_reporting(E_ERROR | E_WARNING | E_PARSE);
      while (!feof($fp)) {    /*Tant que la fin du fichier n'est pas atteinte */
        // récupération d'une ligne dans une liste
        $no +=1;     // incrémente le numéro de la ligne
        $ligne =  fgetcsv($fp,4096,$separateur);
        // première ligne
        if ($no==1) {
           $ligne1 = $ligne;
           continue;
        }
        // autres lignes
        if (count($ligne)>=6) {

        list(${$ligne1[0]},${$ligne1[1]},${$ligne1[2]},${$ligne1[3]},${$ligne1[4]},${$ligne1[5]},${$ligne1[6]},${$ligne1[7]},${$ligne1[8]},${$ligne1[9]},${$ligne1[10]},${$ligne1[11]},${$ligne1[12]},${$ligne1[13]}) = $ligne;

        }
        else {
             echo '<tr><td>'.$no.'</td><td></td><td> <span style="color:#FF0000"> Cette ligne ne comporte pas le minimum de 6 colonnes </span></td></tr>';
             continue;
        }
            

            if (!isset($nom) || $nom=="") {
                  	echo '<tr><td>'.$no. '</td><td></td><td> <span style="color:#FF0000"> Erreur Nom absent</span></td></tr>';
                  	continue;
            }
            if (!isset($prenom) || $prenom=="") {
                  	echo '<tr><td>'.$no. '</td><td></td><td> <span style="color:#FF0000"> Erreur Prénom absent</span></td></tr>';
                  	continue;
            }
            if (!isset($competition) || $competition=="") {
                  	echo '<tr><td>'.$no. '</td><td>'.$nom.' '.$prenom.'</td><td> <span style="color:#FF0000"> Erreur libellé compétition absent</span></td></tr>';
                  	continue;
            }
            if (!isset($nomcourse) || $nomcourse=="") {
                  	echo '<tr><td>'.$no. '</td><td>'.$nom.' '.$prenom.'</td><td> <span style="color:#FF0000"> Erreur libellé de la course absent</span></td></tr>';
                  	continue;
            }
            if (!isset($anneenaissance) || $anneenaissance=="") {
                  	echo '<tr><td>'.$no. '</td><td>'.$nom.' '.$prenom.'</td><td> <span style="color:#FF0000"> Erreur année de naissance absent</span></td></tr>';
                  	continue;
            }
            if (!isset($sexe) || $sexe=="") {
                  	echo '<tr><td>'.$no. '</td><td>'.$nom.' '.$prenom.'</td><td> <span style="color:#FF0000"> Erreur sexe absent</span></td></tr>';
                  	continue;
            }

            // Contrôle de la catégorie pour l'épreuve
           $cat = cat_ffa($anneenaissance,$sexe);

       $sql = sprintf("SELECT categorie,sexe FROM `cross_route_epreuve` WHERE `code`=%s AND `competition`=%s", 
              GetSQLValueString($nomcourse, "text"),
              GetSQLValueString($competition, "text")
              );

     $reponse = mysql_query($sql);
     $epreuve = mysql_fetch_object ($reponse);



     if (!test_cat($anneenaissance,$epreuve->categorie,$sexe)) {
        echo '<tr><td>'.$no.'</td><td>'.$nom.' '.$prenom."</td><td> <span style=\"color:#FF0000\">Catégorie ".$cat." non autorisée pour l'épreuve ".$nomcourse."</span></td></tr>";
        continue;
     }

     // Contrôle du sexe autorisé pour l'épreuve
     $sexe_autorises= split(",",$epreuve->sexe);

     if  (!in_array($sexe, $sexe_autorises)) {

        echo '<tr><td>'.$no."</td><td></td><td> <span style=\"color:#FF0000\">sexe non autorisé pour l'épreuve ".$nomcourse."</span></td></tr>";
        continue;

     }

            //------------Ajout d'un nouvel enregistrement dans la table---------------------

              $insertSQL = sprintf("INSERT INTO cross_route_engagement (competition,date,dossard,nolicence,nom,prenom,noclub,typeparticipant,sexe,anneenaissance,categorie,nomcourse,nomequipe,commentaire,certifmedicalfourni,cotisationpaye) VALUES (%s,CURRENT_TIMESTAMP,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
                       GetSQLValueString($competition, "text"),
                       GetSQLValueString($dossard, "int"),
                       GetSQLValueString($nolicence, "int"),
                       GetSQLValueString($nom, "text"),
                       GetSQLValueString($prenom, "text"),
                       GetSQLValueString($noclub, "text"),
                       GetSQLValueString($typeparticipant, "text"),
                       GetSQLValueString($sexe, "text"),
                       GetSQLValueString($anneenaissance, "int"),
                       GetSQLValueString(cat_ffa($anneenaissance), "text"),
                       GetSQLValueString($nomcourse, "text"),
                       GetSQLValueString($nomequipe, "text"),
                       GetSQLValueString($commentaire, "text"),
                       GetSQLValueString($certifmedicalfourni, "text"),
                       GetSQLValueString($cotisationpaye, "text")
	               );
                      $Result1 = mysql_query($insertSQL);
              if ($Result1) echo '<tr><td>'.$no.'</td><td>'.$nom.' '.$prenom.'</td><td><span style="color:#00FF00">ajouté avec succès</span></td></tr>' ; // engagé ajouté
              else echo '<tr><td>'.$no.'</td><td>'.$nom.' '.$prenom.'</td><td> <span style="color:#FF0000">'.mysql_error().'</span></td></tr>';
      }
    @mysql_close();

    echo '</table><p><br /><a href="orga_menu.php">Retour menu organisateur</a></p></div> ';
    echo '<div id="pied"> Site hébergé par Endurance72 - 2, avenue d\'HAOUZA - 72100 LE MANS - Tél: 02.43.23.64.18<br /></div>';
    echo '</div></body></html>';
    exit;
    }
}

// fonction pour déterminer la catégorie FFA
  function cat_ffa($annee){
     $age=SAISON-$annee;

  if ($age>=70) return "V4";
  if ($age<70 && $age>=60) return "V3";
  if ($age<60 && $age>=50) return "V2";
  if ($age<50 && $age>=40) return "V1";
  if ($age<40 && $age>=23) return "SE";
  if ($age<23 && $age>=20) return "ES";
  if ($age<20 && $age>=18) return "JU";
  if ($age<18 && $age>=16) return "CA";
  if ($age<16 && $age>=14) return "MI";
  if ($age<14 && $age>=12) return "BE";
  if ($age<12 && $age>=10) return "PO";
  if ($age<10) return "EA";
  }
  
// fonction pour tester la catégorie autorisé sur une épreuve
// si l'age de l'engagé est dans une catégorie autorisée la fct revoie TRUE

  function test_cat($annee,$cat_autorisees,$sexe){
    $age=SAISON-$annee;
    $tableau=split(",",$cat_autorisees);
    foreach ($tableau as $cat) {
      if ($age>=1  && $age<=9 && $cat=="EA") return true;
      if ($age>=10 && $age<=11 && $cat=="PO") return true;
      if ($age>=12 && $age<=13 && $cat=="BE") return true;
      if ($age>=14 && $age<=15 && $cat=="MI") return true;
      if ($age>=16 && $age<=17 && $cat=="CA") return true;
      if ($age>=18 && $age<=19 && $cat=="JU") return true;
      if ($age>=20 && $age<=22 && $cat=="ES") return true;
      if ($age>=23 && $age<=39 && $cat=="SE") return true;
      if ($age>=40 && $age<=100 && $cat=="VE") return true;
      if ($age>=40 && $age<=49 && $cat=="V1") return true;
      if ($age>=50 && $age<=59 && $cat=="V2") return true;
      if ($age>=60 && $age<=69  && $sexe=='M' && $cat=="V3") return true;
      if ($age>=70 && $age<=120 && $sexe=='M' && $cat=="V4") return true;
      if ($age>=60 && $age<=120  && $sexe=='F' && $cat=="V3") return true;
      if ($age>=11 && $age<=100  && $cat=="TC") return true;
  }
  return false;
 }

// début du fichier bandeau menu horizontal
  if (!is_readable('en_tete.html'))  die ("fichier non accessible");
  @readfile('en_tete.html') or die('Erreur fichier');
?>

<div id="menu" style="width: 100px;">
</div>
<div id="contenu" style="min-height:500px;">
     <h2>Importer les engagés à partir d'un fichier csv ou txt</h2>

     <p align="left">
        <br />
        La structure du fichier csv doit comporter au minimum 6 colonnes nommées:<br />
        <span style="font-weight: bold ;">competition; nom; prenom; anneenaissance; sexe; nomcourse</span><br />
        Auquel on peut ajouter les colonnes suivantes si nécessaire,<br /> 
        <span style="font-weight: bold ;"> nolicence; noclub; typeparticipant; nomequipe; commentaire; certifmedicalfourni; cotisationpaye;</span>
        <br />l'ordre des colonnes est sans importance. (nommé sans accent et sans majuscule).
      </p>
      <p><a href="essai.csv"> Exemple à télécharger d'un fichier tableur compatible au format csv</a><br /></p>

    <p align="center"></p>
     <div class="item">

     <form method="post" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>"  name="import" enctype="multipart/form-data">
     <input type="hidden" name="mode" value="import" />
     <input type="hidden" name="MAX_FILE_SIZE" value="200000">

     <table style="text-align: left; width: 600px; height: 160px;"   border="0" cellpadding="2" cellspacing="2">
     <tbody>
        <tr>
            <td style="width: 25%; " ><img border="0" src="../images/xls.png"></td>
            <td>Fichier :</td>
            <td style="width: 25%; "><input type="file" size="40" name="datacsv" class="normal"/>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>Séparateur :</td>
            <td><input type="radio" name="separateur" value="PVIR" checked>Point-virgule -
                 <input type="radio" name="separateur" value="VIR">Virgule -
                 <input type="radio" name="separateur" value="TAB">Tabulation -</td>

         </tr>
         <tr>
            <td></td>
            <td></td>
            <td><input name="envoyer" value="Importer"  type="submit" /></td>

        </tr>
    </tbody>
  </table>
</form>
</div>
</div>
</div>
<div id="pied"> Site hébergé par Endurance72 - 2, avenue
d'HAOUZA - 72000 LE MANS - Tél: 02.43.23.64.18<br />
</div>

</body></html>
