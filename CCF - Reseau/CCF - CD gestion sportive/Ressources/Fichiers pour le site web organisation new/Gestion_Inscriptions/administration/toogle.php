<?php
//--------------------------------------------------------------------
// Ce script permet de modifier la validation d'une compétition
// Compétition identifiée par son id transmis par la méthose GET
//
// page protégée
//--------------------------------------------------------------------

	include "authentification/authcheck.php" ;
	// Vérification des droits pour cette page uniquement organisateur
	if ($_SESSION['droits']<'2') { 
		header("Location: index.php");
	};

	require_once('../definitions.inc.php');
	require_once('utile_sql.php');


	// connexion à la base de données
	$bdd = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);

	if (isset($_GET['id']) && isset($_GET['val'])){
		$sql = sprintf("UPDATE competition SET validation=%s WHERE id=%s",
			GetSQLValueString($_GET['val'] , "text"),
			$_GET['id']
		);
        $stmt = $bdd->query($sql);   



	}
    
    $GoTo = "orga_menu.php";
    header("Location: " . $GoTo);
	exit;

?>
