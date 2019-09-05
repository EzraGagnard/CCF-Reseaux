<?php
// Ce script enregistre l'engagement d'un licencié FFA
// seul le n° de licence et le nom pour vérification est nécessaire

// page autoréférente
require_once('../definitions.inc.php');
require_once('utile_sql.php');

// connexion à la base marsouin
  @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
  @mysql_select_db(BASE) or die("Echec de selection de la base cdt");
  
// lecture de la configuration et définition des constantes ENABLE SAISON DATE DESIGNATION etc
      $sql = 'SELECT * FROM `cross_route_configuration`';
      $reponse = mysql_query($sql) or die(mysql_error());
      while ($conf = mysql_fetch_object ($reponse)){
       define($conf->conf_key, $conf->conf_value);
      }
// fin de la lecture de la configuration



if (!empty($_POST['envoyer'])){
      // recole les éléments du tableau cat avec la virgule comme séparateur
      $categorie = implode(',',$_POST['cat']);
      // même chose pour le sexe
      $sexe =  implode(',',$_POST['sexe']);
      $sql = sprintf("UPDATE cross_route_epreuve SET designation=%s , code=%s , horaire=%s , categorie=%s, sexe=%s, prix=%s  WHERE id_epreuve=%s",

                       GetSQLValueString($_POST['designation'], "text"),
                       GetSQLValueString($_POST['code'] , "text"),
                       GetSQLValueString($_POST['horaire'] , "text"),
                       GetSQLValueString($categorie , "text"),
                       GetSQLValueString($sexe , "text"),
                       GetSQLValueString($_POST['prix'] , "text"),
                       $_POST['id_epreuve']
		);
          $Result1 = mysql_query($sql) or die(mysql_error());


    @mysql_close();
    $GoTo = "epreuve.php?competition=".stripslashes($_GET['competition']);
    header(sprintf("Location: %s", $GoTo));
}

// recherche des infos en fct de id_epreuve

if ((isset($_GET['id_epreuve'])) && ($_GET['id_epreuve'] != "")) {
        $sql = "SELECT * FROM cross_route_epreuve WHERE id_epreuve=".$_GET['id_epreuve']."";
        $resultat = mysql_query($sql)or die (mysql_error());

        $epreuve = mysql_fetch_object ($resultat);
         }

 @mysql_close();

// début du fichier bandeau menu horizontal
  if (!is_readable('en_tete.html'))  die ("fichier non accessible");
  @readfile('en_tete.html') or die('Erreur fichier');
?>


<script language="javascript">
           // fonction pour tester la validité de l'adresse mail
         function testMail(champ){
          if (champ.value!=""){
           mail=/^[a-zA-Z0-9]+[a-zA-Z0-9\.\-_]+@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9])+$/;
           if (!mail.test(champ.value)) {
                   alert ("L'adresse email est invalide.\nElle doit être de la forme xxx@xxx.xxx");
                   champ.focus();
                   return false;
           }
          }
        }

        // fonction pour interdire les caractères numériques
        function pasNum(e){
          if (window.event) caractere = window.event.keyCode;
               else  caractere = e.which;
                 return (caractere < 48 || caractere > 57);
          }

        // fonction pour autoriser uniquement les numériques
        function pasCar(e){
          if (window.event) caractere = window.event.keyCode;
               else  caractere = e.which;
                 return (caractere == 8 || (caractere > 47 && caractere < 58));
          }

        // fonction pour mettre en majuscule
        function majuscule(champ){
        champ.value = champ.value.toUpperCase();
        }



        // fonction pour vérifier les infos avant enregistrement
        function verif(){
         }

	


  </script>

<div id="menu" style="text-align: left; width: 100px;">

</div>
<div id="contenu" style="width: 800px; ">
     <h2><a href="epreuve.php?competition=<?php echo stripslashes(urlencode($_GET['competition'])) ?>"><img src="../images/fleche_retour.png" title="Retour" border="0" width="44" height="41"></a>
          Modifier une épreuve pour : <?php echo stripslashes($_GET['competition']) ?><br/></h2>
     <div class="item">
     <p>informations épreuve : </p>
     <form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']."?competition=".stripslashes($_GET['competition']) ?>"  name="engagement" onSubmit="return verif();">
     <input type="hidden" name="mode" value="update" />
     <input type='hidden' name='id_epreuve' value='<?php echo $_GET['id_epreuve']; ?>'/>
     <table style="text-align: left; width: 700px; "   border="0" cellpadding="2" cellspacing="2">
     <tbody>
      <tr>
        <td style="width: 25%; text-align: right; ">libellé : </td>
        <td><input name="designation" value="<?php echo $epreuve->designation; ?>" /></td>
      </tr>
      <tr>
        <td style="width: 25%; text-align: right; ">Horaire : </td>
        <td><input name="horaire" value="<?php echo $epreuve->horaire; ?>" /></td>
      </tr>
      <tr>
        <td style="width: 25%; text-align: right; ">Code : </td>
        <td><input name="code" value="<?php echo $epreuve->code; ?>" /></td>
      </tr>
      <tr>
        <td style="width: 25%; text-align: right; ">Catégorie :</td>
        <?php 
         // décomposition de la chaine categorie en tableau
         $cat=split(",",$epreuve->categorie); ?>
        <td><input name="cat[]" value="EA" type="checkbox" <?php if (in_array("EA",$cat)) echo "CHECKED" ?>/>EA
                <input name="cat[]" value="PO" type="checkbox" <?php if (in_array("PO",$cat)) echo "CHECKED" ?> />PO
                <input name="cat[]" value="BE" type="checkbox" <?php if (in_array("BE",$cat)) echo "CHECKED" ?> />BE
                <input name="cat[]" value="MI" type="checkbox" <?php if (in_array("MI",$cat)) echo "CHECKED" ?> />MI
                <input name="cat[]" value="CA" type="checkbox" <?php if (in_array("CA",$cat)) echo "CHECKED" ?> />CA
                <input name="cat[]" value="JU" type="checkbox" <?php if (in_array("JU",$cat)) echo "CHECKED" ?> />JU
                <input name="cat[]" value="ES" type="checkbox" <?php if (in_array("ES",$cat)) echo "CHECKED" ?> />ES
                <input name="cat[]" value="SE" type="checkbox" <?php if (in_array("SE",$cat)) echo "CHECKED" ?> />SE
                <input name="cat[]" value="VE" type="checkbox" <?php if (in_array("VE",$cat)) echo "CHECKED" ?> />VE
                <input name="cat[]" value="V1" type="checkbox" <?php if (in_array("V1",$cat)) echo "CHECKED" ?> />V1
                <input name="cat[]" value="V2" type="checkbox" <?php if (in_array("V2",$cat)) echo "CHECKED" ?> />V2
                <input name="cat[]" value="V3" type="checkbox" <?php if (in_array("V3",$cat)) echo "CHECKED" ?> />V3
                <input name="cat[]" value="V4" type="checkbox" <?php if (in_array("V4",$cat)) echo "CHECKED" ?> />V4<br>
                <input name="cat[]" value="TC" type="checkbox" <?php if (in_array("TC",$cat)) echo "CHECKED" ?> />TC
        </td>

      </tr>
      <tr>
        <td style="width: 25%; text-align: right; ">Sexe : </td>
        <?php 
         // décomposition de la chaine sexe en tableau
         $sexe=split(",",$epreuve->sexe); ?>
         <td>
         <input name="sexe[]" value="M" type="checkbox" <?php if (in_array("M",$sexe)) echo "CHECKED" ?>/> Masculin
         <input name="sexe[]" value="F" type="checkbox" <?php if (in_array("F",$sexe)) echo "CHECKED" ?>/> Feminin
         </td>
      </tr>
      <tr>
        <td style="width: 25%; text-align: right; ">Prix :</td>
        <td><input name="prix" size="3" value="<?php echo $epreuve->prix; ?>"  /></td>
      </tr>
      <tr>
        <td></td>
        <td><input name="envoyer" value="Valider"  type="submit" /></td>

      </tr>
    </tbody>
  </table>
</form>
</div>
</div>
</div>
<div id="pied"> Site hébergé par Endurance72 - 2, avenue
d'HAOUZA - 72100 LE MANS - Tél: 02.43.23.64.18<br />
</div>
</div>
</body></html>
