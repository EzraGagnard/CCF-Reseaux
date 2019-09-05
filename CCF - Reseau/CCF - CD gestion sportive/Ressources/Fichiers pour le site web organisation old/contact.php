<?php

// page autoréférente publique
$erreur = "";
if( !empty($_POST['envoyer'])){
   
   /* démarrage la session afin de récupérer la valeur codée */
    session_start();

    /* Nous testons que la variable existe et qu'elle a bien la longueur souhaitée */
    if(!isset($_SESSION['code']) OR strlen($_SESSION['code']) !=5) $erreur="Erreur session !";
    /* Comparaison entre les deux valeurs si elles sont différentes on arrete tout sinon on continue et on envoie le mail */
    if($_SESSION['code'] != $_POST['verif']) $erreur= "Le code de vérification avait une valeur différente !";



   
   $email= "endurance72@wanadoo.fr";
   $emailcc= "philaure@wanadoo.fr";

   if($_POST['objet']=="") $erreur = "il n'y a pas d'objet !";
   $sujet=''.stripslashes($_POST['objet']).'';


   //Headers
   $headers = 'Mime-Version: 1.0'."\n";
   $headers .= 'Content-type: text/html; charset="iso-8859-3"'."\n";
   $headers .= 'From: '.$_POST['expediteur']."\n";
   $headers .= 'Bcc: '.$emailcc."\n";

   //message
   if($_POST['message']=="") $erreur="Votre message est vide !";
   $msg = ''.stripslashes($_POST['message']).'';

   if (!$erreur){
   mail($email,$sujet,$msg,$headers);
   /* on efface et détruit les variables de session */
   session_unset();
   session_destroy();

   // retour vers la page d'accueil
    $updateGoTo = "../";
    header(sprintf("Location: %s", $updateGoTo));
   }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
  <title>Contact Endurance 72</title>
  <link rel=stylesheet href='css/bassin.css' TYPE='text/css'>

<style type="text/css">
    <!--
    tr.ligne {
       height: 45px;
    }
    -->
</style>

<script language="JavaScript" type="text/JavaScript">
<!--
function GoToURL() { //v3.0
  var i, args=GoToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}
function GoToURL_conf() { //v3.0
  var i, args=GoToURL_conf.arguments;
  document.MM_returnValue = false;
  Confirmation = confirm("Confirmez-vous la suppression de cet article?");
  if (Confirmation){
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
  }
}

function verification() {
		 if(document.mail.expediteur.value=="")
                      { alert("Indiquez votre email afin que nous puissions vous répondre !"); return false
                      }
                 if(document.mail.objet.value=="")
                      { alert("l'objet de votre mail est vide !"); return false
                      }
                 if(document.mail.message.value=="")
                          { alert ("Votre message est vide!"); return false
                          }
                 return true

                      }


//-->
</script>
</head>

<body topmargin="0" leftmargin="0">
<script type="text/javascript">
        _editor_url = "xinha/";
        _editor_lang = "fr";

</script>
<script type="text/javascript" src="xinha/XinhaCore.js"></script>

<script type="text/javascript">
    xinha_editors = null;
    xinha_init    = null;
    xinha_config  = null;
    xinha_plugins = null;

    xinha_init = xinha_init ? xinha_init : function()
    {
     xinha_plugins = xinha_plugins ? xinha_plugins :
     [];

     xinha_editors = xinha_editors ? xinha_editors :
      ['message'];

     xinha_config = xinha_config ? xinha_config : new Xinha.Config();
     xinha_config.pageStyle = 'body { font-family: verdana,sans-serif; font-size: 11px; color: #000066 }';
     xinha_editors   = Xinha.makeEditors(xinha_editors, xinha_config, xinha_plugins);
     Xinha.startEditors(xinha_editors);
     window.onload = null;
    }

    window.onload   = xinha_init;
    // window.onunload = HTMLArea.collectGarbageForIE;
</script>
<div id="page">
   <div id="bandeau_flash"><img border="0" src="images/bandeau_E72.jpg" width="1024" height="150">
   </div>
   <div id="nav-horizon">
<ul id="menuDeroulant">
     <li><a href="#">Club</a>
    	<ul class="sousMenu">
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique107">infos express</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique35">A la Une !!</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique2">Le Club</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique3">Les rendez-vous</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique4">Calendrier-Inscriptions</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique62">Formations-Stages-Colloques</a></li>
	</ul>
</li>
 <li><a href="#">Entrainements</a>
          <ul class="sousMenu">
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique31">Infos entrainement</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique108">Tests de VMA club</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique26">Médicaux-paramédicaux</a></li>
	  </ul>
     </li>
    <li><a href="#">Résultats</a>
	<ul class="sousMenu">
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique5">Les résultats</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique46">Bilan hors stade</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique142">Bilan Piste</a></li>

	  </ul>
    </li>
		
     <li><a href="#">Athlètes</a>
         <ul class="sousMenu">
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique1">Athlètes et leurs portraits</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique73">Trombinoscope</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique94">Anniversaire du mois</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique63">Carnet du club</a></li>
	  </ul>
     </li>
     
     <li><a href="#">Partenaires</a>
	<ul class="sousMenu">
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique23">Nos partenaires</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique97">Nos actions partenaires</a></li>
	 </ul>
     </li>

     <li><a href="#">Liens</a>
	<ul class="sousMenu">
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique38">Votre presse sportive</a></li>
		<li><a href="http://www.endurance72.fr/spip/spip.php?rubrique30">Sites favoris</a></li>
	 </ul>

    </li>
    <li><a href="http://www.endurance72.fr/ForumE72/">Forum</a>
    </li>
    <li><a href="http://www.endurance72.fr/store/index.php">Boutique</a>
    </li>
</ul>
</div><!-- fin de menu horizontal -->
<div>

   <div id="menu" style="width: 120px">
   </div>
   <div id="contenu" style="width: 750px; border: solid 1px #A0A0A0; background-color:#F0F0F0;">


        <form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="POST" name="mail" onSubmit="return verification()">
                    <h2><b>N'hésitez pas à nous contacter !</b></h2>
                    <?php if ($erreur) {echo '<p style="color:#FF0000;">'.$erreur."</p>"; } else { echo "<p> </p>"; }?>
                    <table border="0"  style="border-collapse: collapse"   >

                      <tr class="ligne">
                        <td width="30%"  style="text-align: right"><p><b>Votre e-mail :</b></p></td>
                        <td width="70%" >

                            <input name="expediteur"  size="60" <?php if (isset($_POST['expediteur'])) echo 'value="'.$_POST['expediteur'].'"'; ?> /><br />
                            <font color="#000000">Afin que nous puissions vous répondre :</font>
                        </td>
                      </tr class="ligne">


                      <tr class="ligne">
                        <td width="30%" style="text-align: right"><p><b>Objet :</b></p></td>
                        <td width="70%">
                        <input type="text" name="objet" size="60" <?php if (isset($_POST['objet'])) echo 'value="'.$_POST['objet'].'"'; ?> /></td>
                      </tr>
                      <tr>
                        <td width="30%"  valign="top" style="text-align: right"><p><b>Votre message :</b></p></td>
                        <td width="70%">
                        <textarea rows="20" name="message" cols="60" id="message"><?php if (isset($_POST['message'])) echo $_POST['message']; ?></textarea></td>
                      </tr>
                      <tr>
                        <td width="30%"  valign="top" style="text-align: right"><p><b>Vérification :</b></p></td>
                        <td width="70%">
                        <img src="captcha.php" alt="image de sécurisation du formulaire" title="image de sécurisation du formulaire" /><br />
                        <font color="#000000">Merci de recopier la combinaison ci-dessus dans le champ qui suit :</font><br />
                        <input type="text" name="verif" size="10" maxlength="5" />

                      </tr>

                      <tr class="ligne">
                        <td width="30%"  valign="top">
                        &nbsp;</td>
                        <td width="70%" >
                        <input type="submit" value="Envoyer" name="envoyer"></td>
                      </tr>
                    </table>
                </form>




    </div>
    <div id="pied"> Site hébergé par Endurance72 - 2, avenue
    d'HAOUZA - 72100 LE MANS - Tél: 02.43.23.64.18<br />
    </div>

</div>
</body>
