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

<body topmargin="0" leftmargin="0" style="background-color: #FFFFFF; background-image: url();" >
<div id="page" style="width: 1099px; border-style: dotted; border-width: 1px">
<div id="bandeau_flash" style="width: 1098px; height: 336px;">
<a href="http://www.esr72.fr/" title="Retour page d'accueil">
<img style="width: 1098px; height: 336px; border: 0px;" alt="Organisations" src="images/bandeau_trail.jpg" /></a><br />
</div>
       
<!--Menu horizontal sous la banniere-->

<div class="contenuArticle" style="text-align: justify;">
<p style="  margin-left: 0;">
<span style="font-family: 'arial black', 'avant garde'; font-size: 10pt;">
<a href="http://esr72.fr" style="margin-right: 28px; margin-left:28px;">ACCUEIL</a>
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

<div>
     <div id="menu" style="width: 100px;"></div>
     <div id="contenu">
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
             $sql = "SELECT `nolicence`,`cross_route_engagement`.`nom`,`prenom`,`categorie`,`nomcourse`,`competition`.`date`,`certifmedicalfourni`,`cotisationpaye`"
                  . "FROM `competition`,`cross_route_engagement`\n"
                  . "WHERE cross_route_engagement.competition = \"Trail nocturne Montbraye\"\n"
                  . "AND cross_route_engagement.competition = competition.nom";
             $sql .= " AND cross_route_engagement.nom LIKE '".$_POST['nom']."%'";



             $resultat = mysql_query($sql) or die(mysql_error());

             @mysql_close();
             $trouve=false;

             echo '<table id="tableau" style="margin:5px;">';
             echo "<tr><th>N° licence</th><th>Nom</th><th>Prénom</th><th>Catégorie</th><th>Course</th><th>Date</th><th>Etat</th></tr>";
             while ($engagement = mysql_fetch_object ($resultat)){

                   echo "<tr><td>".$engagement->nolicence."</td>";
                   echo "<td>".$engagement->nom."</td>";
                   echo "<td>".$engagement->prenom."</td>";
                   echo "<td>".$engagement->categorie."</td>";
                   echo "<td>".$engagement->nomcourse."</td>";
                   echo "<td>".date("j M Y",strtotime($engagement->date))."</td>";
                   echo "<td>";
                   $complet = ($engagement->nolicence || $engagement->certifmedicalfourni=="oui") && $engagement->cotisationpaye=="oui";
                   if ($complet) echo "<span style=\"color:#2EBA0E\">- OK</span>";
                   else {   echo "<span style=\"color:#F00\">";
                            if (!$engagement->nolicence && $engagement->certifmedicalfourni=="non") echo "- Certificat médical ";
                            if ($engagement->cotisationpaye=="non") echo "- Paiement ";
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
<?php
     @readfile('administration/pied_de_page.html') or die('Erreur fichier');
?>

</div>
</body></html>
