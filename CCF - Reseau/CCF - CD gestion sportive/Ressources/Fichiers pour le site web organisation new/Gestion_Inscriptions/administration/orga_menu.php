<?php
//---------------------------------------------------------------------
// Menu organisateur
// Page en accès restreint   uniquement les organisateurs de niveau 2 et plus
// droit supérieur ou égal à 2
// Auteur Simier Philippe
//---------------------------------------------------------------------
// vérification des variables de session
	include "authentification/authcheck.php" ;
	// Vérification des droits pour cette page uniquement organisateur
	if ($_SESSION['droits']<'2') { 
		var_dump($_SESSION);
		exit;
		
		header("Location: index.php");
	};

	// connexion à la base
    require_once('../definitions.inc.php');
    $bdd = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);
     
	// Lecture configuration  saison
    $sql = "SELECT * FROM `cross_route_configuration`";
	$stmt = $bdd->query($sql);
	
    while ($conf = $stmt->fetchObject()){
		define($conf->conf_key, $conf->conf_value);
    }
	// fin de la lecture configuration
   
   // cette fonction affiche oui en vert ou non en rouge
	function aff_oui_non($val){
		if ($val=='1')  
			return "0\"<span style=\"color:#00FF00\">oui</span>"; 
		else 
			return "1\"<span style=\"color:#FF0000\">non</span>";
	}

	// début du fichier bandeau menu horizontal
	if (!is_readable('en_tete.html'))  die ("fichier non accessible");
	@readfile('en_tete.html') or die('Erreur fichier');
?>
    <div id="contenu" style="min-height:500px;">

        <h2>Gestion des inscriptions : <?php echo SAISON ; echo " pour : " . $_SESSION['identite']?></h2>
        <center>
        <table style="width:100%; margin: 0px;">
			<tr>
				<td bordercolor="#FFFFFF" bgcolor="#FFFFFF" colspan="4">

					<table id="tableau" style="width:100%; margin: 0px;">
                        <tr style="background-color: #D0D0D0;">
							<th><b><a href="export_vcalendar.php" title="exporter le calendrier dans mon agenda">Date</a></b></th>
							<th><b>Compétition</b></th>
							<th><b>Val</b></th>
                            <th><b>Nb</b></th>
                            <th colspan="2" style="text-align: center"><b>Export</b></th>
                            <th colspan="2" style="text-align: center"><b>Configurer</b></th>
                        </tr>
                        <?php
                            $sql = "SELECT * FROM `competition` WHERE `saison`=" . SAISON . " AND `id_utilisateur` = " . $_SESSION['ID_user'] . " ORDER BY `competition`.`date` ASC";
                            $stmt =  $bdd->query($sql);
                            while ($competition = $stmt->fetchObject()){
                                echo '<tr><td><a href="export_vcalendar.php?id=' . $competition->id . '" title="exporter cette date dans mon agenda">'.date("j M Y",strtotime($competition->date)).'</a></td>';
                                echo '<td><b><a href="orga_tab_enga.php?&competition=' . $competition->id .'" title="Liste des engagés">'.$competition->nom."</a></b></td>";
                                echo '<td><a href="toogle.php?id='.$competition->id.'&val='.aff_oui_non($competition->validation).'</a></td>';
                                   $sql = "SELECT COUNT(*) valeur FROM cross_route_engagement WHERE `competition`='".addslashes($competition->nom)."'";
                                   $stmt2 =  $bdd->query($sql);
                                   $nb = $stmt2->fetchObject();
                                echo "<td><b>".$nb->valeur.'</b></td>';
                                echo '<td><a href="export_logica.php?competition='. $competition->id .'"> Logica</a></td>';
                                echo '<td><a href="export_excel.php?competition='. $competition->id .'"> Excel</a></td>';
                                echo '<td><a href="epreuve.php?competition='. $competition->id .'" title="Configurer les épreuves" > Epreuves</a></td>';
                                echo '<td><a href="modif_competition.php?id_competition='.$competition->id.'" title="Configurer la compétition" > Comp.</a></td>';
                                echo '</tr>';
                            }
                            
                        ?>
                    </table>
			</tr>
			<tr>
			    <td bordercolor="#C0C0C0" bgcolor="#D0D0D0"><p style="text-align: center">
					<?php
					if ($_SESSION['droits']>'2'){
						echo'<a href="/phpmyadmin"><img border="0" src="../images/phpmyadmin.png" width="48" height="48"></a></p>';
						echo'<p style="text-align: center"><b><a href="/phpmyadmin">Gestion base<br />de données</a></b>';
					}
					?>
                </td>
				
                <td bordercolor="#C0C0C0" bgcolor="#D0D0D0"><p style="text-align: center"><a href="importer_engages.php" title="Importer"><img border="0" src="../images/xls.png"></a></p>
                    <p style="text-align: center">
                    <b><a href="importer_engages.php" title="importer">Importer<br /> les engagé(e)s</a></b></p>
                </td>

                <td bordercolor="#C0C0C0" bgcolor="#D0D0D0"><p style="text-align: center">
                    <a href="competition.php">
					<img border="0" src="../images/running.png"></a></p>
                    <p style="text-align: center">
					<b><a href="competition.php">Gérer les <br />compétitions</a></b>
                </td>
				
				<td bordercolor="#C0C0C0" bgcolor="#D0D0D0"><p style="text-align: center">
                    <a href="up_load.php">
					<img border="0" src="../images/upload.png"></a></p>
                    <p style="text-align: center">
					<b><a href="up_load.php">uploader<br />les fichiers</a></b>
                </td>
            </tr>
            <tr>
			    <td bordercolor="#C0C0C0" bgcolor="#D0D0D0"><p style="text-align: center">
					
                </td>
				
                <td bordercolor="#C0C0C0" bgcolor="#D0D0D0"><p style="text-align: center">
					<?php
					if ($_SESSION['droits']>'2'){
						echo'<a href="orga_benevoles.php"><img border="0" src="../images/user.png" width="48" height="48"></a></p>';
						echo'<p style="text-align: center"><b><a href="orga_benevoles.php">Acteurs</a></b>';
					}
					?>
                </td>
				
                <td bordercolor="#C0C0C0" bgcolor="#D0D0D0"><p style="text-align: center">
                    <a href="configuration.php">
					<img border="0" src="../images/configuration.png"></a></p>
                    <p style="text-align: center">
					<b><a href="configuration.php">Configurer<br /> la Saison</a></b>
                </td>
				
				<td bordercolor="#C0C0C0" bgcolor="#D0D0D0"><p style="text-align: center">
				</td>
				
            </tr>

		</table>
        </center>
    </div>
    <?php
     @readfile('pied_de_page.html') or die('Erreur fichier');
	?>

