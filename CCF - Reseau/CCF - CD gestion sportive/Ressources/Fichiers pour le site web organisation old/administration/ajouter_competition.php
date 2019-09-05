<?php
//---------------------------------------------------------------------
// Ce script enregistre une nouvelle compétition
// avec son horaire
// 28 Octobre 2009
// page autoréférente protégée
//---------------------------------------------------------------------
   include "authentification/authcheck.php" ;
   // Vérification des droits pour cette page uniquement organisateurs
   if ($_SESSION['droits']<>'2') { header("Location: index.php");};
require_once('../definitions.inc.php');
require_once('utile_sql.php');

 // connexion à la base marsouin
    @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
    @mysql_select_db(BASE) or die("Echec de selection de la base cdt");
    
        // Lecture configuration  saison
    $sql = "SELECT * FROM `cross_route_configuration`";
    $resultat = mysql_query($sql) or die(mysql_error());

    while ($conf = mysql_fetch_object ($resultat)){
       define($conf->conf_key, $conf->conf_value);
      }
    // fin de la lecture configuration saison

$erreur="";
if( !empty($_POST['envoyer'])){
     
     if ($_POST['nom']=="") {
        $erreur = "Vous devez indiquer un libellé à la compétition";
     };
     if ($_POST['date']=="") {
        $erreur = "Vous devez indiquer la date de la compétition";
     };

 if ($erreur == ""){
    if ($_POST['mode']=="insertion"){


      $insertSQL = sprintf("INSERT INTO competition (`saison` ,`nom` ,`date` , `lieu` , `organisateur` ,`validation` ,`licence` , `email` ) VALUES (%s,%s,%s,%s,%s,%s,%s,%s)",
                 SAISON,
                 GetSQLValueString($_POST['nom'], "text"),
                 GetSQLValueString($_POST['date'], "text"),
                 GetSQLValueString($_POST['lieu'], "text"),
                 GetSQLValueString($_POST['organisateur'], "text"),
                 GetSQLValueString($_POST['validation'], "text"),
                 GetSQLValueString($_POST['licence'], "text"),
                 GetSQLValueString($_POST['email'], "text")
                 );
      $Result1 = mysql_query($insertSQL) or die(mysql_error());
    }


    @mysql_close();

    header("Location: competition.php");
    exit;
 }
}
@mysql_close();
// début du fichier bandeau menu horizontal
  if (!is_readable('en_tete.html'))  die ("fichier non accessible");
  @readfile('en_tete.html') or die('Erreur fichier');
?>
<link href="calendrier.css" type="text/css" rel="StyleSheet" />
<script src="jscript/calendrier.js" type="text/javascript"></script>

<div id="menu" style="width: 100px">
<p></p>
</div>
<div id="contenu">
     <h2><a href="competition.php"><img src="../images/fleche_retour.png" title="Retour" border="0" width="44" height="41"></a>Nouvelle Compétition pour : <?php echo DESIGNATION ; ?> </h2>
     <?php if ($erreur) {echo '<p style="color:#FF0000;">'.$erreur."</p>"; } else { echo "<p> </p>"; }?>
     <div class="item">

     <p style="font-weight:bold;">informations sur la compétition : </p>
     <form method="post" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>"  name="epreuve" onSubmit="return verif();">
     <input type="hidden" name="mode" value="insertion" />


     <table style="text-align: left; width: 600px; height: 160px;"   border="0" cellpadding="2" cellspacing="2">
     <tbody>
        <tr>
            <td style="width: 50%; text-align: right;" >Libellé de la compétition : </td>
            <td style="width: 50%; ">
               <input name="nom" class="normal" size="25" maxlength="25" />
            </td>
        </tr>
        <tr>
            <td style="width: 50%; text-align: right;">Lieu de la compétition : </td>
            <td style="width: 75%; ">
               <input name="lieu" class="normal" size="25" maxlength="25" />
            </td>
        </tr>
        <tr>
            <td style="width: 50%; text-align: right;">Organisateur : </td>
            <td>
               <input name="organisateur" class="normal" size="25" maxlength="25" />
            </td>
        </tr>
        <tr>
            <td style="width: 50%; text-align: right;">Email organisateur : </td>
            <td><input name="email" class="normal"/></td>
        </tr>
        <tr>
            <td style="width: 50%; text-align: right;">Date : </td>
            <td><input name="date" class="date" size="10" /></td>
        </tr>
        <tr>
            <td style="width: 50%; text-align: right;">Validation des inscriptions: </td>
            <td> <input type="radio" name="validation" value="0" checked>NON -
                 <input type="radio" name="validation" value="1">OUI
            </td>
        </tr>
        <tr>
        <td style="width: 50%; text-align: right;">Type de licence autorisés : </td>
        <td><input type="radio" name="licence" value="" />Toutes -
            <input type="radio" name="licence" value="COMP" />COMP -
        </td>
      </tr>


        <td style="width: 50%; text-align: right;">
           <input name="envoyer" value="Valider"  type="submit" />
        </td>
        <td></td>
        <td></td>

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
</div>
</body></html>
