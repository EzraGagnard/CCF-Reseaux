<?php
//---------------------------------------------------------------------------------------------
// Ce script confirme l'inscription et remercie l'internaute qui vient de s'inscrire
//
//
// Auteur Simier Philippe Janvier 2011    philaure@wanadoo.fr
//---------------------------------------------------------------------------------------------

	require_once('definitions.inc.php');
	require_once('cotisation.php');

	// connexion à la base de données pour les inscriptions

	$bdd = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);
	// lecture de la configuration et définition des constantes ENFFA SAISON DATE DESIGNATION etc
		  $sql = 'SELECT * FROM `cross_route_configuration`';
		  $stmt = $bdd->query($sql);
		  while ($conf = $stmt->fetchObject()){
			define($conf->conf_key, $conf->conf_value);
		  }
	// fin de la lecture de la configuration
	// début de la page bandeau menu horizontal
		@readfile('administration/en_tete.html') or die('Erreur fichier');
?>


		<div id="contenu">
			<h2>Votre demande a bien été prise en compte</h2>
			<p>Merci pour votre inscription</p>
			<?php 
				if  ($_GET['cas']=="0") {
					echo "<p>Vous êtes non licencié FFA, votre inscription ne sera définitivement prise en compte
					que lorsque vous aurez fait parvenir au secrétariat<b> votre certificat médical de non contre-indication à la pratique de la course à pied en
					compétition (mention obligatoire)</b>. Les licences sportives hors FFA ne remplacent pas le certificat médical.</p>";
				}

				if  ($_GET['cas']=="1") {
				   echo "<p>Vous êtes licencié FFA </p>"; 
				}
				
				if  ($_GET['cas']=="2") {
					echo "<p>Vous venez d'inscrire votre équipe <b>".$_GET['nomequipe']."</b> au challenge entreprises/militaires, si vous êtes non-licenciés FFA, votre inscription ne sera définitivement prise en compte
					que lorsque vous aurez fait parvenir au secrétariat les certificats médicaux de non contre-indication à la pratique de la course à pied en
					compétition (mention obligatoire). Les licences sportives hors FFA ne remplacent pas le certificat médical.</p>";
				}
				
				if (!isset($_GET['gratuit'])) { 
					$_GET['gratuit']="non";
				}
				if ($_GET['gratuit']=="non"){
				echo "<p>Pour valider votre engagement nous attendons votre règlement ".$_GET['info']."€ par chèque au nom des SNIR lycée Touchard.<br />
				Ou par paiement en ligne.".'
				<center>
				   <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
						<input type="hidden" name="cmd" value="_s-xclick">
						<input type="hidden" name="hosted_button_id" value="AUBNBM46X4QJA">
						<table>
						<tr><td><input type="hidden" name="on0" value="Epreuves">Epreuves</td></tr><tr><td><select name="os0">
							<option value="Trail 14km">Trail 14km 10,00€</option>
							<option value="Trail 7km">Trail 7km 5,00€</option>
							<option value="Marche 7km">Marche 7km 3,00€</option>
						</select> </td></tr>
						</table>
						<input type="hidden" name="currency_code" value="EUR">
						<input type="image" src="https://www.paypalobjects.com/fr_FR/FR/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - la solution de paiement en ligne la plus simple et la plus s�curis�e !">
						<img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
					</form>
				</center>'."
				</p>

				<p>Nous vous rappelons que l'adresse postale est la suivante :<br /></p><div style='text-align: center;'><span style='font-weight: bold;'>
				Lycée Touchard</span><br /> Place Washington <br />72017 le Mans</div>";
				}
				else{
				 echo "<p> L'inscription est gratuite </p>";	
				}	
					
			?>

			<p>Vous pouvez consulter  la prise en compte de votre inscription : <a href="verif_inscription.php">vérification</a></p> 
			<p></p>
			<p><b>
			<?php 
			if ($_GET['sexe']=="F") echo 'Mme ';   else echo 'M ';
			echo $_GET['prenom']." ".$_GET['nom'] ?>
			</b>, nous vous souhaitons une agréable course.</p>
		</div>
		<?php
			// pied de page
			@readfile('administration/pied_de_page.html') or die('Erreur fichier');
		?>
		
	
