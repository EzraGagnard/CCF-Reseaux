<?php
// Ce script donne aux compétiteurs le résultat de leur vérification
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
      
      $sql = "SELECT * FROM marsouin_engagement WHERE `nom` LIKE '".$_POST['nom']."%'";
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
<div id="bandeau_flash" style="width: 1024px; height: 150px;"><img style="width: 1023px; height: 150px;  border: 0px;" alt="u" src="images/bandeau-bassin%201.jpg" /><br />
</div>
<div id="nav-horizon">
     <ul id="menuDeroulant">
     <li><a href="index.html">Accueil</a></li>
     <li><a href="course.html">Course</a>
          <ul class="sousMenu">
		<li><a href="presentation.html">Présentation</a></li>
		<li><a href="prix.html">Grille des prix</a></li>
		<li><a href="organisation_courses.html">Organisation</a></li>
                <li><a href="epreuves_horaires.html">Horaires</a></li>
                <li><a href="parcours.html">Parcours</a></li>
                <li><a href="palmares.html">Résultats</a></li>
                <li><a href="tourisme.html">Tourisme</a></li>
                <li><a href="hebergement.html">Hébergements</a></li>
	  </ul>
     </li>
     <li><a href="inscription.html">Inscription</a>
         <ul class="sousMenu">
		<li><a href="inscription.html#en_ligne">En ligne</a></li>
		<li><a href="inscription.html#papier">Papier</a></li>
		<li><a href="inscription.html#liste">Liste inscrits</a></li>
		<li><a href="inscription.html#reglement">Règlement</a></li>
	  </ul>
     </li>
     <li><a href="partenaires.html">Partenaires</a></li>
     <li><a href="liens.html">Liens</a></li>
     <li><a href="contact.php">Contact</a></li>
     <li><a href="administration/index.php">Intra</a></li>
</ul>
</div>
<div>
<div id="menu" style="width: 100px;">
<p></p>
</div>
<div id="contenu">
     <h2>Recherche d'un compétiteur</h2>
     <div class="item">
     <p> résultat de la recherche pour :
     '<?php      echo "<b>".$_POST['nom']."</b><br /><br /></p>";
     $trouve=false;

     echo '<table id="tableau">';
     echo "<tr><th>Nom</th><th>Prénom</th><th>Catégorie</th><th>Course</th><th>Documents manquants</th></tr>";
     while ($engagement = mysql_fetch_object ($resultat)){
      echo "<tr><td>".$engagement->nom."</td>";
      echo "<td>".$engagement->prenom."</td>";
      echo "<td>".$engagement->categorie."</td>";
      echo "<td>".$engagement->nomcourse."</td>";
      echo "<td>";
      $complet = ($engagement->nolicence || $engagement->certifmedicalfourni) && $engagement->cotisationpaye;
      if ($complet) echo "<span style=\"color:#2EBA0E\">- complet</span>";
      else {   if (!$engagement->nolicence && !$engagement->certifmedicalfourni) echo "- Certificat médical ";
               if (!$engagement->cotisationpaye) echo "- Paiement ";

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
