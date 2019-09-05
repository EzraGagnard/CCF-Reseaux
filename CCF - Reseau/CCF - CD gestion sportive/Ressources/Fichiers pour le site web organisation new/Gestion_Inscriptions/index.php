<?php

		// début de la page bandeau menu horizontal
		@readfile('administration/en_tete.html') or die('Erreur fichier');
?>

   	<div id="contenu">

		<h2>Organisateurs</h2>
		<p>Un outil sur-mesure pour les organisateurs de manifestations sportives
		Formulaires d'inscription, gestion des participants, paiement en ligne
		La souplesse  permet d’utiliser le système pour différents types d’événements sportifs, 
		de collecter des inscriptions avec paiement par CB mais aussi de gérer facilement les participants !</p>
		
		<p>
		Vous êtes organisateurs d’une course à obstacles, d’un trail ou d’une randonnée ? Fini la vérification des certificats médicaux jusqu’à 4h du matin la veille de la course ! Terminé la transformation du salon en pièce d’Etat major qui croule sous les dossiers : On vous simplifie la gestion des coureurs et on vous aide à booster les inscriptions.
        </p>
		
		<h2>Participants</h2>
		<p>Les participants s'inscrivent facilement
		Nul besoin de leur faire créer un compte : les participants s’inscrivent facilement à votre course, depuis leur ordinateur, tablette ou smartphone !</p>
    </div>
	
<?php
     // pied de page
	 @readfile('administration/pied_de_page.html') or die('Erreur fichier');
?>

