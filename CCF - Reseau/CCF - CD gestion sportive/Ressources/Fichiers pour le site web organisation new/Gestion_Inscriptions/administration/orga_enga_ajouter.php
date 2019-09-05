<?php
//--------------------------------------------------------------------------------------------
// Ce script enregistre un nouvel engagement
// partie Administrateur
// page protégée autoréférente
// Avril 2014 suppression du champ compétition pour afficher uniquement celui en cours
// Avril 2014 ajout des champs noligue nodept et typelicence
// Avril 2014 controle du sexe avec la table des prénoms
// 19 février 2015 ajout du champ tel et des licences tri
// 7 mai 2018 upsate pour compatibilité php7
//--------------------------------------------------------------------------------------------
// vérification des variables de session pour le temps d'inactivité et de l'adresse IP
	
	include "authentification/authcheck.php" ;
	// Vérification des droits pour cette page uniquement organisateurs
	if ($_SESSION['droits']<'2'){ 
		header("Location: ../index.html");
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
	
	
	// Création d'un objet compétition
	if( !empty($_GET['competition'])){
		$sql = 'SELECT * FROM `competition` WHERE id =' . $_GET['competition'];
		$stmt = $bdd->query($sql);
		$competition = $stmt->fetchObject();
	}
	if( !empty($_POST['competition'])){
		$sql = 'SELECT * FROM `competition` WHERE id =' . $_POST['competition'];
		$stmt = $bdd->query($sql);
		$competition = $stmt->fetchObject();
	}
	
	
	if( !empty($_POST['envoyer'])){
     
		if ($_POST['nom']=="") {
			echo "Vous devez indiquer un nom";
        exit;
		};
		// test de la concordance entre le prénom et le sexe
		$sql = "SELECT * FROM `prenom` WHERE `prenom` = '".$_POST['prenom']."'";
		$stmt = $bdd->query($sql);
		// création de l'objet prénom
		$prenom = $stmt->fetchObject();
	
		if (($_POST['sexe']=='M' && $prenom->genre=='f')||($_POST['sexe']=='F' && $prenom->genre=='m')) {
			echo "Erreur : ".$_POST['prenom']." est ".$prenom->genre;
		exit;
		};	
	
        
		//----------- Création de l'objet club  ---------------
		if ($_POST['noclub']=="") { 
			$_POST['noclub']= "00000";
		}
		else {
			$sql = "SELECT * FROM `ffa_club` WHERE `noclub`=".$_POST['noclub']." LIMIT 0, 30 ";
			$stmt = $bdd->query($sql);			
			$club = $stmt->fetchObject();
		};

	//------------Création de l'objet licencie ------------

		if ($_POST['nolicence']) {
			$sql = sprintf("SELECT typelicence FROM  `ffa_licence` WHERE `nolicence`=%s",
				GetSQLValueString($_POST['nolicence'], "int")
			);
			$stmt = $bdd->query($sql);
			$licencie = $stmt->fetchObject() ;
	} 	
	
	//-------------Création de l'objet epreuve--------------
		if ($_POST['id_epreuve']) {
			$sql = "SELECT * FROM `cross_route_epreuve` WHERE `id_epreuve`= " . $_POST['id_epreuve']; 
			$stmt = $bdd->query($sql);
			$epreuve = $stmt->fetchObject();
		}	
	
	
	// recherche du dernier n° de dossard attribué pour l'épreuve
		$sql = "SELECT MAX(`dossard`) AS high_dossard FROM cross_route_engagement WHERE `id_epreuve`= " . $_POST['id_epreuve'];
		$stmt = $bdd->query($sql);
		$dossard = $stmt->fetchObject();
		
	//------------------------------------------------------	
		if ($_POST['mode']=="insertion"){
			$sql = sprintf("INSERT INTO cross_route_engagement (date,id_epreuve,dossard,competition,nolicence,nom,prenom,noclub,typeparticipant,sexe,nodept,noligue,anneenaissance,categorie,nomcourse,nomequipe,adresse1,codepostal,ville,email,typelicence,certifmedicalfourni,cotisationpaye,commentaire,paiement,tel) VALUES (CURRENT_TIMESTAMP,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
				GetSQLValueString($_POST['id_epreuve'], "int"),
				GetSQLValueString(($dossard->high_dossard+1), "int"),
				GetSQLValueString($competition->nom, "text"),
				GetSQLValueString($_POST['nolicence'], "text"),
				GetSQLValueString($_POST['nom'], "text"),
				GetSQLValueString($_POST['prenom'], "text"),
				GetSQLValueString($_POST['noclub'], "int"),
				GetSQLValueString($_POST['typeparticipant'], "text"),
				GetSQLValueString($_POST['sexe'], "text"),
				GetSQLValueString($club->noclub, "text"),
				GetSQLValueString($club->ligue, "text"),
				GetSQLValueString($_POST['anneenaissance'], "int"),
				GetSQLValueString(cat_ffa($_POST['anneenaissance'],$_POST['sexe']), "text"),
				GetSQLValueString($epreuve->code, "text"),
				GetSQLValueString($_POST['nomequipe'], "text"),
				GetSQLValueString($_POST['adresse1'], "text"),
				GetSQLValueString($_POST['codepostal'], "text"),
				GetSQLValueString($_POST['ville'], "text"),
				GetSQLValueString($_POST['email'], "text"),
				GetSQLValueString($licencie->typelicence, "text"),
				GetSQLValueString($_POST['certifmedicalfourni'] , "text"),
				GetSQLValueString($_POST['cotisationpaye'] , "text"),
				GetSQLValueString($_POST['commentaire'], "text"),
				GetSQLValueString($_POST['paiement'] , "text"),
				GetSQLValueString($_POST['tel'], "text")
			);
			
			$stmt = $bdd->query($sql);
		}
		

		// retour à la page tableau des engagements
		$GoTo = "orga_tab_enga.php?&competition=".$_POST['competition'];
		header(sprintf("Location: %s", $GoTo));
	}
    
	// fonction pour déterminer la catégorie FFA
	function cat_ffa($annee,$sexe){
		$age=SAISON-$annee;

		if ($age>=1  && $age<=9 ) return "EA";
		if ($age>=10 && $age<=11) return "PO";
		if ($age>=12 && $age<=13) return "BE";
		if ($age>=14 && $age<=15) return "MI";
		if ($age>=16 && $age<=17) return "CA";
		if ($age>=18 && $age<=19) return "JU";
		if ($age>=20 && $age<=22) return "ES";
		if ($age>=23 && $age<=39) return "SE";
		if ($age>=40 && $age<=49) return "V1";
		if ($age>=50 && $age<=59) return "V2";
		if ($age>=60 && $age<=69  && $sexe=='M') return "V3";
		if ($age>=70 && $age<=120 && $sexe=='M') return "V4";
		if ($age>=60 && $age<=120  && $sexe=='F') return "V3";
		return "??";
	}

	// début du fichier bandeau menu horizontal
	if (!is_readable('en_tete.html'))  die ("fichier non accessible");
		@readfile('en_tete.html') or die('Erreur fichier');
?>


	<div id="contenu">
		<h2>Nouvel engagement pour : <?php echo $competition->nom; ?></h2>
		<div class="item" style="width:800px" >
			<p><b>informations engagé(e) : </b><span id="loader" style="display:none;"><img src="../images/loader.gif"  alt="loader" /></span></p>
			<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>"  name="engagement" onSubmit="return verif();">
				<input type="hidden" name="mode" value="insertion" />
				<input type="hidden" name="competition" value="<?php echo stripslashes($_GET['competition']); ?>" />
				<input type='hidden' name='dep' />
				<table style="text-align: left; width: 780px; height: 160px;"   border="0" cellpadding="2" cellspacing="2">
					<tbody>
						<tr>
							<td>N° licence</td>
							<td><input name="nolicence"  onChange="licence_ffa(this)" /></td>
							<td>N° club</td>
							<td><input name="noclub" onKeyPress="return pasCar(event)" onChange="club_ffa(this)" /></td>
						</tr>
						<tr>
							<td>Nom</td>
							<td><input name="nom" onKeyPress="return pasNum(event)" onChange="majuscule(this)" required/></td>
							<td>Prénom</td>
							<td><input name="prenom" onKeyPress="return pasNum(event)" required/></td>
						</tr>
						<tr>
							<td>Année de naissance</td>
							<td><input name="anneenaissance" size="4" onKeyPress="return pasCar(event)" onChange="verif_nais(this)" required/></td>
							<td>Sexe</td>
							<td>
								<input  name="sexe"  value="M" type="radio" checked="checked" />Masculin 
								<input  name="sexe" value="F" type="radio" />Féminin
							</td>
						</tr>
						<tr>
							<td>Adresse</td>
							<td colspan="3"><input name="adresse1" size="60" required/></td>
						</tr>
						<tr>
							<td>Code Postal</td>
							<td><input name="codepostal" onKeyPress="return pasCar(event)" required/></td>
							<td>Ville</td>
							<td><input name="ville" onKeyPress="return pasNum(event)" required/></td>
						</tr>
						<tr>
							<td>Tel</td>
							<td><input name="tel" size="16" required/></td>
							
							<td>Email</td>
							<td><input name="email" size="50" onChange="testMail(this)" required/></td>
							
						</tr>
						<tr>
							<td>Compétition :</td>
							<td colspan="2" style='font-size: 14pt; font-weight: bold;'><?php echo $competition->nom; ?></td>
							<td id="epreuve">
								<select name="id_epreuve">
									<?php
									// connexion à la base 
									$bdd2 = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);
									// Lecture de la table cross_route_epreuve pour obtenir les désignations et codes des epreuves
									$sql = "SELECT * FROM cross_route_epreuve WHERE `id_competition`=".$_GET['competition'];
									
									$stmt = $bdd2->query($sql);
									while ($epreuve = $stmt->fetchObject()){
										echo '<option value="'.$epreuve->id_epreuve.'">'.$epreuve->designation." (".$epreuve->horaire.')</option>';
										}
									// fin de la lecture des epreuves
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="1">Inscription au challenge :</td>
							<td colspan="3">
								<input checked="checked" name="typeparticipant" value="" type="radio" />Individuel
								<input name="typeparticipant" value="ffa" type="radio" />Club
								<input name="typeparticipant" value="Ent" type="radio" />Entreprise
							</td>
						</tr>
						<tr>
							<td>Nom de l'équipe :</td>
							<td colspan="3"><input name="nomequipe" value=""/></td>
						</tr>
						<tr>
							<td>Commentaire :</td>
							<td colspan="3"><input name="commentaire" value="" size="50" /></td>
						</tr>
						<tr>
							<td>Certificat médical :</td>
							<td colspan="3">
								<input  name="certifmedicalfourni" value="oui" type="radio" />OUI
								<input  checked='checked' name="certifmedicalfourni" value="non" type="radio" />NON
							</td>
						</tr>
						<tr>
							<td>Cotisation payée :</td>
							<td colspan="3">
								<input  name="cotisationpaye" value="oui" type="radio" />OUI
								<input  checked='checked' name="cotisationpaye" value="non" type="radio" />NON
							</td>
						</tr>
						<tr>
							<td >Mode de paiement :</td>
							<td colspan="3">
							<input  name="paiement" value="" type="radio" checked='checked'/>En attente
							<input  name="paiement" value="chèque" type="radio" />Chèque
							<input  name="paiement" value="paypal" type="radio" />Paypal
							<input  name="paiement" value="espèces" type="radio" />Espèces
							</td>
						</tr>
						<tr>
							<td><input name="envoyer" value="Valider"  type="submit" /></td>
							<td colspan="3"></td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>
	</div>
<?php
		@readfile('pied_de_page.html') or die('Erreur fichier');
?>

