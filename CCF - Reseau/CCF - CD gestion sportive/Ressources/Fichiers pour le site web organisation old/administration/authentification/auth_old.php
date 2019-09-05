<?php


if(!isset($_POST['md5']))
{
  header("Location: ../index.php");
  exit();
}
if(!isset($_POST['login']))
{
  header("Location: ../index.php");
  exit();
}

if($_POST['login']==NULL)
{
  header("Location: ../index.php?&erreur=Requiert un identifiant et un mot de passe.");
  exit();
}


require_once('../../definitions.inc.php');
// connexion � la base
@mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
@mysql_select_db(BASE) or die("Echec de selection de la base cdt");

        // utilisation de mysql_real_escape_string 
        // pour prot�ger la donn�e avant d'envoyer la requ�te � MySQL

        $sql = sprintf("SELECT * FROM utilisateur WHERE utilisateur.login='%s'", mysql_real_escape_string($_POST['login']));
        $Reponse = mysql_query($sql) or die(mysql_error());

        $ligne_rep = mysql_fetch_assoc($Reponse);
        mysql_free_result($Reponse);


// v�rification des identifiants login et md5 par rapport � ceux enregistr�s dans la base
if (!($_POST['login']==$ligne_rep['login'] && $_POST['md5']==$ligne_rep['passe'])){
  header("Location: ../index.php?&erreur=Incorrectes! V�rifiez vos identifiant et mot de passe.");
  @mysql_close();
  exit();
}

// A partir de cette ligne l'utilisateur est authentifi�
// donc nouvelle session
session_start();

// �criture des variables de session pour cet utilisateur

        $_SESSION['last_access']=time();
        $_SESSION['ipaddr']=$_SERVER['REMOTE_ADDR'];
        $_SESSION['ID_user'] = $ligne_rep['ID_user'];
	$_SESSION['login'] = $ligne_rep['login'];
        $_SESSION['identite'] = $ligne_rep['identite'];
	$_SESSION['email'] = $ligne_rep['email'];
	$_SESSION['droits'] = $ligne_rep['droits'];


// enregistrement de la date et heure de son passage dans le champ date_connexion de la table utilisateur
        $ID_user  = $ligne_rep['ID_user'];
        $sql = "UPDATE `utilisateur` SET `date_connexion` = CURRENT_TIMESTAMP  WHERE `utilisateur`.`ID_user` =$ID_user LIMIT 1" ;
        $Res = mysql_query($sql) or die(mysql_error());

// Incr�mentation du compteur de session
       $sql = "UPDATE utilisateur SET `nb_session` = `nb_session`+1 WHERE `utilisateur`.`ID_user` =$ID_user LIMIT 1" ;
       $Res =  mysql_query($sql) or die(mysql_error());
       @mysql_close();

// s�lection de la page de menu en fonction des droits accord�s

switch ($ligne_rep['droits']) {
    case 0:  // Utilisateur r�voqu� sans droit
         header("Location: ../index.php?&erreur=r�voqu�! ");
         break;
    case 1:  // Administrateur tous les droits (webmestre)
         header("Location: ../ad_menu.php");
         break;
    case 2:  // Organisateur)
         header("Location: ../orga_menu.php");
         break;
    
    default:
         header("Location: ../index.php");
    }

?>
