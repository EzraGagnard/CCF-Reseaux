<?php
//-----------------------------------------------------
// Ce script liste les compétitions pour une saison
// enregistrées dans la table compétition
// version 1.0
// 28/10/2009
// auteur SIMIER Philippe pour ESR72
//-----------------------------------------------------

	include "authentification/authcheck.php" ;
	// Vérification des droits pour cette page uniquement organisateur
	if ($_SESSION['droits']<'2'){ 
		header("Location: index.php");};

	require_once('../definitions.inc.php');

		// connexion à la base de données
		$bdd = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);

		// Lecture configuration  saison
		$sql = "SELECT * FROM `cross_route_configuration`";
		$stmt = $bdd->query($sql);

		while ($conf = $stmt->fetchObject()){
			define($conf->conf_key, $conf->conf_value);
		}
		// fin de la lecture configuration saison
	 
	function aff_oui_non($val){
		if ($val=='1')  
			return "<span style=\"color:#00FF00\">oui</span>"; 
		else 
			return "<span style=\"color:#FF0000\">non</span>";
	}
	// début du fichier html bandeau menu horizontal
	if (!is_readable('en_tete.html'))  die ("fichier non accessible");
		@readfile('en_tete.html') or die('Erreur fichier');
?>



	<script language="javascript">

        // fonction pour interdire les caractéres numériques
        function pasNum(e){
            if (window.event) 
				caractere = window.event.keyCode;
            else  
				caractere = e.which;
            return (caractere < 48 || caractere > 57);
        }

        // fonction pour autoriser uniquement les numériques
        function pasCar(e){
			if (window.event) 
				caractere = window.event.keyCode;
            else  
				caractere = e.which;
            return (caractere == 8 || (caractere > 47 && caractere < 58));
        }

        // fonction pour mettre en majuscule
        function majuscule(champ){
			champ.value = champ.value.toUpperCase();
        }

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
    <div id="contenu" style="min-height:500px;">
        <h2><a href="orga_menu.php"><img src="../images/fleche_retour.png" title="Retour" border="0" width="44" height="41"></a>
         Liste des compétitions : <?php echo SAISON ; echo " pour : " . $_SESSION['identite']?> </h2>
        <div>
			<table id="tableau" style="width:100%; margin: 0px;">
                <tr><th>Libellé</th>
                    <th>Lieu</th>
                    <th>Organisateur</th>
                    <th>Email</th>
                    <th>Date</th>
                    <th>Validation</th>
                    <th>Type Licence</th>
                    <th>Sup.</th>
                    <th>Mod.</th>
                </tr>
				<?php
					$sql = "SELECT * FROM `competition` WHERE `saison`=" . SAISON . " AND `id_utilisateur` = " . $_SESSION['ID_user'] . " ORDER BY `competition`.`date` ASC";
					$stmt = $bdd->query($sql);
					while ($competition = $stmt->fetchObject()){
                        echo '<tr><td>'.$competition->nom.'</td>';
                        echo '<td>'.$competition->lieu.'</td>';
                        echo '<td>'.$competition->organisateur.'</td>';
                        echo '<td>'.$competition->email.'</td>';
                        echo '<td>'.date("j M Y",strtotime($competition->date)).'</td>';
                        echo '<td>'.aff_oui_non($competition->validation).'</td>';
                        echo '<td>'.$competition->licence.'</td>';
                        echo '<td><img style="border :0px; cursor: pointer" src="../images/ed_delete.gif"  title="Supprimer" onClick="GoToURL_conf(\'window\',\'supprimer_competition.php?id_competition='.$competition->id.'\',\''.addslashes($competition->nom).'\');return document.MM_returnValue"></td>';
                        echo '<td><img src="../images/button_edit.png" style="cursor: pointer;" title="Modifier" width="12" height="13" onClick="GoToURL(\'window\',\'modif_competition.php?id_competition='.$competition->id.'\');return document.MM_returnValue"></td>'."\n";
                        echo '</tr>';
                    }
            
				?>
            </table>

        </div>
        <?php
			echo '<p><b><a href="ajouter_competition.php?id_utilisateur='. $_SESSION['ID_user'] . '">Ajouter une compétition</a></b></p>';
        ?>
    </div>

	<?php
		 @readfile('pied_de_page.html') or die('Erreur fichier');
	?>
	</div>
	</body>
</html>
