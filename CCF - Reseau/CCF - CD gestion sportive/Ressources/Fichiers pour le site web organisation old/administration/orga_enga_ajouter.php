<?php
//--------------------------------------------------------------------------------------------
// Ce script enregistre un nouvel engagement
// partie Administrateur
// page protégée autoréférente
// Avril 2014 suppression du champ compétition pour afficher uniquement celui en cours
// Avril 2014 ajout des champs noligue nodept et typelicence
// Avril 2014 controle du sexe avec la table des prénoms
// 19 février 2015 ajout du champ tel et des licences tri
//--------------------------------------------------------------------------------------------
// vérification des variables de session pour le temps d'inactivité et de l'adresse IP
include "authentification/authcheck.php" ;
// Vérification des droits pour cette page uniquement organisateurs
if ($_SESSION['droits']<>'2') { header("Location: ../index.html");};
require_once('../definitions.inc.php');
require_once('utile_sql.php');
require_once('../cotisation.php');
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

if( !empty($_POST['envoyer'])){
     
    if ($_POST['nom']=="") {
        echo "Vous devez indiquer un nom";
        exit;
    };
	// test de la concordance entre le prénom et le sexe
	$sql = "SELECT * FROM `prenom` WHERE `prenom` = '".$_POST['prenom']."'";
	$reponse = mysql_query($sql) or die(mysql_error());
    // création de l'objet prénom
	$prenom = mysql_fetch_object ($reponse);
	
	If (($_POST['sexe']=='M' && $prenom->genre=='f')||($_POST['sexe']=='F' && $prenom->genre=='m')) {
		echo "Erreur : ".$_POST['prenom']." est ".$prenom->genre;
		exit;
	};	
	
        
//----------- Création de l'objet club  ---------------
	if ($_POST['noclub']=="") { 
		$_POST['noclub']= "00000";
    }
    else {
        $sql = "SELECT * FROM `ffa_club` WHERE `noclub`=".$_POST['noclub']." LIMIT 0, 30 ";
		
        $reponse = mysql_query($sql) or die(mysql_error());
        $club = mysql_fetch_object ($reponse);
    };

//------------Création de l'objet licencie ------------

    if ($_POST['nolicence']) {
		$sql = sprintf("SELECT typelicence FROM  `ffa_licence` WHERE `nolicence`=%s",
           GetSQLValueString($_POST['nolicence'], "int")
        );
        $reponse = mysql_query($sql);
        $licencie =  mysql_fetch_object ($reponse);
	} 	
//------------------------------------------------------
	// recherche du dernier n° de dossard attribué
	$sql= "SELECT MAX(`dossard`) AS high_dossard FROM cross_route_engagement Where `competition`= \"".$_POST['competition']."\"";
	$reponse = mysql_query($sql) or die(mysql_error());
	$dossard = mysql_fetch_object ($reponse);
//------------------------------------------------------	
    if ($_POST['mode']=="insertion"){
        $insertSQL = sprintf("INSERT INTO cross_route_engagement (date,dossard,competition,nolicence,nom,prenom,noclub,typeparticipant,sexe,nodept,noligue,anneenaissance,categorie,nomcourse,nomequipe,adresse1,codepostal,ville,email,typelicence,certifmedicalfourni,cotisationpaye,commentaire,paiement,tel) VALUES (CURRENT_TIMESTAMP,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
            GetSQLValueString(($dossard->high_dossard+1), "int"),
			GetSQLValueString($_POST['competition'], "text"),
            GetSQLValueString($_POST['nolicence'], "text"),
            GetSQLValueString($_POST['nom'], "text"),
            GetSQLValueString($_POST['prenom'], "text"),
            GetSQLValueString($_POST['noclub'], "int"),
            GetSQLValueString($_POST['typeparticipant'], "text"),
            GetSQLValueString($_POST['sexe'], "text"),
			GetSQLValueString($club->noclub, "text"),
			GetSQLValueString($club->ligue, "text"),
            GetSQLValueString($_POST['anneenaissance'], "int"),
            GetSQLValueString(cat_ffa($_POST['anneenaissance'],$_POST['sexe']), "text"),
            GetSQLValueString($_POST['nomcourse'], "text"),
            GetSQLValueString($_POST['nomequipe'], "text"),
            GetSQLValueString($_POST['adresse1'], "text"),
            GetSQLValueString($_POST['codepostal'], "text"),
            GetSQLValueString($_POST['ville'], "text"),
            GetSQLValueString($_POST['email'], "text"),
			GetSQLValueString($licencie->typelicence, "text"),
            GetSQLValueString($_POST['certifmedicalfourni'] , "text"),
            GetSQLValueString($_POST['cotisationpaye'] , "text"),
            GetSQLValueString($_POST['commentaire'], "text"),
			GetSQLValueString($_POST['paiement'] , "text"),
			GetSQLValueString($_POST['tel'], "text")
	    );
		$Result1 = mysql_query($insertSQL) or die(mysql_error());
    }


    @mysql_close();

    $GoTo = "orga_menu.php";
    header(sprintf("Location: %s", $GoTo));
}
    
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

// début du fichier bandeau menu horizontal
  if (!is_readable('en_tete.html'))  die ("fichier non accessible");
  @readfile('en_tete.html') or die('Erreur fichier');
?>


<script language="javascript">
// avec jQuery
// Quand le dom est chargé
        $(function(){
        // à l'évènement "change" de l'élément id competition, on associe la fonction epreuve  .
           $("#competition").change(function() {
               load_epreuve(this.value);
            });


        });

        // cette fonction lance une requête AJAX pour mettre à jour le sélecteur epreuve
        function load_epreuve(code) {
        $("#loader").show();   // affiche le loader
        $.post("../ajax_epreuve.php", { competition: code },
                  function(code_html){
                     $("#loader").hide();   //cache le loader
                     $("#epreuve").html(code_html);  // ajoute dans l'élément id épreuve le contenu html reçu

                  }
               );
        }
        
// cette fonction lance une requête AJAX pour mettre à jour les infos sur l'engagé(e)
        function licence_ffa(code) {
        $("#loader").show();   // affiche le loader
        $.post("../ajax_licence.php", { nolicence: code.value },
               function(reponse){
                $("#loader").hide();   //cache le loader
                retour = eval('('+reponse+')');
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
                if(retour.club)   document.engagement.nomequipe.value=retour.club; else document.engagement.nomequipe.value="";
               }
            );
        }
// cette fonction lance une requête AJAX pour mettre à jour les infos club
         function club_ffa(champ){
         $("#loader").show();   // affiche le loader
         $.post("../ajax_club.php", { noclub: champ.value },
              function(reponse){
                 $("#loader").hide();   //cache le loader
                 retour = eval('('+reponse+')');
                 if(retour.club)
                document.engagement.nomequipe.value=retour.club; else document.engagement.nomequipe.value="";
              }
          );
         }



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
        
        // fonction pour vérifier la date de naissance
        function verif_nais(champ){
        if (champ.value<1930 || champ.value>2007){
            alert("Année de naissance sur 4 chiffres\net comprise entre 1930 et 2007");
            champ.value = "";
            champ.focus();
          }
        }

        // fonction pour vérifier les infos avant enregistrement
        function verif(){

         if (document.engagement.nom.value == "") {
            alert ("Nom : Le champ est obligatoire, il doit être renseigné");
            document.engagement.nom.focus();
            return false;
         }
         if (document.engagement.prenom.value == ""){
            alert ("Prénom : Le champ est obligatoire, il doit être renseigné ");
            document.engagement.nolicence.focus();
            return false;
         }
         if (document.engagement.nomcourse.value == ""){
            alert ("Vous devez choisir une course ");
            document.engagement.nolicence.focus();
            return false;
         }
         if ((document.engagement.nolicence.value == "") && (document.engagement.anneenaissance.value == "")){
            alert ("Pour les non-licenciés \nvous devez indiquez l'année de naissance");
            return false;
         }
         // boucle pour rechercher le type d'engagement
         for (var i=0; i<document.engagement.typeengagement.length; i++) {
         if (document.engagement.typeengagement[i].checked) {
            type = document.engagement.typeengagement[i].value;
            }
         }
         if ((type == "E")&& (document.engagement.nolicence.value == "")&&(document.engagement.nomequipe.value == "")){
            alert ("Pour les non-licenciés engagés dans une équipe \nvous devez donner le nom de l'équipe");
            return false;
         }
         if ((type == "I")&& (document.engagement.nomequipe.value != "")){
            alert ("Pour s'engager au nom d'une équipe, \nvous devez cocher entreprise ou militaire");
            return false;
         }
        }
        

	



  </script>

<div id="menu" style="width:100px;">
</div>
<div id="contenu">
    <h2>Engagement </h2>
    <div class="item" style="width:800px" >
		<p><b>informations engagé(e) : </b><span id="loader" style="display:none;"><img src="../images/loader.gif"  alt="loader" /></span></p>
		<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>"  name="engagement" onSubmit="return verif();">
			<input type="hidden" name="mode" value="insertion" />
			<input type="hidden" name="competition" value="<?php echo $_GET['competition']; ?>" />
			<input type='hidden' name='dep' />
			<table style="text-align: left; width: 780px; height: 160px;"   border="0" cellpadding="2" cellspacing="2">
				<tbody>
					<tr>
						<td>N° licence</td>
						<td><input name="nolicence"  onChange="licence_ffa(this)" /></td>
						<td>N° club</td>
						<td><input name="noclub" onKeyPress="return pasCar(event)" onChange="club_ffa(this)" /></td>
					</tr>
					<tr>
						<td>Nom</td>
						<td><input name="nom" onKeyPress="return pasNum(event)" onChange="majuscule(this)"/></td>
						<td>Prénom</td>
						<td><input name="prenom" onKeyPress="return pasNum(event)" /></td>
					</tr>
					<tr>
						<td>Année de naissance</td>
						<td><input name="anneenaissance" size="4" onKeyPress="return pasCar(event)" onChange="verif_nais(this)"/></td>
						<td>Sexe</td>
						<td>
							<input  name="sexe"  value="M" type="radio" checked="checked" />Masculin 
							<input  name="sexe" value="F" type="radio" />Féminin
						</td>
					</tr>
					
					<tr>
						<td>Adresse</td>
						<td colspan="3"><input name="adresse1" size="60" /></td>
					</tr>
					<tr>
						<td>Code Postal</td>
						<td><input name="codepostal" onKeyPress="return pasCar(event)"/></td>
						<td>Ville</td>
						<td><input name="ville" onKeyPress="return pasNum(event)"/></td>
					</tr>
					<tr>
						<td>Tel</td>
						<td><input name="tel" size="16" /></td>
						
						<td>Email</td>
						<td><input name="email" size="50" onChange="testMail(this)"/></td>
						
					</tr>
					<tr>
						<td>Compétition :</td>
						<td colspan="2" style='font-size: 14pt; font-weight: bold;'><?php echo $_GET['competition']; ?></td>
						<td id="epreuve">
							<select name="nomcourse">
							<?php
							// connexion à la base marsouin
							@mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
							@mysql_select_db(BASE) or die("Echec de selection de la base cdt");
							// Lecture de la table cross_route_epreuve pour obtenir les désignations et codes des epreuves
							$sql = "SELECT * FROM cross_route_epreuve WHERE `competition`='".$_GET['competition']."'";
							$reponse = mysql_query($sql) or die(mysql_error());
							while ($epreuve = mysql_fetch_object ($reponse)){
								echo '<option value="'.$epreuve->code.'"';
								if ($engagement->nomcourse == $epreuve->code) echo ' selected="selected" ';
									echo '>'.$epreuve->designation.'</option>';
								}
							@mysql_close();
							// fin de la lecture des epreuves
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="1">Inscription au challenge :</td>
						<td colspan="3">
							<input checked="checked" name="typeparticipant" value="" type="radio" />Individuel
							<input name="typeparticipant" value="ffa" type="radio" />Club
							<input name="typeparticipant" value="Ent" type="radio" />Entreprise
						</td>
					</tr>
					<tr>
						<td>Nom de l'équipe :</td>
						<td colspan="3"><input name="nomequipe" value=""/></td>
					</tr>
					<tr>
						<td>Commentaire :</td>
						<td colspan="3"><input name="commentaire" value="" size="50" /></td>
					</tr>
					<tr>
						<td>Certificat médical :</td>
						<td colspan="3">
							<input  name="certifmedicalfourni" value="oui" type="radio" />OUI
							<input  checked='checked' name="certifmedicalfourni" value="non" type="radio" />NON
						</td>
					</tr>
					<tr>
						<td>Cotisation payée :</td>
						<td colspan="3">
							<input  name="cotisationpaye" value="oui" type="radio" />OUI
							<input  checked='checked' name="cotisationpaye" value="non" type="radio" />NON
						</td>
					</tr>
					<tr>
						<td >Mode de paiement :</td>
						<td colspan="3">
						<input  name="paiement" value="" type="radio" checked='checked'/>En attente
						<input  name="paiement" value="chèque" type="radio" />Chèque
						<input  name="paiement" value="paypal" type="radio" />Paypal
						<input  name="paiement" value="espèces" type="radio" />Espèces
						</td>
					</tr>
					<tr>
						<td><input name="envoyer" value="Valider"  type="submit" /></td>
						<td colspan="3"></td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
</div>
</div>
<div id="pied"> Site hébergé par ESR72 - <br />
</div>
</div>
</body></html>
