<?php
// Ce script enregistre les propriété d'une compétition
// 

// page autoréférente  protégée
   include "authentification/authcheck.php" ;
   // Vérification des droits pour cette page uniquement organisateurs
   if ($_SESSION['droits']<>'2') { header("Location: index.php");};

require_once('../definitions.inc.php');


     // connexion à la base
    @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
    @mysql_select_db(BASE) or die("Echec de selection de la base cdt");

    if (!empty($_POST['envoyer'])){
      $sql = sprintf("UPDATE cross_route_configuration SET conf_value=%s  WHERE conf_id=%s  LIMIT 1",

                       GetSQLValueString($_POST['conf_value'], "text"),
                       GetSQLValueString($_POST['conf_id'] , "text")
                       );
          $Result1 = mysql_query($sql) or die(mysql_error());


    @mysql_close();
    $GoTo = "configuration.php";
    header(sprintf("Location: %s", $GoTo));
	exit;
    }

// création des constantes php
$sql = 'SELECT * FROM `cross_route_configuration`';
    $resultat2 = mysql_query($sql) or die(mysql_error());

    while ($conf = mysql_fetch_object ($resultat2)){
       define($conf->conf_key, $conf->conf_value);
      }

// recherche des infos enregistrées en fct de conf_id

if ((isset($_GET['conf_id'])) && ($_GET['conf_id'] != "")) {
        $sql = "SELECT * FROM cross_route_configuration WHERE conf_id=".$_GET['conf_id']."";
        $resultat = mysql_query($sql)or die (mysql_error());

        $conf = mysql_fetch_object ($resultat);
         }

 @mysql_close();


// fonction de protection SQL
    function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")
     {
     $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

     switch ($theType) {
       case "text":
          $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
          break;
       case "long":
       case "int":
         $theValue = ($theValue != "") ? intval($theValue) : "NULL";
         break;
       case "double":
         $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
       break;
          case "date":
          $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
       break;
          case "defined":
          $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
       break;
       }
       return $theValue;
      }
    // début du fichier bandeau menu horizontal
	if (!is_readable('en_tete.html'))  die ("fichier non accessible");
		@readfile('en_tete.html') or die('Erreur fichier');
?>



<script language="javascript">


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



  </script>




<div id="menu">
<p></p>
</div>
<div id="contenu">
     <h2>Configuration<br />
     <?php echo DESIGNATION ?></h2></h2>
     <div class="item">
     <p><?php echo $conf->conf_titre ?> </p>
     <form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']."?&conf_id=".$_GET['conf_id'] ?>"  >

     <input type='hidden' name='conf_id' value='<?php echo $_GET['conf_id']; ?>'/>
     <table style="text-align: left; width: 600px; height: 160px;"   border="0" cellpadding="2" cellspacing="2">
     <tbody>
      <tr>
        <td><b>Description</b></td>
        <td><?php echo $conf->conf_description ?></td>

      </tr>
      <tr>
        <td><b>Valeur</b></td>
        <td><input name="conf_value" value="<?php echo $conf->conf_value; ?>" size="50"/></td>

      </tr>


      <tr>
        <td><input name="envoyer" value="Valider"  type="submit" /></td>
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
