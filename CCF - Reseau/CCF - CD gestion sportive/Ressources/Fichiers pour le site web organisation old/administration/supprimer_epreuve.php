<?php
 //------------------------------------------------------------------------
 // ce script supprime une �preuve dans la table des �preuves.
 // l'�preuve est identifi�e par id_epreuve m�thode GET
 // version 1.0    2009
 // Endurance72    SIMIER Philippe
 //------------------------------------------------------------------------
 
// v�rification des variables de session pour le temps d'inactivit� et de l'adresse IP
include "authentification/authcheck.php" ;
// V�rification des droits pour cette page uniquement l'organisateur
if ($_SESSION['droits']<>'2') { header("Location: ../index.html");};


// connexion � la base
 require_once('../definitions.inc.php');
 @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("<p>Connexion impossible</p>");
 @mysql_select_db(BASE) or die("<p>Echec de selection de la base</p>");

 // r�cup�ration de la variable id_epreuve transmise par GET
 // et cr�ation de la requ�te SQL
 if ((isset($_GET['id_epreuve'])) && ($_GET['id_epreuve'] != "")) {

        // on efface l'engagement
        $sql = "DELETE FROM cross_route_epreuve WHERE id_epreuve=".$_GET['id_epreuve']."";
        $Res = mysql_query($sql) or die (mysql_error());
 }
 @mysql_close();
 // retour vers la page configuration des epreuve

 header("Location: epreuve.php");

?>
