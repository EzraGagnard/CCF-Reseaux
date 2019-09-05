<?php
//------------------------------------------------------------------------------------
// Ce script liste les engagés(es) pour une compétition
// version 2.2
// février 2014 ajout de l'export des emails
// 2 Mars 2014 inversion de l'ordre d'affichage
// 17 Avril 2014 modification du bouton nouvel engagement 
// par ajout du nom de la compétition par la méthode GET
// 19 février 2015 ajout de la colonne Dossard
// page protégée 
//------------------------------------------------------------------------------------
	include "authentification/authcheck.php" ;
	// Vérification des droits pour cette page uniquement organisateurs
	if ($_SESSION['droits']<>'2') { 
		header("Location: ../index.html");
	};
	require_once('../definitions.inc.php');
	require_once('utile_sql.php');

	if(!isset($_GET['course'])) { 
		$_GET['course']=""; 
	}
	// connexion à la base
		$bdd = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);
		// création de l'objet competition pour connaitre la saison
		$sql = "SELECT * FROM `competition` WHERE `id`='".$_GET['competition']."'";
		$stmt = $bdd->query($sql);
		$competition = $stmt->fetchObject();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>Liste des engagé(e)s</title>
		<link rel=stylesheet href='../css/bassin.css' TYPE='text/css'>
		<style type="text/css">
		<!--
		div#rechercher {
			position: absolute;
			top: 12px;
			left: 120px;
			width: 155px;
		}
		form {
			background-color:#D0D0D0;
			border: 1px solid #999998;
			height: 28px;
		}

		form input {
			background-color:#D0D0D0;
			color: #000;
			font-size: 18px;
			border: 0px solid #ECE9D8;
			padding: 0px;
			padding-left: 5px;
			height: 25px;
			width: 117px;
			cursor: text;
		}

		.bouton {
			background-image: url(../images/loupe.png);
			background-color:#D0D0D0;
			color: #D0D0D0;
			border: 0px solid #ECE9D8;
			padding: 0px;
			height: 23px;
			width: 24px;
			cursor: pointer;
		}

		div#outils {
			position: relative;
			height: 60px;
		}

		table#tableau {
			clear: both;
			width: 99%;
			margin-left:5px;
			border-collapse: collapse;
			border-top: 5px solid #D0D0D0;
			border-bottom: 5px solid #D0D0D0;
			border-left:1px solid #D0D0D0;
			border-right:1px solid #D0D0D0;
		}
		
		table#tableau td {
			border-top: 1px solid #D0F0F0;
			padding: 4px;
		}

		tr.impaire {
			 background-color: #F0F0F0;
		}

		table#tableau th {
			background-color: #D0D0D0;
			color: #000000;
			padding: 4px;
		}

		#multi_page {
			position: absolute;
			top: 12px;
			left: 380px;
		}

		#multi_page  a {
			border: 1px solid #999998;
			padding: 5px;
			font-size: 14px;
			font-family:"Trebuchet MS",Verdana,Arial,Helvetica,sans-serif;
			line-height: 31px;
		}

		#multi_page  a:hover {
			background-color: #7074b3;
		}

		#multi_page .actuel {
			border: 1px solid #5d668c;
			padding: 5px;
			font-size: 14px;
			background-color: #384478;
			color: #FFF;
		}
		
		a.tooltip {
			cursor: help;
			text-decoration:none;
		}
		
		a.tooltip em {
			display:none;
		}
		
		a.tooltip:hover {
			border: 0; 
			position: relative; 
			z-index: 500; 
			text-decoration:none;
		}
		
		a.tooltip:hover em {
			font-style: normal; 
			display: block; 
			position: absolute; 
			top: 20px; 
			left: -10px; 
			padding: 5px; 
			color: #000; 
			border: 1px solid #bbb; 
			background: #ffc; 
			width:170px;
		}
		
		a.tooltip:hover em span {
			position: absolute; 
			top: -7px; 
			left: 15px;
			height: 7px; 
			width: 11px; 
			background: transparent url(images/image-infobulle.png);
			margin:0; 
			padding: 0;
			border: 0;
		}

		-->
		</style>

		<script language="JavaScript" type="text/JavaScript">
			<!--
			function GoToURL() { //v3.0
				var i, args=GoToURL.arguments; document.MM_returnValue = false;
				for (i=0; i<(args.length-1); i+=2) 
					eval(args[i]+".location='"+args[i+1]+"'");
			}
			function GoToURL_conf() { //v3.0
				var i, args=GoToURL_conf.arguments;
				document.MM_returnValue = false;
				Confirmation = confirm("Confirmez-vous la suppression de "+args[2]);
				if (Confirmation){
					for (i=0; i<(args.length-1); i+=2) 
						eval(args[i]+".location='"+args[i+1]+"'");
				}
			}

			//-->
		</script>
	</head>

<body topmargin="0" leftmargin="0">
	<div id="page">
		<div id="bandeau_flash" style="width: 1098px; height: 336px;">
			<a href="../../"><img style="width: 1098px; height: 336px; border: 0px;" title="Retour accueil" src="../images/bandeau_trail.jpg" /><br /></a>
		</div>

		<div id="contenu" style="width: 1090px; min-height:500px; margin-left: 4px; margin-right: 4px;">
			<h2>Liste des <I>Engagé(e)s <?php echo $competition->nom; ?></I></h2>
			<div id="outils">
				<a href="orga_menu.php"><img src="../images/fleche_retour.png" title="Retour" border="0" width="44" height="41"></a>
				<a href="orga_enga_ajouter.php?competition=<?php echo stripslashes($_GET['competition']); ?>" title="Créer un nouvel engagement">
					<img border="0" src="../images/addusers.png">
				</a>
				<div id="rechercher">
                    <form action=""<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="GET" name="form1">
						<input  type='hidden' name="competition" value="<?php echo stripslashes($_GET['competition']); ?>" />
						<input  name="lettre"  value="Rechercher" style="" size="10" onFocus="this.value='';" />
						<input  class= "bouton" type="submit"  value="_" name="rechercher" />
                    </form>
				</div>


	<?php
		require_once('inc/multi_lettre.php');

		
	
		// Affiche le tableau des engagé(e)s
		// en associant à chaque engagé un lien pour la modification et un autre pour la suppression
     
		// Si la variable lettre n'exite pas on commence par la lettre vide tout classé par date
		if(!isset($_GET['lettre'])) { $_GET['lettre']=""; }
		if(!isset($_GET['ordre'])) { $_GET['ordre']="date DESC"; }

		$sql = sprintf("SELECT * FROM cross_route_engagement WHERE `id_competition`=%s",
              GetSQLValueString($_GET['competition'], "text")
              );
		$sql .= " AND nom LIKE '".$_GET['lettre']."%' ORDER BY ".$_GET['ordre'];
		$stmt = $bdd->query($sql);
		
		$lien =  $_SERVER['SCRIPT_NAME'].'?competition='.$_GET['competition']; //+? pour la methode GET
		makeListLettre($lien);


		echo '</div><table id="tableau">'."\n"
		   . "<tr><th><a href='".$lien."&ordre=date&lettre=".$_GET['lettre']."'>Date</a></th>"
		   . "<th><a href='".$lien."&ordre=dossard&lettre=".$_GET['lettre']."'>Dossard</a></th>"
		   . "<th><a href='".$lien."&ordre=nolicence&lettre=".$_GET['lettre']."'>Licence</a></th>"
		   . "<th><a href='".$lien."&ordre=nom&lettre=".$_GET['lettre']."'>Nom</a></th>"
		   . "<th><a href='".$lien."&ordre=prenom&lettre=".$_GET['lettre']."'>Prénom</a></th>"
		   . "<th><a href='".$lien."&ordre=anneenaissance&lettre=".$_GET['lettre']."'>Naiss.</a></th>"
		   . "<th><a href='".$lien."&ordre=sexe&lettre=".$_GET['lettre']."'>Sexe</a></th>"
		   . "<th><a href='".$lien."&ordre=categorie&lettre=".$_GET['lettre']."'>Cat</a></th>"
		   . "<th><a href='".$lien."&ordre=email&lettre=".$_GET['lettre']."'>Email</a></th>"
		   . "<th><a href='".$lien."&ordre=nomequipe&lettre=".$_GET['lettre']."'>Equipe</a></th>"
		   . "<th><a href='".$lien."&ordre=certifmedicalfourni&lettre=".$_GET['lettre']."'>Certi.</a></th>"
		   . "<th><a href='".$lien."&ordre=cotisationpaye&lettre=".$_GET['lettre']."'>Coti.</a></th>"
		   . "<th><a href='".$lien."&ordre=nomcourse&lettre=".$_GET['lettre']."'>Course</a></th>"
		   . "<th>M</th><th>S</th></tr>\n";

		$ligne_i = 0;
		while ($engagement = $stmt->fetchObject()){   
			if ($ligne_i++ % 2 == 1) {
                $class=' class="impaire"';
			} 
			else {
               	$class='';
            }
			echo  '<tr'.$class.'><td>'.$engagement->date.'</td>'."\n"
				. '<td>'.$engagement->dossard.'</td>'."\n"
				. '<td><a href="http://bases.athle.com/asp.net/liste.aspx?frmpostback=true&frmbase=resultats&frmmode=1&frmespace=0&frmsaison='
				. '&frmclub=&frmnom=&frmprenom=&frmsexe=&frmlicence='
				. $engagement->nolicence.'&frmdepartement=&frmligue=&frmcomprch=" target="_blank">'.$engagement->nolicence.'</a></td>'."\n"
				. '<td>'.substr($engagement->nom, 0, 10).'</td>'."\n"         // les 10 premiers caractères
				. '<td>'.$engagement->prenom.'</td>'."\n"
				. '<td>'.$engagement->anneenaissance.'</td>'."\n"
				. '<td>'.$engagement->sexe.'</td>'."\n"
				. '<td>'.$engagement->categorie.'</td>'."\n"
				. '<td>'.substr($engagement->email, 0, 20).'</td>'."\n"		// les 20 premiers caractères
				. '<td>'.substr($engagement->nomequipe, 0, 20).'</td>'."\n"   // les 20 premiers caractères
				. '<td>'.aff_oui_non($engagement->certifmedicalfourni).'</td>'."\n"
				. '<td>'.aff_oui_non($engagement->cotisationpaye).'</td>'."\n"
				. '<td>'.$engagement->nomcourse.'</td>'."\n"
				. '<td>'
				. '<img src="../images/button_edit.png" style="cursor: pointer;" title="Modifier" width="12" height="13" onClick="GoToURL(\'window\',\'orga_enga_modif.php?'
				. '&id='.$engagement->id.'&competition='.urlencode(stripslashes($_GET['competition'])).'\');return document.MM_returnValue"></td>'."\n";


			echo '<td>'
				. '<img src="../images/ed_delete.gif" style="cursor: pointer;" title="Supprimer" width="12" height="13" onClick="GoToURL_conf(\'window\',\'orga_enga_supprime.php?'
				. '&id='.$engagement->id.'&competition='.urlencode(stripslashes($_GET['competition'])).'\',\''.$engagement->nom.'\');return document.MM_returnValue"></td>'."\n"
				. '</tr>'."\n";

		}
		echo "</table>\n<br />";


		echo '<p><a href="export_logica.php?id_competition='. $_GET['competition'] .'"><img src="../images/logica.jpg" title="exporter pour logica" border="0" width="49" height="48"></a>';
		echo '<a href="export_emails.php?id_competition='   . $_GET['competition'] .'"><img src="../images/email.jpg" title="exporter les emails" border="0" width="49" height="48"></a>';
		echo '  <a href="export_excel.php?id_competition='  . $_GET['competition'] .'"><img src="../images/excel.jpg" title="exporter pour excel" border="0" width="49" height="48"></a>';
		echo '<br /><p/> ';

	// cette fonction affiche oui en vert ou non en rouge

    function aff_oui_non($val){
		if ($val=='oui')  return "<span style=\"color:#00FF00\">oui</span>"; else return "<span style=\"color:#FF0000\">non</span>";
	}

	?>
    </div>
    <?php
     @readfile('pied_de_page.html') or die('Erreur fichier');
	?>
    </div>
</body>
</html>
