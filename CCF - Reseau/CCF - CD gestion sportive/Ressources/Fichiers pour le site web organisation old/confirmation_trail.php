<?php
//---------------------------------------------------------------------------------------------
// Ce script remercie l'internaute qui vient de s'inscrire
//
//
// Auteur Simier Philippe Janvier 2011    philaure@wanadoo.fr
//---------------------------------------------------------------------------------------------

require_once('definitions.inc.php');
require_once('cotisation.php');
// connexion à la base marsouin
 @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
 @mysql_select_db(BASE) or die("Echec de selection de la base cdt");

// lecture de la configuration et définition des constantes ENFFA SAISON DATE DESIGNATION etc
      $sql = 'SELECT * FROM `cross_route_configuration`';
      $reponse = mysql_query($sql) or die(mysql_error());
      while ($conf = mysql_fetch_object ($reponse)){
       define($conf->conf_key, $conf->conf_value);
      }
// fin de la lecture de la configuration
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" /><title>la course</title>

<link href="css/bassin.css" type="text/css" rel="StyleSheet" />
</head>

<body topmargin="0" leftmargin="0" style="background-color: #FFFFFF; background-image: url();" >
<div id="page" style="width: 1099px; border-style: dotted; border-width: 1px">
<div id="bandeau_flash" style="width: 1098px; height: 336px;">
<a href="http://www.esr72.fr/" title="Retour page d'accueil">
<img style="width: 1098px; height: 336px; border: 0px;" alt="ESR72" src="images/bandeau_trail.jpg" /></a><br />
</div>

<!--Menu horizontal sous la banniere-->

<div class="contenuArticle" style="text-align: justify;">
<p style="  margin-left: 0;">
<span style="font-family: 'arial black', 'avant garde'; font-size: 10pt;">
<a href="#" style="margin-right: 28px; margin-left:28px;">ACCUEIL</a>
<a href="#" style="margin-right: 28px;">ACTUALITE</a>
<a href="#" style="margin-right: 28px;">LE TRAIL</a>
<a href="#" style="margin-right: 28px;">PRESENTATION</a>
<a href="#" style="margin-right: 28px;">PARCOURS</a>
<a href="http://esr72.fr/organisations/inscription.php" style="margin-right: 28px;">INSCRIPTIONS</a>
&nbsp; &nbsp; &nbsp; &nbsp;PUBLIC &nbsp; &nbsp; &nbsp; &nbsp;
<a target="_blank" href="#" style="margin-right: 28px;">IMAGES</a>
<a href="http://esr72.fr/organisations/administration/index.php" style="margin-right: 28px;">INTRA</a>
</span>
</p>
<div class="clear"></div>
</div>

<!-- fin de menu horizontal -->

<div id="menu"><br />
</div>
<div id="contenu">
<h2>Votre demande a bien été prise en compte</h2>
<p>Merci pour votre inscription</p>
<?php if  ($_GET['cas']=="0") {
   echo "
<p>Vous êtes non licencié FFA, votre inscription ne sera définitivement prise en compte
que lorsque vous aurez fait parvenir au secrétariat<b> votre certificat médical de non contre-indication à la pratique de la course à pied en
compétition (mention obligatoire)</b>. Les licences sportives hors FFA ne remplacent pas le certificat médical.</p>";
}

if  ($_GET['cas']=="1") {
   echo ""; }
if  ($_GET['cas']=="2") {
   echo "
<p>Vous venez d'inscrire votre équipe <b>".$_GET['nomequipe']."</b> au challenge entreprises/militaires, si vous êtes non-licenciés FFA, votre inscription ne sera définitivement prise en compte
que lorsque vous aurez fait parvenir au secrétariat les certificats médicaux de non contre-indication à la pratique de la course à pied en
compétition (mention obligatoire). Les licences sportives hors FFA ne remplacent pas le certificat médical.</p>";
}
if (!isset($_GET['gratuit'])) { $_GET['gratuit']="non";}
if ($_GET['gratuit']=="non"){
echo "<p>Pour valider votre engagement nous attendons votre réglement ".$_GET['info']."€ par chèque au nom d'Endurance Shop Running 72.<br />
Ou par paiement en ligne.".'
<center>
   <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="hosted_button_id" value="AUBNBM46X4QJA">
		<table>
		<tr><td><input type="hidden" name="on0" value="Epreuves">Epreuves</td></tr><tr><td><select name="os0">
			<option value="Trail 14km">Trail 14km 10,00 €</option>
			<option value="Trail 7km">Trail 7km 5,00 €</option>
			<option value="Marche 7km">Marche 7km 3,00 €</option>
		</select> </td></tr>
		</table>
		<input type="hidden" name="currency_code" value="EUR">
		<input type="image" src="https://www.paypalobjects.com/fr_FR/FR/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - la solution de paiement en ligne la plus simple et la plus sécurisée !">
		<img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
	</form>
</center>'."
</p>

<p>Nous vous rappelons que l'adresse postale est la suivante :<br /></p><div style='text-align: center;'><span style='font-weight: bold;'>
Chéreau Elise</span><br /> Champs Elysées <br />72250 Brette les Pins</div>";
}
?>

<p>Vous pouvez consulter  la prise en compte de votre inscription : <a href="verif_inscription_trail.php">vérification</a></p> <p></p>
<p><b>
<?php 
if ($_GET['sexe']=="F") echo 'Mme ';   else echo 'M ';
echo $_GET['prenom']." ".$_GET['nom'] ?>
</b>, nous vous souhaitons un agréable trail.</p>
</div>

<div id="pied"> Site hébergé par Esr72 - 72000 LE MANS <br />

</div>
</div>
</body></html>
