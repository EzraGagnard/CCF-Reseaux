<?php
//---------------------------------------------------------------------
// Menu organisateur
// Page en accès restreint   uniquement les organisateurs
// 30 oct 2009 ajout de urlencode
// Auteur Simier Philippe
//---------------------------------------------------------------------
// vérification des variables de session
include "authentification/authcheck.php" ;
// Vérification des droits pour cette page uniquement organisateur
if ($_SESSION['droits']<>'2') { header("Location: index.php");};

// connexion à la base
     require_once('../definitions.inc.php');
     @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
     @mysql_select_db(BASE) or die("Echec de selection de la base cdt");
     
// Lecture configuration  saison
    $sql = "SELECT * FROM `cross_route_configuration`";
    $resultat = mysql_query($sql) or die(mysql_error());

    while ($conf = mysql_fetch_object ($resultat)){
       define($conf->conf_key, $conf->conf_value);
      }
// fin de la lecture configuration saison
   
   // cette fonction affiche oui en vert ou non en rouge
   function aff_oui_non($val){
    if ($val=='1')  return "0\"<span style=\"color:#00FF00\">oui</span>"; else return "1\"<span style=\"color:#FF0000\">non</span>";
   }

// début du fichier bandeau menu horizontal
  if (!is_readable('en_tete.html'))  die ("fichier non accessible");
  @readfile('en_tete.html') or die('Erreur fichier');
?>
     <div id="contenu" style="width: 1024px; min-height:500px;">

                <h2>Gestion des inscriptions en ligne saison : <?php echo SAISON ; ?></h2>
                <center>
                <table style="text-align: center" border="0" width="700px" cellpadding="2" bordercolor="#808080" cellspacing="6">
		  <tr>
		      <td bordercolor="#FFFFFF" bgcolor="#FFFFFF" colspan="3">

			    <table id="tableau" style="width:100%; margin: 0px;">
                            <tr style="background-color: #D0D0D0;"><th><b><a href="export_vcalendar.php" title="exporter le calendrier dans mon agenda">Date</a></b></th>
			    <th><b>Compétition</b></th>
			    <th><b>Val</b></th>
                            <th><b>Nb</b></th>
                            <th colspan="2" style="text-align: center"><b>Export</b></th>
                            <th colspan="2" style="text-align: center"><b>Configurer</b></th>

                            </tr>
                            <?php
                                $sql = "SELECT * FROM `competition` WHERE `saison`='".SAISON."' ORDER BY `competition`.`date` ASC";
                                $reponse = mysql_query($sql);

                                  while ($competition = mysql_fetch_object ($reponse)){
                                  echo '<tr><td><a href="export_vcalendar.php?id='.$competition->id.'" title="exporter cette date dans mon agenda">'.date("j M Y",strtotime($competition->date)).'</a></td>';
                                  echo '<td><b><a href="orga_tab_enga.php?&competition='.urlencode($competition->nom).'" title="Liste des engagés">'.$competition->nom."</a></b></td>";
                                  echo '<td><a href="toogle.php?id='.$competition->id.'&val='.aff_oui_non($competition->validation).'</a></td>';
                                   $sql="SELECT COUNT(*) valeur FROM cross_route_engagement WHERE `competition`='".addslashes($competition->nom)."'";
                                   $res=mysql_query($sql);
                                    $nb=mysql_fetch_object($res);
                                  echo "<td><b>".$nb->valeur.'</b></td>';
                                  echo '<td><a href="export_logica.php?competition='.urlencode($competition->nom).'"> Logica</a></td>';
                                  echo '<td><a href="export_excel.php?competition='.urlencode($competition->nom).'"> Excel</a></td>';
                                  echo '<td><a href="epreuve.php?competition='.urlencode($competition->nom).'" title="Configurer les épreuves" > Epreuves</a></td>';
                                  echo '<td><a href="modif_competition.php?id_competition='.$competition->id.'" title="Configurer la compétition" > Comp.</a></td>';
                                  echo '</tr>';
                                  }
                            @mysql_close();
                            ?>
                            </table>

			</tr>
			<tr>
			    <td bordercolor="#C0C0C0" bgcolor="#D0D0D0"><p style="text-align: center">
			    <a href="http://phpmyadmin.endurance-du-marsouin.fr"><img border="0" src="../images/phpmyadmin.png" width="48" height="48"></a></p>
			    <p style="text-align: center"><b><a href="http://phpmyadmin.endurance-du-marsouin.fr">Gestion base<br />de données</a></b>
                            </td>

                            <td bordercolor="#C0C0C0" bgcolor="#D0D0D0"><p style="text-align: center"><a href="importer_engages.php" title="Importer"><img border="0" src="../images/xls.png"></a></p>
                            <p style="text-align: center">
                            <b><a href="importer_engages.php" title="importer">Importer<br /> les engagé(e)s</a></b></p>
                            </td>

                            <td bordercolor="#C0C0C0" bgcolor="#D0D0D0"><p style="text-align: center">
                            <a href="competition.php">
			    <img border="0" src="../images/configuration.png"></a></p>
                            <p style="text-align: center">
			    <b><a href="competition.php">Ajouter une compétition</a></b>
                            </td>
                        </tr>
                        <tr>
			    <td bordercolor="#C0C0C0" bgcolor="#D0D0D0"><p style="text-align: center">
			    <a href="http://stats.endurance-du-marsouin.fr/awstats/awstats.pl?config=www.endurance-du-marsouin.fr"><img border="0" src="../images/statistics.png" width="40" height="40"></a></p>
			    <p style="text-align: center"><b><a href="http://stats.endurance-du-marsouin.fr/awstats/awstats.pl?config=www.endurance-du-marsouin.fr">Statistiques<br /></a></b>
                            </td>
                            <td bordercolor="#C0C0C0" bgcolor="#D0D0D0"><p style="text-align: center">
			     <a href="orga_benevoles.php"><img border="0" src="../images/user.png" width="48" height="48"></a></p>
			     <p style="text-align: center"><b><a href="orga_benevoles.php">Acteurs</a></b>
                            </td>
                            <td bordercolor="#C0C0C0" bgcolor="#D0D0D0"><p style="text-align: center">
                            <a href="configuration.php">
			    <img border="0" src="../images/configuration.png"></a></p>
                            <p style="text-align: center">
			    <b><a href="configuration.php">Configurer la Saison</a></b>
                            </td>
                        </tr>

		</table>
                </center>
     </div>
     <?php
     @readfile('pied_de_page.html') or die('Erreur fichier');
	 ?>

</div>
</body>
</html>
