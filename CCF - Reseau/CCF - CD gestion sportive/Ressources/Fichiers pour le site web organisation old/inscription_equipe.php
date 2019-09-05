<?php
// Ce script enregistre l'engagement d'une équipe pour le challenge 
// Entreprise CCI
// page autoréférente publique
//---------------------------------------------------------------------

require_once('definitions.inc.php'); 
// connexion à la base marsouin
   @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
   @mysql_select_db(BASE) or die("Echec de selection de la base cdt");

// lecture de la configuration et définition des constantes ENABLE SAISON DATE DESIGNATION etc
      $sql = 'SELECT * FROM `cross_route_configuration`';
      $reponse = mysql_query($sql) or die(mysql_error());
      while ($conf = mysql_fetch_object ($reponse)){
       define($conf->conf_key, $conf->conf_value);
      }
// fin de la lecture de la configuration

// ------------si les inscriptions sont fermées affichage de la page close.html-----------------
      if (EN_CHAL==FALSE) { header("Location: close.html");};

//--------------------si des données sont reçues---------------------------------------------------------

if( !empty($_POST['envoyer'])){


   $total=0;

while (list($indice,$valeur)=each($_POST['nom'])){
  $nom = $_POST['nom'][$indice];
  $prenom = $_POST['prenom'][$indice];
  $anneenaissance = $_POST['anneenaissance'][$indice];
  $sexe = $_POST['sexe'][$indice];
  $commentaire = $_POST['commentaire'][$indice];

  if ($nom=="") { echo "Vous devez indiquer votre nom"; @mysql_close(); exit; };
  if ($anneenaissance> (SAISON-16))  { echo "Vous devez être au minimum cadet le jour de la course"; @mysql_close(); exit; };
  // calcul du montant de la cotisation avec les repas
      $cotisation = 12+$commentaire*12;
      $total += $cotisation;
      $info = $cotisation ."€ (".$commentaire." repas)";

  $insertSQL = sprintf("INSERT INTO cross_route_engagement (date,nom,prenom,sexe,typeparticipant,anneenaissance,categorie,nomcourse,nomequipe,adresse1,codepostal,ville,email,commentaire) VALUES (CURRENT_TIMESTAMP,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
     GetSQLValueString($nom, "text"),
     GetSQLValueString($prenom, "text"),
     GetSQLValueString($sexe, "text"),
     GetSQLValueString($_POST['typeparticipant'], "text"),
     GetSQLValueString($anneenaissance, "int"),
     GetSQLValueString(cat_ffa($anneenaissance), "text"),
     GetSQLValueString($_POST['nomcourse'], "text"),
     GetSQLValueString($_POST['nomequipe'], "text"),
     GetSQLValueString($_POST['adresse1'], "text"), 
     GetSQLValueString($_POST['codepostal'], "text"), 
     GetSQLValueString($_POST['ville'], "text"), 
     GetSQLValueString($_POST['email'], "text"), 
     GetSQLValueString($info, "text") );
     
     $Result1 = mysql_query($insertSQL) or die(mysql_error());
      }


  // retour vers la page de confirmation evec cas=2 engagement équipe challenge
              $total=$total . "€ ";
              $GoTo = "confirmation.php?nom=".$_POST['nom'][1]."&prenom=".$_POST['prenom'][1]."&sexe=".$_POST['sexe'][1]."&cas=2&nomequipe=".$_POST['nomequipe']."&info=".$total;
              header(sprintf("Location: %s", $GoTo));
  @mysql_close();
  exit;
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


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
      <meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
      <title>Inscription challenge entreprise</title>
      <link href="css/bassin.css" type="text/css" rel="StyleSheet" />
      <script type="text/javascript" src="scripts/swfobject.js"></script>
      <style type="text/css">
      <!--
      .cellule {
       margin: 2px 20px 2px 4px;
      }
       .cellule2 {
       margin: 2px 2px 2px 20px;
      }

      -->
      </style>



      <script language="javascript">
// variable globale contenant le nombre de ligne du tableau
var nombre=4;
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
         else caractere = e.which;
         return (caractere < 48 || caractere > 57);
}
// fonction pour autoriser uniquement les numériques
function pasCar(e){
         if (window.event) caractere = window.event.keyCode;
         else caractere = e.which;
         return (caractere == 8 || (caractere > 47 && caractere < 58));
}
// fonction pour mettre en majuscule
function majuscule(champ){
         champ.value = champ.value.toUpperCase();
}
// fonction pour vérifier la date de naissance
function verif_nais(champ){
         if (champ.value<1920 || champ.value><?php echo SAISON-16;  ?>){
         alert("Année de naissance sur 4 chiffres\net comprise entre 1920 et <?php echo SAISON-16; // cadet ?>");
         champ.value = "";
         champ.focus();
         }
}
// fonction pour ajuster le nombre de ligne du tableau (3 pour le trente) (4 pour le douze)
function ajuste(champ){
  if (champ.value=='30km' && nombre==4){
     engage_moins();
  }
  if (champ.value=='12km' && nombre==3){
     engage_plus();
  }
}
// fonction pour vérifier les infos principales avant envoi
function verif(){
   if (document.engagement.nomcourse.value == ""){
   alert ("Vous devez choisir une course ");
   document.engagement.nomcourse.focus();
   displayAlert = true;
   return false;
   }
   if (document.engagement.nomequipe.value == ""){
   alert ("Vous devez donner un nom à votre équipe");
   document.engagement.nomequipe.focus();
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

    function engage_plus(){
        ++nombre;
        table = document.getElementById("tableau");
        nouvelle_ligne = document.createElement('div');
        contient ='<input class="cellule" name="nom['+nombre+']" onkeypress="return pasNum(event)" onchange="majuscule(this)" />';
        contient +=' <input class="cellule" name="prenom['+nombre+']" onkeypress="return pasNum(event)" />';
        contient +=' <input class="cellule2" name="anneenaissance['+nombre+']" size="4" onkeypress="return pasCar(event)" onchange="verif_nais(this)" />';
        contient +=' <input class="cellule2" checked="checked" name="sexe['+nombre+']" value="M" type="radio" />Masculin <input name="sexe['+nombre+']" value="F" type="radio" />Féminin';
        contient +=' <select class="cellule2"  name="commentaire['+nombre+']"><option selected="selected" value="0">Choisissez....</option><option value="0">Non merci</option><option value="1">1 repas</option><option value="2">2 repas</option><option value="3">3 repas</option><option value="4">4 repas</option><option value="5">5 repas</option></select>';
        nouvelle_ligne.innerHTML = contient;
        table.appendChild(nouvelle_ligne);
}

 function engage_moins(){
    if (nombre>3) {
        table = document.getElementById("tableau");
        derniere_ligne = table.lastChild;
        table.removeChild(derniere_ligne);
        --nombre;
    }else{
         alert("Une équipe doit être composée d'au moins\n trois coureurs");
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
      <div id="bandeau_flash" style="width: 1024px; height: 150px;">
           <img style="width: 1023px; height: 150px;" alt="u" src="images/bandeau-bassin%201.jpg" />
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
         <div style="width: 81px;" id="menu">
      </div>
      <div id="contenu" style="width: 830px;">
      <h2><?php echo DESIGNATION ?><br />Inscription en ligne <?php echo SAISON ?> challenge Entreprise/militaire</h2>

      <div class="item">
           <p>Vos informations pour votre inscription au challenge : </p>
           <form method="post" action="inscription_equipe.php" name="engagement" onsubmit="return verif();">
           <table style="text-align: left; width: 732px; height: 160px;" border="0" cellpadding="2" cellspacing="2">
           <tbody>
           <tr>
              <td  style="width: 162px;">Nom  de l'équipe</td>
              <td><input name="nomequipe" /></td>
              <td colspan="3">
               Challenge :
               <input name="typeparticipant" checked="checked" value="Ent" type="radio"   />Entreprise
               <input name="typeparticipant" value="Mil" type="radio" />Militaire

              </td>
           </tr>
           <tr>
               <td style="width: 162px;" >Vous souhaitez vous inscrire à </td>
               <td colspan="2"><select name="nomcourse" onchange="ajuste(this)">
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
               </select></td>
           </tr>
           <tr>
               <td style="width: 168px; background-color: rgb(204, 204, 204);">Nom</td>
               <td style="width: 162px; background-color: rgb(204, 204, 204);">Prénom</td>
               <td style="width: 86px; background-color: rgb(204, 204, 204);">Année<br />de naissance</td>
               <td style="text-align: center; width: 152px; background-color: rgb(204, 204, 204);">Sexe</td>
               <td style="width: 120px; background-color: rgb(204, 204, 204);">Repas champêtre</td>
           </tr>
           <tr>
                <td colspan="5">
                <div id="tableau"><div>
                   <input class="cellule" name="nom[1]" onkeypress="return pasNum(event)" onchange="majuscule(this)" />
                   <input class="cellule" name="prenom[1]" onkeypress="return pasNum(event)" />
                   <input class="cellule2" name="anneenaissance[1]" size="4" onkeypress="return pasCar(event)" onchange="verif_nais(this)" />
                   <input class="cellule2" checked="checked" name="sexe[1]" value="M" type="radio" />Masculin <input name="sexe[1]" value="F" type="radio" />Féminin
                   <select class="cellule2"  name="commentaire[1]"><option selected="selected" value="0">Choisissez....</option><option value="0">Non merci</option><option value="1">1 repas</option><option value="2">2 repas</option><option value="3">3 repas</option><option value="4">4 repas</option><option value="5">5 repas</option></select>
                </div>
                <div>
                   <input class="cellule" name="nom[2]" onkeypress="return pasNum(event)" onchange="majuscule(this)" />
                   <input class="cellule" name="prenom[2]" onkeypress="return pasNum(event)" />
                   <input class="cellule2" name="anneenaissance[2]" size="4" onkeypress="return pasCar(event)" onchange="verif_nais(this)" />
                   <input class="cellule2" checked="checked" name="sexe[2]" value="M" type="radio" />Masculin <input name="sexe[2]" value="F" type="radio" />Féminin
                   <select class="cellule2" name="commentaire[2]"><option selected="selected" value="0">Choisissez....</option><option value="0">Non merci</option><option value="1">1 repas</option><option value="2">2 repas</option><option value="3">3 repas</option><option value="4">4 repas</option><option value="5">5 repas</option></select>
                </div>
                <div>
                   <input class="cellule" name="nom[3]" onkeypress="return pasNum(event)" onchange="majuscule(this)" />
                   <input class="cellule"  name="prenom[3]" onkeypress="return pasNum(event)" />
                   <input class="cellule2"  name="anneenaissance[3]" size="4" onkeypress="return pasCar(event)" onchange="verif_nais(this)" />
                   <input class="cellule2" checked="checked" name="sexe[3]" value="M" type="radio" />Masculin <input name="sexe[3]" value="F" type="radio" />Féminin
                   <select class="cellule2" name="commentaire[3]"><option selected="selected" value="0">Choisissez....</option><option value="0">Non merci</option><option value="1">1 repas</option><option value="2">2 repas</option><option value="3">3 repas</option><option value="4">4 repas</option><option value="5">5 repas</option></select>
                </div>
                <div>
                   <input class="cellule"  name="nom[4]" onkeypress="return pasNum(event)" onchange="majuscule(this)" />
                   <input class="cellule"  name="prenom[4]" onkeypress="return pasNum(event)" />
                   <input class="cellule2"  name="anneenaissance[4]" size="4" onkeypress="return pasCar(event)" onchange="verif_nais(this)" />
                   <input class="cellule2" checked="checked" name="sexe[4]" value="M" type="radio" />Masculin <input name="sexe[4]" value="F" type="radio" />Féminin
                   <select class="cellule2" name="commentaire[4]"><option selected="selected" value="0">Choisissez....</option><option value="0">Non merci</option><option value="1">1 repas</option><option value="2">2 repas</option><option value="3">3 repas</option><option value="4">4 repas</option><option value="5">5 repas</option></select>
                </div>
                </div>
                </td>
          </tr>
         <tr>
         <td style="width: 168px;" colspan="1"><br />
</td>
<td style="width: 162px;" colspan="1"></td>
<td style="width: 86px;"></td>
<td style="width: 152px;"><input name="ajouter" value="Supprimer une ligne" onclick="engage_moins()" type="button" /></td>
<td style="width: 120px;"><input name="ajouter" value="Ajouter un nom" onclick="engage_plus()" type="button" /></td>
</tr>

<tr>
<td style="width: 162px;" colspan="1">Email
pour joindre l'équipe </td>
<td colspan="3"><input name="email" size="50" onchange="testMail(this)" /></td>
</tr>
<tr>
<td style="width: 168px;" colspan="1">Adresse :</td>
<td style="width: 162px;" colspan="3"><input size="50" name="adresse1" /></td>
<td style="width: 120px;">
<br />
</td>
</tr>
<tr>
<td style="width: 168px;">Code postal :</td>
<td style="width: 162px;"><input name="codepostal" onkeypress="return pasCar(event)" /></td>
<td style="width: 86px;">Ville :</td>
<td style="width: 152px;"><input name="ville" onkeypress="return pasNum(event)" /></td>
<td style="width: 120px;"></td>
</tr>
<tr>
<td style="width: 168px;"><input name="envoyer" value="Valider" type="submit" onclick="alertNotNeeded()"/></td>
<td style="width: 162px;"></td>
<td style="width: 86px;"></td>
<td style="width: 152px;"></td>
<td style="width: 120px;"></td>
</tr>
</tbody>
</table>
</form>
</div>
</div>
</div>
<div id="pied"> Site hébergé par Endurance72 - 2, avenue
d'HAOUZA - 72100 LE MANS - Tél: 02.43.23.64.18<br />
</div>
</div>
</body></html>
