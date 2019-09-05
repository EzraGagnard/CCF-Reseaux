<?php
 // ce script supprime un engagement dans la liste des engagé(e)s.
 // l'engagement est identifié par id méthode GET
 
// vérification des variables de session pour le temps d'inactivité et de l'adresse IP
include "authentification/authcheck.php" ;
// Vérification des droits pour cette page uniquement l'organisateur
if ($_SESSION['droits']<>'2') { header("Location: ../index.html");};


// connexion à la base
 require_once('../definitions.inc.php');
 @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("<p>Connexion impossible</p>");
 @mysql_select_db(BASE) or die("<p>Echec de selection de la base</p>");

 // récupération de la variable id transmise par GET
 // et création de la requète SQL
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
