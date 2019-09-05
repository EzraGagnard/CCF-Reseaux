<?php
//---------------------------------------------------------------------------------------------
// Ce script enregistre l'engagement d'un participant
// page autoréférente publique (non protégée)
// notes: sep/2009 modification pour demander le n° du club
// ajout du libellé de la compétition 27 octobre 2009
// ajout filtrage  sexe et type licence COMP 29 octobre 2009
// ajout acceptation du règlement de l'épreuve
// utilisation du framework Jquery
// 26 janvier 2014  ajout du champ email
// 30 mars 2014 ajout des champs noligue nodept et typelicence
// Auteur Simier Philippe mai 2009    philaure@wanadoo.fr
// Février 2015 controle du sexe avec la table des prénoms
// Février 2015 Ajout du champ GET compétition pour afficher uniquement celle demandée
// 19 Février 2015 Ajout du n° de téléphone et attribution automatique du dossard
// 04 Mai 2018 Adaptation PHP7 & HTML5
//---------------------------------------------------------------------------------------------

require_once('definitions.inc.php');
require_once('cotisation.php');
require_once('administration/utile_sql.php');
// connexion à la base de données pour les inscriptions

	$bdd = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);
	// lecture de la configuration et définition des constantes ENFFA SAISON DATE DESIGNATION etc
	$sql = 'SELECT * FROM `cross_route_configuration`';
	$stmt = $bdd->query($sql);
	while ($conf = $stmt->fetchObject()){
		define($conf->conf_key, $conf->conf_value);
	}
	// fin de la lecture de la configuration

	$erreur="";

// ------------si les inscriptions sont fermées affichage de la page close.php---------------------------
	if (isset($_GET['competition']) === false){
		// Lecture de la table competition pour obtenir  toutes les compétitions ouvertes à l'inscription
            
		$stmt = $bdd->query("SELECT * FROM `competition` WHERE  `validation`='1'");
		if ($stmt->fetchColumn()==false) { 
			header("Location: close.php");
			$bdd = NULL;
			exit;
			};
	}
	else
	{	$sql = "SELECT * FROM `competition` WHERE `nom`= \"".$_GET['competition']."\" and `validation`=1";
		$stmt = $bdd->query($sql);
		if ($stmt->fetchColumn()==false) { 
			header("Location: close.php");
			$bdd = NULL;
			exit;
			};
	}
	if (EN_FFA==FALSE) { 
		header("Location: close.php");
		exit;
		};

//--------------------si des données  sont reçues---------------------------------------------------------
if( !empty($_POST['envoyer'])){

    if (empty($_POST['reglement'])) {  
		$erreur = "Vous devez avoir lu et accepté le règlement pour cette compétition !";
    };
	
    if ($_POST['nom']=="") { 
		$erreur = "Vous devez indiquer votre nom !";
    };
	
	// Contrôle du champ email
	if ($_POST['email']=="") { 
		$erreur = "Oups vous devez indiquer votre email !";
    }
	else { 
		if ( !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$erreur = "Votre email n'est pas valide !!!";
		}	
	};

    if ($_POST['noclub']=="") { 
		$club = new stdClass();  // création d'un club vide pour les non licenciés
	    $club->noclub = "";
		$club->ligue = "";
		$club->nom = "";
    }
    else {
        $sql = "SELECT * FROM `ffa_club` WHERE `noclub`=" . $_POST['noclub'];
       	$stmt = $bdd->query($sql);
       	$club = $stmt->fetchObject();
    };
	
	// Création d'un objet epreuve
	$sql = "SELECT * FROM `cross_route_epreuve` WHERE `id_epreuve`= " . $_POST['id_epreuve']; 
	$stmt = $bdd->query($sql);
	$epreuve = $stmt->fetchObject();
	
    // Contrôle de la catégorie pour l'épreuve
    $cat = cat_ffa($_POST['anneenaissance'],$_POST['sexe']);
	if (!test_cat($_POST['anneenaissance'],$epreuve->categorie_autorise,$_POST['sexe'])) {
        $erreur = "Votre catégorie ".$cat." n'est pas autorisée pour cette épreuve !";
    }

	// Contrôle du sexe autorisé pour l'épreuve

    $sexe_autorises= explode(",",$epreuve->sexe_autorise);
	 
    if  (!in_array($_POST['sexe'], $sexe_autorises)) {
        if ($_POST['sexe']=='M') { $genre='Hommes';  $accord='s'; }
        else { $genre='Femmes'; $accord='es';}
        $erreur = "Oups, les ".$genre." ne sont pas autorisé".$accord." pour cette épreuve !";
    }
     
    // Création de l'objet licencie 
	if ($_POST['nolicence']) {
		$sql = sprintf("SELECT typelicence FROM  `ffa_licence` WHERE `nolicence`=%s",
           GetSQLValueString($_POST['nolicence'], "int")
        );
        //$reponse = mysql_query($sql);
		$stmt = $bdd->query($sql);
        //$licencie =  mysql_fetch_object ($reponse);
		$licencie = $stmt->fetchObject();
	} 
	else{
		$licencie = new stdClass();  // création d'une licence vide pour les non licenciés
		$licencie->typelicence = "";
	}	
	 
    // Contrôle du type de licence pour les compétitions uniquement autorisées au type COMP
     
		$sql = sprintf("SELECT licence FROM  `competition` WHERE `nom`=%s",
              GetSQLValueString($_POST['competition'], "text")
        );

		$stmt = $bdd->query($sql);
		$competition = $stmt->fetchObject();

    if ($competition->licence == "COMP") {
        if ($licencie->typelicence != "COMP") {
          $erreur = "Oups, les licences de type ".$licencie->typelicence." ne sont pas autorisées pour cette compétition !";

        }
    }
 
	// controle du sexe avec la table des prénoms
		$prenom = new stdClass();
		$prenom->genre = " ";
		$sql = "SELECT * FROM `prenom` WHERE `prenom` = '".$_POST['prenom']."'";
		$stmt = $bdd->query($sql);
		$prenom = $stmt->fetchObject();
	
	if (($_POST['sexe']=='M' && $prenom->genre == 'f')||($_POST['sexe'] == 'F' && $prenom->genre == 'm')) {
		$erreur = "Oups, " . $_POST['prenom'] . " est " . get_genre($prenom->genre);
		
	};		




 if (!$erreur){
      // voir module cotisation.php pour les prestations complémentaires et régles
    $cotisation = prix_cotisation($bdd, $_POST['id_epreuve']);

    if ($cotisation==0) $gratuit='oui'; else $gratuit='non';
    $commentaire = $cotisation;
    // si n° de licence est vide alors c'est un non licencié
    if ($_POST['nolicence']=='') $cas='0'; else $cas='1';
    if ($_POST['nolicence']=='') $certif='non'; else $certif='oui';
	
	// recherche du dernier n° de dossard attribué pour cette épreuve
	$sql = "SELECT MAX(`dossard`) AS high_dossard FROM cross_route_engagement WHERE `id_epreuve`= " . $_POST['id_epreuve'];
	$stmt = $bdd->query($sql);
	$dossard = $stmt->fetchObject();


       $insertSQL = sprintf("INSERT INTO cross_route_engagement (date,id_epreuve, dossard,competition,nom,prenom,noclub,anneenaissance,categorie,sexe,nodept,noligue,nolicence,nomequipe,nomcourse,typelicence,certifmedicalfourni,cotisationpaye,email,tel,commentaire) VALUES (CURRENT_TIMESTAMP,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
              GetSQLValueString($_POST['id_epreuve'], "int"),
			  GetSQLValueString(($dossard->high_dossard+1), "int"),
			  GetSQLValueString($_POST['competition'], "text"),
              GetSQLValueString($_POST['nom'], "text"),
              GetSQLValueString($_POST['prenom'], "text"),
              GetSQLValueString($_POST['noclub'], "text"),
              GetSQLValueString($_POST['anneenaissance'], "int"),
              GetSQLValueString(cat_ffa($_POST['anneenaissance'],$_POST['sexe']), "text"),
              GetSQLValueString($_POST['sexe'], "text"),
			  GetSQLValueString($club->noclub, "text"),
			  GetSQLValueString($club->ligue, "text"),
              GetSQLValueString($_POST['nolicence'], "text"),
              GetSQLValueString($club->nom, "text"),
              GetSQLValueString($epreuve->code, "text"),
			  GetSQLValueString($licencie->typelicence, "text"),
              "'".$certif."'",
              "'".$gratuit."'",
			  GetSQLValueString($_POST['email'], "text"),
			  GetSQLValueString($_POST['tel'], "text"),
              GetSQLValueString($commentaire, "text") );

              //$Result1 = mysql_query($insertSQL);
			  $stmt = $bdd->query($insertSQL);
			  
			  
              if ($stmt) {
              // retour vers la page de confirmation evec cas=1 licencié challenge ffa
              $GoTo = "confirmation.php?nom=".$_POST['nom']."&prenom=".$_POST['prenom']."&sexe=".$_POST['sexe']."&cas=".$cas."&info=".$commentaire."&gratuit=".$gratuit;
              header(sprintf("Location: %s", $GoTo));
              }
              else {
                $erreur = $bdd->errorInfo();
				
                if ($_POST['sexe']=='M') $accord =''; else $accord='e';
                if (substr($erreur[2],0,8)=="Duplicat") { $erreur = " Oups, Vous êtes déja inscrit".$accord." pour cette course !"; }
              }
     }
	 
}
//------------------------------------------------------------------------------------------------------------
$pdo = null; 

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

// fonction pour tester la catégorie autorisé sur une épreuve
// si l'age de l'engagé est dans une catégorie autorisée la fct renvoie TRUE

  function test_cat($annee,$cat_autorisees,$sexe){
    $age=SAISON-$annee;
    $tableau=explode(",",$cat_autorisees);
    foreach ($tableau as $cat) {
      if ($age>=1  && $age<=9 && $cat=="EA") return true;
      if ($age>=10 && $age<=11 && $cat=="PO") return true;
      if ($age>=12 && $age<=13 && $cat=="BE") return true;
      if ($age>=14 && $age<=15 && $cat=="MI") return true;
      if ($age>=16 && $age<=17 && $cat=="CA") return true;
      if ($age>=18 && $age<=19 && $cat=="JU") return true;
      if ($age>=20 && $age<=22 && $cat=="ES") return true;
      if ($age>=23 && $age<=39 && $cat=="SE") return true;
      if ($age>=40 && $age<=100 && $cat=="VE") return true;
      if ($age>=40 && $age<=49 && $cat=="V1") return true;
      if ($age>=50 && $age<=59 && $cat=="V2") return true;
      if ($age>=60 && $age<=69  && $sexe=='M' && $cat=="V3") return true;
      if ($age>=70 && $age<=120 && $sexe=='M' && $cat=="V4") return true;
      if ($age>=60 && $age<=120  && $sexe=='F' && $cat=="V3") return true;
      if ($age>=11 && $age<=100  && $cat=="TC") return true;
  }
  return false;
 }

 // cette fonction donne masculin ou féminin en fonction en fct du genre
   function get_genre($x){
    if ($x == "m") return "masculin"; 
	elseif ($x == "f") return "féminin"; else return "";
   }
 
	// début du fichier bandeau menu horizontal
	if (!is_readable('administration/en_tete.html'))  die ("fichier non accessible");
	@readfile('administration/en_tete.html') or die('Erreur fichier'); 

?>
			<script type="text/javascript"><!--
				alertNeeded();
			//-->
			</script>
			
			<div id="contenu" style="width:90%;" >
				<h2>Inscription en ligne <?php echo SAISON ?></h2>
				<?php if ($erreur) {echo '<p style="color:#FF0000;">'.$erreur."</p>"; } else { echo "<p> </p>"; }?>
				
				<div class="item" >
					<p><b>Vos informations personnelles : </b><span id="loader" style="display:none;"><img src="images/loader.gif"  alt="loader" /></span></p>

					<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" name="engagement" >

						<table  border="0" cellpadding="2" cellspacing="2">
							<tbody>
								<tr>					
									<td >
										<label for="nolicence">Numéro de licence :</label>
										<input type="text" class="normal" name="nolicence"  maxlength="10" onChange="licence_ffa(this)" value="<?php if (isset($_POST['nolicence'])) echo $_POST['nolicence']; ?>"/>
									</td>
									
									<td>
										<label for="noclub">Numéro de club :</label>
										<input class="normal" name="noclub" onKeyPress="return pasCar(event)" maxlength="6"<?php if (isset($_POST['noclub'])) echo 'value="'.$_POST['noclub'].'" readonly="readonly"'; else echo ' value="" ';?>/>
									</td>
								</tr>
							  
								<tr> 
									<td>
										<label for="nom">Nom :</label>
										<input type="text" class="normal" name="nom" onKeyPress="return pasNum(event)" onChange="majuscule(this)"  maxlength="20" <?php if (isset($_POST['nom'])) echo 'value="'.$_POST['nom'].'" readonly="readonly"'; ?> required/>
									</td>
									<td>
										<label for="prenom">Prénom :</label>
										<input type="text" class="normal" name="prenom" onKeyPress="return pasNum(event)" onChange="majuscule(this)" maxlength="20" <?php if (isset($_POST['prenom'])) echo 'value="'.$_POST['prenom'].'" readonly="readonly"'; ?> required/>
									</td>
								</tr>
								<tr>
									<td>
										<label for="anneenaissance">Année de naissance :</label>
										<input type="number" min="1920" class="normal" name="anneenaissance" size="4" maxlength="4" onKeyPress="return pasCar(event)" <?php if (isset($_POST['anneenaissance'])) echo 'value="'.$_POST['anneenaissance'].'" readonly="readonly"'; ?> required/>
									</td>
									<td>
										<label for="sexe">Sexe :</label>
										<input type="radio"  name="sexe"  value="M" checked="checked" />Masculin
										<input type="radio" <?php if (isset($_POST['sexe']) && $_POST['sexe']=='F')echo 'checked="checked"' ?> name="sexe" value="F"   />Féminin
									</td>
								</tr>
							  
								<tr>
									<td>
										<label for="email">Email :</label>
										<input type="email" class="normal" name="email" id="email" maxlength="50" <?php if (isset($_POST['email'])) echo 'value="'.$_POST['email'].'"'; ?> required/>
									</td>
									<td>
										<label for="tel">Tel :</label>
										<input type="tel" class="normal" name="tel" id="tel" maxlength="10"  <?php if (isset($_POST['tel'])) echo 'value="'.$_POST['tel'].'"'; ?> required/>
									</td>
								</tr>
							  
								<tr>	
									<td>
										<label for="competition">Vous souhaitez vous inscrire : </label>
										<?php 

										if ((isset($_GET['competition']) == false) && (isset($_POST['competition']) == false)){
											echo '<select name="competition" id="competition">';
											echo '<option selected="selected" value="">Choisissez l\'évènement\'</option>';
											// connexion à la base de données BASE
											$bdd = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);
											// Lecture de la table competition pour obtenir libellés et dates  de toutes les compétitions validées
									
											$sql = "SELECT * FROM `competition` WHERE  `validation`='1'";
											$stmt = $bdd->query($sql);
											//if ((stmt->rowCount())==0) echo "<optgroup label=\"Aucun évènement disponible\">";
											//if ((stmt->rowCount())==1) echo "<optgroup label=\"évènement disponible\">";
											//if ((stmt->rowCount())>1) echo "<optgroup label=\"évènements disponibles\">";
											while ($competition = $stmt->fetchObject()){
												echo '<option value="'.$competition->nom.'">'.$competition->nom.' ('.date("j M Y",strtotime($competition->date)).')</option>';
											}
													
									
											// fin de la lecture des competitions
								   
											echo '</optgroup>';
											echo '</select>';
										}	
										else
										{
											if (!empty($_GET['competition'])){
												echo "<b>".$_GET['competition']."</b>";
												echo '<input type="hidden" name="competition" value="'.$_GET['competition'].'">';
											}
											if (!empty($_POST['competition'])){
												echo "<b>".$_POST['competition']."</b>";
												echo '<input type="hidden" name="competition" value="'.$_POST['competition'].'">';
											}
										}
										?>
									</td>
									<td id="epreuve">
										<label for="id_epreuve">choisissez : </label>
										<?php 
											echo '<select name="id_epreuve">';
											if ( !isset($_GET['competition']) && !isset($_POST['competition']))
											{
											  echo '<option selected="selected" value="">Choisissez l\'option</option>';
											}
											else
											{
											  // connexion à la base 
												$bdd = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);
												// Lecture de la table cross_route_epreuve pour obtenir les désignations et codes des epreuves
												if (isset($_GET['competition'])){
													$sql = "SELECT * FROM cross_route_epreuve WHERE `competition`='".$_GET['competition']."'";}
												if (isset($_POST['competition'])){
													$sql = "SELECT * FROM cross_route_epreuve WHERE `competition`='".$_POST['competition']."'";}
															
												$stmt = $bdd->query($sql);
												while ($epreuve = $stmt->fetchObject() ){
													echo '<option value="'.$epreuve->id_epreuve.'"';
													echo $_POST['id_epreuve']; //
													echo $epreuve->code;  //
																
													if ($_POST['id_epreuve']==$epreuve->id_epreuve) echo ' selected="selected" ';
														echo '>'.$epreuve->designation.'</option>';
													}
															
													// fin de la lecture des epreuves	
											}
											echo '</select>';
										?>	
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<br />Pour pouvoir valider votre inscription, vous devez accepter le règlement suivant :
										<br />En cochant la case, vous reconnaissez avoir lu et accepté le règlement de cette épreuve
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<input type="checkbox" name="reglement" > <b>Règlement de la compétition</b>
										<a href="reglement.php" target="_blank"> (réglement)</a>
									</td>
								</tr>
								<tr>
									<td colspan="2" style=" width:28%;"><input value="Valider" name="envoyer" type="submit" onclick="alertNotNeeded()"/>
									</td>
							  
								</tr>
							</tbody>
						</table>
					
					</form>
					
				</div>
			</div>
		
		<?php
			 @readfile('administration/pied_de_page.html') or die('Erreur fichier');
		?>
		
