<?php
//-----------------------------------------------------
// Ce script liste les epreuves pour une compétition
// enregistrées dans la table cross_route_epreuve
// version 2.0
// le paramètre compétition est donné en GET
// auteur SIMIER Philippe pour Endurance 72
//-----------------------------------------------------

include "authentification/authcheck.php" ;
// Vérification des droits pour cette page uniquement organisateur
if ($_SESSION['droits']<>'2') { header("Location: index.php");};

require_once('../definitions.inc.php');
require_once('utile_sql.php');

     // connexion à la base marsouin
    @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
    @mysql_select_db(BASE) or die("Echec de selection de la base cdt");

    // Si la variable competition n'exite pas
     if(!isset($_GET['competition'])) { $_GET['competition']=""; }

    //Lecture configuration  compétition
    $sql = "SELECT * FROM `cross_route_configuration`";
    $resultat = mysql_query($sql) or die(mysql_error());

    while ($conf = mysql_fetch_object ($resultat)){
       define($conf->conf_key, $conf->conf_value);
      }
    // fin de la lecture configuration  compétition

// début du fichier bandeau menu horizontal
  if (!is_readable('en_tete.html'))  die ("fichier non accessible");
  @readfile('en_tete.html') or die('Erreur fichier');

?>
<script language="javascript">

        // fonction pour interdire les caractères numériques
        function pasNum(e){
          if (window.event) caractere = window.event.keyCode;
               else  caractere = e.which;
                 return (caractere < 48 || caractere > 57);
          }

        // fonction pour autoriser uniquement les numériques
        function pasCar(e){
          if (window.event) caractere = window.event.keyCode;
               else  caractere = e.which;
                 return (caractere == 8 || (caractere > 47 && caractere < 58));
          }

        // fonction pour mettre en majuscule
        function majuscule(champ){
        champ.value = champ.value.toUpperCase();
        }

        function GoToURL() { //v3.0
                 var i, args=GoToURL.arguments; document.MM_returnValue = false;
                 for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
        }

        function GoToURL_conf() { //v3.0
                 var i, args=GoToURL_conf.arguments;
                 document.MM_returnValue = false;
                 Confirmation = confirm("Confirmez-vous la suppression de "+args[2]);
                 if (Confirmation){
                 for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
                 }
        }


  </script>



     <div id="contenu" style="width: 1024px; min-height:500px;">
          <h2><a href="orga_menu.php"><img src="../images/fleche_retour.png" title="Retour" border="0" width="44" height="41"></a>
          Liste des épreuves<br />
          <?php echo stripslashes($_GET['competition']) ?></h2>
          <div>
          <table id="tableau">
                   <tr><th>Désignation</th>
                   <th>Code épreuve</th>
                   <th>Horaire</th>
                   <th>Cat autorisée(s)</th>
                   <th>Sexe</th>
                   <th>Prix</th>
                   <th>Sup.</th>
                   <th>Mod.</th></tr>
            <?php
                   //$sql = "SELECT * FROM `cross_route_epreuve` WHERE `competition`='".$_GET['competition']."'";
                     $sql = sprintf("SELECT * FROM `cross_route_epreuve` WHERE `competition`=%s",
                       GetSQLValueString($_GET['competition'], "text")
                     );
                   $reponse = mysql_query($sql);
                   while ($epreuve = mysql_fetch_object ($reponse)){
                         echo '<tr><td>'.$epreuve->designation.'</td>';
                         echo '<td>'.$epreuve->code.'</td>';
                         echo '<td>'.$epreuve->horaire.'</td>';
                         echo '<td>'.$epreuve->categorie.'</td>';
                         echo '<td>'.$epreuve->sexe.'</td>';
                         echo '<td>'.$epreuve->prix.'</td>';
                         echo '<td><img style="border :0px; cursor: pointer" src="../images/ed_delete.gif"  title="Supprimer" onClick="GoToURL_conf(\'window\',\'supprimer_epreuve.php?id_epreuve='.urlencode($epreuve->id_epreuve).'\',\''.urlencode($epreuve->designation).'\');return document.MM_returnValue"></td>';
                         echo '<td><img src="../images/button_edit.png" style="cursor: pointer;" title="Modifier" width="12" height="13" onClick="GoToURL(\'window\',\'modif_epreuve.php?id_epreuve='.urlencode($epreuve->id_epreuve).'&competition='.urlencode(stripslashes($_GET['competition'])).'\');return document.MM_returnValue"></td>'."\n";
                         echo '</tr>';
                   }
            @mysql_close();
            ?>
            </table>

          </div>
          <?php
          echo '<p><b><a href="ajouter_epreuve.php?competition='.urlencode(stripslashes($_GET['competition'])).'">Ajouter une épreuve</a></b></p>';
          ?>
     </div>
</div>
<div id="pied"> Site hébergé par Endurance72 - 2, avenue
d'HAOUZA - 72100 LE MANS - Tél: 02.43.23.64.18<br />
</div>
</div>
</body>
</html>
