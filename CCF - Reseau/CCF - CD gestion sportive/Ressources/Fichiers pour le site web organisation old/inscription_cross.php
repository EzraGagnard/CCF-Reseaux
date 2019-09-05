<?php
//---------------------------------------------------------------------------------------------
// Ce script enregistre l'engagement d'un coureur ou d'un bénévole pour les organisations club
// page autoréférente publique (non protégée)
// notes: sep/2009 modification pour demander le n° du club
// ajout du libellé de la compétition 27 octobre 2009
// ajout filtrage  sexe et type licence COMP 29 octobre 2009
// utilisation du framework Jquery
// Auteur Simier Philippe mai 2009    philaure@wanadoo.fr
// Modification pour tenir compte des non_licenciés janvier 2012
// janvier 2014 ajout du champ email
// mars 2014 ajout du champ nodept noligue typelicence
// Avril 2014 controle du sexe avec la table des prénoms
//---------------------------------------------------------------------------------------------

require_once('definitions.inc.php');
require_once('cotisation.php');
require_once('administration/utile_sql.php');
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
$erreur="";

// ------------si les inscriptions sont fermées affichage de la page close.html-----------------
 if (EN_FFA==FALSE) { header("Location: close.html");};

//--------------------si les données  sont reçues---------------------------------------------------------
if( !empty($_POST['envoyer'])){
    if ($_POST['nom']=="") { 
		$erreur = "Vous devez indiquer votre nom !";
    };
	
	// test du champ email
	if ($_POST['email']=="") { 
		$erreur = "Oups vous devez indiquer votre email !";
    }
	else { 
		if ( !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$erreur = "Votre email n'est pas valide !!!";
		}	
	};
	
       // recherche du club pour les licenciés FFA
    if ($_POST['nolicence']!="") {
       if ($_POST['noclub']=="") { 
			$erreur = "Vous devez indiquer le n° de votre club";

        }
        else {
          $sql = "SELECT * FROM `ffa_club` WHERE `noclub`=".$_POST['noclub']." LIMIT 0, 30 ";
          $reponse = mysql_query($sql) or die(mysql_error());
          $club = mysql_fetch_object ($reponse);

        };
    };
    // Contrôle de la catégorie pour l'épreuve
    $cat = cat_ffa($_POST['anneenaissance'],$_POST['sexe']);

    $sql = sprintf("SELECT categorie,sexe FROM `cross_route_epreuve` WHERE `code`=%s AND `competition`=%s",
        GetSQLValueString($_POST['nomcourse'], "text"),
        GetSQLValueString($_POST['competition'], "text")
    );

    $reponse = mysql_query($sql);
    $epreuve = mysql_fetch_object ($reponse);



     if (!test_cat($_POST['anneenaissance'],$epreuve->categorie,$_POST['sexe'])) {
        $erreur = "Votre catégorie ".$cat." n'est pas autorisée pour cette épreuve !";

     }

     // Contrôle du sexe autorisé pour l'épreuve
     $sexe_autorises= split(",",$epreuve->sexe);

     if  (!in_array($_POST['sexe'], $sexe_autorises)) {
        if ($_POST['sexe']=='M') { $genre='Hommes';  $accord='s'; }
        else { $genre='Femmes'; $accord='es';}
        $erreur = "Oups, les ".$genre." ne sont pas autorisé".$accord." pour cette épreuve !";

     }
    // Création de l'objet licence pour les licenciés
	if ($_POST['nolicence']) {
		$sql = sprintf("SELECT typelicence FROM  `ffa_licence` WHERE `nolicence`=%s",
           GetSQLValueString($_POST['nolicence'], "int")
        );
        $reponse = mysql_query($sql);
        $licencie =  mysql_fetch_object ($reponse);
	} 
	 
    // Contrôle du type de licence pour les compétitions uniquement autorisées au type COMP
     
		$sql = sprintf("SELECT licence FROM  `competition` WHERE `nom`=%s",
              GetSQLValueString($_POST['competition'], "text")
        );

		$reponse = mysql_query($sql);
		$competition = mysql_fetch_object ($reponse);

    if ($competition->licence == "COMP") {
        if ($licencie->typelicence != "COMP") {
          $erreur = "Oups, les licences de type ".$licencie->typelicence." ne sont pas autorisées pour cette compétition !";

        }
    }
		
	// controle du sexe avec la table des prénoms
		$sql = "SELECT * FROM `prenom` WHERE `prenom` = '".$_POST['prenom']."'";
		$reponse = mysql_query($sql) or die(mysql_error());
		// création de l'objet prénom
		$prenom = mysql_fetch_object ($reponse);
	
	If (($_POST['sexe']=='M' && $prenom->genre=='f')||($_POST['sexe']=='F' && $prenom->genre=='m')) {
		$erreur = "Oups, ".$_POST['prenom']." est ".get_genre($prenom->genre);
		
	};		



 if (!$erreur){
    // voir module cotisation.php pour les prestations complémentaires et régles
    $cotisation = prix_cotisation($_POST['noclub'],$_POST['nomcourse'],$_POST['competition'],0,0);

    if ($cotisation==0) $gratuit='oui'; else $gratuit='non';
    if ($cotisation==0) $commentaire = 'Cross'; else $commentaire = ' '.$cotisation.'€';
    // Les licenciés n'ont pas besoin de présenter un certificat médical
	if ($_POST['nolicence']=='') $certif='non'; else $certif='oui';

      $insertSQL = sprintf("INSERT INTO cross_route_engagement (date,competition,nom,prenom,noclub,anneenaissance,categorie,sexe,nodept,noligue,nolicence,nomequipe,nomcourse,typelicence,certifmedicalfourni,cotisationpaye,email,commentaire) VALUES (CURRENT_TIMESTAMP,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
              GetSQLValueString($_POST['competition'], "text"),
              GetSQLValueString($_POST['nom'], "text"),
              GetSQLValueString($_POST['prenom'], "text"),
              GetSQLValueString($_POST['noclub'], "text"),
              GetSQLValueString($_POST['anneenaissance'], "int"),
              GetSQLValueString(cat_ffa($_POST['anneenaissance'],$_POST['sexe']), "text"),
              GetSQLValueString($_POST['sexe'], "text"),
			  GetSQLValueString($club->noclub, "text"),
			  GetSQLValueString($club->ligue, "text"),
              GetSQLValueString($_POST['nolicence'], "int"),
              GetSQLValueString($club->nom, "text"),
              GetSQLValueString($_POST['nomcourse'], "text"),
			  GetSQLValueString($licencie->typelicence, "text"),
              "'".$certif."'",
              "'".$gratuit."'",
			  GetSQLValueString($_POST['email'], "text"),
              GetSQLValueString($commentaire, "text") );

              $Result1 = mysql_query($insertSQL);

              if ($Result1) {
              // retour vers la page de confirmation evec cas=1 licencié challenge ffa
              $GoTo = "confirmation_cross.php?nom=".$_POST['nom']."&prenom=".$_POST['prenom']."&sexe=".$_POST['sexe']."&cas=1"."&info=".$commentaire."&gratuit=".$gratuit;
              header(sprintf("Location: %s", $GoTo));
              }
              else {
                $erreur=mysql_error();
                if ($_POST['sexe']=='M') $accord =''; else $accord='e';
                if (substr($erreur,0,8)=="Duplicat") { $erreur = " Oups, Vous êtes déja inscrit".$accord." pour cette course !"; }
              }
     }
}
//------------------------------------------------------------------------------------------------------------
@mysql_close();

// fonction pour déterminer la catégorie FFA
  function cat_ffa($annee,$sexe){
  $age=SAISON-$annee;

  if ($age>=1  && $age<=9 ) return "EA";
  if ($age>=10 && $age<=11) return "PO";
  if ($age>=12 && $age<=13) return "BE";
  if ($age>=14 && $age<=15) return "MI";
  if ($age>=16 && $age<=17) return "CA";
  if ($age>=18 && $age<=19) return "JU";
  if ($age>=20 && $age<=22) return "ES";
  if ($age>=23 && $age<=39) return "SE";
  if ($age>=40 && $age<=49) return "V1";
  if ($age>=50 && $age<=59) return "V2";
  if ($age>=60 && $age<=69  && $sexe=='M') return "V3";
  if ($age>=70 && $age<=120 && $sexe=='M') return "V4";
  if ($age>=60 && $age<=120  && $sexe=='F') return "V3";
  return "??";
  }

// fonction pour tester la catégorie autorisé sur une épreuve
// si l'age de l'engagé est dans une catégorie autorisée la fct revoie TRUE

  function test_cat($annee,$cat_autorisees,$sexe){
    $age=SAISON-$annee;
    $tableau=split(",",$cat_autorisees);
    foreach ($tableau as $cat) {
      if ($age>=1  && $age<=9 && $cat=="EA") return true;
      if ($age>=10 && $age<=11 && $cat=="PO") return true;
      if ($age>=12 && $age<=13 && $cat=="BE") return true;
      if ($age>=14 && $age<=15 && $cat=="MI") return true;
      if ($age>=16 && $age<=17 && $cat=="CA") return true;
      if ($age>=18 && $age<=19 && $cat=="JU") return true;
      if ($age>=20 && $age<=22 && $cat=="ES") return true;
      if ($age>=23 && $age<=39 && $cat=="SE") return true;
      if ($age>=40 && $age<=100 && $cat=="VE") return true;
      if ($age>=40 && $age<=49 && $cat=="V1") return true;
      if ($age>=50 && $age<=59 && $cat=="V2") return true;
      if ($age>=60 && $age<=69  && $sexe=='M' && $cat=="V3") return true;
      if ($age>=70 && $age<=120 && $sexe=='M' && $cat=="V4") return true;
      if ($age>=60 && $age<=120  && $sexe=='F' && $cat=="V3") return true;
      if ($age>=11 && $age<=100  && $cat=="TC") return true;
  }
  return false;
 }
 
 // cette fonction donne masculin ou féminin en fonction en fct du genre
   function get_genre($x){
    if ($x == "m") return "masculin"; 
	elseif ($x == "f") return "féminin"; else return "";
   }

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
      <head>
      <meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
      <title>Inscription Cross-Route licenciés ffa</title>
      <link href="css/bassin.css" type="text/css" rel="StyleSheet" />
      <script type="text/javascript" src="scripts/swfobject.js"></script>
      <script src="scripts/jquery.js" type="text/javascript"></script>
        <script language="javascript">
         // avec jQuery
        // Quand le dom est chargé
        $(function(){
           // à l'évènement "change" de l'élément id competition, on associe la fonction epreuve  .
           $("#competition").change(function(){
               load_epreuve(this.value);
            });
           // à l'évènement focus des éléments input ayant la class normal, on associe la class focus
           $("input.normal").focus(function(){
	  	$(this).removeClass();
		$(this).addClass("focus");

	     });
          // à l'évènement blur des éléments input ayant la class focus, on associe la classe normal
	  $("input").blur(function(){
		$(this).removeClass();
		$(this).addClass("normal");

	    });

        });

        // cette fonction lance une requête AJAX pour mettre à jour le sélecteur epreuve
        function load_epreuve(code) {
        $("#loader").show();   // affiche le loader
        $.post("ajax_epreuve.php", { competition: code },
                  function(code_html){
                     $("#loader").hide();   //cache le loader
                     $("#epreuve").html(code_html);  // ajoute dans l'élément id épreuve le contenu html reçu

                  }
               );
        }
        
        // cette fonction lance une requête AJAX pour mettre à jour les infos sur l'engagé(e)
        function licence_ffa(code) {
        $("#loader").show();   // affiche le loader
        //$("input").attr("readonly","readonly");  verouillage des champs input
		$("#email").attr("readonly","");         // deverouillage du champ email
        $.post("ajax_licence.php", { nolicence: code.value },
               function(reponse){
                $("#loader").hide();   //efface le loader
                retour = eval('('+reponse+')');    // puis mise à jour des champs input
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

                //if (!retour.nom)    $("input").attr("readonly","");  si pas de nom déverouillage des champs input
                if (retour.nom)  $("input").focus(function(){
                    $(this).removeClass();
		    $(this).addClass("normal");
                 });
               }
            );
        }

        // fonction pour interdire les caractères numériques et ponctuations *+,-./0123456789:;
        function pasNum(e){
          if (window.event) caractere = window.event.keyCode;
               else  caractere = e.which;
                 return (caractere < 33 || caractere > 63) ;
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


         if (document.engagement.nolicence.value != "" && document.engagement.noclub.value == ""){
            alert ("donnez le numéro de votre club ");
            document.engagement.noclub.focus();
            displayAlert = true;
            return false;
         }

         if (document.engagement.nom.value == "") {
            alert ("Indiquez votre nom !!!");
            document.engagement.nom.focus();
            displayAlert = true;
            return false;
         }
         
         if (document.engagement.prenom.value == "") {
            alert ("Indiquez votre prénom !!!");
            document.engagement.prenom.focus();
            displayAlert = true;
            return false;
         }

         if (document.engagement.anneenaissance.value == "") {
            alert ("Indiquez votre année de naissance 4 chiffres !!!");
            document.engagement.anneenaissance.focus();
            displayAlert = true;
            return false;
         }

         if (document.engagement.nomcourse.value == ""){
            alert ("Vous devez choisir votre course ");
            document.engagement.competition.focus();
            displayAlert = true;
            return false;
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
<div id="bandeau_flash" style="width: 1024px; height: 150px;">
<a href="http://www.endurance72.fr" title="Retour page d'accueil">
<img style="width: 1023px; height: 150px; border: 0px;" alt="u" src="images/bandeau_E72.jpg" /></a><br />
</div>

<!--Menu horizontal sous la banniere-->
<div id="nav-horizon">
<ul id="menuDeroulant">
     <li><a href="#"></a>
    	
</li>
     <li><a href="#"></a>
     </li>
     <li><a href="#"></a>
	 </li>
	 <li><a href="#"></a>
     </li>    
</ul>
</div><!-- fin de menu horizontal -->


<div>
<div id="menu" style="width:100px;">
<p></p>
</div>
<div id="contenu">
<h2><?php echo DESIGNATION ?><br />Inscription en ligne <?php echo SAISON ?> licencié(e) FFA</h2>
<?php if ($erreur) {echo '<p style="color:#FF0000;">'.$erreur."</p>"; } else { echo "<p> </p>"; }?>
<div class="item" style="width:700px;">
<p><b>Vos informations personnelles : </b><span id="loader" style="display:none;"><img src="images/loader.gif"  alt="loader" /></span></p>

<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" name="engagement" onSubmit="return verif();">

      <table style="text-align: left; width: 700px;" border="0" cellpadding="2" cellspacing="2">
        <tbody>
          <tr>
            <td style="text-align:right; width:28%;">Numéro de licence FFA :</td>
            <td><input class="normal" name="nolicence" onKeyPress="return pasCar(event)" maxlength="7" onChange="licence_ffa(this)" value="<?php if (isset($_POST['nolicence'])) echo $_POST['nolicence']; ?>"/></td>
            <td style="text-align:right; width:11%;">N° du club : </td>
            <td><input class="normal" name="noclub" onKeyPress="return pasCar(event)" maxlength="6"<?php if (isset($_POST['noclub'])) echo 'value="'.$_POST['noclub'].'"'; ?>/></td>

          </tr>
          <tr>
              <td style="text-align:right; width:28%;">Nom : </td>
              <td><input class="normal" name="nom" onKeyPress="return pasNum(event)" onChange="majuscule(this)"  maxlength="20" <?php if (isset($_POST['nom'])) echo 'value="'.$_POST['nom'].'"'; ?>/></td>
              <td style="text-align:right; width:11%;">Prénom : </td>
              <td><input class="normal" name="prenom" onKeyPress="return pasNum(event)" onChange="majuscule(this)" maxlength="20" <?php if (isset($_POST['prenom'])) echo 'value="'.$_POST['prenom'].'"'; ?>/></td>
          </tr>
          <tr>
          <td style="text-align:right; width:28%;">Année de naissance : </td>
          <td><input class="normal" name="anneenaissance" size="4" maxlength="4" onKeyPress="return pasCar(event)" onChange="verif_nais(this)" <?php if (isset($_POST['anneenaissance'])) echo 'value="'.$_POST['anneenaissance'].'"'; ?>/>
          </td>
          <td style="text-align:right; width:11%;">Sexe : </td>
          <td><input <?php if (isset($_POST['sexe']) && $_POST['sexe']=='M')echo 'checked="checked"' ?> name="sexe"  value="M" type="radio" />Masculin
          <input <?php if (isset($_POST['sexe']) && $_POST['sexe']=='F')echo 'checked="checked"' ?> name="sexe" value="F" type="radio" />Féminin</td>
          </tr>
          <tr>
		  
		  <tr>
				<td style="text-align:right; width:28%;">Email : </td>
				<td><input class="normal" name="email" id="email" maxlength="50" <?php if (isset($_POST['email'])) echo 'value="'.$_POST['email'].'"'; ?>/>
				</td>
				<td></td>
				<td></td>
          </tr>
		  
           <td style="text-align:right; width:11%;">Vous souhaitez vous inscrire : </td>
           <td>
           <select name="competition" id="competition">
               <option selected="selected" value="">Choisissez l'événement</option>

                <?php
                // connexion à la base marsouin
                @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
                @mysql_select_db(BASE) or die("Echec de selection de la base cdt");
                // Lecture de la table competition pour obtenir libellés et dates  des compétitions
                $sql = "SELECT * FROM `competition` WHERE `validation`='1'";
                $reponse = mysql_query($sql) or die(mysql_error());

                if (mysql_num_rows($reponse)==0) echo "<optgroup label=\"Aucun événement disponible\">";
                if (mysql_num_rows($reponse)==1) echo "<optgroup label=\"événement disponible\">";
                if (mysql_num_rows($reponse)>1) echo "<optgroup label=\"événements disponibles\">";
                while ($competition = mysql_fetch_object ($reponse)){
                      echo '<option value="'.$competition->nom.'">'.$competition->nom.' ('.date("j M Y",strtotime($competition->date)).')</option>';
                      }
                      @mysql_close();
               // fin de la lecture des competitions
               ?>
               </optgroup>
           </select>

           </td>
           <td colspan="2" id="epreuve">
              <select name="nomcourse">
              <option selected="selected" value="">Choisissez l'option</option>
              </select>
           </td>
         </tr>

       <tr>
          <td  style="text-align:right; width:28%;"><input value="Valider" name="envoyer" type="submit" onclick="alertNotNeeded()"/></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
  </tbody>
</table>
<br />
</form>
<br />
</div>
</div>
</div>
<div id="pied"> Site hébergé par Endurance72 - 24, rue Louis Crétois  - 72000 LE MANS - Tél: 02.43.23.64.18<br />
</div>
</div>
</body>
</html>
