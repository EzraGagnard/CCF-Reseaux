<?php
 //-------------------------------------------------------
 // script réponse format html aux requètes Ajax épreuve
 // un seul parametre competition methode post
 //-------------------------------------------------------
   require_once('definitions.inc.php');
   require_once('administration/utile_sql.php');

   
   if ($_POST['competition']=="") {
        echo '<select name="id_epreuve"><option selected="selected" value="">Choisissez ...</option></select>';
        exit;
     };

    // ouverture connexion à la base
    $bdd = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);
   
    $reponse = '<select name="id_epreuve"><option selected="selected" value="">Choisissez l\'épreuve</option>';
    // course pour les femmes
    $sql = sprintf ("SELECT * FROM cross_route_epreuve WHERE `competition`=%s AND `sexe_autorise`='F' ORDER BY `horaire`",
    GetSQLValueString($_POST['competition'], "text")
    );
	
    //$resultat = mysql_query($sql) or die(mysql_error());
	$stmt = $bdd->query($sql);
	if ($stmt->rowCount() > 0){

       $reponse .= "<optgroup label=\"Femmes\">";

       While ($epreuve = $stmt->fetchObject()){
        
        $reponse .= '<option value="'.$epreuve->id_epreuve.'">'.$epreuve->designation." (".$epreuve->horaire.')</option>';
        }
      $reponse.="</optgroup>";
      }
    // fin des femmes
    // courses pour les hommes
    $sql = sprintf ("SELECT * FROM cross_route_epreuve WHERE `competition`=%s AND `sexe_autorise`='M' ORDER BY `horaire`",
    GetSQLValueString($_POST['competition'], "text")
    );

    
	$stmt = $bdd->query($sql);
	
    if ($stmt->rowCount() > 0){
       $reponse .= "<optgroup label=\"Hommes\">";

       While ($epreuve = $stmt->fetchObject() ){
        $reponse .= '<option value="'.$epreuve->id_epreuve.'">'.$epreuve->designation.' ('.$epreuve->horaire.')</option>';
        }
       $reponse.="</optgroup>";
    }
    // fin des hommes
    
    // courses mixtes
    $sql = sprintf ("SELECT * FROM cross_route_epreuve WHERE `competition`=%s AND `sexe_autorise`='M,F' ORDER BY `horaire`",
    GetSQLValueString($_POST['competition'], "text")
    );

   	$stmt = $bdd->query($sql);
	
    if ($stmt->rowCount() > 0){

    $reponse .= "<optgroup label=\"Epreuves Mixtes\">";

    While ($epreuve = $stmt->fetchObject()){
        $reponse .= '<option value="'.$epreuve->id_epreuve.'">'.$epreuve->designation.' ('.$epreuve->horaire.')</option>';
        }
    $reponse.="</optgroup>";
    // fin des mixtes
    }
   $reponse .='</select>';
   echo $reponse;


exit;
?>



