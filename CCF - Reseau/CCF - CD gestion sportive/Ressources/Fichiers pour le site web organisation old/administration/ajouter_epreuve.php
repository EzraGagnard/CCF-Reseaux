<?php
// Ce script enregistre une épreuve avec son horaire

// page autoréférente protégée
   include "authentification/authcheck.php" ;
   // Vérification des droits pour cette page uniquement organisateurs
   if ($_SESSION['droits']<>'2') { header("Location: index.php");};
require_once('../definitions.inc.php');
require_once('utile_sql.php');

if( !empty($_POST['envoyer'])){
     
     if ($_POST['designation']=="") {
        echo "Vous devez indiquer un nom d'épreuve";
        exit;
     };
    $categorie = implode(',',$_POST['cat']);
    $sexe =  implode(',',$_POST['sexe']);
     // connexion à la base marsouin
    @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
    @mysql_select_db(BASE) or die("Echec de selection de la base cdt");


    if ($_POST['mode']=="insertion"){


      $insertSQL = sprintf("INSERT INTO cross_route_epreuve (`competition` ,`designation` , `horaire` , `prix` ,`code` ,`categorie`,`sexe`) VALUES (%s,%s,%s,%s,%s,%s,%s )",
                 GetSQLValueString($_POST['competition'], "text"),
                 GetSQLValueString($_POST['designation'], "text"),
                 GetSQLValueString($_POST['horaire'], "text"),
                 GetSQLValueString($_POST['prix'], "text"),
                 GetSQLValueString($_POST['code'], "text"),
                 GetSQLValueString($categorie, "text"),
                 GetSQLValueString($sexe, "text")
                 );
      $Result1 = mysql_query($insertSQL) or die(mysql_error());
    }


    @mysql_close();

    header("Location: epreuve.php?competition=".stripslashes($_POST['competition']));
    exit;
}
// début du fichier bandeau menu horizontal
  if (!is_readable('en_tete.html'))  die ("fichier non accessible");
  @readfile('en_tete.html') or die('Erreur fichier');

?>


<div id="menu" style="text-align: left; width: 100px;">
<p></p>
</div>
<div id="contenu" style="width: 800px; ">
     <h2>Nouvelle épreuve pour <?php  echo stripslashes($_GET['competition']); ?> </h2>
     <div class="item">

     <form method="post" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>"  name="epreuve" onSubmit="return verif();">
     <input type="hidden" name="mode" value="insertion" />
     <input type="hidden" name="competition" value="<?php echo stripslashes($_GET['competition']); ?>" />

     <table style="text-align: left; width: 700px; "   border="0" cellpadding="2" cellspacing="2">
     <tbody>
        <tr>
            <td style="width: 25%; text-align: right; " >Libellé :</td>
            <td style="width: 75%; ">
               <input name="designation" class="normal"/>
            </td>
        </tr>
        <tr>
            <td style="width: 25%; text-align: right; ">Horaire :</td>
            <td><input name="horaire" class="normal"/></td>
        </tr>
        <tr>
            <td style="width: 25%; text-align: right; ">Code :</td>
            <td><input name="code" class="normal"/></td>
        </tr>
        <tr><td colspan="3"> </td></tr>
        <tr>
            <td style="width: 25%; text-align: right; ">Cat. autorisé(s) :</td>
            <td><input name="cat[]" value="EA" type="checkbox" />EA
                <input name="cat[]" value="PO" type="checkbox" />PO
                <input name="cat[]" value="BE" type="checkbox" />BE
                <input name="cat[]" value="MI" type="checkbox" />MI
                <input name="cat[]" value="CA" type="checkbox" />CA
                <input name="cat[]" value="JU" type="checkbox" />JU
                <input name="cat[]" value="ES" type="checkbox" />ES
                <input name="cat[]" value="SE" type="checkbox" />SE
                <input name="cat[]" value="SE" type="checkbox" />VE
                <input name="cat[]" value="V1" type="checkbox" />V1
                <input name="cat[]" value="V2" type="checkbox" />V2
                <input name="cat[]" value="V3" type="checkbox" />V3
                <input name="cat[]" value="V4" type="checkbox" />V4 <br/>
                <input name="cat[]" value="TC" type="checkbox" />TC
            </td>
        </tr>
        <tr>
            <td style="width: 25%; text-align: right; ">Sexe :</td>
            <td><input name="sexe[]" value="M" type="checkbox" />Masculin
                <input name="sexe[]" value="F" type="checkbox" />Feminin
            </td>
        </tr>
        <tr><td colspan="3"> </td></tr>
        <tr>
            <td style="width: 25%; text-align: right; ">Prix d'engagement :</td>
            <td><input name="prix" value="0" size="3" class="normal"/></td>
        </tr>
        <td></td>
        <td><input name="envoyer" value="Valider"  type="submit" /></td>
        <td></td>
      </tr>
    </tbody>
  </table>
</form>
</div>
</div>
</div>
<div id="pied"> Site hébergé par Endurance72 - 2, avenue
d'HAOUZA - 72000 LE MANS - Tél: 02.43.23.64.18<br />
</div>
</div>
</body></html>
