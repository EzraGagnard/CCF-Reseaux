<?php
//---------------------------------------------------------------------
// Ce script enregistre une nouvelle compétition
// avec son horaire
// 28 Octobre 2009
// page autoréférente protégée
//---------------------------------------------------------------------
	include "authentification/authcheck.php" ;
	// Vérification des droits pour cette page uniquement organisateurs
	if ($_SESSION['droits']<'2'){ 
		header("Location: index.php");
	};
	require_once('../definitions.inc.php');
	require_once('utile_sql.php');

	// connexion à la base de données
    $bdd = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);
    
    // Lecture de des variables de configuration 
    $sql = "SELECT * FROM `cross_route_configuration`";
    $stmt = $bdd->query($sql);

    while ($conf =  $stmt->fetchObject()){
		define($conf->conf_key, $conf->conf_value);
    }
    // fin de la lecture configuration saison

	$erreur="";
	if( !empty($_POST['envoyer'])){
     
		if ($_POST['nom']=="") {
			$erreur = "Vous devez indiquer un libellé pour la compétition";
		};
		if ($_POST['date']=="") {
			$erreur = "Vous devez indiquer la date de la compétition";
		};

		if ($erreur == ""){
			if ($_POST['mode']=="insertion"){

				$sql = sprintf("INSERT INTO competition (`id_utilisateur`,`saison` ,`nom` ,`date` , `lieu` , `organisateur` ,`validation` ,`licence` , `email` ) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s)",
                GetSQLValueString($_POST['id_utilisateur'], "text"),
				SAISON,
                GetSQLValueString($_POST['nom'], "text"),
                GetSQLValueString($_POST['date'], "text"),
                GetSQLValueString($_POST['lieu'], "text"),
                GetSQLValueString($_POST['organisateur'], "text"),
                GetSQLValueString($_POST['validation'], "text"),
                GetSQLValueString($_POST['licence'], "text"),
                GetSQLValueString($_POST['email'], "text")
                );
				$stmt = $bdd->query($sql);
				
			}
 			header("Location: competition.php");
			exit;
		}
	}

	// début du fichier bandeau menu horizontal
	if (!is_readable('en_tete.html'))  die ("fichier non accessible");
	@readfile('en_tete.html') or die('Erreur fichier');
?>
		
	<div id="contenu">
		<h2><a href="competition.php"><img src="../images/fleche_retour.png" title="Retour" border="0" width="44" height="41"></a>Nouvelle Compétition pour : <?php echo DESIGNATION ; ?> </h2>
		<?php if ($erreur) {echo '<p style="color:#FF0000;">'.$erreur."</p>"; } else { echo "<p> </p>"; }?>
		<div class="item">

			<p style="font-weight:bold;">informations sur la compétition : </p>
			<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>"  name="epreuve" onSubmit="return verif();">
				<input type="hidden" name="mode" value="insertion" />
				<input type="hidden" name="id_utilisateur" value="<?php echo $_SESSION['ID_user'] ?>" />
				<table style="text-align: left; width: 600px; height: 160px;"   border="0" cellpadding="2" cellspacing="2">
					<tbody>
					<tr>
						
						<td>
							<label for="nom">Libellé de la compétition : </label>
							<input name="nom" class="normal" size="25" maxlength="25" required/>
						</td>
					</tr>
					<tr>
						
						<td>
							<label for="lieu">Lieu de la compétition : </label>
							<input name="lieu" class="normal" size="25" maxlength="25" required/>
						</td>
					</tr>
					<tr>
						<td>
							<label for="organisateur">Organisateur : </label>
							<input name="organisateur" class="normal" size="25" maxlength="25" required/>
						</td>
					</tr>
					<tr>
						<td>
							<label for="email">Email organisateur : </label>
							<input type="email" name="email" class="normal" required/>
						</td>
					</tr>
					<tr>
						<td>
							<label for="date">Date : </label>
							<input type="date" name="date" class="date" size="10" required/>
						</td>
					</tr>
					<tr>
						<td> 
							<label for="validation">Validation des inscriptions : </label>
							<input type="radio" name="validation" value="0" checked>NON -
							<input type="radio" name="validation" value="1">OUI
						</td>
					</tr>
					<tr>
						
						<td>
							<label for="licence">Type de licences autorisées : </label>
							<input type="radio" name="licence" value="" />Toutes -
							<input type="radio" name="licence" value="COMP" />COMP -
						</td>
					</tr>
					<tr>
						<td style="width: 50%; text-align: right;">
						   <input name="envoyer" value="Valider"  type="submit" />
						</td>
						
					</tr>
					</tbody>
				</table>
			</form>
		</div>
	</div>

<?php
		@readfile('pied_de_page.html') or die('Erreur fichier');
?>

