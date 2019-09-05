<?php
 //-------------------------------------------------------
 // script réponse format html aux requêtes Ajax épreuve
 // un seul parametre competition methode post
 //-------------------------------------------------------
   require_once('definitions.inc.php');
   require_once('administration/utile_sql.php');

   $_POST['competition']=utf8_decode($_POST['competition']);
   if ($_POST['competition']=="") {
        echo '<select name="nomcourse"><option selected="selected" value="">Choisissez ...</option></select>';
        exit;
     };

    // ouverture connexion à la base marsouin
    @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
    @mysql_select_db(BASE) or die("Echec de selection de la base cdt");
   
    $reponse = utf8_encode('<select name="nomcourse"><option selected="selected" value="">Choisissez l\'épreuve</option>');
    // course pour les femmes
    $sql = sprintf ("SELECT * FROM cross_route_epreuve WHERE `competition`=%s AND `sexe`='F' ORDER BY `horaire`",
    GetSQLValueString($_POST['competition'], "text")
    );

    $resultat = mysql_query($sql) or die(mysql_error());
    if (mysql_num_rows($resultat)>0){

       $reponse .= "<optgroup label=\"Femmes\">";

       While ($epreuve = mysql_fetch_object ($resultat)){
        $epreuve->designation = utf8_encode ($epreuve->designation);
        $epreuve->code = utf8_encode ($epreuve->code);
        $reponse .= '<option value="'.$epreuve->code.'">'.$epreuve->designation." (".$epreuve->horaire.')</option>';
        }
      $reponse.="</optgroup>";
      }
    // fin des femmes
    // courses pour les hommes
    $sql = sprintf ("SELECT * FROM cross_route_epreuve WHERE `competition`=%s AND `sexe`='M' ORDER BY `horaire`",
    GetSQLValueString($_POST['competition'], "text")
    );

    $resultat = mysql_query($sql) or die(mysql_error());
    if (mysql_num_rows($resultat)>0){
       $reponse .= "<optgroup label=\"Hommes\">";

       While ($epreuve = mysql_fetch_object ($resultat)){
        $epreuve->designation = utf8_encode ($epreuve->designation);
        $epreuve->code = utf8_encode ($epreuve->code);
        $reponse .= '<option value="'.$epreuve->code.'">'.$epreuve->designation.' ('.$epreuve->horaire.')</option>';
        }
       $reponse.="</optgroup>";
    }
    // fin des hommes
    
    // courses mixtes
    $sql = sprintf ("SELECT * FROM cross_route_epreuve WHERE `competition`=%s AND `sexe`='M,F' ORDER BY `horaire`",
    GetSQLValueString($_POST['competition'], "text")
    );

    $resultat = mysql_query($sql) or die(mysql_error());
    if (mysql_num_rows($resultat)>0){

    $reponse .= "<optgroup label=\"Epreuves Mixtes\">";

    While ($epreuve = mysql_fetch_object ($resultat)){
        $epreuve->designation = utf8_encode ($epreuve->designation);
        $epreuve->code = utf8_encode ($epreuve->code);
        $reponse .= '<option value="'.$epreuve->code.'">'.$epreuve->designation.' ('.$epreuve->horaire.')</option>';
        }
    $reponse.="</optgroup>";
    // fin des mixtes
    }
   $reponse .='</select>';
   echo $reponse;

@mysql_close();
exit;
?>



