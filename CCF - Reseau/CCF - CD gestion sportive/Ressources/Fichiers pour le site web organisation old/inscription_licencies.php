<?php
//---------------------------------------------------------------------------------------------
// Ce script enregistre l'engagement d'un licencié FFA // seul le n° de licence  est nécessaire
// page autoréférente publique (non protégée)
// notes: sep/2009 modification pour demander le n° du club
// Auteur Simier Philippe mai 2009    philaure@wanadoo.fr
//---------------------------------------------------------------------------------------------

require_once('definitions.inc.php');
require_once('cotisation.php');
// connexion à la base marsouin
 @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
 @mysql_select_db(BASE) or die("Echec de selection de la base cdt");

// lecture de la configuration et définition des constantes ENFFA SAISON DATE DESIGNATION etc
      $sql = 'SELECT * FROM `cross_route_configuration`';
      $reponse = mysql_query($sql) or die(mysql_error());
      while ($conf = mysql_fetch_object ($reponse)){
       define($conf->conf_key, $conf->conf_value);
      }
// fin de la lecture de la configuration

// ------------si les inscriptions sont fermées affichage de la page close.html-----------------
      if (EN_FFA==FALSE) { header("Location: close.html");};

//--------------------si les données  sont reçues---------------------------------------------------------
if( !empty($_POST['envoyer'])){
       if ($_POST['nom']=="") { echo "Vous devez indiquer votre nom";
              @mysql_close();
              exit;
          };



       if ($_POST['noclub']=="") { echo "Vous devez indiquer le n° de votre club";
              @mysql_close();
              exit;
          }
       else {
          $sql = "SELECT * FROM `ffa_club` WHERE `noclub`=".$_POST['noclub']." LIMIT 0, 30 ";
          $reponse = mysql_query($sql) or die(mysql_error());
          $club = mysql_fetch_object ($reponse);

       };
     // Contrôle de la catégorie
     $cat = cat_ffa($_POST['anneenaissance']);
     $sql = "SELECT categorie FROM `cross_route_epreuve` WHERE `code`='".$_POST['nomcourse']."'";
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
      $cotisation = prix_cotisation($_POST['noclub'],$_POST['nomcourse'],$_POST['commentaire']);

      if ($cotisation==0) $gratuit='oui'; else $gratuit='non';
      $commentaire = $cotisation ."€ (".$_POST['commentaire']." repas)";

      $insertSQL = sprintf("INSERT INTO cross_route_engagement (date,nom,prenom,anneenaissance,categorie,sexe,nolicence,typeparticipant,nomequipe,nomcourse,certifmedicalfourni,cotisationpaye,commentaire) VALUES (CURRENT_TIMESTAMP,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
              GetSQLValueString($_POST['nom'], "text"),
              GetSQLValueString($_POST['prenom'], "text"),
              GetSQLValueString($_POST['anneenaissance'], "text"),
              GetSQLValueString(cat_ffa($_POST['anneenaissance']), "text"),
              GetSQLValueString($_POST['sexe'], "text"),
              GetSQLValueString($_POST['nolicence'], "text"),
              GetSQLValueString($_POST['typeparticipant'], "text"),
              GetSQLValueString($club->nom, "text"),
              GetSQLValueString($_POST['nomcourse'], "text"),
              "'oui'",
              "'".$gratuit."'",
              GetSQLValueString($commentaire, "text") );

              $Result1 = mysql_query($insertSQL) or die(mysql_error());


              // retour vers la page de confirmation evec cas=1 licencié challenge ffa
              $GoTo = "confirmation.php?nom=".$_POST['nom']."&prenom=".$_POST['prenom']."&sexe=".$_POST['sexe']."&cas=1"."&info=".$commentaire."&gratuit=".$gratuit;
              header(sprintf("Location: %s", $GoTo));
}
//------------------------------------------------------------------------------------------------------------
@mysql_close();

// fonction de protection SQL
     function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") {
          $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue; switch ($theType) {
                 case "text": $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
                   break;
                 case "long": case "int": $theValue = ($theValue != "") ? intval($theValue) : "NULL";
                   break;
                 case "double": $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
                   break;
                 case "date": $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL"; 
                   break;
                 case "defined": $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue; 
                   break; }
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
<html>
      <head>
      <meta http-equiv="Content-Type" content="text/html; charset=windows-1252" /><title>Inscriptions licenciés ffa</title>
      <link href="css/bassin.css" type="text/css" rel="StyleSheet" />
      <script type="text/javascript" src="scripts/swfobject.js"></script>
        <script language="javascript">
           // fonction pour tester la validité de l'adresse mail
         function testMail(champ){
          if (champ.value!=""){
           mail=/^[a-zA-Z0-9]+[a-zA-Z0-9\.\-_]+@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9])+$/;
           if (!mail.test(champ.value)) {
                   alert ("Votre adresse e-mail est invalide!");
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
            alert ("Indiquez votre nom !!!");
            document.engagement.nom.focus();
            displayAlert = true;
            return false;
         }
         if (document.engagement.nolicence.value == ""){
            alert ("donnez votre numéro de licence FFA ");
            document.engagement.nolicence.focus();
            displayAlert = true;
            return false;
         }
         if (document.engagement.nomcourse.value == ""){
            alert ("Vous devez choisir votre course ");
            document.engagement.nomcourse.focus();
            displayAlert = true;
            return false;
         }

        }
        
        var xhr = null;
        function getXhr(){
		if(window.XMLHttpRequest) // Firefox et autres
                   xhr = new XMLHttpRequest();
		else if(window.ActiveXObject){ // Internet Explorer
		   try {
		    xhr = new ActiveXObject("Msxml2.XMLHTTP");
		       } catch (e) {
		    xhr = new ActiveXObject("Microsoft.XMLHTTP");
		       }
		}
		else { // XMLHttpRequest non supporté par le navigateur
		   alert("Votre navigateur ne supporte pas Ajax...");
		   xhr = false;
		}
	}
	
	function licence_ffa(champ){
	 getXhr();
	 xhr.onreadystatechange = function(){
            if(xhr.readyState == 4 && xhr.status == 200){
            retour = eval('('+xhr.responseText+')');

               if (retour.nom) document.engagement.nom.value=retour.nom; else document.engagement.nom.value="";
               if (retour.prenom) document.engagement.prenom.value=retour.prenom; else document.engagement.prenom.value="";
               if (retour.annee) document.engagement.anneenaissance.value=retour.annee; else document.engagement.anneenaissance.value="";
               if(retour.sexe=="F"){
                document.engagement.sexe[1].checked=true;
                document.engagement.sexe[0].checked=false;
                }
               if(retour.sexe=="M"){
                document.engagement.sexe[0].checked=true;
                document.engagement.sexe[1].checked=false;
               }
               if(retour.noclub) document.engagement.noclub.value=retour.noclub; else document.engagement.noclub.value="";



            }
         }
         xhr.open("POST","ajax_licence.php",true);
         xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
         xhr.send("nolicence="+champ.value);
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


<body topmargin="0" leftmargin="0" >
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
<h2><?php echo DESIGNATION ?><br />Inscription en ligne <?php echo SAISON ?> licencié(e) FFA</h2>

<div class="item">
<p>Vos informations personnelles : </p>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" name="engagement" onSubmit="return verif();">

      <table style="text-align: left; width: 570px;" border="0" cellpadding="2" cellspacing="2">
        <tbody>
          <tr>
            <td>Numéro de licence FFA :</td>
            <td><input name="nolicence" onKeyPress="return pasCar(event)" onChange="licence_ffa(this)"/></td>
            <td>N° du club</td>
            <td><input name="noclub" onKeyPress="return pasCar(event)"/></td>

          </tr>
          <tr>
              <td>Nom</td>
              <td><input name="nom" onKeyPress="return pasNum(event)" onChange="majuscule(this)"  MAXLENGTH="20"/></td>
              <td>Prénom</td>
              <td><input name="prenom" onKeyPress="return pasNum(event)" onChange="majuscule(this)" MAXLENGTH="20"/></td>
          </tr>
          <tr>
          <td>Année de naissance</td>
          <td><input name="anneenaissance" size="4" MAXLENGTH="4" onKeyPress="return pasCar(event)" onChange="verif_nais(this)"/>
          </td>
          <td>Sexe</td>
          <td><input checked="checked" name="sexe"  value="M" type="radio" />Masculin
          <input name="sexe" value="F" type="radio" />Féminin</td>
          </tr>
          <tr>
           <td colspan="3">Vous souhaitez vous inscrire à :
           </td>
           <td>
           <select name="nomcourse">
               <option selected="selected" value="">Choisissez ...</option>
                <?php
                // connexion à la base marsouin
                @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
                @mysql_select_db(BASE) or die("Echec de selection de la base cdt");
                // Lecture de la table cross_route_epreuve pour obtenir les désignations et codes des epreuves
                $sql = 'SELECT * FROM `cross_route_epreuve`';
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
           <td>inscription à titre :</td>
           <td colspan="3"><input checked="checked" name="typeparticipant" value="ffa" type="radio" />Club
               <input name="typeparticipant" value="" type="radio" />Individuel

           </td>
        </tr>

        <tr>
            <td colspan="3">Vous souhaitez vous inscrire au  repas champêtre :</td>
        <td>
             <select name="commentaire">
                     <option selected="selected" value="0">
                         Choisissez....</option>
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
          <td></td>
          <td><input value="Valider" name="envoyer" type="submit" onclick="alertNotNeeded()"/></td>
          <td></td>
          <td></td>
        </tr>
  </tbody>
</table>
<br />
</form>
<p></p>
</div>
</div>
</div>
<div id="pied"> Site hébergé par Endurance72 - 2, avenue  d'HAOUZA - 72000 LE MANS - Tél: 02.43.23.64.18<br />
</div>
</div>
</body>
</html>
