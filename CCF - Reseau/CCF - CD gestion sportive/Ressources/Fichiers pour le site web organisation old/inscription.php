<?php
//---------------------------------------------------------------------------------------------
// Ce script enregistre l'engagement d'un participant
// page autor�f�rente publique (non prot�g�e)
// notes: sep/2009 modification pour demander le n� du club
// ajout du libell� de la comp�tition 27 octobre 2009
// ajout filtrage  sexe et type licence COMP 29 octobre 2009
// ajout acceptation du r�glement de l'�preuve
// utilisation du framework Jquery
// 26 janvier 2014  ajout du champ email
// 30 mars 2014 ajout des champs noligue nodept et typelicence
// Auteur Simier Philippe mai 2009    philaure@wanadoo.fr
// F�vrier 2015 controle du sexe avec la table des pr�noms
// F�vrier 2015 Ajout du champ GET comp�tition pour afficher uniquement celle demand�e
// 19 F�vrier 2015 Ajout du n� de t�l�phone et attribution automatique du dossard
//---------------------------------------------------------------------------------------------

require_once('definitions.inc.php');
require_once('cotisation.php');
require_once('administration/utile_sql.php');
// connexion � la base de donn�es pour les inscriptions
 @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
 @mysql_select_db(BASE) or die("Echec de selection de la base cdt");

// lecture de la configuration et d�finition des constantes ENFFA SAISON DATE DESIGNATION etc
      $sql = 'SELECT * FROM `cross_route_configuration`';
      $reponse = mysql_query($sql) or die(mysql_error());
      while ($conf = mysql_fetch_object ($reponse)){
       define($conf->conf_key, $conf->conf_value);
      }
// fin de la lecture de la configuration
$erreur="";

// ------------si les inscriptions sont ferm�es affichage de la page close.php---------------------------
	if ($_GET['competition'] ==''){
		// Lecture de la table competition pour obtenir  toutes les comp�titions ouvertes � l'inscription
            
		$sql = "SELECT * FROM `competition` WHERE  `validation`='1'";
		$reponse = mysql_query($sql) or die(mysql_error());
		if (mysql_num_rows($reponse)==0) { 
			header("Location: close.php");
			@mysql_close();
			exit;
			};
	}
	else
	{	$sql = "SELECT * FROM `competition` WHERE `nom`= \"".$_GET['competition']."\" and `validation`=1";
		$reponse = mysql_query($sql) or die(mysql_error());
		if (mysql_num_rows($reponse)==0) { 
			header("Location: close.php");
			@mysql_close();
			exit;
			};
	}
	if (EN_FFA==FALSE) { header("Location: close.php");};

//--------------------si des donn�es  sont re�ues---------------------------------------------------------
if( !empty($_POST['envoyer'])){

    if (empty($_POST['reglement'])) {  
		$erreur = "Vous devez avoir lu et accept� le r�glement de cette �preuve !";
    };
	
    if ($_POST['nom']=="") { 
		$erreur = "Vous devez indiquer votre nom !";
    };
	
	// Contr�le du champ email
	if ($_POST['email']=="") { 
		$erreur = "Oups vous devez indiquer votre email !";
    }
	else { 
		if ( !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$erreur = "Votre email n'est pas valide !!!";
		}	
	};

    if ($_POST['noclub']=="") { 
		$_POST['noclub']= "00000";

    }
    else {
        $sql = "SELECT * FROM `ffa_club` WHERE `noclub`=".$_POST['noclub']." LIMIT 0, 30 ";
        $reponse = mysql_query($sql) or die(mysql_error());
        $club = mysql_fetch_object ($reponse);

    };
	
    // Contr�le de la cat�gorie pour l'�preuve
    $cat = cat_ffa($_POST['anneenaissance'],$_POST['sexe']);

       $sql = sprintf("SELECT categorie,sexe FROM `cross_route_epreuve` WHERE `code`=%s AND `competition`=%s", 
              GetSQLValueString($_POST['nomcourse'], "text"),
              GetSQLValueString($_POST['competition'], "text")
              );

     $reponse = mysql_query($sql);
     $epreuve = mysql_fetch_object ($reponse);



     if (!test_cat($_POST['anneenaissance'],$epreuve->categorie,$_POST['sexe'])) {
        $erreur = "Votre cat�gorie ".$cat." n'est pas autoris�e pour cette �preuve !";

     }

     // Contr�le du sexe autoris� pour l'�preuve
     $sexe_autorises= explode(",",$epreuve->sexe);

     if  (!in_array($_POST['sexe'], $sexe_autorises)) {
        if ($_POST['sexe']=='M') { $genre='Hommes';  $accord='s'; }
        else { $genre='Femmes'; $accord='es';}
        $erreur = "Oups, les ".$genre." ne sont pas autoris�".$accord." pour cette �preuve !";

     }
     
    // Cr�ation de l'objet licence pour les licenci�s
	if ($_POST['nolicence']) {
		$sql = sprintf("SELECT typelicence FROM  `ffa_licence` WHERE `nolicence`=%s",
           GetSQLValueString($_POST['nolicence'], "int")
        );
        $reponse = mysql_query($sql);
        $licencie =  mysql_fetch_object ($reponse);
	} 
	 
    // Contr�le du type de licence pour les comp�titions uniquement autoris�es au type COMP
     
		$sql = sprintf("SELECT licence FROM  `competition` WHERE `nom`=%s",
              GetSQLValueString($_POST['competition'], "text")
        );

		$reponse = mysql_query($sql);
		$competition = mysql_fetch_object ($reponse);

    if ($competition->licence == "COMP") {
        if ($licencie->typelicence != "COMP") {
          $erreur = "Oups, les licences de type ".$licencie->typelicence." ne sont pas autoris�es pour cette comp�tition !";

        }
    }
 
	// controle du sexe avec la table des pr�noms
		$sql = "SELECT * FROM `prenom` WHERE `prenom` = '".$_POST['prenom']."'";
		$reponse = mysql_query($sql) or die(mysql_error());
		// cr�ation de l'objet pr�nom
		$prenom = mysql_fetch_object ($reponse);
	
	If (($_POST['sexe']=='M' && $prenom->genre=='f')||($_POST['sexe']=='F' && $prenom->genre=='m')) {
		$erreur = "Oups, ".$_POST['prenom']." est ".get_genre($prenom->genre);
		
	};		




 if (!$erreur){
      // voir module cotisation.php pour les prestations compl�mentaires et r�gles
      $cotisation = prix_cotisation($_POST['noclub'],$_POST['nomcourse'],$_POST['competition'],0,0);

    if ($cotisation==0) $gratuit='oui'; else $gratuit='non';
    $commentaire = $cotisation;
    // si n� de licence est vide alors c'est un non licenci�
    if ($_POST['nolicence']=='') $cas='0'; else $cas='1';
    if ($_POST['nolicence']=='') $certif='non'; else $certif='oui';
	
	// recherche du dernier n� de dossard attribu�
	$sql= "SELECT MAX(`dossard`) AS high_dossard FROM cross_route_engagement Where `competition`= \"".$_POST['competition']."\"";
	$reponse = mysql_query($sql) or die(mysql_error());
	$dossard = mysql_fetch_object ($reponse);
	

       $insertSQL = sprintf("INSERT INTO cross_route_engagement (date,dossard,competition,nom,prenom,noclub,anneenaissance,categorie,sexe,nodept,noligue,nolicence,nomequipe,nomcourse,typelicence,certifmedicalfourni,cotisationpaye,email,tel,commentaire) VALUES (CURRENT_TIMESTAMP,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
              GetSQLValueString(($dossard->high_dossard+1), "int"),
			  GetSQLValueString($_POST['competition'], "text"),
              GetSQLValueString($_POST['nom'], "text"),
              GetSQLValueString($_POST['prenom'], "text"),
              GetSQLValueString($_POST['noclub'], "text"),
              GetSQLValueString($_POST['anneenaissance'], "int"),
              GetSQLValueString(cat_ffa($_POST['anneenaissance'],$_POST['sexe']), "text"),
              GetSQLValueString($_POST['sexe'], "text"),
			  GetSQLValueString($club->noclub, "text"),
			  GetSQLValueString($club->ligue, "text"),
              GetSQLValueString($_POST['nolicence'], "text"),
              GetSQLValueString($club->nom, "text"),
              GetSQLValueString($_POST['nomcourse'], "text"),
			  GetSQLValueString($licencie->typelicence, "text"),
              "'".$certif."'",
              "'".$gratuit."'",
			  GetSQLValueString($_POST['email'], "text"),
			  GetSQLValueString($_POST['tel'], "text"),
              GetSQLValueString($commentaire, "text") );

              $Result1 = mysql_query($insertSQL);

              if ($Result1) {
              // retour vers la page de confirmation evec cas=1 licenci� challenge ffa
              $GoTo = "confirmation_trail.php?nom=".$_POST['nom']."&prenom=".$_POST['prenom']."&sexe=".$_POST['sexe']."&cas=".$cas."&info=".$commentaire."&gratuit=".$gratuit;
              header(sprintf("Location: %s", $GoTo));
              }
              else {
                $erreur=mysql_error();
                if ($_POST['sexe']=='M') $accord =''; else $accord='e';
                if (substr($erreur,0,8)=="Duplicat") { $erreur = " Oups, Vous �tes d�ja inscrit".$accord." pour cette course !"; }
              }
     }
	 
}
//------------------------------------------------------------------------------------------------------------
@mysql_close();

// fonction pour d�terminer la cat�gorie FFA
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

// fonction pour tester la cat�gorie autoris� sur une �preuve
// si l'age de l'engag� est dans une cat�gorie autoris�e la fct revoie TRUE

  function test_cat($annee,$cat_autorisees,$sexe){
    $age=SAISON-$annee;
    $tableau=explode(",",$cat_autorisees);
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

 // cette fonction donne masculin ou f�minin en fonction en fct du genre
   function get_genre($x){
    if ($x == "m") return "masculin"; 
	elseif ($x == "f") return "f�minin"; else return "";
   }

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
      <head>
      <meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
      <title>Inscription en ligne</title>
      <link href="css/bassin.css" type="text/css" rel="StyleSheet" />
      <script type="text/javascript" src="scripts/swfobject.js"></script>
      <script src="scripts/jquery.js" type="text/javascript"></script>
        <script language="javascript">
         // avec jQuery
        // Quand le dom est charg�
        $(function(){
           // � l'�v�nement "change" de l'�l�ment id competition, on associe la fonction epreuve  .
           $("#competition").change(function(){
               load_epreuve(this.value);
            });
           // � l'�v�nement focus des �l�ments input ayant la class normal, on associe la class focus
           $("input.normal").focus(function(){
	  	$(this).removeClass();
		$(this).addClass("focus");

	     });
          // � l'�v�nement blur des �l�ments input ayant la class focus, on associe la classe normal
	  $("input").blur(function(){
		$(this).removeClass();
		$(this).addClass("normal");

	    });

        });

        // cette fonction lance une requ�te AJAX pour mettre � jour le s�lecteur epreuve
        function load_epreuve(code) {
        $("#loader").show();   // affiche le loader
        $.post("ajax_epreuve.php", { competition: code },
                  function(code_html){
                     $("#loader").hide();   //cache le loader
                     $("#epreuve").html(code_html);  // ajoute dans l'�l�ment id �preuve le contenu html re�u

                  }
               );
        }
        
        // cette fonction lance une requ�te AJAX pour mettre � jour les infos sur l'engag�(e)
        function licence_ffa(code) {
        $("#loader").show();                     // affiche le loader
        $("input").attr("readonly","readonly");  //verouillage des champs input
		$("#email").attr("readonly","");         // deverouillage du champ email
		$("#tel").attr("readonly","");           // deverouillage du champ tel
        $.post("ajax_licence.php", { nolicence: code.value },
               function(reponse){
                $("#loader").hide();   //efface le loader
                retour = eval('('+reponse+')');    // puis mise � jour des champs input
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

                if (!retour.nom)    $("input").attr("readonly","");  // si pas de nom d�verouillage des champs input
                if (retour.nom)  $("input").focus(function(){
                    $(this).removeClass();
		    $(this).addClass("normal");
                 });
               }
            );
        }

        // fonction pour interdire les caract�res num�riques et ponctuations *+,-./0123456789:;
        function pasNum(e){
          if (window.event) caractere = window.event.keyCode;
               else  caractere = e.which;
                 return (caractere < 33 || caractere > 63) ;
          }
          
        // fonction pour autoriser uniquement les num�riques
        function pasCar(e){
          if (window.event) caractere = window.event.keyCode;
               else  caractere = e.which;
                 return (caractere == 8 || (caractere > 47 && caractere < 58));
          }

        // fonction pour mettre en majuscule
        function majuscule(champ){
        champ.value = champ.value.toUpperCase();
        }
        
        // fonction pour v�rifier les infos avant envoi
        function verif(){
         /*
         if (document.engagement.nolicence.value == ""){
            alert ("donnez votre num�ro de licence FFA ");
            document.engagement.nolicence.focus();
            displayAlert = true;
            return false;
         }
         
         if (document.engagement.noclub.value == ""){
            alert ("donnez le num�ro de votre club ");
            document.engagement.noclub.focus();
            displayAlert = true;
            return false;
         }
         */
         if (document.engagement.nom.value == "") {
            alert ("Indiquez votre nom !!!");
            document.engagement.nom.focus();
            displayAlert = true;
            return false;
         }
         
         if (document.engagement.prenom.value == "") {
            alert ("Indiquez votre pr�nom !!!");
            document.engagement.prenom.focus();
            displayAlert = true;
            return false;
         }

         if (document.engagement.anneenaissance.value == "") {
            alert ("Indiquez votre ann�e de naissance 4 chiffres !!!");
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
        

	


    // demande confirmation � l'utilisateur s'il veut vraiment quitter la page
    // et par cons�quent perdre toutes les donn�es saisie au niveau du formulaire
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


<body topmargin="0" leftmargin="0" style="background-color: #FFFFFF; background-image: url();" >
<div id="page" style="width: 1099px; border-style: dotted; border-width: 1px">
<div id="bandeau_flash" style="width: 1098px; height: 336px;">
<a href="http://www.esr72.fr" title="Retour page d'accueil">
<img style="width: 1098px; height: 336px; border: 0px;" alt="Inscription en ligne" src="images/bandeau_trail.jpg" /></a><br />
</div>

<!--Menu horizontal sous la banniere-->
<div class="contenuArticle" style="text-align: justify;">
<p style="  margin-left: 0;">
   <span style="font-family: 'arial black', 'avant garde'; font-size: 10pt;">
         <a href="../" style="margin-right: 28px; margin-left:28px;">ACCUEIL</a>
         
         <a href="administration/index.php" style="margin-right: 28px;">INTRA</a>
   </span>
</p>
<div class="clear"></div>
</div>
<!-- fin de menu horizontal -->


<div>
<div id="menu" style="width:100px;">
<p></p>
</div>
<div id="contenu">
<h2>Inscription en ligne <?php echo SAISON ?></h2>
<?php if ($erreur) {echo '<p style="color:#FF0000;">'.$erreur."</p>"; } else { echo "<p> </p>"; }?>
<div class="item" style="width:700px;">
<p><b>Vos informations personnelles : </b><span id="loader" style="display:none;"><img src="images/loader.gif"  alt="loader" /></span></p>

<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" name="engagement" onSubmit="return verif();">

      <table style="text-align: left; width: 700px;" border="0" cellpadding="2" cellspacing="2">
        <tbody>
          <tr>
            <td style="text-align:right; width:28%;">Num�ro de licence FFA FFTri :</td>
            <td><input class="normal" name="nolicence"  maxlength="10" onChange="licence_ffa(this)" value="<?php if (isset($_POST['nolicence'])) echo $_POST['nolicence']; ?>"/></td>
            <td style="text-align:right; width:11%;">N� du club : </td>
            <td><input class="normal" name="noclub" onKeyPress="return pasCar(event)" maxlength="6"<?php if (isset($_POST['noclub'])) echo 'value="'.$_POST['noclub'].'" readonly="readonly"'; else echo ' value="" ';?>/></td>

          </tr>
          <tr>
              <td style="text-align:right; width:28%;">Nom : </td>
              <td><input class="normal" name="nom" onKeyPress="return pasNum(event)" onChange="majuscule(this)"  maxlength="20" <?php if (isset($_POST['nom'])) echo 'value="'.$_POST['nom'].'" readonly="readonly"'; ?>/></td>
              <td style="text-align:right; width:11%;">Pr�nom : </td>
              <td><input class="normal" name="prenom" onKeyPress="return pasNum(event)" onChange="majuscule(this)" maxlength="20" <?php if (isset($_POST['prenom'])) echo 'value="'.$_POST['prenom'].'" readonly="readonly"'; ?>/></td>
          </tr>
          <tr>
				<td style="text-align:right; width:28%;">Ann�e de naissance : </td>
				<td><input class="normal" name="anneenaissance" size="4" maxlength="4" onKeyPress="return pasCar(event)" onChange="verif_nais(this)" <?php if (isset($_POST['anneenaissance'])) echo 'value="'.$_POST['anneenaissance'].'" readonly="readonly"'; ?>/>
				</td>
				<td style="text-align:right; width:11%;">Sexe : </td>
				<td><input <?php if (isset($_POST['sexe']) && $_POST['sexe']=='M')echo 'checked="checked"' ?> name="sexe"  value="M" type="radio" />Masculin
					<input <?php if (isset($_POST['sexe']) && $_POST['sexe']=='F')echo 'checked="checked"' ?> name="sexe" value="F" type="radio" />F�minin</td>
          </tr>
		  
		  <tr>
				<td style="text-align:right; width:28%;">Email : </td>
				<td><input class="normal" name="email" id="email" maxlength="50" <?php if (isset($_POST['email'])) echo 'value="'.$_POST['email'].'"'; ?>/>
				</td>
				<td style="text-align:right; width:28%;">TEl :</td>
				<td><input class="normal" name="tel" id="tel" maxlength="10"  <?php if (isset($_POST['tel'])) echo 'value="'.$_POST['tel'].'"'; ?>/>
				</td>
          </tr>
		  
          <tr>
				<td style="text-align:right; width:11%;">Vous souhaitez vous inscrire : </td>
				<td>
					<?php 

					if ($_GET['competition'] ==''&& $_POST['competition']==''){
						echo '<select name="competition" id="competition">';
						echo '<option selected="selected" value="">Choisissez l\'�v�nement\'</option>';
						// connexion � la base de donn�es BASE
						@mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
						@mysql_select_db(BASE) or die("Echec de selection de la base cdt");
						// Lecture de la table competition pour obtenir libell�s et dates  de toutes les comp�titions valid�es
                
						$sql = "SELECT * FROM `competition` WHERE  `validation`='1'";
						$reponse = mysql_query($sql) or die(mysql_error());

						if (mysql_num_rows($reponse)==0) echo "<optgroup label=\"Aucun �v�nement disponible\">";
						if (mysql_num_rows($reponse)==1) echo "<optgroup label=\"�v�nement disponible\">";
						if (mysql_num_rows($reponse)>1) echo "<optgroup label=\"�v�nements disponibles\">";
						while ($competition = mysql_fetch_object ($reponse)){
							echo '<option value="'.$competition->nom.'">'.$competition->nom.' ('.date("j M Y",strtotime($competition->date)).')</option>';
						}
						@mysql_close();
				
				
						// fin de la lecture des competitions
               
						echo '</optgroup>';
						echo '</select>';
					}	
					else
					{
						if (!empty($_GET['competition'])){
							echo "<b>".$_GET['competition']."</b>";
							echo '<input type="hidden" name="competition" value="'.$_GET['competition'].'">';
						}
						if (!empty($_POST['competition'])){
							echo "<b>".$_POST['competition']."</b>";
							echo '<input type="hidden" name="competition" value="'.$_POST['competition'].'">';
						}
					}
					?>
				</td>
				<td colspan="2" id="epreuve">
           <?php 
			echo '<select name="nomcourse">';
            if ($_GET['competition'] =='' && $_POST['competition'] =='')
			{
			  
              echo '<option selected="selected" value="">Choisissez l\'option</option>';
              
			}
			else
			{
			  // connexion � la base 
							@mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
							@mysql_select_db(BASE) or die("Echec de selection de la base cdt");
							// Lecture de la table cross_route_epreuve pour obtenir les d�signations et codes des epreuves
							if ($_GET['competition'] !=''){
							$sql = "SELECT * FROM cross_route_epreuve WHERE `competition`='".$_GET['competition']."'";}
							if ($_POST['competition'] !=''){
							$sql = "SELECT * FROM cross_route_epreuve WHERE `competition`='".$_POST['competition']."'";}
							$reponse = mysql_query($sql) or die(mysql_error());
							while ($epreuve = mysql_fetch_object ($reponse)){
								echo '<option value="'.$epreuve->code.'"';
								echo $_POST['nomcourse']; //
								echo $epreuve->code;  //
								
								if ($_POST['nomcourse']==$epreuve->code) echo ' selected="selected" ';
									echo '>'.$epreuve->designation.'</option>';
								}
							@mysql_close();
							// fin de la lecture des epreuves	
			}
			echo '</select>';
			?>	
           </td>
         </tr>


        <tr>

          <td colspan="4"><br />Pour pouvoir valider votre inscription, vous devez accepter le r�glement suivant :
                          <br />En cochant la case, vous reconnaissez avoir lu et accept� le r�glement de cette �preuve
          </td>
        </tr>
        <tr>
          <td></td>
          <td colspan="3">
          <input type="checkbox" name="reglement" > <b>R�glement de la comp�tition</b>
          <a href="reglement.pdf" target="_blank"> (reglement.pdf)</a>
          </td>
        </tr>
         <tr>
          <td></td>
          <td  style=" width:28%;"><input value="Valider" name="envoyer" type="submit" onclick="alertNotNeeded()"/></td>
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
<div id="pied" style="width: 1090px; color:#FFF;"> Site h�berg� par esr72 - 72000 LE MANS <br />
</div>
</div>
</body>
</html>
