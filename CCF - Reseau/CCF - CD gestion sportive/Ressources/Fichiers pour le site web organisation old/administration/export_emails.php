<?php
//------------------------------------------------------------------------------------
// Ce script  exporte  la liste des emails
// format TSV tab-separated-values
// Auteur: SIMIER Philippe Endurance72  Février 2014
// 
//------------------------------------------------------------------------------------
// vérification des variables de session pour le temps d'inactivité et de l'adresse IP
include "authentification/authcheck.php" ;
// Vérification des droits pour cette page tous sauf les exclus
if ($_SESSION['droits']<>'2') { header("Location: index.php");};


require_once('../definitions.inc.php');
require_once('utile_sql.php');
// connexion à la base
     @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
     @mysql_select_db(BASE) or die("Echec de selection de la base cdt");


     // Si la variable ordre n'exite pas on trie suivant id

     if(!isset($_GET['ordre'])) { $_GET['ordre']="id"; }

     if (isset($_GET['course'])) {

     $sql = sprintf("SELECT * FROM cross_route_engagement WHERE `nomcourse`=%s AND email<>'NULL' ORDER BY %s",
            GetSQLValueString($_GET['course'], "text"),
            GetSQLValueString($_GET['ordre'], "text")
            );
     $nom_fichier="engages_".stripslashes($_GET['course']).".txt";
     }
     else {

     $sql = sprintf("SELECT * FROM cross_route_engagement WHERE `competition`=%s AND email<>'NULL' ORDER BY %s",
            GetSQLValueString($_GET['competition'], "text"),
            GetSQLValueString($_GET['ordre'], "text")
            );
     $nom_fichier=stripslashes($_GET['competition']).".txt";
     }

    $resultat = mysql_query($sql);



    header('Content-Type: application/csv-tab-delimited-table');
	// header pour définir le nom du fichier (les espace sont remplacer par _ )
	 $search  = array(' ');
	 $replace = array('_');
	 $nom_fichier = str_replace($search, $replace, $nom_fichier);
	
    header('Content-Disposition:attachment;filename='.$nom_fichier);
 
if (mysql_num_rows($resultat) != 0) {

  // données de la table
  while ($engagement = mysql_fetch_object ($resultat))
    {
    	echo '"'.$engagement->prenom.' ';        // prenom
        echo $engagement->nom.'"<';              // nom
		echo $engagement->email.'>,';  			// email	
        

    }
}


