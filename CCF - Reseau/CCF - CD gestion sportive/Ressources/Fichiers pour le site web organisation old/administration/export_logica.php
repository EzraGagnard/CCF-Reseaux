<?php
//------------------------------------------------------------------------------------
// ce script  exporte  le tableau des engagements au format Logica
// format TSV tab-separated-values
// Auteur: SIMIER Philippe Endurance72  Janvier 2009
// octobre 2009 ajout du champ dossard
// 27 octobre 2009 sélection en fonction du champ compétition
// 31 mars 2014 ajout des champs Nodept, Noligue, typelicence
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

		$sql = sprintf("SELECT * FROM cross_route_engagement WHERE `nomcourse`=%s ORDER BY %s",
            GetSQLValueString($_GET['course'], "text"),
            GetSQLValueString($_GET['ordre'], "text")
        );
		$nom_fichier="engages_".stripslashes($_GET['course']).".txt";
    }
    else {

		$sql = sprintf("SELECT * FROM cross_route_engagement WHERE `competition`=%s ORDER BY %s",
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
	
	
    // les deux premières lignes d'un fichier Logica
    echo "Dossard\tLicence\tNom\tPrénom\tNationalité\tN° club athlète\tN° club équipe\téquipe\tN° équipe\tE/I\tinfo libre\tDépartement équipe\tLigue équipe\tChallenge\tInfo utilisateur\tAnnée naissance\tCatégorie\tSexe\tDépartement\tLigue\tNom liste d'engagé(e)s\tNom course\tCode d'appel\tDistance\tDurée\tPlace\tPerf\tQualif\tLieu\tTitre compétition\tDate compétition\tAdresse 1 athlète\tAdresse 2 athlète\tCode postal athlète\tVille athlète\tPratiquant\ttype de licence\tCotisation\tCertif. médical\tHC\tInvite\tPerf Engagement\r\n";
    echo "dossard\tnolicence\tnom\tprenom\tnationalite\tnoclub\tnoclubequipe\tnomequipe\tindiceequipe\ttypeengagement\tcommentaireengagement\tnodeptequipe\tligueequipe\ttypeparticipant\tcommentaire\tanneenaissance\tcategorie\tsexe\tnodept\tnoligue\tnomepreuve\tnomcourse\tcodeappel\tdistancecourse\tduree\tplace\tperformancen\tqualif\tlieucompetition\tnomcompetition\tdebutcompetition\tadresse1\tadresse2\tcodepostal\tville\tpratiquant\ttypelicence\tcotisationpaye\tcertifmedicalfourni\thc\tinvite\tperfengagementn\r\n";

if (mysql_num_rows($resultat) != 0) {

  // données de la table
  while ($engagement = mysql_fetch_object ($resultat))
    {
    	echo $engagement->dossard."\t";            		// dossard
        echo $engagement->nolicence."\t";          		// numéro de licence
        echo $engagement->nom."\t";                		// nom
        echo $engagement->prenom."\t\t";           		// prénom
        echo $engagement->noclub."\t";             		// Numéro du club athlète
        echo $engagement->noclub."\t";             		// Numéro du club équipe
        echo $engagement->nomequipe."\t\t";        		// le nom de l'équipe
        echo $engagement->typeengagement."\t";     		// engagement individuel ou en équipe
        for ($i=0; $i<3; $i++) {echo "\t"; };      		// saut de 3 colonnes
        echo $engagement->typeparticipant."\t";    		// le challenge ffa, ent
        echo $engagement->paiement." - ".$engagement->commentaire."\t"; // Le mode de paiement et le commentaire
        echo $engagement->anneenaissance."\t";     		// l'année de naissance
        echo $engagement->categorie."\t";          		// la catégorie FFA
        echo $engagement->sexe."\t";               		// le sexe   M ou F
        echo $engagement->nodept."\t";             		// n° du département
		echo $engagement->noligue."\t";            		// n° de la ligue
        echo $engagement->nomcourse."\t";          		// Nom épreuve
        echo $engagement->nomcourse."\t";          		// Nom de la course
        for ($i=0; $i<9; $i++) {echo "\t"; };      		// saut de 9 colonnes
        echo $engagement->adresse1."\t\t";         		// adresse de l'engagé(e)
        echo $engagement->codepostal."\t";         		// son code postal
        echo $engagement->ville."\t\t";            		// la ville
		echo $engagement->typelicence."\t";		   		// le type de licence	
        echo v_f($engagement->cotisationpaye)."\t";         // cotisation payée
        echo v_f($engagement->certifmedicalfourni)."\t";    // certificat médical fourni
        echo "\t\t\t";
        echo "\r\n";                                // retour à la ligne

    }
}

// fonction pour convertir un oui/non en chaine Vrai/Faux
function v_f($bool)
 { 
 if ($bool=="oui") return "Vrai"; else return "Faux";
 }
