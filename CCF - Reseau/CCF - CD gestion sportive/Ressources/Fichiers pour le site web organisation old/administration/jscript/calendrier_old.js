//----------------------------------------------
// calendrier dynamique
// Script avec jQuery
// Auteur Simier Philippe Novembre 2009
//----------------------------------------------

// variables globales

   var ds_element; // Element input ayant la class date
   var ds_monthnames = [
   'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
   'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
   ];
   var ds_daynames = ['lun.', 'mar.', 'mer.', 'jeu.', 'ven.', 'sam.', 'dim.'];
   
   var fetes = [
      {jour:1,mois:1,libelle:'Nouvel an'},
      {jour:14,mois:2,libelle:'Saint Valentin'},
      {jour:1,mois:5,libelle:'Fête du travail'},
      {jour:8,mois:5,libelle:'Victoire 1945'},
      {jour:14,mois:7,libelle:'Fête nationale'},
      {jour:15,mois:8,libelle:'Assomption'},
      {jour:1,mois:11,libelle:'Toussaint'},
      {jour:11,mois:11,libelle:'Armistice 1918'},
      {jour:25,mois:12,libelle:'Noël'},
      {jour:31,mois:12,libelle:'Saint Sylvestre'}
   ];

   var ds_oe;
   var ds_ce;
   var ds_ob = '';  // Output Buffering
   var jour_sel;
   var mois_sel;
   var annee_sel;
   
// avec jQuery Quand le dom est chargé
  $(function(){
        $("body").append("<div class=\"ds_box\"  id=\"ds_conclass\" style=\"display: none;\"><div id=\"ds_calclass\"></div></div>");
        // élément input class date en lecture seule
        $("input.date").attr("readonly","readonly");
        // à l'évènement "onclick" de l'élément input class date, on associe la fonction ds_sh(this)
        $("input.date").click( function() { ds_sh(this); } );
        // Output Element
           ds_oe = document.getElementById("ds_calclass");
           // Container
           ds_ce = document.getElementById("ds_conclass");


  });

// retoune un libellé si la date est un jour férié
function ds_est_ferie(j,m){
     for (id=0 ; id<fetes.length ; id ++){
     if (j==fetes[id].jour && m==fetes[id].mois) return fetes[id].libelle;
     }
   return '';
}

//----------------------------------------------------
// donne la position en x de l'élément el par rapport à body.
function ds_getleft(el) {
	var tmp = el.offsetLeft;
	el = el.offsetParent
	while(el) {
		tmp += el.offsetLeft;
		el = el.offsetParent;
	}
	return tmp;
}
// donne la position en y hauteur de l'élément el par rapport à body
function ds_gettop(el) {
	var tmp = el.offsetTop;
	el = el.offsetParent
	while(el) {
                tmp += el.offsetTop;
		el = el.offsetParent;
	}
	return tmp;
}


function ds_ob_clean() {
	ds_ob = '';
}
function ds_ob_flush() {
	ds_oe.innerHTML = ds_ob;
	ds_ob_clean();
}
function ds_echo(t) {
	ds_ob += t;
}



// Calendrier titre
function ds_template_main_above(m,y) {
	var aujourdhui = new Date();
        ce_mois = aujourdhui.getMonth() + 1;
        cette_annee = aujourdhui.getFullYear();

        code  = '<table cellpadding="3" cellspacing="0" class="ds_tbl"><tr>';
        code += '<td class="ds_head" title="aujourd\'hui" style="cursor: pointer" onclick="aujourdhui();">?</td>';
        if (cette_annee<y && ce_mois<=m || cette_annee<(y-1))
	code += '<td class="ds_head" title="année précédente" style="cursor: pointer" onclick="ds_py();">&lt;&lt;</td>';
	else code += '<td class="ds_head" style="color:#888;"></td>';
	if ((ce_mois<m) || (cette_annee<y))
        code += '<td class="ds_head" title="mois précédent" style="cursor: pointer" onclick="ds_pm();">&lt;</td>';
        else code += '<td class="ds_head" style="color:#888;"></td>';
        code += '<td class="ds_head"></td>';
	code += '<td class="ds_head" title="mois suivant" style="cursor: pointer" onclick="ds_nm();">&gt;</td>';
	code += '<td class="ds_head" title="année suivante" style="cursor: pointer" onclick="ds_ny();">&gt;&gt;</td>';

	code += '<td class="ds_head" style="cursor: pointer; text-align:right;" title="Fermer" onclick="ds_hi();" ><img src="isr_c.gif" /></td></tr>';
	code += '<tr>';
	code += '<td colspan="7" class="ds_titre">' + ds_monthnames[m - 1] + ' ' + y + '</td>';
	code += '</tr>';
	code += '<tr>';
      return code;
}

function ds_template_day_row(t) {
	return '<td class="ds_subhead">' + t + '</td>';
	// Define width in CSS, XHTML 1.0 Strict doesn't have width property for it.
}

function ds_template_new_week() {
	return '</tr><tr>';
}

//dessine nb cellules vides et les numérotes
function ds_template_blank_cell(nb,deb) {
	html='';
	for (i = 0; i < nb; i ++) {
	    html += '<td class="ds_cell_vide">'+deb+'</td>';
	    deb ++;
        }
        return html;
}

function ds_template_day(d, m, y) {
	return '<td class="ds_cell" onclick="ds_onclick(' + d + ',' + m + ',' + y + ')">' + d + '</td>';
	// Define width the day row.
}

function ds_template_day_c(d, m, y, color,libelle) {
	return '<td class="'+color+'" title="'+libelle+'" onclick="ds_onclick(' + d + ',' + m + ',' + y + ')">' + d + '</td>';
	// Define width the day row.
}

function ds_template_main_below() {
	return '</tr>'
	     + '</table>';
}

// donne le nombre de jours pour un mois
function ds_nb_jours(m,annee) {
   if (m == 1 || m == 3 || m == 5 || m == 7 || m == 8 || m == 10 || m == 12) {
		nb = 31;
	} else if (m == 4 || m == 6 || m == 9 || m == 11) {
		nb = 30;
	} else {
		nb = (annee % 4 == 0) ? 29 : 28;
	}
    return nb;
}

// ceci dessine le calendrier...
function ds_draw_calendar(jo, m, y) {
	// effacer le buffer de sortie.

	ds_ob_clean();
	// ici fabriquation de l'entête
	ds_echo (ds_template_main_above(m,y));
	for (i = 0; i < 7; i ++) {
		ds_echo (ds_template_day_row(ds_daynames[i])); //colonne du lundi au dimanche
	}

	// fabrique un objet date pour le premier du mois.
	var ds_dc_date = new Date();
	ds_dc_date.setMonth(m - 1);
	ds_dc_date.setFullYear(y);
	ds_dc_date.setDate(1);

        //days = ds_nb_jours(m,y);

	var premier_jour = ds_dc_date.getDay();  // donne le jour de la semaine du premier du mois
	var first_loop = 1;
	// commence par la première semaine
	ds_echo (ds_template_new_week());
        m_1= m-1;
        if (m_1<1) m_1=12;
        days= ds_nb_jours(m_1,y);

        // si lundi n'est pas le premier jour du mois, dessiner des cellules blanches...
        if (premier_jour > 1) {   // de mardi à samedi
                ds_echo (ds_template_blank_cell(premier_jour-1,days-premier_jour+2));
	}else if (premier_jour==0) {    // dimanche
                ds_echo (ds_template_blank_cell(6,days-6+1));
        }
	var j = premier_jour;
	for (i = 0; i < ds_nb_jours(m,y); i ++) {
		// si jour est un lundi alors nouvelle semaine
		// si lundi est le premier jour du mois,
		// ne pas faire une nouvelle semaine car elle est déja faite plus haut.
		if (j == 1 && !first_loop) {
			// nouvelle semaine !!
			ds_echo (ds_template_new_week());
		}
		// fabrique une cellule pour le jour
		jf = ds_est_ferie(i+1,m);
		if (jour_sel == (i+1) && mois_sel==m && annee_sel==y) ds_echo (ds_template_day_c(i + 1, m, y,'ds_cell_v','')); // marque cette cellule en vert
		else { 
                   if (jf!='') ds_echo (ds_template_day_c(i + 1, m, y,'ds_cell_j',jf)); // marque les jour férié en jaune
                    else ds_echo (ds_template_day(i + 1, m, y));
                   }
		// ce n'est plus la première autre boucle ...
		first_loop = 0;
		// passe au prochain jour de la semaine
		j ++;
		j %= 7;    // modulo 7
	}
	// affiche des cellules pour les jours du mois suivant
        reste = (7-j)+1;
	reste %= 7;
	if (reste!=0) ds_echo (ds_template_blank_cell(reste,1));

	// fabrique le pied du calendrier
	ds_echo (ds_template_main_below());
	// et affichage..
	ds_ob_flush();
	// puis déplace la fenetre pour montrer le calendrier en totalité
	ds_ce.scrollIntoView();
}

// cette fonction affiche le calendrier.
// quand l'utilisateur clique sur date.
function ds_sh(t) {
	//
	ds_element = t;
	if (ds_element.value == '') {  // si le champ est vide
	var ds_sh_date = new Date(); // date du jour
	ds_c_jour = ds_sh_date.getDate();
        ds_c_mois = ds_sh_date.getMonth() + 1;
	ds_c_annee = ds_sh_date.getFullYear();
	}
	else {  // sinon on extrait la date
        ds_c_jour = ds_element.value.substring(8,10);
        ds_c_mois = ds_element.value.substring(5,7);
        ds_c_annee = ds_element.value.substring(0,4);

	}
	jour_sel = ds_c_jour;
	mois_sel = ds_c_mois;
	annee_sel = ds_c_annee;

	// dessine le calendrier
	ds_draw_calendar(ds_c_jour,ds_c_mois, ds_c_annee);
	// To change the position properly, we must show it first.
	$("#ds_conclass").slideDown("normal");
        //ds_ce.style.display = '';
	// Move the calendar container!
	the_left = ds_getleft(t);
	the_top = ds_gettop(t) + t.offsetHeight;
	ds_ce.style.left = the_left + 'px';
	ds_ce.style.top = the_top + 'px';
	// Scroll it into view.
	ds_ce.scrollIntoView();
}

// fait disparaitre progressivement le calendrier.
function ds_hi() {
        $("#ds_conclass").slideUp("normal");


}
// va à aujourd'hui
function aujourdhui(){
        var ds_sh_date = new Date(); // date du jour
	ds_c_jour = ds_sh_date.getDate();
        ds_c_mois = ds_sh_date.getMonth() + 1;
	ds_c_annee = ds_sh_date.getFullYear();
        jour_sel = ds_c_jour;
	mois_sel = ds_c_mois;
	annee_sel = ds_c_annee;
   // Redessine le calendrier.
   ds_draw_calendar(ds_c_jour,ds_c_mois, ds_c_annee);
}

// va au mois prochain...
function ds_nm() {
	// Incrémente le mois courant.
	ds_c_mois ++;
	// si nous dépassons décembre va à l'année suivante.
	// Incrémente l'année courante, et positionne janvier comme mois courant.
	if (ds_c_mois > 12) {
		ds_c_mois = 1; 
		ds_c_annee++;
	}
	// Redessine le calendrier.
	ds_draw_calendar(ds_c_jour,ds_c_mois, ds_c_annee);
}

// Moves to the previous month...
function ds_pm() {
	ds_c_mois = ds_c_mois - 1; // Can't use dash-dash here, it will make the page invalid.
	// We have passed January, let's go back to the previous year.
	// Decrease the current year, and set the current month to December.
	if (ds_c_mois < 1) {
		ds_c_mois = 12; 
		ds_c_annee = ds_c_annee - 1; // Can't use dash-dash here, it will make the page invalid.
	}
	// Redessine le calendrier.
	ds_draw_calendar(ds_c_jour,ds_c_mois, ds_c_annee);
}

// va vers l'année suivante...
function ds_ny() {
	// incrémente l'année courante.
	ds_c_annee++;
	// Redessine le calendrier..
	ds_draw_calendar(ds_c_jour,ds_c_mois, ds_c_annee);
}

// va à l'année précédente...
function ds_py() {
	// Decrémente l'année courante.
	ds_c_annee = ds_c_annee - 1; // Can't use dash-dash here, it will make the page invalid.
	// Redessine le calendrier..
	ds_draw_calendar(ds_c_jour,ds_c_mois, ds_c_annee);
}

// Format de la date pour la sortie .
function ds_format_date(d, m, y) {
        if (m<10) m='0'+m;
        if (d<10) d='0'+d;
	return y+'-'+m+'-'+d;
}

// Quand l'utilisateur clique sur une date.
function ds_onclick(d, m, y) {
	// Cache le calendrier.
	ds_hi();
	// ecrit la valeur dans value si possible.
	if (typeof(ds_element.value) != 'undefined') {
		ds_element.value = ds_format_date(d, m, y);
	// ou place le valeur en html.
	} else if (typeof(ds_element.innerHTML) != 'undefined') {
		ds_element.innerHTML = ds_format_date(d, m, y);
	// sinon renvoie la date dans une fenêtre d'alerte
	} else {
		alert (ds_format_date(d, m, y));
	}
}

// fin du calendier
