<?php
// Ce script enregistre l'engagement d'un non  licencié
//
// page autoréférente publique
//----------------------------------------------------------------------------------------------
require_once('definitions.inc.php');
// connexion à la base marsouin
    @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
    @mysql_select_db(BASE) or die("Echec de selection de la base cdt");

// lecture de la configuration et définition des constantes ENABLE SAISON DATE DESIGNATION etc
      $sql = 'SELECT * FROM `marsouin_configuration`';
      $reponse = mysql_query($sql) or die(mysql_error());
      while ($conf = mysql_fetch_object ($reponse)){
       define($conf->conf_key, $conf->conf_value);
      }
// fin de la lecture de la configuration

// ----si les inscriptions pour les NL sont fermées affichage de la page close.html--------------
      if (EN_NLFFA==FALSE) { header("Location: close.html");};

//--------------------si données reçues----------------------------------------------------------

if( !empty($_POST['envoyer'])){



    if ($_POST['nom']=="") {
        echo "Vous devez indiquer votre nom";
        @mysql_close();
        exit;
     };
     // Contrôle de la catégorie
     $cat = cat_ffa($_POST['anneenaissance']);
     $sql = "SELECT categorie FROM `marsouin_epreuve` WHERE `code`='".$_POST['nomcourse']."'";
     $reponse = mysql_query($sql);
     $epreuve = mysql_fetch_object ($reponse);
     $cat_autorisees= split(",",$epreuve->categorie);
     if (!in_array($cat, $cat_autorisees)) { 
        echo "Votre catégorie ".$cat." n'est pas autorisée pour cette épreuve !";
        @mysql_close();
        exit;
     }
     // calcul du montant de la cotisation
     // voir module cotisation.php
      require_once('cotisation.php');
      $cotisation = prix_cotisation($_POST['noclub'],$_POST['nomcourse'],$_POST['commentaire']);

      if ($cotisation==0) $gratuit='oui'; else $gratuit='non';
      $commentaire = $cotisation ."€ (".$_POST['commentaire']." repas)";

      $insertSQL = sprintf("INSERT INTO marsouin_engagement (date,nom,prenom,typeparticipant,sexe,anneenaissance,categorie,nodept,nomcourse,nomequipe,adresse1,codepostal,ville,email,commentaire) VALUES (CURRENT_TIMESTAMP,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",

		       GetSQLValueString($_POST['nom'], "text"),
                       GetSQLValueString($_POST['prenom'], "text"),
                       GetSQLValueString($_POST['typeparticipant'], "text"),
                       GetSQLValueString($_POST['sexe'], "text"),
                       GetSQLValueString($_POST['anneenaissance'], "int"),
                       GetSQLValueString($cat, "text"),
                       GetSQLValueString($_POST['nodept'], "int"),
                       GetSQLValueString($_POST['nomcourse'], "text"),
                       GetSQLValueString($_POST['nomequipe'], "text"),
                       GetSQLValueString($_POST['adresse1'], "text"),
                       GetSQLValueString($_POST['codepostal'], "text"),
                       GetSQLValueString($_POST['ville'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($commentaire, "text")
	               );
      $Result1 = mysql_query($insertSQL) or die(mysql_error());



    // retour vers page de confirmation avec cas = 0 pour un non licencié.
    $GoTo = "confirmation.php?nom=".$_POST['nom']."&prenom=".$_POST['prenom']."&sexe=".$_POST['sexe']."&cas=0&info=".$commentaire."&gratuit=".$gratuit;
    header(sprintf("Location: %s", $GoTo));
    @mysql_close();
}
    
// fonction de protection SQL
    function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")
     {
     $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

     switch ($theType) {
       case "text":
          $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
          break;
       case "long":
       case "int":
         $theValue = ($theValue != "") ? intval($theValue) : "NULL";
         break;
       case "double":
         $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
       break;
          case "date":
          $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
       break;
          case "defined":
          $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
       break;
       }
       return $theValue;
      }

// fonction pour déterminer la catégorie FFA
  function cat_ffa($annee){
  $age=SAISON-$annee;

  if ($age>=70) return "V4";
  if ($age<70 && $age>=60) return "V3";
  if ($age<60 && $age>=50) return "V2";
  if ($age<50 && $age>=40) return "V1";
  if ($age<40 && $age>=23) return "SE";
  if ($age<23 && $age>=20) return "ES";
  if ($age<20 && $age>=18) return "JU";
  if ($age<18 && $age>=16) return "CA";
  if ($age<16 && $age>=14) return "MI";
  if ($age<14 && $age>=12) return "BE";
  if ($age<12 && $age>=10) return "PO";
  if ($age<10) return "EA";
  }

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<title>Inscription non licencié(e) ffa</title>

<link href="css/bassin.css" type="text/css" rel="StyleSheet" />
<script type="text/javascript" src="scripts/swfobject.js"></script>
<script language="javascript">
           // fonction pour tester la validité de l'adresse mail
         function testMail(champ){
          if (champ.value!=""){
           mail=/^[a-zA-Z0-9]+[a-zA-Z0-9\.\-_]+@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9])+$/;
           if (!mail.test(champ.value)) {
                   alert ("L'adresse email est invalide.\nElle doit être de la forme xxx@xxx.xxx");
                   champ.focus();
                   return false;
           }
          }
        }

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
        



        // fonction pour vérifier les infos avant envoi
        function verif(){
         if (document.engagement.nom.value == "") {
            alert ("Nom : Le champ est obligatoire, il doit être renseigné");
            document.engagement.nom.focus();
            displayAlert = true;
            return false;
         }
         if (document.engagement.prenom.value == ""){
            alert ("Prénom : Le champ est obligatoire, il doit être renseigné ");
            document.engagement.prenom.focus();
            displayAlert = true;
            return false;
         }
         if (document.engagement.nomcourse.value == ""){
            alert ("Vous devez choisir une course ");
            document.engagement.nomcourse.focus();
            displayAlert = true;
            return false;
         }
         if (document.engagement.anneenaissance.value == ""){
            alert ("Vous devez indiquez votre année de naissance ");
            document.engagement.anneenaissance.focus();
            displayAlert = true;
            return false;
         }

        }
        
        function afficher(Identificateur,etat){
	 var Element_cible;
	Element_cible = document.getElementById(Identificateur) ;
	if (etat){
	   Element_cible.style.display = "" ;
	 }
        else {
           Element_cible.style.display = "none" ;
        }
       }
       
       // demande confirmation à l'utilisateur s'il veut vraiment quitter la page
       // et par conséquent perdre toutes les données saisie au niveau du formulaire
       var displayAlert = true;
       window.onbeforeunload = function(e){
       if (displayAlert) {
        return 'En fermant cette page vous perdrez les informations saisies.';
       }
    }
    function alertNotNeeded() {
  	displayAlert = false;
    }
  </script>
</head>

<body topmargin="0" leftmargin="0">
<div id="page">
<div id="bandeau_flash" style="width: 1024px; height: 150px;"><img style="width: 1023px; height: 150px;" alt="u" src="images/bandeau-bassin%201.jpg" /><br />
</div>
<div id="nav-horizon">
     <ul id="menuDeroulant">
     <li><a href="index.html">Accueil</a></li>
     <li><a href="course.html">Course</a>
          <ul class="sousMenu">
		<li><a href="presentation.html">Présentation</a></li>
		<li><a href="prix.html">Grille des prix</a></li>
		<li><a href="organisation_courses.html">Organisation</a></li>
                <li><a href="epreuves_horaires.html">Horaires</a></li>
                <li><a href="parcours.html">Parcours</a></li>
                <li><a href="palmares.html">Résultats</a></li>
                <li><a href="tourisme.html">Tourisme</a></li>
                <li><a href="hebergement.html">Hébergements</a></li>
	  </ul>
     </li>
     <li><a href="inscription.html">Inscription</a>
         <ul class="sousMenu">
		<li><a href="inscription.html#en_ligne">En ligne</a></li>
		<li><a href="inscription.html#papier">Papier</a></li>
		<li><a href="inscription.html#liste">Liste inscrits</a></li>
		<li><a href="inscription.html#reglement">Règlement</a></li>
	  </ul>
     </li>
     <li><a href="partenaires.html">Partenaires</a></li>
     <li><a href="challenge.html">Challenge</a></li>
     <li><a href="contact.php">Contact</a></li>
     <li><a href="administration/index.php">Intra</a></li>
</ul>
</div>
<div>
<div id="menu">
<p></p>
</div>
<div id="contenu">
     <h2><?php echo DESIGNATION ?><br />Inscription en ligne <?php echo SAISON ?> non licencié(e) FFA</h2>
     <div class="item">
     <p>Vos informations personnelles : </p>
     <form method="post" action="inscription_nl.php"  name="engagement" onSubmit="return verif();">
     <table style="text-align: left; width: 600px; height: 160px;"   border="0" cellpadding="2" cellspacing="2">
     <tbody>
      <tr>
        <td>Nom</td>
        <td><input name="nom" onKeyPress="return pasNum(event)" onChange="majuscule(this)" /></td>
        <td>Prénom</td>
        <td><input name="prenom" onKeyPress="return pasNum(event)" /></td>
      </tr>
      <tr>
        <td>Année de naissance</td>
        <td><input name="anneenaissance" size="4" onKeyPress="return pasCar(event)" onChange="verif_nais(this)"/>
        </td>
        <td>Sexe</td>
        <td><input checked="checked" name="sexe"  value="M" type="radio" />Masculin 
        <input name="sexe" value="F" type="radio" />Féminin</td>
      </tr>
      <tr>
        <td>Email</td>
        <td colspan="3"><input name="email" size="50"  onChange="testMail(this)" /></td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td>Adresse</td>
        <td colspan="3"><input name="adresse1" size="60" /></td>
      </tr>
      <tr>
        <td>Code Postal</td>
        <td><input name="codepostal" onKeyPress="return pasCar(event)" /></td>
        <td>Ville</td>
        <td><input name="ville" onKeyPress="return pasNum(event)" /></td>
      </tr>
      <tr>
        <td colspan="3">Vous souhaitez vous inscrire à :</td>
        <td>
        <select name="nomcourse">
           <option selected value="">Choisissez ...</option>
           <?php
                // connexion à la base marsouin
                @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
                @mysql_select_db(BASE) or die("Echec de selection de la base cdt");
                // Lecture de la table marsouin_epreuve pour obtenir les désignations et codes des epreuves
                $sql = 'SELECT * FROM `marsouin_epreuve`';
                $reponse = mysql_query($sql) or die(mysql_error());
                while ($epreuve = mysql_fetch_object ($reponse)){
                      echo '<option value="'.$epreuve->code.'">'.$epreuve->designation.'</option>';
                      }
                      @mysql_close();
               // fin de la lecture des epreuves
               ?>
        </select>
        </td>
      </tr>
      
      <tr>
        <td colspan="3">Vous souhaitez vous inscrire au repas champêtre :</td>
        <td>
        <select name="commentaire">
        <option selected value="0">Choisissez....</option>
        <option value="0">Non merci</option>
        <option value="1">1 repas</option>
        <option value="2">2 repas</option>
        <option value="3">3 repas</option>
        <option value="4">4 repas</option>
        <option value="5">5 repas</option>
        </select>
        </td>
      </tr>
      <tr>
        <td><input name="envoyer" value="Valider"  type="submit" onclick="alertNotNeeded()"/></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
    </tbody>
  </table>
</form>
</div>
</div>
</div>
<div id="pied"> Site hébergé par Endurance72 - 2, avenue
d'HAOUZA - 72000 LE MANS - Tél: 02.43.23.64.18<br />
</div>
</div>
</body></html>
