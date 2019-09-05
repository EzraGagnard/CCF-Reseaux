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
      <style type="text/css">
       <!--
         td {  padding: 10px;  }
        -->
      </style>

     <div id="contenu" style="width: 1024px; min-height:500px;">

        <h2>Authentification :</h2>
        <center>
	<div id="auth">
      	  <p>Bienvenue! pour continuer le serveur<br /><span style="font-weight:bold;"> <?php echo $_SERVER["SERVER_NAME"]; ?></span><br /></p>
          <?php if (isset($_GET["erreur"])) echo '<p style="color: #ff0000;">'.$_GET["erreur"].'</p>';
                  else echo '<p>Requiert un identifiant et un mot de passe.</p>';
           ?>
          <p>
          <form method="POST" action="./authentification/auth.php" onSubmit="javascript:submit_pass();" name="form2" id="form2">
          <input type='hidden' name='md5' />
	  <table><tr>
	       	     <td>Identifiant :</td>
		     <td><input type="text" name="login" size="30"></td>
		</tr>
		<tr>
		    <td>Mot de passe :</td>
		    <td><input type="password" name="passe" size="30"></td>
		</tr>
		<tr>
	            <td></td>
	            <td><input type="submit" value="Valider" name="B1"></td>
	        </tr>
	  </table>
          </form>
          </p>
        </div>
        </center>
     </div>
	 <?php
     @readfile('pied_de_page.html') or die('Erreur fichier');
	 ?>
</div>
</body>
</html>
