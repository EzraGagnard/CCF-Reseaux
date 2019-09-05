<?php
//-------------------------------------------------------------------------------
// ce script permet uploader un fichier vers son espace perso
// le dossier est défini par la variable de session : $_SESSION['path_fichier_perso']
//-------------------------------------------------------------------------------

// vérification des variables de session pour le temps d'inactivité et de l'adresse IP
include "authentification/authcheck.php" ;
// Vérification des droits pour cette page tous sauf les exclus
if ($_SESSION['droits']=='0') { header("Location: ../index.php");};

// initialisation des variables globales utiliées pas scan.php
$url_abs = 'http://'.$_SERVER['HTTP_HOST'].$_SESSION['path_fichier_perso'];

$dir     = $_SERVER['DOCUMENT_ROOT'].$_SESSION['path_fichier_perso'];



// page autoréférente
if( !empty($_POST['envoyer'])){

	if($_FILES['fichier']['name']){
		$dossier = '../../'.$_SESSION['path_fichier_perso'];
		// si le dossier n'est pas présent sur le serveur alors création
		if(!is_dir($dossier)){ 
			mkdir($dossier, 0777);
        }
        
		$fichier = basename($_FILES['fichier']['name']);
		$taille_maxi = 10000000;
		$taille = filesize($_FILES['fichier']['tmp_name']);
		$extensions = array('.png', '.gif', '.jpg', '.jpeg', '.txt', '.pdf' );
		$extension = strrchr($_FILES['fichier']['name'], '.');
		//Début des vérifications de sécurité...
		//Si l'extension n'est pas dans le tableau
		if(!in_array($extension, $extensions))    { $erreur = 'Vous ne pouvez pas uploader ce type de fichier';    }
		// si le fichier dépasse la taille maxi
		if($taille>$taille_maxi)  { $erreur = 'Le fichier est trop volumineux...'; }
		if(!isset($erreur)) //S'il n'y a pas d'erreur, on upload
			{   //On formate le nom du fichier ici...
				$fichier = strtr($fichier,
				'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ',
				'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
				$fichier = preg_replace('/([^.a-z0-9]+)/i', '_', $fichier);
	   

				if(move_uploaded_file($_FILES['fichier']['tmp_name'], $dossier.'/'.$fichier)) //Si la fonction renvoie TRUE, c'est que ça a fonctionné...
				{
				// OK retour vers la page up_load
				$GoTo = "up_load.php";
				header(sprintf("Location: %s", $GoTo));
				}

			}

	}
// fin de la section pour upload
 }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>

	<head>
		<meta http-equiv="Content-Type"  content="text/html; charset=windows-1252">
		<title>Uploader un fichier</title>
		<link href="../css/bassin.css" type="text/css" rel="StyleSheet">
		<link href="../css/dtree.css" type="text/css" rel="StyleSheet">
		<script type="text/javascript" src="jscript/dtree.js"></script>
		<style type="text/css">
		<!--
			table.formulaire {
				clear: both;
				width: 800px;
				border-collapse: collapse;
				background-color: #808080;
			}

			tr.ligne {
				height: 35px;
			}
			
			td.droite {
				text-align: right;
				font-weight: bold;
				font-size: 12px;
				padding-right: 15px;
				width: 20%;
			}
		-->
		</style>
		<script language="javascript">
			function fab_arbre(){
                   files = <?php include "inc/scan.php" ?> ;    // retourne un tableau d'objets au format Json
                   nxtid = 0;
                   dTree = new dTree('dTree');                              // création d'un nouvel objet arbre
                   var info_racine = "Dossier :(<?php echo $url_abs ?> )";
                   dTree.add(nxtid++, -1,info_racine, null, 'essai');      //création de la racine
                   fab_noeuds(files, 0);                                   //création des noeuds avec le tableau files
                   document.getElementById("arbre").innerHTML = dTree;   //copie de l'arbre dans le conteneur contenu
            }
   
			function fab_noeuds(files, parent){
                   for(var i = 0; i < files.length; i++)
                   {
					if(typeof files[i] == 'object')
                        {   id = nxtid++;
                             if(files[i].url)
                             var item = files[i].url.replace(/^.*\//, '');
                             else
                             var item = "sans titre";

                             if(files[i].url)
                             var lien = files[i].url
                             else
                             var lien = '#';

                             if(files[i].children) {
                                dTree.add(id, parent, item, null);
                                fab_noeuds(files[i].children, id);
                             }
                             else
                             dTree.add(id, parent, item, lien);
						}
					}
			}
		</script>

	</head>

	<body link="#FFFFFF" vlink="#008000" topmargin="0" leftmargin="0" onLoad="fab_arbre();">
		<div id="page">

			<div id="bandeau_flash" style="width: 1098px; height: 336px;">
				<a href="../../"><img style="width: 1098px; height: 336px; border: 0px;" title="Retour accueil" src="../images/bandeau_trail.jpg" /></a>
			</div>
			<div>
				<div id="menu" style="width: 50px;">
					<a href="javascript:history.back(1)"><img src="../images/fleche_retour.png" border="0" width="44" height="41"></a>
				</div>

				<div id="contenu" style="margin-left: 50px; ">
					<h2> Envoyer un fichier sur le site :</h2>
					<div id="message" >
				<p style="color: #ff0000;"><?php echo $erreur; ?></p>
			</div>
					<div id="arbre" style="width: 800px; height: 300px; border: 1px solid #7F9DB9; background: White; overflow: auto;">
            </div>
					<form action='up_load.php' method="POST" name="ajou_fichier" enctype="multipart/form-data">

                    <table class="formulaire">
						<tr class="ligne">
							<td class="droite">
								<br /><b>Nouveau fichier :</b>
							</td>
							<td>
								<!-- On limite le fichier à 1000Ko -->
								Sélectionner le fichier à envoyer depuis votre ordinateur.
								<input type="hidden" name="MAX_FILE_SIZE" value="10000000">
								<input type="file" name="fichier" size="90">
							</td>
						</tr>
						<tr class="ligne">
							<td class="droite">
							</td>
							<td>
								<input type="submit" value="Envoyer" name="envoyer">
							</td>
						</tr>
                    </table>
            </form>
				</div>
				<div id="pied">
				</div>
			</div>
		</div>	
	
	</body>
</html>
