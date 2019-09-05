<?php
// Ce script enregistre les modifications de l'engagement d'un coureur
// par exemple pour ajouter des informations 
// mettre à jour son paiement etc... 
// seul les epreuves de la compétition en cours sont affichées mars 2014
// ajout du champ Numéro de dossard
// ajout du champ Invité (pour gérer les dossards donnés)
// 19 février 2015 ajout du champ téléphone
// page autoréférente
//--------------------------------------------------------------------
	require_once('../definitions.inc.php');
	require_once('utile_sql.php');

	// connexion à la base de données
	$bdd = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);
  
	// lecture de la configuration et définition des constantes ENABLE SAISON DATE DESIGNATION etc
    $sql = 'SELECT * FROM `cross_route_configuration`';
    $stmt = $bdd->query($sql);  
    while ($conf = $stmt->fetchObject()){
		define($conf->conf_key, $conf->conf_value);
    }
	// fin de la lecture de la configuration  

    if (!empty($_POST['envoyer'])){
		$sql = sprintf("UPDATE cross_route_engagement SET nolicence=%s, nom=%s , prenom=%s , noclub=%s,  anneenaissance=%s ,categorie=%s, sexe=%s , nomequipe=%s , typeparticipant=%s , nomcourse=%s , adresse1=%s , codepostal=%s , ville=%s , certifmedicalfourni=%s, cotisationpaye=%s, invite=%s, email=%s, commentaire=%s, dossard=%s, paiement=%s, tel=%s WHERE id=%s",
			
			GetSQLValueString($_POST['nolicence'], "text"),
			GetSQLValueString($_POST['nom'], "text"),
			GetSQLValueString($_POST['prenom'] , "text"),
			GetSQLValueString($_POST['noclub'] , "text"),
			GetSQLValueString($_POST['anneenaissance'] , "text"),
			GetSQLValueString(cat_ffa($_POST['anneenaissance'],$_POST['sexe']), "text"),
			GetSQLValueString($_POST['sexe'] , "text"),
			GetSQLValueString($_POST['nomequipe'] , "text"),
			GetSQLValueString($_POST['typeparticipant'] , "text"),
			GetSQLValueString($_POST['nomcourse'] , "text"),
			GetSQLValueString($_POST['adresse1'] , "text"),
			GetSQLValueString($_POST['codepostal'] , "int"),
			GetSQLValueString($_POST['ville'] , "text"),
			GetSQLValueString($_POST['certifmedicalfourni'] , "text"),
			GetSQLValueString($_POST['cotisationpaye'] , "text"),
			GetSQLValueString($_POST['invite'] , "text"),
			GetSQLValueString($_POST['email'] , "text"),
			GetSQLValueString($_POST['commentaire'] , "text"),
			GetSQLValueString($_POST['dossard'] , "text"),
			GetSQLValueString($_POST['paiement'] , "text"),
			GetSQLValueString($_POST['tel'] , "text"),
			GetSQLValueString($_POST['id'], "int")
		);
        $stmt = $bdd->query($sql);
		
		// retour à la page tableau des engagements
		$GoTo = "orga_tab_enga.php?&competition=".stripslashes($_GET['competition']);
		header(sprintf("Location: %s", $GoTo));
    }

	// recherche des infos en fonction de id de l'engagé à modifier

    if ((isset($_GET['id'])) && ($_GET['id'] != "")) {
			$sql = "SELECT * FROM cross_route_engagement WHERE id=".$_GET['id']."";
			$stmt = $bdd->query($sql);
			$engagement = $stmt->fetchObject();
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

        // fonction pour interdire les caract�res num�riques
        function pasNum(e){
          if (window.event) caractere = window.event.keyCode;
               else  caractere = e.which;
                 return (caractere < 48 || caractere > 57);
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

        // fonction pour v�rifier la date de naissance
        function verif_nais(champ){
        if (champ.value<1930 || champ.value>2007){
            alert("Ann�e de naissance sur 4 chiffres\net comprise entre 1930 et 2007");
            champ.value = "";
            champ.focus();
          }
        }

        // fonction pour v�rifier les infos avant enregistrement
        function verif(){

         if (document.engagement.nom.value == "") {
            alert ("Nom : Le champ est obligatoire, il doit �tre renseign�");
            document.engagement.nom.focus();
            return false;
         }

         if (document.engagement.nomcourse.value == ""){
            alert ("Vous devez choisir une course ");
            document.engagement.nolicence.focus();
            return false;
         }
         if ((document.engagement.nolicence.value == "") && (document.engagement.anneenaissance.value == "")){
            alert ("Pour les non-licenci�s \nvous devez indiquez l'ann�e de naissance");
            return false;
         }
         // boucle pour rechercher le type d'engagement
         for (var i=0; i<document.engagement.typeengagement.length; i++) {
         if (document.engagement.typeengagement[i].checked) {
            type = document.engagement.typeengagement[i].value;
            }
         }
         if ((type == "E")&& (document.engagement.nolicence.value == "")&&(document.engagement.nomequipe.value == "")){
            alert ("Pour les non-licenci�s engag�s dans une �quipe \nvous devez donner le nom de l'�quipe");
            return false;
         }
         if ((type == "I")&& (document.engagement.nomequipe.value != "")){
            alert ("Pour s'engager au nom d'une �quipe, \nvous devez cocher entreprise ou militaire");
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
		else { // XMLHttpRequest non support� par le navigateur
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
               if(retour.club){
                document.engagement.typeparticipant[0].checked=false;
                document.engagement.typeparticipant[1].checked=true;
                document.engagement.typeparticipant[2].checked=false;
                document.engagement.typeparticipant[3].checked=false;
               }
               else{
                document.engagement.typeparticipant[0].checked=true;
                document.engagement.typeparticipant[1].checked=false;
                document.engagement.typeparticipant[2].checked=false;
                document.engagement.typeparticipant[3].checked=false;
               }
               if(retour.club)
                document.engagement.nomequipe.value=retour.club; else document.engagement.nomequipe.value="";
               if(retour.noclub) {

                document.engagement.noclub.value=retour.noclub;
               }
               else { document.engagement.dep.value="";
                      document.engagement.noclub.value="";
               }
               if (retour.nom) {
                document.engagement.certifmedicalfourni[0].checked=true;
                document.engagement.certifmedicalfourni[1].checked=false;
               }
               else {
                document.engagement.certifmedicalfourni[0].checked=false;
                document.engagement.certifmedicalfourni[1].checked=true;
               }
            }
         }
         xhr.open("POST","../ajax_licence.php",true);
         xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
         xhr.send("nolicence="+champ.value);
	}

    function club_ffa(champ){
	 getXhr();
	 xhr.onreadystatechange = function(){
            if(xhr.readyState == 4 && xhr.status == 200){
            retour = eval('('+xhr.responseText+')');

               if(retour.club)
                document.engagement.nomequipe.value=retour.club; else document.engagement.nomequipe.value="";

            }
         }
         xhr.open("POST","../ajax_club.php",true);
         xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
         xhr.send("noclub="+champ.value);
	}

  </script>


	<div id="contenu" style="width: 90%;">
		<h2>Modification Engagement</h2>
		<div class="item">
			<p>informations engagé(e) : </p>
			<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']."?&competition=". $_GET['competition'] ?>"  name="engagement" onSubmit="return verif();">
				<input type="hidden" name="mode" value="update" />
				<input type='hidden' name='id' value='<?php echo $_GET['id']; ?>'/>
				<table style="text-align: left; width: 100%; height: 160px;"   border="0" cellpadding="2" cellspacing="2">
					<tbody>
						<tr>
							<td>N° licence</td>
							<td><input name="nolicence" value="<?php echo $engagement->nolicence; ?>"/></td>
							<td>N° du club</td>
							<td><input name="noclub" value="<?php echo $engagement->noclub; ?>" onKeyPress="return pasCar(event)" onChange="club_ffa(this)"/></td>
						</tr>
						<tr>
							<td>Nom</td>
							<td><input name="nom" value="<?php echo $engagement->nom; ?>" onKeyPress="return pasNum(event)" onChange="majuscule(this)" required/></td>
							<td>Prénom</td>
							<td><input name="prenom" value="<?php echo $engagement->prenom; ?>" onKeyPress="return pasNum(event)" required/></td>
						</tr>
						<tr>
							<td>Année de naissance</td>
							<td><input name="anneenaissance" size="4" value="<?php echo $engagement->anneenaissance; ?>" onKeyPress="return pasCar(event)" onChange="verif_nais(this)" required/>
							</td>
							<td>Sexe</td>
							<td><input  name="sexe"  value="M" type="radio" <?php if ($engagement->sexe == "M") echo "checked='checked'"; ?> />Masculin 
								<input  name="sexe" value="F" type="radio" <?php if ($engagement->sexe == "F") echo "checked='checked'"; ?>/>Féminin
							</td>
						</tr>
						<tr>
							<td>Tel</td>
							<td><input name="tel" value="<?php echo $engagement->tel; ?>" size="12" required/></td>
							<td>Email</td>
							<td><input name="email" value="<?php echo $engagement->email; ?>" size="50" onChange="testMail(this)" required/></td>
							
						</tr>
						<tr>
							<td>Adresse</td>
							<td colspan="3"><input name="adresse1" value="<?php echo $engagement->adresse1; ?>" size="60" /></td>
						</tr>
						<tr>
							<td>Code Postal</td>
							<td><input name="codepostal" value="<?php echo $engagement->codepostal; ?>" onKeyPress="return pasCar(event)"/></td>
							<td>Ville</td>
							<td><input name="ville" value="<?php echo $engagement->ville; ?>" onKeyPress="return pasNum(event)"/></td>
						</tr>
						<tr>
							<td>Course :</td>
							<td>
								<select name="nomcourse">
								<?php
									// connexion à la base 
									$bdd2 = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);
									// table cross_route_epreuve pour obtenir les désignations et codes des epreuves
									$sql = "SELECT * FROM cross_route_epreuve WHERE `id_competition`='" . $_GET['competition'] . "'";
									//$sql = 'SELECT * FROM `cross_route_epreuve`';
									$stmt = $bdd2->query($sql);
									while ($epreuve = $stmt->fetchObject()){
										echo '<option value="'.$epreuve->code.'"';
										if ($engagement->nomcourse == $epreuve->code) echo ' selected="selected" ';
											echo '>'.$epreuve->designation.'</option>';
									}
										  
									// fin de la lecture des epreuves
								?>
								</select>
							</td>
							<td>Dossard</td>
							<td>
								<input  style="font-size: 14pt; font-weight: bold;" size="5" name="dossard" value="<?php echo $engagement->dossard; ?>" onKeyPress="return pasCar(event)"/>
							</td>
						</tr>
						<tr>
							<td >Challenge :</td>
							<td colspan="3">
								<input <?php if ($engagement->typeparticipant == "") echo "checked='checked'"; ?> name="typeparticipant" value="" type="radio" />Individuel
								<input <?php if ($engagement->typeparticipant == "ffa") echo "checked='checked'"; ?> name="typeparticipant" value="ffa" type="radio" />ffa
								<input <?php if ($engagement->typeparticipant == "Ent") echo "checked='checked'"; ?> name="typeparticipant" value="Ent" type="radio" />Entreprise
							</td>
						</tr>
						<tr>
							<td>Nom de l'équipe :</td>
							<td colspan="3">
								<input name="nomequipe" value="<?php echo $engagement->nomequipe; ?>"/>
							</td>
						</tr>
						<tr>
							<td>Commentaire :</td>
							<td colspan="3">
								<input name="commentaire" value="<?php echo $engagement->commentaire; ?>"/>
							</td>
						</tr>
						<tr>
							<td>Certificat médical :</td>
							<td colspan="3">
								<input <?php if ($engagement->certifmedicalfourni == "oui") echo "checked='checked'"; ?> name="certifmedicalfourni" value="oui" type="radio" />OUI
								<input <?php if ($engagement->certifmedicalfourni == "non") echo "checked='checked'"; ?> name="certifmedicalfourni" value="non" type="radio" />NON
							</td>
						</tr>
						<tr>
							<td>Cotisation payée :</td>
							<td>
							   <input <?php if ($engagement->cotisationpaye == "oui") echo "checked='checked'"; ?> name="cotisationpaye" value="oui" type="radio" />OUI
							   <input <?php if ($engagement->cotisationpaye == "non") echo "checked='checked'"; ?> name="cotisationpaye" value="non" type="radio" />NON
							</td>
							<td>Invité :</td>
							<td>
							   <input <?php if ($engagement->invite == "oui") echo "checked='checked'"; ?> name="invite" value="oui" type="radio" />OUI
							   <input <?php if ($engagement->invite == "non") echo "checked='checked'"; ?> name="invite" value="non" type="radio" />NON
							</td>
						</tr>
						<tr>
							<td >Mode de paiement :</td>
							<td colspan="3">
							   <input <?php if ($engagement->paiement == "") echo "checked='checked'"; ?> name="paiement" value="" type="radio" />En attente
							   <input <?php if ($engagement->paiement == "chèque") echo "checked='checked'"; ?> name="paiement" value="chèque" type="radio" />Chèque
							   <input <?php if ($engagement->paiement == "paypal") echo "checked='checked'"; ?> name="paiement" value="paypal" type="radio" />Paypal
							   <input <?php if ($engagement->paiement == "espèces") echo "checked='checked'"; ?> name="paiement" value="espèces" type="radio" />Espèces
							</td>
						</tr>
						<tr>
							<td><input name="envoyer" value="Valider"  type="submit" /></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>
	</div>

	<?php
			@readfile('pied_de_page.html') or die('Erreur fichier');
	?>
</div>
</body>
</html>
