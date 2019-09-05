<?php
//--------------------------------------------------------------------
// Ce script permet de modifier les paramètres d'une compétition
// Compétition identifiée par son id tranis par la méthose GET
//
// page autoréférente protégée
//--------------------------------------------------------------------

include "authentification/authcheck.php" ;
// Vérification des droits pour cette page uniquement organisateur
if ($_SESSION['droits']<>'2') { header("Location: index.php");};

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

      $sql = sprintf("UPDATE competition SET nom=%s , lieu=%s, organisateur=%s, date=%s , validation=%s , licence=%s, email=%s    WHERE id=%s",

                       GetSQLValueString($_POST['nom'], "text"),
                       GetSQLValueString($_POST['lieu'], "text"),
                       GetSQLValueString($_POST['organisateur'], "text"),
                       GetSQLValueString($_POST['date'] , "text"),
                       GetSQLValueString($_POST['validation'] , "text"),
                       GetSQLValueString($_POST['licence'] , "text"),
                       GetSQLValueString($_POST['email'] , "text"),
                       $_POST['id_competition']
		);
          $Result1 = mysql_query($sql) or die(mysql_error());


    @mysql_close();
    $GoTo = "competition.php";
    header(sprintf("Location: %s", $GoTo));
}

// recherche des infos en fct de id_epreuve

if ((isset($_GET['id_competition'])) && ($_GET['id_competition'] != "")) {
        $sql = "SELECT * FROM competition WHERE id=".$_GET['id_competition']."";
        $resultat = mysql_query($sql)or die (mysql_error());

        $competition = mysql_fetch_object ($resultat);
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

        // fonction pour vérifier la date de naissance
        function verif_nais(champ){
        if (champ.value<1930 || champ.value>2010){
            alert("Année de naissance sur 4 chiffres\net comprise entre 1930 et 2010");
            champ.value = "";
            champ.focus();
          }
        }

        // fonction pour vérifier les infos avant enregistrement
        function verif(){
         }
</script>
<script src="jscript/calendrier.js" type="text/javascript"></script>
<link href="calendrier.css" type="text/css" rel="StyleSheet" />

<div id="menu" style="width: 100px">
</div>
<div id="contenu">
     <h2><a href="orga_menu.php"><img src="../images/fleche_retour.png" title="Retour" border="0" width="44" height="41"></a>
          Modifier une compétition <?php echo DESIGNATION ?></h2>
     <div class="item">
     <p style="font-weight:bold;">informations sur la compétition : </p>
     <form method="post" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>"  name="engagement" onSubmit="return verif();">
     <input type="hidden" name="mode" value="update" />
     <input type='hidden' name='id_competition' value='<?php echo $_GET['id_competition']; ?>'/>

     <table style="text-align: left; width: 600px; height: 160px;"   border="0" cellpadding="2" cellspacing="2">
     <tbody>
      <tr>
        <td style="width: 50%; text-align: right;">Libellé : </td>
        <td><input name="nom" value="<?php echo $competition->nom; ?>" size="25" maxlength="25"/></td>
      </tr>
      <tr>
        <td style="width: 50%; text-align: right;">Lieu : </td>
        <td><input name="lieu" value="<?php echo $competition->lieu; ?>" size="25" maxlength="25"/></td>
      </tr>
      <tr>
        <td style="width: 50%; text-align: right;">Organisateur : </td>
        <td><input name="organisateur" value="<?php echo $competition->organisateur; ?>" size="25" maxlength="25"/></td>
      </tr>
      <tr>
        <td style="width: 50%; text-align: right;">Email organisateurs : </td>
        <td><input name="email" value="<?php echo $competition->email; ?>"  /></td>
      </tr>
      <tr>
        <td style="width: 50%; text-align: right;" >Date : </td>
        <td><input name="date" value="<?php echo date("Y-m-j",strtotime($competition->date)); ?>" class="date" size="10" />

        </td>
      </tr>
      <tr>
        <td style="width: 50%; text-align: right;">Type de licence autorisés : </td>
        <td><input type="radio" name="licence" value="" <?php  if ($competition->licence =="" )echo "checked"; ?>/>Toutes -
            <input type="radio" name="licence" value="COMP" <?php  if ($competition->licence =="COMP" )echo "checked"; ?>/>COMP -
        </td>
      </tr>
      <tr>
        <td style="width: 50%; text-align: right;">Validation des inscriptions : </td>
        <td><input type="radio" name="validation" value="0" <?php  if ($competition->validation =="0" )echo "checked"; ?>/>NON -
            <input type="radio" name="validation" value="1" <?php  if ($competition->validation =="1" )echo "checked"; ?>/>OUI -
        </td>
      </tr>

      <tr>
        <td style="width: 50%; text-align: right;"><input name="envoyer" value="Valider"  type="submit" /></td>
        <td></td>
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
