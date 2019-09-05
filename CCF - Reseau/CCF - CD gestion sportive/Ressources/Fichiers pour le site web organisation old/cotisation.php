<?php
//------------------------------------------------------------
// Module pour déterminer le montant de la cotisation
// doit être modifié en fonction des régles adoptées
//------------------------------------------------------------
 
 function prix_engagement($noclub,$nomcourse,$competition,$regle){
   // règle 1 engagement est gratuit pour les licenciés Sarthois
   // les NL FFA n'ont pas de numéro de club
   if ($noclub!="" && $regle==1){
      $dep = substr($noclub,0,3);
      if ($dep=='072') return 0;
   }
   // pour les autres licenciés hors département ou NL
   // le prix de l'engagement est fonction de l'épreuve sélectionnée
   $sql = "SELECT prix FROM `cross_route_epreuve` WHERE `code`='".$nomcourse."' AND `competition`='".$competition."'";

   $reponse = mysql_query($sql);
   $epreuve = mysql_fetch_object ($reponse);
   return $epreuve->prix;
 }
 
 // le prix des prestations complémentaires est mémorisé dans prix de la table compétition
 function prix_cotisation($noclub,$nomcourse,$competition,$nb,$regle){
    // prestation complémentaire (repas transport ...) A finir
    $prestation = 0;
    return $prestation+prix_engagement($noclub,$nomcourse,$competition,$regle);

 }
 
?>
