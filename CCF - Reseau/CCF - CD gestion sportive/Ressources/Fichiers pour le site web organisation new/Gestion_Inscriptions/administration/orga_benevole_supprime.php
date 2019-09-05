<?php
//-----------------------------------------------------------------------
// ce script supprime un acteur
// page protégée
//-----------------------------------------------------------------------
include "authentification/authcheck.php" ;
// Vérification des droits pour cette page uniquement acteur
if ($_SESSION['droits']<'3') { header("Location: ../index.php");};
require_once('../definitions.inc.php');
require_once('utile_sql.php');

   @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
   @mysql_select_db(BASE) or die("Echec de selection de la base");

if ((isset($_GET['id_prof'])) && ($_GET['id_prof'] != "")) {
  if ($_GET['id_prof']<> 1) {

  $delete1SQL = sprintf("DELETE FROM utilisateur WHERE ID_user=%s",
      GetSQLValueString($_GET['id_prof'], "int")
      );
  $Result1 = mysql_query($delete1SQL) or die(mysql_error());
 }
  

  // retour vers tableau_actualites
  $GoTo = "orga_benevoles.php";
  header(sprintf("Location: %s", $GoTo));
}
@mysql_close();

?>
