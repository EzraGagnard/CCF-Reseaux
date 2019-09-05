<?php
// Ce script enregistre les paramètres de configuration
// table cross_route_configuration dans la base

	include "authentification/authcheck.php" ;
	// Vérification des droits pour cette page uniquement organisateur de niveau 2 et plus
	if ($_SESSION['droits'] < '2'){ 
		header("Location: index.php");
	};

	// page autoréférente
	require_once('../definitions.inc.php');
	require_once('utile_sql.php');


	// connexion à la base de donnée
	$bdd = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);
	$sql = 'SELECT * FROM `cross_route_configuration`';
	$stmt = $bdd->query($sql);

	while ($conf = $stmt->fetchObject()){
		   define($conf->conf_key, $conf->conf_value);
	}

	// début du fichier bandeau menu horizontal
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

	</script>


    <div id="menu" style="width: 10px;">
    </div>
    <div id="contenu" style="width: 900px;">
        <h2>
			<a href="orga_menu.php"><img src="../images/fleche_retour.png" title="Retour" border="0" width="44" height="41"></a>
			Configuration<br />
			<?php echo DESIGNATION ?>
		</h2>

		<table id="tableau">
			<tr>
				<th style="width: 55%;">Titre</th>
				<th style="width: 40%;">Valeur</th>
				<th>Modifier</th>
			</tr>
            <?php
                $stmt = $bdd->query($sql);
				while ($conf = $stmt->fetchObject()){
                    echo '<tr><td>'.$conf->conf_titre.'</td>';
                    echo '<td>'.$conf->conf_value.'</td>';
                    echo '<td><a href="modifier_conf.php?conf_id='.$conf->conf_id.'"><img src="../images/button_edit.png" style="cursor: pointer; border:0px;" title="Modifier" width="12" height="13"></a></td>';
                    echo '</tr>';
				}
            ?>
        </table>

    </div>
	<?php
		@readfile('pied_de_page.html') or die('Erreur fichier');
	?>

</body>
</html>
