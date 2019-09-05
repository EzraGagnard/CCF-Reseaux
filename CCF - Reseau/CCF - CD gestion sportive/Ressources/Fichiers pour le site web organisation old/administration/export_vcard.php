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
   include "inc/class_vcard.php" ;

// Connexion à la base pour rechercher les infos de l'utilisateur
     @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
     @mysql_select_db(BASE) or die("Echec de selection de la base cdt");
     
//  requète SQL:
//  si l'id est absent alors id=1
     if(!isset($_GET['id'])) { $_GET['id']="1"; }


     $sql = sprintf('SELECT * FROM `utilisateur` WHERE `ID_user` = %s',
     GetSQLValueString($_GET['id'], "int")
     );

     $resultat = mysql_query($sql);
     $utilisateur = mysql_fetch_object ($resultat);
     @mysql_close();

// Création d'un nouvel objet vCard
   $v = new vCard();
// Ecriture des propriétés vCard
// pour extraire le prénom du nom le champ identite est séparé en 3 parties.
     $n= explode(' ', $utilisateur->identite, 3);
     switch (count($n)) {
     case 1 :$v->setName($n[0], "", "", "");
             break;
     case 2 :$v->setName($n[1], $n[0], "", "");
             break;
     case 3 :$v->setName($n[2], $n[1], "", $n[0]);
     }
// s'ils sont renseignés, L'adresse, l'e-mail, le téléphone, le portable, la fonction

     if ($utilisateur->email) $v->setEmail($utilisateur->email);
     if ($utilisateur->telephone) $v->setPhoneNumber($utilisateur->telephone, "CELL;VOICE");
     if ($utilisateur->fonction) $v->setKey("ROLE",$utilisateur->fonction);

     $output = $v->getVCard();
     $filename = $v->getFileName();


     Header("Content-Disposition: attachment; filename=$filename");
     Header("Content-Length: ".strlen($output));
     Header("Connection: close");
     Header("Content-Type: text/x-vCard; name=$filename");

     echo $output;
?>
