<?php
 //-----------------------------------------------------
 // script réponse au format JSON à la requete Ajax
 // un seul paramétre noclub (par la méthode POST)
 //-----------------------------------------------------
	require_once('definitions.inc.php');
	
	if (!isset($_POST['noclub']) {
        echo "{}";
        exit;
    };

    // ouverture connexion à la base 
    
	$bdd = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);
    $sql = "SELECT * FROM ffa_club WHERE `noclub`=".$_POST['noclub'];
    $stmt = $bdd->query($sql);   
    $club = $stmt->fetchObject();
    $reponse = '{';

    if ($club){
        $reponse .= '"club":"'.trim($club->nom).'"';
        $reponse .= ',"sigle":"'.trim($club->sigle).'"';
        $reponse .= ',"noclub":"'.trim($club->noclub).'"';
    }

    $reponse .='}';
    echo $reponse;

exit;
?>



