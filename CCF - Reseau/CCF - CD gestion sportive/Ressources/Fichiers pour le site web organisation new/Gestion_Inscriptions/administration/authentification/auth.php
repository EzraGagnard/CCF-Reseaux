<?php


	if(!isset($_POST['md5']))
	{
		header("Location: ../index.php");
		exit();
	}
	
	if(!isset($_POST['login']))
	{
		  header("Location: ../index.php");
		  exit();
	}

	if($_POST['login']==NULL)
	{
		  header("Location: ../index.php?&erreur=Requiert un identifiant et un mot de passe.");
		  exit();
	}


	require_once('../../definitions.inc.php');
	// connexion à la base
	$bdd = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);

    // utilisation de la méthode quote() 
    // Retourne une chaîne protégée, qui est théoriquement sûre à utiliser dans une requête SQL.

    $sql = sprintf("SELECT * FROM utilisateur WHERE utilisateur.login=%s", $bdd->quote($_POST['login']));
    $stmt = $bdd->query($sql);

	$utilisateur =  $stmt->fetchObject();
	
	
	// vérification des identifiants login et md5 par rapport à ceux enregistrés dans la base
	if (!($_POST['login'] == $utilisateur->login && $_POST['md5'] == $utilisateur->passe)){
		header("Location: ../index.php?&erreur=Incorrectes! Vérifiez vos identifiant et mot de passe.");
  		exit();
	}

	// A partir de cette ligne l'utilisateur est authentifié
	// donc nouvelle session
	session_start();

	// écriture des variables de session pour cet utilisateur

        $_SESSION['last_access'] = time();
        $_SESSION['ipaddr']		 = $_SERVER['REMOTE_ADDR'];
        $_SESSION['ID_user'] 	 = $utilisateur->ID_user;
		$_SESSION['login'] 		 = $utilisateur->login;
        $_SESSION['identite'] 	 = $utilisateur->identite;
		$_SESSION['email'] 		 = $utilisateur->email;
		$_SESSION['droits'] 	 = $utilisateur->droits;
		$_SESSION['path_fichier_perso'] = $utilisateur->path_fichier_perso;


	// enregistrement de la date et heure de son passage dans le champ date_connexion de la table utilisateur
        $ID_user  = $utilisateur->ID_user;
        $sql = "UPDATE `utilisateur` SET `date_connexion` = CURRENT_TIMESTAMP  WHERE `utilisateur`.`ID_user` =$ID_user LIMIT 1" ;
        $stmt = $bdd->query($sql);

	// Incrémentation du compteur de session
       $sql = "UPDATE utilisateur SET `nb_session` = `nb_session`+1 WHERE `utilisateur`.`ID_user` =$ID_user LIMIT 1" ;
       $stmt = $bdd->query($sql);
       

	// sélection de la page de menu en fonction des droits accordés

	switch ($utilisateur->droits) {
		case 0:  // Utilisateur révoqué sans droit
			 header("Location: ../index.php?&erreur=révoqué! ");
			 break;
		case 1:  // Organisateurs de niveau 1
			 header("Location: ../orga_menu.php");
			 break;
		case 2:  // Organisateurs de niveau 2
			 header("Location: ../orga_menu.php");
			 break;
		case 3:  // Administrateurs de niveau 3
			 header("Location: ../orga_menu.php");
			 break;	 
		
		default:
			 header("Location: ../index.php");
	}

?>
