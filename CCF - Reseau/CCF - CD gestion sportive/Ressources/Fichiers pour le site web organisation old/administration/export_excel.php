<?php
//------------------------------------------------------------------------------------
// Ce script  exporte  le tableau des engagements au format Excel
// format excel
// Auteur: SIMIER Philippe Endurance72  Février 2014
// pour les besoins de l'organisation 
// 29 mars 2014 ajout de la colonne Course
// Février 2015 ajout de la colonne Dossard et TEL
//------------------------------------------------------------------------------------
// vérification des variables de session pour le temps d'inactivité et de l'adresse IP
include "authentification/authcheck.php" ;
// Vérification des droits pour cette page tous sauf les exclus
if ($_SESSION['droits']<>'2') { header("Location: index.php");};


require_once('../definitions.inc.php');
require_once('utile_sql.php');
include 'Classes/PHPExcel.php';
include 'Classes/PHPExcel/Writer/Excel5.php';

    $classeur  = new PHPExcel;

    $feuille = $classeur->getActiveSheet();
    $feuille->setTitle(utf8_encode($_GET['competition']));

//-------------------------------------------------------------------------------
//   Première ligne de la feuille   Arial taille 16 bold gris  alignement centré
//   hauteur 75 (100px)
//-------------------------------------------------------------------------------

    $styleA1 = $feuille->getStyle('A1:I1');
    $styleA1->applyFromArray(array(
        'font'=>array(
				'bold'=>true,
				'size'=>16,
				'name'=>Arial,
				'color'=>array(
				'rgb'=>'808080')
			)
        ));
		
	$A1 = 'Liste : '.$_GET['competition'];
//---- On fusionne les cellules pour la première ligne -----
     $feuille->mergeCells('A1:K1');
     // hauteur de la première ligne
     $feuille->getRowDimension('1')->setRowHeight(40);


     $feuille->duplicateStyleArray(array(
                   'alignment'=>array(
                            'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)), 'A1:J1');        
     $feuille->setCellValue('A1',utf8_encode($A1));

//-------------------------------------------------------------------------------
//   Deuxième ligne de la feuille   Arial taille 12 bold gris
//-------------------------------------------------------------------------------
    $A2 = "Edition du : ".date("d.m.Y"); 
	 $styleA1 = $feuille->getStyle('A2:K2');
     $styleA1->applyFromArray(array(
        'font'=>array(
          'bold'=>true,
          'size'=>12,
          'name'=>Arial,
          'color'=>array(
            'rgb'=>'404000'))
        ));
     // On fusionne les cellules pour la deuxième ligne
     $feuille->mergeCells('A2:K2');
     $feuille->getRowDimension('2')->setRowHeight(40);
     $feuille->setCellValue('A2',$A2);

//-------------------------------------------------------------------------
//   Entête du tableau    Arial 13 gras blanc
//-------------------------------------------------------------------------
     $feuille->getRowDimension('3')->setRowHeight(10);
     $styleA4 = $feuille->getStyle('A4:K4');
     $styleA4->applyFromArray(array(
        'font'=>array(
        'bold'=>true,
        'size'=>13,
        'name'=>Arial,
        'color'=>array(
            'rgb'=>'FFFFFF'))
        ));


        $feuille->getStyle('A4:K4')->applyFromArray(array(

            'fill'=>array(
                'type'=>PHPExcel_Style_Fill::FILL_SOLID,
                'color'=>array(
                    'rgb'=>'A0A0A0'))
		));
                


     // Préparation des colonnes sur la troisième ligne
     $feuille->getColumnDimension('A')->setWidth(7);
     $feuille->setCellValue('A4','Dossard');
	 $feuille->getColumnDimension('B')->setWidth(17);
     $feuille->setCellValue('B4','Prenom');
     $feuille->getColumnDimension('C')->setWidth(17);
     $feuille->setCellValue('C4',utf8_encode('Nom'));
     $feuille->getColumnDimension('D')->setWidth(7);
     $feuille->setCellValue('D4','Naiss');
	 $feuille->getColumnDimension('E')->setWidth(5);
     $feuille->setCellValue('E4','Sexe');
     $feuille->getColumnDimension('F')->setWidth(20);
     $feuille->setCellValue('F4','Equipe');
     $feuille->getColumnDimension('G')->setWidth(20);
     $feuille->setCellValue('G4','Commentaire');
	 $feuille->getColumnDimension('H')->setWidth(6);
     $feuille->setCellValue('H4','Certi.');
	 $feuille->getColumnDimension('I')->setWidth(6);
     $feuille->setCellValue('I4','Coti.');
	 $feuille->getColumnDimension('J')->setWidth(30);
     $feuille->setCellValue('J4','Email');
	 $feuille->getColumnDimension('K')->setWidth(15);
     $feuille->setCellValue('K4','Course');
                    


//-------------------------------------------------------------------------
//   remplissage du tableau
//-------------------------------------------------------------------------
// connexion à la base
     @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
     @mysql_select_db(BASE) or die("Echec de selection de la base cdt");


     // Si la variable ordre n'exite pas on trie suivant nom
    if(!isset($_GET['ordre'])) { 
		$_GET['ordre']="nom"; 
		}

    if (isset($_GET['course'])) {

		$sql = sprintf("SELECT * FROM cross_route_engagement WHERE `nomcourse`=%s AND email<>'NULL' ORDER BY %s",
            GetSQLValueString($_GET['course'], "text"),
            GetSQLValueString($_GET['ordre'], "text")
        );
		$nom_fichier="engages_".stripslashes($_GET['course']).".txt";
    }
    else {

		$sql = sprintf("SELECT * FROM cross_route_engagement WHERE `competition`=%s  ORDER BY %s",
            GetSQLValueString($_GET['competition'], "text"),
            GetSQLValueString($_GET['ordre'], "text")
        );
		$nom_fichier="engages_".stripslashes($_GET['competition']).".txt";
    }

    $resultat = mysql_query($sql);
   
 
if (mysql_num_rows($resultat) != 0) {

	// il y a des données dans la table demandée
	$ligne = 5;
	while ($engagement = mysql_fetch_object ($resultat))
	{
    	$feuille->setCellValueByColumnAndRow(0, $ligne, utf8_encode($engagement->dossard ));
		$feuille->setCellValueByColumnAndRow(1, $ligne, utf8_encode($engagement->prenom ));
        $feuille->setCellValueByColumnAndRow(2, $ligne, utf8_encode($engagement->nom ));
        $feuille->setCellValueByColumnAndRow(3, $ligne, utf8_encode($engagement->anneenaissance ));
		$feuille->setCellValueByColumnAndRow(4, $ligne, utf8_encode($engagement->sexe ));
        $feuille->setCellValueByColumnAndRow(5, $ligne, utf8_encode($engagement->nomequipe ));
        $feuille->setCellValueByColumnAndRow(6, $ligne, utf8_encode($engagement->commentaire ));
		$feuille->setCellValueByColumnAndRow(7, $ligne, utf8_encode($engagement->certifmedicalfourni ));
		$feuille->setCellValueByColumnAndRow(8, $ligne, utf8_encode($engagement->cotisationpaye ));
		$feuille->setCellValueByColumnAndRow(9, $ligne, utf8_encode($engagement->email ));
		$feuille->setCellValueByColumnAndRow(10, $ligne, utf8_encode($engagement->nomcourse ));
		// la ligne où l'engagement n'est pas complet est surlignée en jaune
		if ($engagement->cotisationpaye == 'non' || $engagement->certifmedicalfourni == 'non' ){
			$zone = 'A'.$ligne.':K'.$ligne;
			$feuille->getStyle($zone)->applyFromArray(array(
				'fill'=>array(
                'type'=>PHPExcel_Style_Fill::FILL_SOLID,
                'color'=>array(
                    'rgb'=>'FFFF99'))
			));
		}
		// fin du surlignage.
        $ligne +=1;
    }
}

//-------------------------------------------------------------------------
//   création des bordures dans la zone des données
//-------------------------------------------------------------------------
     $ligne -=1;
     $zone = "A4:K".$ligne;

     $feuille->getStyle($zone)->getBorders()->applyFromArray(
    		array(
    			'allborders' => array(
    				'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
    				'color' => array(
    					'rgb' => 'A0A0A0'
    				)
    			)
    		)
    );
	
//--------------------------------------------------------------------------
//   Envoie du classeur
//   vers le client
//--------------------------------------------------------------------------

     $writer = new PHPExcel_Writer_Excel5($classeur);
	 $titre = $_GET['competition'];
	 
     header('Content-type: application/vnd.ms-excel');
     // header pour définir le nom du fichier (les espace sont remplacer par _)
	 $search  = array(' ');
	 $replace = array('_');
	 $titre = str_replace($search, $replace, $titre);
     $entete="Content-Disposition:inline;filename=".$titre.".xls";
	 
     header($entete);

     $writer->save('php://output');


     	

	
