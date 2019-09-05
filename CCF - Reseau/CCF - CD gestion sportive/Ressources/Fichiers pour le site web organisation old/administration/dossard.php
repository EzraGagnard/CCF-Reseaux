<?php
//---------------------------------------------------------------------------
// Ce script réalise l'affectation des dossards pour une compétition
// le format du fichier doit avoir au minimum 6 colonnes:
//
//
//
//
// Simier Philippe 19/02/2015
// version 1.0
// page autoréférente protégée
//---------------------------------------------------------------------------
   include "authentification/authcheck.php" ;
   require_once('utile_sql.php');

   // Vérification des droits pour cette page uniquement organisateurs
   if ($_SESSION['droits']<>'2') { header("Location: index.php");};
require_once('../definitions.inc.php');
@mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
@mysql_select_db(BASE) or die("Echec de selection de la base");

// lecture de la configuration et définition des constantes ENABLE SAISON DATE DESIGNATION etc
      $sql = 'SELECT * FROM `cross_route_configuration`';
      $reponse = mysql_query($sql) or die(mysql_error());
      while ($conf = mysql_fetch_object ($reponse)){
       define($conf->conf_key, $conf->conf_value);
      }
// fin de la lecture de la configuration
if( !empty($_POST['Affecter'])){

    $sql = "set @valeur =".($_POST['start']-1).";";  
	$Result = mysql_query($sql) or die(mysql_error());
	$sql2 = "update cross_route_engagement set `dossard` = @valeur := @valeur + 1 where `competition`= \"".$_POST['competition']."\";";
	$Result = mysql_query($sql2) or die(mysql_error());

	@mysql_close();
    
	$GoTo = "orga_tab_enga.php?competition=".stripslashes($_POST['competition']);
	
    header(sprintf("Location: %s", $GoTo));
}
// début du fichier bandeau menu horizontal
  if (!is_readable('en_tete.html'))  die ("fichier non accessible");
  @readfile('en_tete.html') or die('Erreur fichier');
?>

<div id="menu" style="width: 100px;">
</div>
<div id="contenu" style="min-height:500px;">
     <h2>Affecter les dossards sur la compétition</h2>

     <p align="left">
        Vous allez affecter les dossards aux athlètes de façon automatique<br />
		Les anciens dossards seront perdus !<br />
		Voulez vous continuer ?<br />
		<br />
		<a href="orga_menu.php"><img src="../images/fleche_retour.png" title="Retour" border="0" width="44" height="41"></a>
		
		 
        
      </p>
      

    <p align="center"></p>
		<div class="item">

			<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>"  name="dossard">
     			<input type="hidden" name="competition" value="<?php echo $_GET['competition']; ?>" />
				<table style="text-align: left; width: 600px; height: 160px;"   border="0" cellpadding="2" cellspacing="2">
					<tbody>
					<tr>
						<td style="width: 25%; " ><img border="0" src="../images/dossard.jpg"></td>
						<td>N° du premier dossard :</td>
						<td style="width: 25%; "><input type="text" size="10" name="start" class="normal"/>
						</td>
					</tr>
        
					<tr>
						<td></td>
						<td></td>
						<td><input name="Affecter" value="Affecter les dossards"  type="submit" /></td>

					</tr>
					</tbody>
				</table>
			</form>
		</div>
</div>
</div>
<div id="pied"> <br />
</div>

</body></html>
