<?php

		// début de la page bandeau menu horizontal
		@readfile('administration/en_tete.html') or die('Erreur fichier');
?>

		<div id="contenu">
			<br /><br /><br />
			<h3>Les inscriptions sont cloturées</h3>
			<p>Toutefois vous pouvez vous inscrire sur place au plus tard 1 heure avant le début de votre course.<br />
			<a href="index.php">retour vers la page d'accueil</a><br />
			</p>

		</div>

<?php
		// pied de page
		@readfile('administration/pied_de_page.html') or die('Erreur fichier');
?>

