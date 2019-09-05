<?php
//-----------------------------------------------------
// Ce script liste les epreuves pour une compétition
// enregistrées dans la table cross_route_epreuve
// version 2.0
// le paramètre Id_compétition est donné en GET
// auteur SIMIER Philippe 
//-----------------------------------------------------

	include "authentification/authcheck.php" ;
	// Vérification des droits pour cette page uniquement organisateur
	if ($_SESSION['droits']<'2'){ 
		header("Location: index.php");
	};

	require_once('../definitions.inc.php');
	require_once('utile_sql.php');

     // connexion à la base de données
    $bdd = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);

    // Si la variable competition n'exite pas
    if(!isset($_GET['competition'])) { 
		$_GET['competition']=""; 
	}

    //Lecture configuration  compétition
    $sql = "SELECT * FROM `cross_route_configuration`";
    $stmt = $bdd->query($sql);

    while ($conf = $stmt->fetchObject() ){
		define($conf->conf_key, $conf->conf_value);
    }
    // fin de la lecture configuration  compétition

	// début du fichier bandeau menu horizontal
	if (!is_readable('en_tete.html'))  die ("fichier non accessible");
		@readfile('en_tete.html') or die('Erreur fichier');

	?>
	<script language="javascript">
	
        function GoToURL() { //v3.0
                 var i, args=GoToURL.arguments; document.MM_returnValue = false;
                 for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
        }

        function GoToURL_conf() { //v3.0
                 var i, args=GoToURL_conf.arguments;
                 document.MM_returnValue = false;
                 Confirmation = confirm("Confirmez-vous la suppression de "+args[2]);
                 if (Confirmation){
                 for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
                 }
        }


	</script>
	
	
    <div id="contenu" style="width: 1000px;">
        <h2><a href="orga_menu.php"><img src="../images/fleche_retour.png" title="Retour" border="0" width="44" height="41"></a>
          Liste des épreuves : 
          <?php 
			$sql = "SELECT * FROM `competition` WHERE `id` = " . $_GET['competition'];   
			$stmt = $bdd->query($sql);
			$competition = $stmt->fetchObject();
		    echo $competition->nom;
		  ?>
		</h2>
        <div>
			<table id="tableau">
                <tr>
					<th>Désignation</th>
					<th>Code épreuve</th>
					<th>Horaire</th>
					<th>Cat autorisée(s)</th>
					<th>Sexe</th>
					<th>Prix</th>
					<th>Dossard</th>
					<th>Sup.</th>
					<th>Mod.</th>
				</tr>
				<?php
					
                    $sql = sprintf("SELECT * FROM `cross_route_epreuve` WHERE `id_competition`=%s",
                       GetSQLValueString($_GET['competition'], "text"));
					$stmt = $bdd->query($sql);
					while ($epreuve = $stmt->fetchObject()){
                        echo '<tr><td>'.$epreuve->designation.'</td>';
                        echo '<td>'.$epreuve->code.'</td>';
                        echo '<td>'.$epreuve->horaire.'</td>';
                        echo '<td>'.$epreuve->categorie_autorise.'</td>';
                        echo '<td>'.$epreuve->sexe_autorise.'</td>';
                        echo '<td>'.$epreuve->prix.'</td>';
						echo '<td><a href="dossard.php?id_epreuve='. $epreuve->id_epreuve .'"><img src="../images/dossard.jpg" title="Affecter les dossards" border="0" width="49" height="48"></a></td>';
                        echo '<td><img style="border :0px; cursor: pointer" src="../images/ed_delete.gif"  title="Supprimer" onClick="GoToURL_conf(\'window\',\'supprimer_epreuve.php?id_epreuve='. $epreuve->id_epreuve .'&id_competition='. $_GET['competition'] .'\');return document.MM_returnValue;"></td>';
                        echo '<td><img src="../images/button_edit.png" style="cursor: pointer;" title="Modifier" width="12" height="13" onClick="GoToURL(\'window\',\'modif_epreuve.php?id_epreuve='. $epreuve->id_epreuve .'&id_competition='. $_GET['competition'] .'\');return document.MM_returnValue;"></td>'."\n";
                        echo '</tr>';
					}
				?>
				
			</table>
			<?php
			echo '<p><b><a href="ajouter_epreuve.php?competition='. $_GET['competition'] . '">Ajouter une épreuve</a></b></p>';
        ?>
		</div>
        
    </div>
	<?php
		 @readfile('pied_de_page.html') or die('Erreur fichier');
	?>

