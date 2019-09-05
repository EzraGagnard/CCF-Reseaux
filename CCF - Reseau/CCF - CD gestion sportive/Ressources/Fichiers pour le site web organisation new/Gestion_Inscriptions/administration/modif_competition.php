<?php
//--------------------------------------------------------------------
// Ce script permet de modifier les paramètres d'une compétition
// Compétition identifiée par son id tranis par la méthose GET
//
// page autoréférente protégée
//--------------------------------------------------------------------

	include "authentification/authcheck.php" ;
	// Vérification des droits pour cette page uniquement organisateur
	if ($_SESSION['droits']<'2') { 
		header("Location: index.php");
	};

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

		$sql = sprintf("UPDATE competition SET nom=%s , lieu=%s, organisateur=%s, date=%s , validation=%s , licence=%s, email=%s    WHERE id=%s",

                       GetSQLValueString($_POST['nom'], "text"),
                       GetSQLValueString($_POST['lieu'], "text"),
                       GetSQLValueString($_POST['organisateur'], "text"),
                       GetSQLValueString($_POST['date'] , "text"),
                       GetSQLValueString($_POST['validation'] , "text"),
                       GetSQLValueString($_POST['licence'] , "text"),
                       GetSQLValueString($_POST['email'] , "text"),
                       $_POST['id_competition']
		);
        $stmt = $bdd->query($sql);  
		$GoTo = "competition.php";
		header(sprintf("Location: %s", $GoTo));
	}

	// recherche des infos en fct de id_epreuve

	if ((isset($_GET['id_competition'])) && ($_GET['id_competition'] != "")) {
        $sql = "SELECT * FROM competition WHERE id=".$_GET['id_competition']."";
        $stmt = $bdd->query($sql);

        $competition = $stmt->fetchObject();
         }

 
	// début du fichier bandeau et menu horizontal
	if (!is_readable('en_tete.html'))  die ("fichier non accessible");
		@readfile('en_tete.html') or die('Erreur fichier');
?>


	

	
	<div id="contenu">
		<h2><a href="orga_menu.php"><img src="../images/fleche_retour.png" title="Retour" border="0" width="44" height="41"></a>
			  Modifier une compétition <?php echo DESIGNATION ?></h2>
		<div class="item">
			<p style="font-weight:bold;">informations sur la compétition : </p>
			<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>"  name="engagement" onSubmit="return verif();">
				<input type="hidden" name="mode" value="update" />
				<input type='hidden' name='id_competition' value='<?php echo $_GET['id_competition']; ?>'/>

				<table style="text-align: left; width: 600px; height: 160px;"   border="0" cellpadding="2" cellspacing="2">
					<tbody>
						  <tr>
							<td style="width: 50%; text-align: right;">Libellé : </td>
							<td><input name="nom" value="<?php echo $competition->nom; ?>" size="25" maxlength="25"/></td>
						  </tr>
						  <tr>
							<td style="width: 50%; text-align: right;">Lieu : </td>
							<td><input name="lieu" value="<?php echo $competition->lieu; ?>" size="25" maxlength="25"/></td>
						  </tr>
						  <tr>
							<td style="width: 50%; text-align: right;">Organisateur : </td>
							<td><input name="organisateur" value="<?php echo $competition->organisateur; ?>" size="25" maxlength="25"/></td>
						  </tr>
						  <tr>
							<td style="width: 50%; text-align: right;">Email organisateurs : </td>
							<td><input name="email" value="<?php echo $competition->email; ?>"  /></td>
						  </tr>
						  <tr>
							<td style="width: 50%; text-align: right;" >Date : </td>
							<td><input name="date" value="<?php echo date("Y-m-j",strtotime($competition->date)); ?>" class="date" size="10" />

							</td>
						  </tr>
						  <tr>
							<td style="width: 50%; text-align: right;">Type de licence autorisés : </td>
							<td><input type="radio" name="licence" value="" <?php  if ($competition->licence =="" )echo "checked"; ?>/>Toutes -
								<input type="radio" name="licence" value="COMP" <?php  if ($competition->licence =="COMP" )echo "checked"; ?>/>COMP -
							</td>
						  </tr>
						  <tr>
							<td style="width: 50%; text-align: right;">Validation des inscriptions : </td>
							<td><input type="radio" name="validation" value="0" <?php  if ($competition->validation =="0" )echo "checked"; ?>/>NON -
								<input type="radio" name="validation" value="1" <?php  if ($competition->validation =="1" )echo "checked"; ?>/>OUI -
							</td>
						  </tr>

						  <tr>
							<td style="width: 50%; text-align: right;"><input name="envoyer" value="Valider"  type="submit" /></td>
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

