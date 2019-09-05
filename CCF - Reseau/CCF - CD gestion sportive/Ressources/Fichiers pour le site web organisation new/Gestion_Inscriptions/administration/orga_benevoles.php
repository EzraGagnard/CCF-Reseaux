<?php
	// vérification des variables de session pour le temps d'inactivité et de l'adresse IP
	include "authentification/authcheck.php" ;
	// Vérification des droits pour cette page uniquement admin niveau 3
	if ($_SESSION['droits']<'3'){ 
		header("Location: ../index.php");
	};
	require_once('../definitions.inc.php');
	// début du fichier bandeau menu horizontal
	if (!is_readable('en_tete.html'))  die ("fichier non accessible");
		@readfile('en_tete.html') or die('Erreur fichier');
?>
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
	  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
	}
	function GoToURL_conf() { //v3.0
	  var i, args=GoToURL_conf.arguments;
	  document.MM_returnValue = false;
	  Confirmation = confirm("Confirmez-vous la suppression de cet acteur?");
	  if (Confirmation){
	  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
	  }
	}
	function effacer(e){
	 if (e.value=="Rechercher"){
		e.value="";
	 }
	}
	//-->
	</script>



    <div id="contenu" style=" min-height:400px;">
        <h2>Liste des acteurs</h2>
        <div id="outils">
            <a href="orga_menu.php"><img src="../images/fleche_retour.png" title="Retour" border="0" width="44" height="41"></a>
            <a href="orga_benevole_ajouter.php" title="Ajouter un acteur"><img border="0" src="../images/addusers.png"></a>
            <div id="rechercher">
                <form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="GET" name="form1">
					<input  name="lettre"  value="Rechercher" style="" size="10" onFocus="effacer(this);" />
					<input  class= "bouton" type="submit"  value="_" name="rechercher" />
                </form>
			</div>
			<?php
				require_once('inc/multi_lettre.php');

			// connexion à la base
			$bdd = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);

			// Affiche le tableau les utilisateurs inscrits dans la table cdt_prof
			// en associant à chaque utilisateur un lien pour la modification et un autre pour la suppression
     
			// Si la variable lettre n'exite pas on commence par la lettre A classé par identite
			if(!isset($_GET['lettre'])) { $_GET['lettre']=""; }
			if(!isset($_GET['ordre'])) { $_GET['ordre']="identite"; }

			$sql = "SELECT * FROM utilisateur WHERE login ";
			$sql .= "LIKE '".$_GET['lettre']."%'";
			$sql .= " ORDER BY ".$_GET['ordre'];


			$stmt = $bdd->query($sql);
			$lien =  $_SERVER['SCRIPT_NAME'].'?'; //+? pour la methode GET
			makeListLettre($lien);


			echo '</div><table id="tableau">'."\n"
			   . "<tr>"
			   . "<th><a href='".$lien."&ordre=login&lettre=".$_GET['lettre']."'>Login</a></th>"
			   . "<th><a href='".$lien."&ordre=date_connexion&lettre=".$_GET['lettre']."'>Dernière visite</a></th>"
			   . "<th><a href='".$lien."&ordre=identite&lettre=".$_GET['lettre']."'>Identité</a></th>"
			   . "<th><a href='".$lien."&ordre=email&lettre=".$_GET['lettre']."'>E-Mail</a></th>"
			   . "<th><a href='".$lien."&ordre=telephone&lettre=".$_GET['lettre']."'>Tel</a></th>"
			   . "<th><a href='".$lien."&ordre=droits&lettre=".$_GET['lettre']."'>Droits</a></th>"
			   . "<th><a href='".$lien."&ordre=fonction&lettre=".$_GET['lettre']."'>Fonction</a></th>"
			   . "<th><a href='".$lien."&ordre=taille&lettre=".$_GET['lettre']."'>Taille</a></th>"
			   . "<th>M</th>"
			   . "<th>S</th>"
			   . "</tr>\n";

			$ligne_i = 0;
			while ($utilisateur = $stmt->fetchObject()){   
				if ($ligne_i++ % 2 == 1) {
					$class=' class="impaire"';
				} else {
					$class='';
				}
				echo  '<tr'.$class.'>'."\n"
				  . '<td>'.$utilisateur->login.'</td>'."\n"
				  . '<td>'.$utilisateur->date_connexion.'</td>'."\n"
				  . '<td><a title="Enregistrer la carte de visite" href="export_vcard.php?&id='.$utilisateur->ID_user.'">'.$utilisateur->identite.'</a></td>'."\n"
				  . '<td>'.$utilisateur->email.'</td>'."\n"
				  . '<td>'.$utilisateur->telephone.'</td>'."\n"

				  . '<td>'.$utilisateur->droits.'</td>'."\n"
				  . '<td>'.$utilisateur->fonction.'</td>'."\n"
				  . '<td>'.$utilisateur->taille.'</td>'."\n"
				  . '<td>'
				  . '<img src="../images/button_edit.png" style="cursor: pointer;" title="Modifier" width="12" height="13" onClick="GoToURL(\'window\',\'orga_benevole_modif.php?'
				  . '&ID_user='.$utilisateur->ID_user.'\');return document.MM_returnValue"></td>'."\n";

				  // pas de bouton supprimer pour l'admin
				if ($utilisateur->ID_user<>1){
					echo '<td>'
					  . '<img src="../images/ed_delete.gif" style="cursor: pointer;" title="Supprimer" width="12" height="13" onClick="GoToURL_conf(\'window\',\'orga_benevole_supprime.php?'
					  . '&id_prof='.$utilisateur->ID_user.'\');return document.MM_returnValue"></td>'."\n"
					  . '</tr>'."\n";
				} else {
					echo "<td></td>\n</tr>\n";
				}
			}
			echo '</table><br />';
			$exporter="export_csv.php?lettre=".$_GET['lettre'];
			echo '<p><a href="'.$exporter.'">Exporter ce tableau (format Excel)</a><br /><p/> ';






			function groupe($code){
			 switch($code)
			  {
			  case '1': // administrateur
				return '<a href="#" class="tooltip" style="color:#ff0000">Webmestre<em><span></span>Effectue la mise en ligne.</em></a>';
			  case '2': //professeur
				return '<a href="#" class="tooltip" style="color:#00ff00">Enseignant<em><span></span>Propose un article à la publication</em></a>';
			  case '4': // administration
				return '<a href="#" class="tooltip" style="color:#ff6633">Direction<em><span></span>Valide les contenus proposés</em></a>';
			  case '3':  // vie scolaire
				return '<a href="#" class="tooltip" style="color:#00ff00">Vie Scolaire<em><span></span>Propose un article à la publication</em></a>';
			  }
			}


		?>
	</div>
	<?php
		@readfile('pied_de_page.html') or die('Erreur fichier');
	?>
	</div>
	</body>
</html>
