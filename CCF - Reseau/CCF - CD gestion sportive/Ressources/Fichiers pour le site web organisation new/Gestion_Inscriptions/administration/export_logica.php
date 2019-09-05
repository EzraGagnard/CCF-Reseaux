<?php
//------------------------------------------------------------------------------------
// ce script  exporte  le tableau des engagements au format Logica
// format TSV tab-separated-values
// Auteur: SIMIER Philippe LycÃ©e Touchard  Janvier 2009
// octobre 2009 ajout du champ dossard
// 27 octobre 2009 sÃ©lection en fonction du champ compÃ©tition
// 31 mars 2014 ajout des champs Nodept, Noligue, typelicence
//  8 mai  2018 mise Ã  jour compatibilitÃ© PHP7 et encodage UTF8
//------------------------------------------------------------------------------------
// vÃ©rification des variables de session pour le temps d'inactivitÃ© et de l'adresse IP
	include "authentification/authcheck.php" ;
	// VÃ©rification des droits pour cette page tous sauf les exclus
	if ($_SESSION['droits']<'2') { 
		header("Location: index.php");
	};


	require_once('../definitions.inc.php');
	require_once('utile_sql.php');
	
	// connexion Ã  la base
    $bdd = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);

	// CrÃ©ation d'un objet compÃ©tition
	if( !empty($_GET['competition'])){
		$sql = 'SELECT * FROM `competition` WHERE id =' . $_GET['competition'];
		$stmt = $bdd->query($sql);
		$competition = $stmt->fetchObject();
	}
	
    // Si la variable ordre n'exite pas on trie suivant id

    if(!isset($_GET['ordre'])) { $_GET['ordre']="id"; }

    if (isset($_GET['course'])) {

		$sql = sprintf("SELECT * FROM cross_route_engagement WHERE `nomcourse`=%s ORDER BY %s",
            GetSQLValueString($_GET['course'], "text"),
            GetSQLValueString($_GET['ordre'], "text")
        );
		$nom_fichier="engages_".stripslashes($_GET['course']).".txt";
    }
    else {

		$sql = sprintf("SELECT * FROM cross_route_engagement WHERE `competition`=%s ORDER BY %s",
            GetSQLValueString($competition->nom, "text"),
            GetSQLValueString($_GET['ordre'], "text")
        );
		$nom_fichier=$competition->nom.".txt";
    }
	
    $stmt = $bdd->query($sql);



    header('Content-Type: application/csv-tab-delimited-table');
	// header pour dÃ©finir le nom du fichier (les espace sont remplacer par _ )
	 $search  = array(' ');
	 $replace = array('_');
	 $nom_fichier = str_replace($search, $replace, $nom_fichier);
    header('Content-Disposition:attachment;filename='.$nom_fichier);
	
	
    // les deux premiÃ©res lignes d'un fichier Logica
    echo "Dossard\tLicence\tNom\tPrénom\tNationalité\tN° club athlète\tN° club équipe\téquipe\tN° équipe\tE/I\tinfo libre\tDépartement équipe\tLigue équipe\tChallenge\tInfo utilisateur\tAnnée naissance\tCatégorie\tSexe\tDépartement\tLigue\tNom liste d'engagé(e)s\tNom course\tCode d'appel\tDistance\tDurée\tPlace\tPerf\tQualif\tLieu\tTitre compétition\tDate compétition\tAdresse 1 athlète\tAdresse 2 athlète\tCode postal athlète\tVille athlète\tPratiquant\ttype de licence\tCotisation\tCertif. médical\tHC\tInvite\tPerf Engagement\r\n";
    echo "dossard\tnolicence\tnom\tprenom\tnationalite\tnoclub\tnoclubequipe\tnomequipe\tindiceequipe\ttypeengagement\tcommentaireengagement\tnodeptequipe\tligueequipe\ttypeparticipant\tcommentaire\tanneenaissance\tcategorie\tsexe\tnodept\tnoligue\tnomepreuve\tnomcourse\tcodeappel\tdistancecourse\tduree\tplace\tperformancen\tqualif\tlieucompetition\tnomcompetition\tdebutcompetition\tadresse1\tadresse2\tcodepostal\tville\tpratiquant\ttypelicence\tcotisationpaye\tcertifmedicalfourni\thc\tinvite\tperfengagementn\r\n";



  // donnÃ©es de la table
  while ($engagement = $stmt->fetchObject())
    {
    	echo $engagement->dossard."\t";            		// dossard
        echo $engagement->nolicence."\t";          		// numÃ©ro de licence
        echo utf8_decode($engagement->nom)."\t";        // nom
        echo utf8_decode($engagement->prenom)."\t\t";   	// prÃ©nom
        echo $engagement->noclub."\t";             			// NumÃ©ro du club 
        echo $engagement->noclub."\t";             			// NumÃ©ro du club Ã©quipe
        echo utf8_decode($engagement->nomequipe)."\t\t";	// le nom de l'Ã©quipe
        echo $engagement->typeengagement."\t";     		// engagement individuel ou en Ã©quipe
        for ($i=0; $i<3; $i++) {echo "\t"; };      		// saut de 3 colonnes
        echo $engagement->typeparticipant."\t";    		// le challenge ffa, ent
        echo utf8_decode($engagement->paiement." - ".$engagement->commentaire)."\t"; // Le mode de paiement et le commentaire
        echo $engagement->anneenaissance."\t";     		// l'annÃ©e de naissance
        echo $engagement->categorie."\t";          		// la catÃ©gorie FFA
        echo $engagement->sexe."\t";               		// le sexe   M ou F
        echo $engagement->nodept."\t";             		// nÂ° du dÃ©partement
		echo $engagement->noligue."\t";            		// nÂ° de la ligue
        echo utf8_decode($engagement->nomcourse)."\t";          		// Nom Ã©preuve
        echo utf8_decode($engagement->nomcourse)."\t";          		// Nom de la course
        for ($i=0; $i<9; $i++) {echo "\t"; };      		// saut de 9 colonnes
        echo utf8_decode($engagement->adresse1)."\t\t";         		// adresse de l'engagÃ©(e)
        echo $engagement->codepostal."\t";         		// son code postal
        echo utf8_decode($engagement->ville)."\t\t";            		// la ville
		echo $engagement->typelicence."\t";		   		// le type de licence	
        echo v_f($engagement->cotisationpaye)."\t";         // cotisation payÃ©e
        echo v_f($engagement->certifmedicalfourni)."\t";    // certificat mÃ©dical fourni
        echo "\t\t\t";
        echo "\r\n";                                // retour Ã  la ligne

    }


// fonction pour convertir un oui/non en chaine Vrai/Faux
function v_f($bool)
 { 
 if ($bool=="oui") return "Vrai"; else return "Faux";
 }
