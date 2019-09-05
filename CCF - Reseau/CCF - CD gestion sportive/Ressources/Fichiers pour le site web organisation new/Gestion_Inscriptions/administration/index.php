<?php
// page d'authentification pour la partie sécurisée du site
// cette page affiche un formulaire avec deux champs (login et passe)
// et un bouton pour soumettre au script auth.php

require_once('../definitions.inc.php');
session_start();
unset($_SESSION['identite']);
unset($_SESSION['login']);
unset($_SESSION['ID_user']);
unset($_SESSION['email']);
unset($_SESSION['droits']);

// début de la page bandeau menu horizontal
    @readfile('en_tete.html') or die('Erreur fichier');
?>

    <script language="javascript" type="text/javascript" src="./authentification/login.js"></script>
    

    <div id="contenu" style="width: 95%">
        <h2>Authentification :</h2>
		<div id="auth" class="jumbotron text-center">
			<p>Bienvenue! pour continuer le serveur<br /><span style="font-weight:bold;"> <?php echo $_SERVER["SERVER_NAME"]; ?></span><br /></p>
			  <?php if (isset($_GET["erreur"])) echo '<p style="color: #ff0000;">'.$_GET["erreur"].'</p>';
					  else echo '<p>Requiert un identifiant et un mot de passe.</p>';
			   ?>
			
			<form method="POST" action="./authentification/auth.php" onSubmit="javascript:submit_pass();" name="form2" id="form2">
				<input type='hidden' name='md5' />
					<div class="form-group">
						<label for="login">Identifiant :</label>
						<input type="text" class="normal" name="login" size="20" onchange="alertNeeded()" required="">
					</div>	
					<div class="form-group">
						<label for="password">Mot de passe :</label>
						<input type="password" class="normal" name="passe" size="20" onchange="alertNeeded()" required="">
					</div>
					<button type="submit" class="btn btn-primary" value="Valider" name="B1" onclick="alertNotNeeded()" > Valider</button>
							
			</form>
        </div>
        
    </div>
<?php
     @readfile('pied_de_page.html') or die('Erreur fichier');
?>

