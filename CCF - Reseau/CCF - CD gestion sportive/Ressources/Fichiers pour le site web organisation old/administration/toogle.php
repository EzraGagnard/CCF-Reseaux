<?php
//--------------------------------------------------------------------
// Ce script permet de modifier la validation d'une compétition
// Compétition identifiée par son id transmis par la méthose GET
//
// page protégée
//--------------------------------------------------------------------

include "authentification/authcheck.php" ;
// Vérification des droits pour cette page uniquement organisateur
if ($_SESSION['droits']<>'2') { header("Location: index.php");};

require_once('../definitions.inc.php');
require_once('utile_sql.php');


// connexion à la base marsouin
  @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
  @mysql_select_db(BASE) or die("Echec de selection de la base cdt");

if (isset($_GET['id']) && isset($_GET['val'])){
  $sql = sprintf("UPDATE competition SET validation=%s WHERE id=%s",

           GetSQLValueString($_GET['val'] , "text"),
           $_GET['id']
  );
           $Result1 = mysql_query($sql) or die(mysql_error());



}
    @mysql_close();
    $GoTo = "orga_menu.php";
    header(sprintf("Location: %s", $GoTo));

?>
