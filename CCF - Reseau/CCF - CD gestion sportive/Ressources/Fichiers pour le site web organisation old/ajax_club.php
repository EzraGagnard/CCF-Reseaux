<?php
 //-----------------------------------------------------
 // script réponse au format JSON à la requete Ajax
 // un seul paramétre noclub (par la méthode POST)
 //-----------------------------------------------------
   require_once('definitions.inc.php');

   if ($_POST['noclub']=="") {
        echo "{}";
        exit;
     };

    // ouverture connexion à la base 
    @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
    @mysql_select_db(BASE) or die("Echec de selection de la base cdt");

       $sql = "SELECT * FROM ffa_club WHERE `noclub`=".$_POST['noclub'];
       $res = mysql_query($sql) or die(mysql_error());
       $club = mysql_fetch_object ($res);



        $reponse = '{';

        if ($club){
               $reponse .= '"club":"'.trim($club->nom).'"';
               $reponse .= ',"sigle":"'.trim($club->sigle).'"';
               $reponse .= ',"noclub":"'.trim($club->noclub).'"';
               }

        $reponse .='}';
        echo $reponse;

@mysql_close();
exit;
?>



