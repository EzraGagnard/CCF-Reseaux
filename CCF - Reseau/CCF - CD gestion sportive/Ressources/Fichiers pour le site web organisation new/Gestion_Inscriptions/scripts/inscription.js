			// avec jQuery
			// Quand le dom est chargé
			$(function(){
			   //  à l'évènement "change" de l'élément id competition, on associe la fonction epreuve  .
			   $("#competition").change(function(){
				   load_epreuve(this.value);
				});
			   // à l'évènement focus des éléments input ayant la class normal, on associe la class focus
			   $("input.normal").focus(function(){
					$(this).removeClass();
					$(this).addClass("focus");
				});
				// à l'évènement blur des éléments input ayant la class focus, on associe la classe normal
				$("input").blur(function(){
					$(this).removeClass();
					$(this).addClass("normal");

				});

			});

			// cette fonction lance une requète AJAX pour mettre à jour le sélecteur epreuve
			function load_epreuve(code) {
				$("#loader").show();   // affiche le loader
				$.post("ajax_epreuve.php", { competition: code },
					function(code_html){
						$("#loader").hide();   //cache le loader
						$("#epreuve").html(code_html);  // ajoute dans l'élément id épreuve le contenu html reçu
					}
				);
			}
        
			// cette fonction lance une requète AJAX pour mettre à jour les infos sur l'engagé(e)
			function licence_ffa(code) {
				$("#loader").show();                     // affiche le loader
				$.post("ajax_licence.php", { nolicence: code.value },
				function(reponse){
					$("#loader").hide();   	//efface le loader
					retour = eval('('+reponse+')');    // puis mise à jour des champs input
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
					if(retour.noclub) 
						document.engagement.noclub.value=retour.noclub; else document.engagement.noclub.value="";

					if (retour.nom)  $("input").focus(function(){
						$(this).removeClass();
						$(this).addClass("normal");
					});
				}
				);
			}

			// fonction pour interdire les caractères numériques et ponctuations *+,-./0123456789:;
			function pasNum(e){
			  if (window.event) caractere = window.event.keyCode;
				   else  caractere = e.which;
						return (caractere < 33 || caractere > 63) ;
			}
          
			// fonction pour autoriser uniquement les numériques
			function pasCar(e){
			  if (window.event) caractere = window.event.keyCode;
				   else  caractere = e.which;
					 return (caractere == 8 || (caractere > 47 && caractere < 58));
			}

			// fonction pour mettre en majuscule
			function majuscule(champ){
				champ.value = champ.value.toUpperCase();
			}
            
			// demande confirmation à l'utilisateur s'il veut vraiment quitter la page
			// sans passer par le bouton submit
			// et par conséquent perdre toutes les données saisie au niveau du formulaire
			var displayAlert = false;
			window.onbeforeunload = function(e){
			   if (displayAlert) {
				return 'En fermant cette page vous perdrez les informations saisies.';
			   }
			}
			
			// fonction pour désactiver les alertes quand l'utilisateur clique sur submit
			// à utiliser dans les boutons submit des formulaires onclick="alertNotNeeded()"
			function alertNotNeeded() {
				displayAlert = false;
			}
			
			// function pour activer les alertes dans les formulaires sensibles
			function alertNeeded() {
				displayAlert = true;
			}
			
			function GoToURL() { //v3.0
				var i, args=GoToURL.arguments; document.MM_returnValue = false;
				for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
			}

			function GoToURL_conf() { //v3.0
				var i, args=GoToURL_conf.arguments;
				document.MM_returnValue = false;
				Confirmation = confirm("Confirmez-vous la suppression de "+args[2]);
				if (Confirmation){
					for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
				}
			}
			
			
