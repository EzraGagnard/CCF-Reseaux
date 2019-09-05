<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <head>
     <meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
     <title>Vérifier son inscription</title>
     <link href="css/bassin.css" type="text/css" rel="StyleSheet" />
     <style type="text/css">
     <!--
div#rechercher {
        float:left;
        width: 155px;
}
form {
  background-color:#D0D0D0;
  border: 1px solid #999998;
  height: 28px;
}

form input {
     background-color:#D0D0D0;
     color: #000;
     font-size: 18px;
     border: 0px solid #ECE9D8;
     padding: 0px;
     padding-left: 5px;
     height: 25px;
     width: 117px;
     cursor: text;
     

     }

 .bouton {
     background-image: url(images/loupe.png);
     background-color:#D0D0D0;
     color: #D0D0D0;
     border: 0px solid #ECE9D8;
     padding: 0px;
     height: 23px;
     width: 24px;
     cursor: pointer;
     }
     -->
     </style>
  </head>

<body topmargin="0" leftmargin="0">
	<div id="page">
		<div id="bandeau_flash" style="width: 1024px; height: 150px;">
			<a href="../spip/"><img style="width: 1023px; height: 150px;  border: 0px; " title="retour accueil" src="images/bandeau_E72.jpg" /></a><br />
		</div>
       
<!--Menu horizontal sous la banniere-->
	<div id="nav-horizon">
		<ul id="menuDeroulant">
			<li><a href="#"></a></li>
			<li><a href="#"></a></li>
			<li><a href="#"></a></li>
			<li><a href="#"></a></li>    
		</ul>
	</div>
<!-- fin de menu horizontal -->

<div>
     <div id="menu" style="width: 80px;"></div>
     <div id="contenu" style="width: 850px;">
       <h2>Un simple clic pour vérifier l’état de votre inscription !</h2>
          <div class="item" style="margin-left:0;">
            <div style="float: left; font-size:11pt; padding: 20px; "> Nom (ou début du nom)<br /> % pour liste complète
            </div>
            <div id="rechercher" style="padding: 20px;">
                    <form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="POST" name="verification">
                     <input  name="nom"  value="Rechercher" style="" size="10" onClick="this.value='';" />
                     <input  class= "bouton" type="submit"  value="_" name="envoyer" />
                    </form>
            </div>
          </div>
          <?php
             if (isset($_POST['nom'])&&($_POST['nom']!="")) {
              require_once('definitions.inc.php');
             // connexion à la base
             @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
             @mysql_select_db(BASE) or die("Echec de selection de la base cdt");

             $sql =  "SELECT * FROM `cross_route_engagement`,`competition`";
             $sql .= " WHERE cross_route_engagement.competition = competition.nom";
             $sql .= " AND cross_route_engagement.nom LIKE '".$_POST['nom']."%'";
             $sql .= " AND competition.date > '".date("Y-m-j")."'";
             $sql .= " ORDER BY `cross_route_engagement`.`competition`";


             $resultat = mysql_query($sql) or die(mysql_error());

             @mysql_close();
             $trouve=false;

             echo '<table id="tableau" style="margin:5px;">';
             echo "<tr><th>Nom</th><th>Prénom</th><th>Catégorie</th><th>Compétition</th><th>Course</th><th>Date</th><th>Etat dossier</th></tr>";
             while ($engagement = mysql_fetch_array ($resultat)){
                   echo "<tr><td>".$engagement[5]."</td>";
                   echo "<td>".$engagement[6]."</td>";
                   echo "<td>".$engagement[10]."</td>";
                   echo "<td>".$engagement[1]."</td>";
                   echo "<td>".$engagement[15]."</td>";
                   echo "<td>".date("j M Y",strtotime($engagement[28]))."</td>";
                   echo "<td>";
                   // dossier complet si n° de licence ou certificat et paiement
                   $complet = ($engagement[4] || $engagement[19]=='oui') && $engagement[20]=='oui';
                   if ($complet) echo "<span style=\"color:#2EBA0E\">- OK</span>";
                   else {   echo "<span style=\"color:#f00\">";
                            if (!$engagement[4] && $engagement[19]=='non') echo "- Certificat médical ";
                            if ($engagement[20]=='non') echo "- Paiement ";
                            echo "</span>";
                        }
                   $trouve=true;
             }
             echo "</td></tr></table>";
             if (!$trouve) echo "<p>pas d'engagement pour ce nom !</p>";
                                    	
             }
          ?>
     </div>
</div>
<div id="pied"> Site hébergé par Endurance72 - 24 rue Louis Crétois - 72100 LE MANS - Tél: 02.43.23.64.18<br />
</div>
</div>
</body></html>
