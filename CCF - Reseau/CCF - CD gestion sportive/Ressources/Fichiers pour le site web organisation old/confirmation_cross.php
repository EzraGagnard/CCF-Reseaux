<?php
//---------------------------------------------------------------------------------------------
// Ce script remercie l'internaute qui vient de s'inscrire
//
//
// Auteur Simier Philippe mai 2009    philaure@wanadoo.fr
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
<body leftmargin="0" topmargin="0">
<div id="page">
<div id="bandeau_flash" style="width: 1024px; height: 150px;">
	<img style="width: 1024px; height: 150px;" alt="f" src="images/bandeau_E72.jpg" /><br />
</div>

<!--Menu horizontal sous la banniere-->
<div id="nav-horizon">
<ul id="menuDeroulant">
     <li><a href="#"></a>
    	
</li>
     <li><a href="#"></a>
     </li>
     <li><a href="#"></a>
	 </li>
	 <li><a href="#"></a>
     </li>    
</ul>
</div><!-- fin de menu horizontal -->
<!-- fin de menu horizontal -->

<div id="menu"><br />
</div>
<div id="contenu">
<h2>Votre demande a bien été prise en compte</h2>
<p>Merci pour votre inscription à <?php echo DESIGNATION ?></p>
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
echo "<p>Pour valider votre engagement nous attendons votre réglement".$_GET['info']." par chèque au nom d'Endurance 72.<br /></p>

<p>Nous vous rappelons que l'adresse postale est la suivante :<br /></p><div style='text-align: center;'><span style='font-weight: bold;'>
Endurance72</span><br /> 24 rue Louis Crétois <br />72100 LE MANS</div>";
}
?>
<p>Vous pouvez consulter  la prise en compte de votre inscription : <a href="verif_inscription_cross.php">vérification</a></p> <p></p>
<p><b>
<?php 
if ($_GET['sexe']=="F") echo 'Mme ';   else echo 'M ';
echo $_GET['prenom']." ".$_GET['nom'] ?>
</b>, nous vous souhaitons une agréable course.</p>
</div>

<div id="pied"> Site hébergé par Endurance72 - 24 rue Louis Crétois -
72100 LE MANS - Tél: 02.43.23.64.18<br />

</div>
</div>
</body></html>
