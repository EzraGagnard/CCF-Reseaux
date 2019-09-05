<?php
// Ce script donne aux copétiteurs le résultat de leur vérification
// seul le nom ou début de nom est nécessaire pour la vérification
// page réponse à verif_inscription.php

require_once('definitions.inc.php');


     // connexion à la base
    @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
    @mysql_select_db(BASE) or die("Echec de selection de la base cdt");

    if ($_POST['nom']=="") {
        echo "Vous devez indiquer votre nom (ou début de nom)";
        exit;
     };
      
      $sql = "SELECT * FROM cross_route_engagement WHERE `nom` LIKE '".$_POST['nom']."%'";
      $resultat = mysql_query($sql) or die(mysql_error());

    @mysql_close();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<title>Vérification résultats</title>

<link href="css/bassin.css" type="text/css" rel="StyleSheet" />
<script type="text/javascript" src="scripts/swfobject.js"></script>
<style type="text/css">
<!--
table#tableau {
        clear: both;
        width: 99%;
        margin-left:5px;
        border-collapse: collapse;
        border-top: 5px solid #D0D0D0;
        border-bottom: 5px solid #D0D0D0;
        border-left:1px solid #D0D0D0;
        border-right:1px solid #D0D0D0;
 }
table#tableau td {
        border-top: 1px solid #D0F0F0;
        padding: 4px;

 }

tr.impaire {
         background-color: #F0F0F0;
}

table#tableau th {
        background-color: #D0D0D0;
        color: #000000;
        padding: 4px;

 }
-->
</style>
</head>

<body topmargin="0" leftmargin="0">
<div id="page">
<div id="bandeau_flash" style="width: 1024px; height: 150px;">
<a href="http://www.endurance72.fr" title="Page d'accueil">
<img style="width: 1023px; height: 150px; border: 0px;" alt="u" src="images/bandeau_E72.jpg" /></a>
<br />
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
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique31">Infos entrainement</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique108">Tests de VMA club</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique26">Médicaux-paramédicaux</a></li>
	  </ul>
     </li>
    <li><a href="#">Résultats</a>
	<ul class="sousMenu">
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique5">Les résultats</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique46">Bilan hors stade</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique142">Bilan Piste</a></li>

	  </ul>
    </li>
		
     <li><a href="#">Athlètes</a>
         <ul class="sousMenu">
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique1">Athlètes et leurs portraits</a></li>
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

<div>
<div id="menu" style="width: 100px;">
<p></p>
</div>
<div id="contenu">
     <h2>Recherche d'un compétiteur</h2>
     <div class="item">
     <p> résultat de la recherche pour :
     <?php
     if ($_POST['nom']=="%") { $_POST['nom']="liste complète"; }
     echo "<b>".$_POST['nom']."</b><br /><br /></p>";
     $trouve=false;

     echo '<table id="tableau">';
     echo "<tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Catégorie</th>
            <th>Compétition</th>
            <th>Course</th>
            <th>Documents manquants</th>
            </tr>";
     while ($engagement = mysql_fetch_object ($resultat)){
      echo "<tr><td>".$engagement->nom."</td>";
      echo "<td>".$engagement->prenom."</td>";
      echo "<td>".$engagement->categorie."</td>";
      echo "<td>".$engagement->competition."</td>";
      echo "<td>".$engagement->nomcourse."</td>";
      echo "<td>";
      $complet = ($engagement->nolicence || ($engagement->certifmedicalfourni == 'oui')) && ($engagement->cotisationpaye == 'oui');
      if ($complet) echo "<span style=\"color:#2EBA0E\">- complet</span>";
      else {   if (!$engagement->nolicence && ($engagement->certifmedicalfourni == 'non')) echo "- Certificat médical ";
               if ($engagement->cotisationpaye=='non') echo "- Paiement ";

            }
      $trouve=true;
     }
     echo "</td></tr></table>";
     if (!$trouve) echo "<p>pas d'engagement pour ce nom !</p>";


     ?></div>

</div>
</div>
<div id="pied"> Site hébergé par Endurance72 - 2, avenue
d'HAOUZA - 72100 LE MANS - Tél: 02.43.23.64.18<br />
</div>
</div>
</body></html>
