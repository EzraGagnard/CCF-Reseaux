<?php
//---------------------------------------------------------------------------
// Ce script réalise l'affectation des dossards pour une épreuve
// le format du fichier doit avoir au minimum 6 colonnes:
//
//
//
//
// Simier Philippe 19/02/2015
// version 1.0
// page autoréférente protégée
//---------------------------------------------------------------------------
	include "authentification/authcheck.php" ;
	require_once('utile_sql.php');

	// Vérification des droits pour cette page uniquement organisateurs
	if ($_SESSION['droits']<'2') { header("Location: index.php");};
	require_once('../definitions.inc.php');
	$bdd = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);

	// lecture de la configuration et définition des constantes ENABLE SAISON DATE DESIGNATION etc
    $sql = 'SELECT * FROM `cross_route_configuration`';
    $stmt = $bdd->query($sql);  
    while ($conf = $stmt->fetchObject()){
		define($conf->conf_key, $conf->conf_value);
    }
	// fin de la lecture de la configuration
	if( !empty($_POST['Affecter'])){

		$sql = "set @valeur =".($_POST['start']-1).";";   // déclaration d'une variable valeur 
		$stmt = $bdd->query($sql);
		$sql = "update cross_route_engagement set `dossard` = @valeur := @valeur + 1 where `id_epreuve`= \"".$_POST['id_epreuve']."\";";
		$stmt = $bdd->query($sql);
    
		$GoTo = "orga_menu.php";
		header("Location: " . $GoTo);
	}
	// début du fichier bandeau menu horizontal
	if (!is_readable('en_tete.html'))  die ("fichier non accessible");
		@readfile('en_tete.html') or die('Erreur fichier');
?>

	<div id="contenu" style="min-height:500px;">
		<h2>Affecter les dossards sur une épreuve</h2>

		<p align="left">
			Vous allez affecter les dossards aux athlètes de façon automatique<br />
			Les anciens dossards seront perdus !<br />
			Voulez vous continuer ?<br />
			<br />
			<a href="orga_menu.php"><img src="../images/fleche_retour.png" title="Retour" border="0" width="44" height="41"></a>
		</p>
		<p align="center"></p>
			<div class="item">

				<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>"  name="dossard">
					<input type="hidden" name="id_epreuve" value="<?php echo $_GET['id_epreuve']; ?>" />
					<table style="text-align: left; width: 600px; height: 160px;"   border="0" cellpadding="2" cellspacing="2">
						<tbody>
						<tr>
							<td style="width: 25%; " ><img border="0" src="../images/dossard.jpg"></td>
							<td>N° du premier dossard :</td>
							<td style="width: 25%; "><input type="text" size="10" name="start" class="normal"/>
							</td>
						</tr>
			
						<tr>
							<td></td>
							<td></td>
							<td><input name="Affecter" value="Affecter les dossards"  type="submit" /></td>

						</tr>
						</tbody>
					</table>
				</form>
			</div>
	</div>
	<?php
			@readfile('pied_de_page.html') or die('Erreur fichier');
	?>	
</div>
</body>
</html>
