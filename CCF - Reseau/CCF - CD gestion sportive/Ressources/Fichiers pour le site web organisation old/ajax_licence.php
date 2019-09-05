<?php
 // script réponse au format JSON à la requete Ajax
 // un seul paramétre nolicence (par la méthode POST)
   require_once('definitions.inc.php');
   

   if ($_POST['nolicence']=="") {
        echo "{}";
        exit;
     };

    // ouverture connexion à la base marsouin
    @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
    @mysql_select_db(BASE) or die("Echec de selection de la base cdt");
   
    $sql = "SELECT * FROM ffa_licence WHERE `nolicence`='".$_POST['nolicence']."'";
    $resultat = mysql_query($sql) or die(mysql_error());
    $licencie = mysql_fetch_object ($resultat);


      // si la recherche est fructueuse on recherche le nom du club
      if ($licencie) {
       $sql = "SELECT * FROM ffa_club WHERE `noclub`=".$licencie->noclub;
       $res = mysql_query($sql) or die(mysql_error());
       $club = mysql_fetch_object ($res);

      }

        $reponse = '{';
        if ($licencie){
        $reponse .= '"nom":"'.trim($licencie->nom).'"';
        $reponse .= ',"prenom":"'.trim($licencie->prenom).'"';
        $reponse .= ',"annee":"'.substr($licencie->date,0,4).'"';
        $reponse .= ',"sexe":"'.trim($licencie->sexe).'"';

        if ($club){
               $reponse .= ',"club":"'.trim($club->nom).'"';
               $reponse .= ',"sigle":"'.trim($club->sigle).'"';
               $reponse .= ',"noclub":"'.trim($club->noclub).'"';
               }
        }
        $reponse .='}';
        echo $reponse;

@mysql_close();
exit;
?>



