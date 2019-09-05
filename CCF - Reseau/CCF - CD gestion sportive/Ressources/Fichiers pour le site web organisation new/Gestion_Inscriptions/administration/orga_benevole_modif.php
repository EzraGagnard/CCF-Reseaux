<?php
	// vérification des variables de session pour le temps d'inactivité et de l'adresse IP
	include "authentification/authcheck.php" ;
	// Vérification des droits pour cette page uniquement admin
	if ($_SESSION['droits']<'2') { 
		header("Location: ../index.html");
	};
	require_once('../definitions.inc.php');
	require_once('utile_sql.php');

	// connexion à la base
	$bdd = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);

	// page autoréférente
	if( !empty($_POST['envoyer'])){


		$sql = sprintf("UPDATE utilisateur SET identite=%s , login=%s ,  droits=%s , email=%s , telephone=%s , fonction=%s , taille=%s WHERE ID_user=%s",
                       GetSQLValueString($_POST['identite'], "text"),
                       GetSQLValueString($_POST['login'], "text"),
                       GetSQLValueString($_POST['droits'] , "int"),
                       GetSQLValueString($_POST['email'] , "text"),
                       GetSQLValueString($_POST['telephone'] , "text"),
                       GetSQLValueString($_POST['fonction'] , "text"),
                       GetSQLValueString($_POST['taille'] , "text"),
			GetSQLValueString($_POST['ID_user'], "int")
			);
		$stmt = $bdd->query($sql);

		// si le mot de passe est changé
		if ($_POST['md5']<>'no_change'){
        $sql = sprintf("UPDATE utilisateur SET  passe=%s WHERE ID_user=%s",
                       GetSQLValueString($_POST['md5'], 'text'),
		       GetSQLValueString($_POST['ID_user'], 'int')
					   );
        $stmt = $bdd->query($sql);
		}

		// retour vers tableau acteurs
 
		$GoTo = "orga_benevoles.php";
		header(sprintf("Location: %s", $GoTo));
	}
	// recherche des infos en fct de ID_acteur

	if ((isset($_GET['ID_user'])) && ($_GET['ID_user'] != "")) {
        $sql = "SELECT * FROM utilisateur WHERE ID_user=".$_GET['ID_user']."";
        $stmt = $bdd->query($sql);
        $utilisateur = $stmt->fetchObject();
    }

 
	// début du fichier bandeau menu horizontal
	if (!is_readable('en_tete.html'))  die ("fichier non accessible");
		@readfile('en_tete.html') or die('Erreur fichier');

?>


	<script type="text/javascript" src="jscript/cryptage_passe.js"></script>

	  <style type="text/css">
		<!--
		tr {
		   height: 35px;
		}
		td {
		   padding: 3px;
		}
		-->
	  </style>

	<script language="javascript">


		// fonction pour tester la validité de l'adresse mail
		  function testMail(champ){
			 if (champ.value!=""){
			   mail=/^[a-zA-Z0-9]+[a-zA-Z0-9\.\-_]+@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9])+$/;
			   if (!mail.test(champ.value)) {
					   alert ("Votre adresse e-mail est invalide!");
					   champ.focus();
					   return false;
			   }
			 }
		  }

		// fonction pour interdire les caractéres numériques
		  function pasNum(e){
			if (window.event) caractere = window.event.keyCode;
			else  caractere = e.which;

			return (caractere < 48 || caractere > 57);
		  }
	</script>

   
    <div id="contenu">
        <h2>Information Acteur</h2>
        <a href="javascript:history.back(1)">
		<img border="0" src="../images/fleche_retour.png" width="44" height="42"></a>
                <img border="0" src="../images/user.png">
                
        <div class="item">
            <form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="POST" name="form1" onSubmit="return submit_modifpass2();">

                <input type='hidden' name='md5' id='md5'/>
                <input type='hidden' name='ID_user' value='<?php echo $_GET['ID_user']; ?>'/>
                <table border="0"   style="border-collapse: collapse"  >
                    <tr>
                        <td width="30%"  style="text-align: right"><b>Nom :</b></td>
                        <td width="70%" ><input  name="identite"  style="cursor: text" size="60" value="<?php echo $utilisateur->identite; ?>"  onKeyPress="return pasNum(event)"/>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right"><b>Identifiant :</b></td>
                        <td><input type="text" name="login" size="60" value="<?php echo $utilisateur->login; ?>"  onKeyPress="return pasNum(event)"/></td>
                    </tr>
                    <tr>
                    <td style="text-align: right"><b>EMail :</b></td>
                        <td><input type="text" name="email" size="60" value="<?php echo $utilisateur->email; ?>" onBlur="testMail(this)"></td></td>
                    </tr>
                    <tr>
                        <td style="text-align: right"><b>Fonction :</b></td>
                        <td><input type="text" name="fonction" size="60" value="<?php echo $utilisateur->fonction; ?>"></td></td>
                    </tr>
                    <tr>
                        <td style="text-align: right"><b>Taille :</b></td>
                        <td>
							<input <?php if ($utilisateur->taille=='S')echo 'checked="checked"' ?> name="taille"  value="S" type="radio" />S -
							<input <?php if ($utilisateur->taille=='M')echo 'checked="checked"' ?> name="taille"  value="M" type="radio" />M -
							<input <?php if ($utilisateur->taille=='L')echo 'checked="checked"' ?> name="taille"  value="L" type="radio" />L -
							<input <?php if ($utilisateur->taille=='XL')echo 'checked="checked"' ?> name="taille"  value="XL" type="radio" />XL -
						</td>
                    </tr>
                    <tr>
                        <td style="text-align: right"><b>Téléphone :</b></td>
                        <td><input type="text" name="telephone" size="14" value="<?php echo $utilisateur->telephone; ?>"></td></td>
                    </tr>
                    <tr>
                        <td style="text-align: right">
                        <b>Mot de passe  :</b></td>
                        <td><input type="password" name="passe" id="passe" value="" size="32"></td>
                    </tr>
                    <tr>
                        <td style="text-align: right">
                        <b>Confirmation Mot de passe :</b></td>
                        <td><input type="password" name="passe2" id="passe2" value="" size="32"></td>
                    </tr>
                    <tr>
                        <td style="text-align: right">
                        <b>Groupe :</b></td>
                        <td>
							<select name="droits" id="droits">
                                <option value="0" <?php if($utilisateur->droits==0) echo "selected" ?>>0</option>
                                <option value="1" <?php if($utilisateur->droits==1) echo "selected" ?>>Organisateur 1</option>
                                <option value="2" <?php if($utilisateur->droits==2) echo "selected" ?>>Organisateur 2</option>
								<option value="3" <?php if($utilisateur->droits==3) echo "selected" ?>>Administrateur 3</option>
							</select>
                        </td>
                    </tr>
                        <td>
                        </td>
                        <td>
							<input type="submit" value="Envoyer" name="envoyer">
						</td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
	<?php
     @readfile('pied_de_page.html') or die('Erreur fichier');
	?>
