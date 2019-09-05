<?php
 //------------------------------------------------------------------------
 // ce script supprime une compétition dans la table des compétitions.
 // l'épreuve est identifiée par id_competition méthode GET
 // version 1.0    2009
 // Auteur   SIMIER Philippe
 //------------------------------------------------------------------------
 
	// vérification des variables de session pour le temps d'inactivité et de l'adresse IP
	include "authentification/authcheck.php" ;
	// Vérification des droits pour cette page uniquement l'organisateur
	if ($_SESSION['droits']<'2') { 
		header("Location: ../index.html");
	};


	// connexion à la base
	require_once('../definitions.inc.php');
	$bdd = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);

	// récupération de la variable id_epreuve transmise par GET
	// et création de la requète SQL
	if ((isset($_GET['id_competition'])) && ($_GET['id_competition'] != "")) {

        // on efface l'engagement
        $sql = "DELETE FROM competition WHERE id=".$_GET['id_competition']."";
        $stmt = $bdd->query($sql);
	}
 
	// retour vers la page configuration des epreuve

	header("Location: competition.php");

?>
