<?php
//------------------------------------------------------------
// Module pour déterminer le montant de la cotisation
// doit être modifié en fonction des régles adoptées
//------------------------------------------------------------
 
 function prix_engagement($bdd, $id_epreuve){
   // règle 1 engagement est gratuit pour les licenciés Sarthois
   // les NL FFA n'ont pas de numéro de club
  
   $sql = "SELECT prix FROM `cross_route_epreuve` WHERE `id_epreuve`='".$id_epreuve."'";
   $stmt = $bdd->query($sql);
   $epreuve = $stmt->fetchObject();
   return $epreuve->prix;
 }
 
 // le prix des prestations complémentaires est mémorisé dans prix de la table compétition
 function prix_cotisation($bdd, $noclub,$nomcourse,$competition,$nb,$regle){
    // prestation complémentaire (repas transport ...) A finir
    $prestation = 0;
    return $prestation + prix_engagement($bdd, $noclub, $nomcourse, $competition, $regle);

 }
 
?>
