<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" /><title>la course</title>

<link href="css/bassin.css" type="text/css" rel="StyleSheet" />
<script type="text/javascript" src="scripts/swfobject.js"></script></head>
<body leftmargin="0" topmargin="0">
<div id="page">
<div id="bandeau_flash" style="width: 1024px; height: 150px;">
<img style="width: 1024px; height: 150px;" alt="f" src="images/bandeau_E72.jpg" /><br />
</div>

<!--Menu horizontal sous la banniere-->
<div id="nav-horizon">
<ul id="menuDeroulant">
     <li><a href="#">Club</a>
    	<ul class="sousMenu">
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique107">infos express</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique35">A la Une !!</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique2">Le Club</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique3">Les rendez-vous</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique4">Calendrier-Inscriptions</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique62">Formations-Stages-Colloques</a></li>
	</ul>
</li>
 <li><a href="#">Entrainements</a>
          <ul class="sousMenu">
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique31">Plans d'entrainement</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique108">Tests de VMA club</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique26">M�dicaux-param�dicaux</a></li>
	  </ul>
     </li>
    <li><a href="#">R�sultats</a>
	<ul class="sousMenu">
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique5">Les r�sultats</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique46">Bilan hors stade</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique142">Bilan Piste</a></li>

	  </ul>
    </li>
		
     <li><a href="#">Athl�tes</a>
         <ul class="sousMenu">
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique1">Athl�tes et leurs portraits</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique73">Trombinoscope</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique94">Anniversaire du mois</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique63">Carnet du club</a></li>
	  </ul>
     </li>
     
     <li><a href="#">Partenaires</a>
	<ul class="sousMenu">
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique23">Nos partenaires</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique97">Nos actions partenaires</a></li>
	 </ul>
     </li>

     <li><a href="#">Liens</a>
	<ul class="sousMenu">
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique38">Votre presse sportive</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique30">Sites favoris</a></li>
	 </ul>

    </li>
    <li><a href="http://www.endurance72.fr/ForumE72/">Forum</a>
    </li>
    <li><a href="http://www.endurance72.fr/store/index.php">Boutique</a>
    </li>
</ul>
</div>
<!-- fin de menu horizontal -->

<div id="menu"><br />
</div>
<div id="contenu">
<h2>Votre demande a bien �t� prise en compte</h2>
<p>Merci pour votre inscription � la 2 �me �dition de l'Endurance du Marsouin 2009.</p>
<?php if  ($_GET['cas']=="0") {
   echo "
<p>Vous �tes non licenci� FFA, votre inscription ne sera d�finitivement prise en compte
que lorsque vous aurez fait parvenir au secr�tariat<b> votre certificat m�dical de non contre-indication � la pratique de la course � pied en
comp�tition (mention obligatoire)</b>. Les licences sportives hors FFA ne remplacent pas le certificat m�dical.</p>";
}

if  ($_GET['cas']=="1") {
   echo ""; }
if  ($_GET['cas']=="2") {
   echo "
<p>Vous venez d'inscrire votre �quipe <b>".$_GET['nomequipe']."</b> au challenge entreprises/militaires, si vous �tes non-licenci�s FFA, votre inscription ne sera d�finitivement prise en compte
que lorsque vous aurez fait parvenir au secr�tariat les certificats m�dicaux de non contre-indication � la pratique de la course � pied en
comp�tition (mention obligatoire). Les licences sportives hors FFA ne remplacent pas le certificat m�dical.</p>";
}
if (!isset($_GET['gratuit'])) { $_GET['gratuit']="non";}
if ($_GET['gratuit']=="non"){
echo "<p>Pour valider votre engagement nous attendons votre r�glement".$_GET['info']." par ch�que au nom d'Endurance 72.<br />
Le payement en ligne sans frais suppl�mentaire est �galement possible sur <a href='http://www.endurance72.fr/store/index.php?cPath=30'>la boutique Endurance 72</a></p>
<p>Nous vous rappelons que l'adresse postale est la suivante :<br /></p><div style='text-align: center;'><span style='font-weight: bold;'>
Endurance72</span><br /> 2 rue d'Haouza <br />72100 LE MANS.&nbsp;</div>";
}
?>
<p>Vous pouvez consulter  la prise en compte de votre inscription : <a href="verif_inscription.php">v�rification</a></p> <p></p>
<p><b>
<?php 
if ($_GET['sexe']=="F") echo 'Mme ';   else echo 'M ';
echo $_GET['prenom']." ".$_GET['nom'] ?>
</b>, nous vous souhaitons une agr�able course Endurance du Marsouin.</p>
</div>

<div id="pied"> Site h�berg� par Endurance72 - 2, avenue d'HAOUZA -
72100 LE MANS - T�l: 02.43.23.64.18<br />

</div>
</div>
</body></html>
