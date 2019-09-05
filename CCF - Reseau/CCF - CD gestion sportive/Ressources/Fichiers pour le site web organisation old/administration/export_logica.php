<?php
//------------------------------------------------------------------------------------
// ce script  exporte  le tableau des engagements au format Logica
// format TSV tab-separated-values
// Auteur: SIMIER Philippe Endurance72  Janvier 2009
// octobre 2009 ajout du champ dossard
// 27 octobre 2009 s�lection en fonction du champ comp�tition
// 31 mars 2014 ajout des champs Nodept, Noligue, typelicence
//------------------------------------------------------------------------------------
// v�rification des variables de session pour le temps d'inactivit� et de l'adresse IP
include "authentification/authcheck.php" ;
// V�rification des droits pour cette page tous sauf les exclus
if ($_SESSION['droits']<>'2') { header("Location: index.php");};


require_once('../definitions.inc.php');
require_once('utile_sql.php');
// connexion � la base
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
	// header pour d�finir le nom du fichier (les espace sont remplacer par _ )
	 $search  = array(' ');
	 $replace = array('_');
	 $nom_fichier = str_replace($search, $replace, $nom_fichier);
    header('Content-Disposition:attachment;filename='.$nom_fichier);
	
	
    // les deux premi�res lignes d'un fichier Logica
    echo "Dossard\tLicence\tNom\tPr�nom\tNationalit�\tN� club athl�te\tN� club �quipe\t�quipe\tN� �quipe\tE/I\tinfo libre\tD�partement �quipe\tLigue �quipe\tChallenge\tInfo utilisateur\tAnn�e naissance\tCat�gorie\tSexe\tD�partement\tLigue\tNom liste d'engag�(e)s\tNom course\tCode d'appel\tDistance\tDur�e\tPlace\tPerf\tQualif\tLieu\tTitre comp�tition\tDate comp�tition\tAdresse 1 athl�te\tAdresse 2 athl�te\tCode postal athl�te\tVille athl�te\tPratiquant\ttype de licence\tCotisation\tCertif. m�dical\tHC\tInvite\tPerf Engagement\r\n";
    echo "dossard\tnolicence\tnom\tprenom\tnationalite\tnoclub\tnoclubequipe\tnomequipe\tindiceequipe\ttypeengagement\tcommentaireengagement\tnodeptequipe\tligueequipe\ttypeparticipant\tcommentaire\tanneenaissance\tcategorie\tsexe\tnodept\tnoligue\tnomepreuve\tnomcourse\tcodeappel\tdistancecourse\tduree\tplace\tperformancen\tqualif\tlieucompetition\tnomcompetition\tdebutcompetition\tadresse1\tadresse2\tcodepostal\tville\tpratiquant\ttypelicence\tcotisationpaye\tcertifmedicalfourni\thc\tinvite\tperfengagementn\r\n";

if (mysql_num_rows($resultat) != 0) {

  // donn�es de la table
  while ($engagement = mysql_fetch_object ($resultat))
    {
    	echo $engagement->dossard."\t";            		// dossard
        echo $engagement->nolicence."\t";          		// num�ro de licence
        echo $engagement->nom."\t";                		// nom
        echo $engagement->prenom."\t\t";           		// pr�nom
        echo $engagement->noclub."\t";             		// Num�ro du club athl�te
        echo $engagement->noclub."\t";             		// Num�ro du club �quipe
        echo $engagement->nomequipe."\t\t";        		// le nom de l'�quipe
        echo $engagement->typeengagement."\t";     		// engagement individuel ou en �quipe
        for ($i=0; $i<3; $i++) {echo "\t"; };      		// saut de 3 colonnes
        echo $engagement->typeparticipant."\t";    		// le challenge ffa, ent
        echo $engagement->paiement." - ".$engagement->commentaire."\t"; // Le mode de paiement et le commentaire
        echo $engagement->anneenaissance."\t";     		// l'ann�e de naissance
        echo $engagement->categorie."\t";          		// la cat�gorie FFA
        echo $engagement->sexe."\t";               		// le sexe   M ou F
        echo $engagement->nodept."\t";             		// n� du d�partement
		echo $engagement->noligue."\t";            		// n� de la ligue
        echo $engagement->nomcourse."\t";          		// Nom �preuve
        echo $engagement->nomcourse."\t";          		// Nom de la course
        for ($i=0; $i<9; $i++) {echo "\t"; };      		// saut de 9 colonnes
        echo $engagement->adresse1."\t\t";         		// adresse de l'engag�(e)
        echo $engagement->codepostal."\t";         		// son code postal
        echo $engagement->ville."\t\t";            		// la ville
		echo $engagement->typelicence."\t";		   		// le type de licence	
        echo v_f($engagement->cotisationpaye)."\t";         // cotisation pay�e
        echo v_f($engagement->certifmedicalfourni)."\t";    // certificat m�dical fourni
        echo "\t\t\t";
        echo "\r\n";                                // retour � la ligne

    }
}

// fonction pour convertir un oui/non en chaine Vrai/Faux
function v_f($bool)
 { 
 if ($bool=="oui") return "Vrai"; else return "Faux";
 }
