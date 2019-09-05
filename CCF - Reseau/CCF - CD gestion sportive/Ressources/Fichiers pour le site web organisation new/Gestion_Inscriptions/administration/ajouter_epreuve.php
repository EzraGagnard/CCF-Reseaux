<?php
// Ce script enregistre une épreuve avec son horaire

// page autoréférente protégée
	include "authentification/authcheck.php" ;
	// Vérification des droits pour cette page uniquement organisateurs
	if ($_SESSION['droits']<'2'){ 
		header("Location: index.php");
	};
	require_once('../definitions.inc.php');
	require_once('utile_sql.php');
	// connexion à la base de données
	$bdd = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);
	
	
	if( !empty($_POST['envoyer'])){
     
		if ($_POST['designation']=="") {
			echo "Vous devez indiquer un nom d'épreuve";
			exit;
		};
		$categorie = implode(',',$_POST['cat']);
		$sexe =  implode(',',$_POST['sexe']);
		

		if ($_POST['mode']=="insertion"){


			$sql = sprintf("INSERT INTO cross_route_epreuve (`id_competition` ,`competition` ,`designation` , `horaire` , `prix` ,`code` ,`categorie_autorise`,`sexe_autorise`) VALUES (%s,%s,%s,%s,%s,%s,%s,%s )",
                 GetSQLValueString($_POST['id_competition'], "text"),
				 GetSQLValueString($_POST['competition'], "text"),
                 GetSQLValueString($_POST['designation'], "text"),
                 GetSQLValueString($_POST['horaire'], "text"),
                 GetSQLValueString($_POST['prix'], "text"),
                 GetSQLValueString($_POST['code'], "text"),
                 GetSQLValueString($categorie, "text"),
                 GetSQLValueString($sexe, "text")
                 );
			
			$stmt = $bdd->query($sql);
		}
    
		header("Location: epreuve.php?competition=".stripslashes($_POST['id_competition']));
		exit;
	}
	// début du fichier bandeau menu horizontal
	if (!is_readable('en_tete.html'))  die ("fichier non accessible");
		@readfile('en_tete.html') or die('Erreur fichier');
?>


	<div id="contenu" style="width: 1000px; ">
		<h2>Nouvelle épreuve pour 
		<?php   
			
			
			$sql = "SELECT * FROM `competition` WHERE `id` = " . $_GET['competition'];	
			$stmt = $bdd->query($sql);
			$competition = $stmt->fetchObject();
			echo $competition->nom;
		?> 
		</h2>
			<div class="item">

				<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>"  name="epreuve" onSubmit="return verif();">
					<input type="hidden" name="mode" value="insertion" />
					<input type="hidden" name="competition" value="<?php echo $competition->nom; ?>" />
					<input type="hidden" name="id_competition" value="<?php echo stripslashes($_GET['competition']); ?>" />
					<table style="text-align: left; width: 700px; "   border="0" cellpadding="2" cellspacing="2">
						<tbody>
						<tr>
							<td style="width: 25%; text-align: right; " >Libellé :</td>
							<td style="width: 75%; ">
							   <input name="designation" class="normal"/>
							</td>
						</tr>
						<tr>
							<td style="width: 25%; text-align: right; ">Horaire :</td>
							<td><input name="horaire" class="normal"/></td>
						</tr>
						<tr>
							<td style="width: 25%; text-align: right; ">Code :</td>
							<td><input name="code" class="normal"/></td>
						</tr>
						<tr>
							<td colspan="3"></td>
						</tr>
						<tr>
							<td style="width: 25%; text-align: right; ">Cat. autorisé(s) :</td>
							<td><input name="cat[]" value="EA" type="checkbox" />EA
								<input name="cat[]" value="PO" type="checkbox" />PO
								<input name="cat[]" value="BE" type="checkbox" />BE
								<input name="cat[]" value="MI" type="checkbox" />MI
								<input name="cat[]" value="CA" type="checkbox" />CA
								<input name="cat[]" value="JU" type="checkbox" />JU
								<input name="cat[]" value="ES" type="checkbox" />ES
								<input name="cat[]" value="SE" type="checkbox" />SE
								<input name="cat[]" value="SE" type="checkbox" />VE
								<input name="cat[]" value="V1" type="checkbox" />V1
								<input name="cat[]" value="V2" type="checkbox" />V2
								<input name="cat[]" value="V3" type="checkbox" />V3
								<input name="cat[]" value="V4" type="checkbox" />V4 <br/>
								<input name="cat[]" value="TC" type="checkbox" />TC
							</td>
						</tr>
						<tr>
							<td style="width: 25%; text-align: right; ">Sexe :</td>
							<td><input name="sexe[]" value="M" type="checkbox" />Masculin
								<input name="sexe[]" value="F" type="checkbox" />Feminin
							</td>
						</tr>
						<tr>
							<td colspan="3"> </td>
						</tr>
						<tr>
							<td style="width: 25%; text-align: right; ">Prix d'engagement :</td>
							<td><input name="prix" value="0" size="3" class="normal"/></td>
						</tr>
						<tr>
							<td></td>
							<td><input name="envoyer" value="Valider"  type="submit" /></td>
							<td></td>
						</tr>
						</tbody>
					</table>
				</form>
			</div>
	</div>

	<?php
		 @readfile('pied_de_page.html') or die('Erreur fichier');
	?>

