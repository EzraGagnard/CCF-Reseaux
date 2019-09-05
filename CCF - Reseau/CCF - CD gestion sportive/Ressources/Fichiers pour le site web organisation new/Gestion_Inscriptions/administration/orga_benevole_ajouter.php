<?php
//--------------------------------------------------
//  Ce script ajoute un acteur
//  page autoréférente protégée  droits >= 2
//--------------------------------------------------
	require_once('../definitions.inc.php');
	require_once('utile_sql.php');

	include "authentification/authcheck.php" ;
	if ($_SESSION['droits']<'2') { 
		header("Location: ../index.html");
	};

	if( !empty($_POST['envoyer'])){

		// connexion à la base de données
		$bdd = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);

		if ($_POST['identite']=="") {
			echo "Vous devez indiquer un nom";
			exit;
		};
    
		if ($_POST['mode']=="insertion"){
			$sql = sprintf("INSERT INTO utilisateur (login,passe,identite,email,telephone,droits,fonction,taille) VALUES (%s,%s,%s,%s,%s,%s,%s,%s)",

                       GetSQLValueString($_POST['login'], "text"),
                       GetSQLValueString($_POST['md5'], "text"),
                       GetSQLValueString($_POST['identite'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['telephone'], "text"),
                       GetSQLValueString($_POST['droits'], "int"),
                       GetSQLValueString($_POST['fonction'], "text"),
                       GetSQLValueString($_POST['taille'], "text")

	            );
			$stmt = $bdd->query($sql);
		}
 

    $GoTo = "orga_benevoles.php";
    header(sprintf("Location: %s", $GoTo));
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
                   alert ("L'adresse email est invalide.\nElle doit être de la forme xxx@xxx.xxx");
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
          
        // fonction pour autoriser uniquement les numériques
        function pasCar(e){
          if (window.event) caractere = window.event.keyCode;
               else  caractere = e.which;
                 return (caractere == 8 || (caractere > 47 && caractere < 58));
          }

        // fonction pour mettre en majuscule
        function majuscule(champ){
        champ.value = champ.value.toUpperCase();
        }
          
	</script>

	<div id="contenu">
		<h2>Gestion des acteurs</h2>
		<div class="item">
			<p>informations: </p>
			<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>"  name="form1" onSubmit="return submit_modifpass2();">
				<input type="hidden" name="mode" value="insertion" />
				<input type='hidden' name='md5' id='md5'/>
				
				<table border="0"   style="border-collapse: collapse"  >
                    <tr>
                        <td width="30%"  style="text-align: right"><b>Nom :</b></td>
                        <td width="70%" ><input  name="identite"  style="cursor: text" size="60" value=""  onKeyPress="return pasNum(event)"/>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right"><b>Identifiant :</b></td>
                        <td><input type="text" name="login" size="60" value=""  onKeyPress="return pasNum(event)"/></td>
                    </tr>
                    <tr>
                        <td style="text-align: right"><b>EMail :</b></td>
                        <td><input type="text" name="email" size="60" value="" onBlur="testMail(this)"></td></td>
                    </tr>
                    <tr>
                        <td style="text-align: right"><b>Fonction :</b></td>
                        <td><input type="text" name="fonction" size="60" value=""></td></td>
                    </tr>
                    <tr>
                        <td style="text-align: right"><b>Taille :</b></td>
                        <td><input  name="taille"  value="S" type="radio" />S -
                            <input  name="taille"  value="M" type="radio" checked="checked"/>M -
                            <input  name="taille"  value="L" type="radio" />L -
                            <input  name="taille"  value="XL" type="radio" />XL -
						</td>
                    </tr>
                    <tr>
                        <td style="text-align: right"><b>Téléphone :</b></td>
                        <td><input type="text" name="telephone" size="14" value=""></td></td>
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
									<option value="0" >sans droit 0</option>
									<option value="1" >Organisateur 1</option>
									<option value="2" selected="selected">Organisateur 2</option>
									<option value="3" >Administrateur 3</option>
							</select>
                        </td>
                    </tr>
					<tr>
                        <td>
                        </td>
                        <td>
                        <input type="submit" value="Envoyer" name="envoyer"></td>
                    </tr>

				</table>
			</form>
		</div>
	</div>
	<?php
     @readfile('pied_de_page.html') or die('Erreur fichier');
	?>
