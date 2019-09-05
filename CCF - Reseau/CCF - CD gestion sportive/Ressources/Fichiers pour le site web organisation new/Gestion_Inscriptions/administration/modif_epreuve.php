<?php
// Ce script met à jour les infos d'une compétition
// compatible PHP7

	// page autoréférente
	require_once('../definitions.inc.php');
	require_once('utile_sql.php');

	// connexion à la base de données
    $bdd = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);
  
// lecture de la configuration et définition des constantes ENABLE SAISON DATE DESIGNATION etc
		$sql = 'SELECT * FROM `cross_route_configuration`';
		$stmt = $bdd->query($sql);
		while ($conf = $stmt->fetchObject()){
			define($conf->conf_key, $conf->conf_value);
		}
// fin de la lecture de la configuration


	if (!empty($_POST['envoyer'])){
		// recolte les éléments du tableau cat avec la virgule comme séparateur
		$categorie = implode(',',$_POST['cat']);
		// même chose pour le sexe
		$sexe =  implode(',',$_POST['sexe']);
		$sql = sprintf("UPDATE cross_route_epreuve SET designation=%s , code=%s , horaire=%s , categorie_autorise=%s, sexe_autorise=%s, prix=%s  WHERE id_epreuve=%s",

                       GetSQLValueString($_POST['designation'], "text"),
                       GetSQLValueString($_POST['code'] , "text"),
                       GetSQLValueString($_POST['horaire'] , "text"),
                       GetSQLValueString($categorie , "text"),
                       GetSQLValueString($sexe , "text"),
                       GetSQLValueString($_POST['prix'] , "text"),
                       $_POST['id_epreuve']
				);
        $stmt = $bdd->query($sql);
	   
		// retour vers la page configuration des epreuve
		$retour = "Location: epreuve.php?competition=".$_POST['id_competition'];
		header($retour);
	}

// recherche des infos en fct de id_epreuve

	if ((isset($_GET['id_epreuve'])) && ($_GET['id_epreuve'] != "")) {
        $sql = "SELECT * FROM cross_route_epreuve WHERE id_epreuve=".$_GET['id_epreuve']."";
        $stmt = $bdd->query($sql);
        $epreuve = $stmt->fetchObject();
    }

	// début du fichier bandeau menu horizontal
	if (!is_readable('en_tete.html'))  die ("fichier non accessible");
		@readfile('en_tete.html') or die('Erreur fichier');
?>


	<div id="contenu" style="width: 800px; ">
		<h2><a href="epreuve.php?competition=<?php echo $_GET['id_competition']; ?>"><img src="../images/fleche_retour.png" title="Retour" border="0" width="44" height="41"></a>
          Modifier une épreuve  : </h2>
		<div class="item">
			<p>informations épreuve : </p>
			<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>">
				<input type="hidden" name="mode" value="update" />
				<input type='hidden' name='id_epreuve' value='<?php echo $_GET['id_epreuve']; ?>'/>
				<input type='hidden' name='id_competition' value='<?php echo $_GET['id_competition']; ?>'/>
				<table style="text-align: left; width: 700px; "   border="0" cellpadding="2" cellspacing="2">
					<tbody>
						<tr>
							<td style="width: 25%; text-align: right; ">libellé : </td>
							<td><input name="designation" value="<?php echo $epreuve->designation; ?>" /></td>
						</tr>
						<tr>
							<td style="width: 25%; text-align: right; ">Horaire : </td>
							<td><input name="horaire" value="<?php echo $epreuve->horaire; ?>" /></td>
						</tr>
						<tr>
							<td style="width: 25%; text-align: right; ">Code : </td>
							<td><input name="code" value="<?php echo $epreuve->code; ?>" /></td>
						</tr>
						<tr>
							<td style="width: 25%; text-align: right; ">Catégorie :</td>
							<?php 
							// décomposition de la chaine categorie en tableau
							$cat=explode(",",$epreuve->categorie_autorise); ?>
							<td><input name="cat[]" value="EA" type="checkbox" <?php if (in_array("EA",$cat)) echo "CHECKED" ?>/>EA
								<input name="cat[]" value="PO" type="checkbox" <?php if (in_array("PO",$cat)) echo "CHECKED" ?> />PO
								<input name="cat[]" value="BE" type="checkbox" <?php if (in_array("BE",$cat)) echo "CHECKED" ?> />BE
								<input name="cat[]" value="MI" type="checkbox" <?php if (in_array("MI",$cat)) echo "CHECKED" ?> />MI
								<input name="cat[]" value="CA" type="checkbox" <?php if (in_array("CA",$cat)) echo "CHECKED" ?> />CA
								<input name="cat[]" value="JU" type="checkbox" <?php if (in_array("JU",$cat)) echo "CHECKED" ?> />JU
								<input name="cat[]" value="ES" type="checkbox" <?php if (in_array("ES",$cat)) echo "CHECKED" ?> />ES
								<input name="cat[]" value="SE" type="checkbox" <?php if (in_array("SE",$cat)) echo "CHECKED" ?> />SE
								<input name="cat[]" value="VE" type="checkbox" <?php if (in_array("VE",$cat)) echo "CHECKED" ?> />VE
								<input name="cat[]" value="V1" type="checkbox" <?php if (in_array("V1",$cat)) echo "CHECKED" ?> />V1
								<input name="cat[]" value="V2" type="checkbox" <?php if (in_array("V2",$cat)) echo "CHECKED" ?> />V2
								<input name="cat[]" value="V3" type="checkbox" <?php if (in_array("V3",$cat)) echo "CHECKED" ?> />V3
								<input name="cat[]" value="V4" type="checkbox" <?php if (in_array("V4",$cat)) echo "CHECKED" ?> />V4<br>
								<input name="cat[]" value="TC" type="checkbox" <?php if (in_array("TC",$cat)) echo "CHECKED" ?> />TC
							</td>
						</tr>
						<tr>
							<td style="width: 25%; text-align: right; ">Sexe : </td>
							<?php 
							// décomposition de la chaine sexe en tableau
							$sexe=explode(",",$epreuve->sexe_autorise); ?>
							<td>
								<input name="sexe[]" value="M" type="checkbox" <?php if (in_array("M",$sexe)) echo "CHECKED" ?>/> Masculin
								<input name="sexe[]" value="F" type="checkbox" <?php if (in_array("F",$sexe)) echo "CHECKED" ?>/> Feminin
							</td>
						</tr>
						<tr>
							<td style="width: 25%; text-align: right; ">Prix :</td>
							<td><input name="prix" size="3" value="<?php echo $epreuve->prix; ?>"  /></td>
						</tr>
						<tr>
							<td></td>
							<td><input name="envoyer" value="Valider"  type="submit" /></td>

						</tr>
					</tbody>
				</table>
			</form>
		</div>
	</div>

	<?php
		 @readfile('pied_de_page.html') or die('Erreur fichier');
	?>

