<?php
//---------------------------------------------------------------------------------------
//  ce script exporte les informations relatives à un acteur
//  dans un fichier au format vCard
//---------------------------------------------------------------------------------------
// vérification des variables de session pour le temps d'inactivité et de l'adresse IP
include "authentification/authcheck.php" ;
// Vérification des droits pour cette page tous sauf les exclus
if ($_SESSION['droits']<>'2') { header("Location: index.php");};

   require_once('../definitions.inc.php');
   require_once('utile_sql.php');
// Classe vcard
   include "inc/class_vcalendar.php" ;

// Connexion à la base pour rechercher les infos de l'utilisateur
     @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
     @mysql_select_db(BASE) or die("Echec de selection de la base cdt");

// Lecture configuration  saison
    $sql = "SELECT * FROM `cross_route_configuration`";
    $resultat = mysql_query($sql) or die(mysql_error());

while ($conf = mysql_fetch_object ($resultat)){
       define($conf->conf_key, $conf->conf_value);
      }
    // fin de la lecture configuration saison

//  requète SQL:
//  si l'id est absent alors toutes les compétitions
     if(isset($_GET['id'])) {
         $sql = sprintf('SELECT * FROM `competition` WHERE `id` = %s',
         GetSQLValueString($_GET['id'], "int")
         );
         $resultat = mysql_query($sql);
         $competition = mysql_fetch_object ($resultat);
         $fichier = $competition->nom;
         mysql_data_seek($resultat, 0);

      } else {
         $sql = 'SELECT * FROM `competition`';
         $resultat = mysql_query($sql);
         $fichier = "calendrier_".SAISON;
     }


     

     // Création d'un nouvel objet vCalendar  en version 2.0
     $e = new vCalendar("UTF8",$fichier);
While ($competition = mysql_fetch_object ($resultat)){
  // Ecriture d'un événement vCalendar
     list($date,$time) = explode(" ", $competition->date);
     // les infos sup dans la description
      $info = "Organisateur : ".$competition->organisateur." \\n";
      $info .= "Email : ".$competition->email." \\n";
      $info .= "Licences autorisées : ";
      if($competition->licence=="") $info .= "toutes"; else $info .= $competition->licence;

     $e->addEvenement($competition->nom , $date." 090000" , $date." 170000", $competition->lieu, $info, "Compétition Cross Route" ,1);
 }
     $output = $e->getvCalendar();
     $filename = $e->getFileName();


     Header("Content-Disposition: attachment; filename=$filename");
     Header("Content-Length: ".strlen($output));
     Header("Connection: close");
     Header($e->getTypeName()."name=$filename");

     echo $output;
     @mysql_close();
?>
