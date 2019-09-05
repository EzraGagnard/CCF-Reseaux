<?php
session_start();

if(!isset($_SESSION['last_access']) || !isset($_SESSION['ipaddr']) || !isset($_SESSION['login']))
{
  header("Location: index.php");
  die();
}

// vérification du temps maxi pour une session (1 heure)
$session_timeout=3600;
if(time()-$_SESSION['last_access']>$session_timeout)
{
  unset($_SESSION['last_access']);
  unset($_SESSION['user']);
  unset($_SESSION['ipaddr']);
  header("Location: index.php?&erreur=Session expirée");
  die();
}

// vérification de l'adresse IP du client elle ne doit pas changer
if($_SERVER['REMOTE_ADDR']!=$_SESSION['ipaddr'])
{
  unset($_SESSION['last_access']);
  unset($_SESSION['user']);
  unset($_SESSION['ipaddr']);
  header("Location: index.php");
  die();
}
$_SESSION['last_access']=time();
?>
