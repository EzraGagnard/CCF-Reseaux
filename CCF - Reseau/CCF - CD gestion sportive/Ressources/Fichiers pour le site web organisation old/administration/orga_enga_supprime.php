<?php
 // ce script supprime un engagement dans la liste des engag�(e)s.
 // l'engagement est identifi� par id m�thode GET
 
// v�rification des variables de session pour le temps d'inactivit� et de l'adresse IP
include "authentification/authcheck.php" ;
// V�rification des droits pour cette page uniquement l'organisateur
if ($_SESSION['droits']<>'2') { header("Location: ../index.html");};


// connexion � la base
 require_once('../definitions.inc.php');
 @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("<p>Connexion impossible</p>");
 @mysql_select_db(BASE) or die("<p>Echec de selection de la base</p>");

 // r�cup�ration de la variable id transmise par GET
 // et cr�ation de la requ�te SQL
 if ((isset($_GET['id'])) && ($_GET['id'] != "")) {

        // on efface l'engagement
        $sql = "DELETE FROM cross_route_engagement WHERE id=".$_GET['id']."";
        $Res = mysql_query($sql) or die (mysql_error());
 }
 @mysql_close();
 // retour vers tableau_actualites
 $GoTo = "orga_tab_enga.php?competition=".urlencode(stripslashes($_GET['competition']));
 header(sprintf("Location: %s", $GoTo));

?>
