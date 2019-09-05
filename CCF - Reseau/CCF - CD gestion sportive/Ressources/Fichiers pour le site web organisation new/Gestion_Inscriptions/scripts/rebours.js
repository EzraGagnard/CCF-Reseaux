

function counter()
{
 maintenant = new Date();
 depart = new Date(2009,8,19,14,0,0); //date de départ de la course attention janv=0 donc septembre = 8
 time_now = maintenant.getTime(); // nb de milliseconde depuis 1 janv 1970
 time_next = depart.getTime();    // même chose pour le départ


 var rebours = ''+time_next-time_now+''; // nb de milliseconde entre les deux dates
 n_f = rebours.length;
 n_d = n_f-3;
 SEC=rebours.substring(0, n_d); // SEC nb de seconde entre les deux dates
 var JOUR=0;
 var HER=0;
 var MIN=0;
 var SEC_aff=0;

 i0 = new Image;
 i1 = new Image;
 i2 = new Image;
 i3 = new Image;
 i4 = new Image;
 i5 = new Image;
 i6 = new Image;
 i7 = new Image;
 i8 = new Image;
 i9 = new Image;
 imgSrc = new Array;
 imgSrc[0] = 'images/chiffres/0.gif';
 imgSrc[1] = 'images/chiffres/1.gif';
 imgSrc[2] = 'images/chiffres/2.gif';
 imgSrc[3] = 'images/chiffres/3.gif';
 imgSrc[4] = 'images/chiffres/4.gif';
 imgSrc[5] = 'images/chiffres/5.gif';
 imgSrc[6] = 'images/chiffres/6.gif';
 imgSrc[7] = 'images/chiffres/7.gif';
 imgSrc[8] = 'images/chiffres/8.gif';
 imgSrc[9] = 'images/chiffres/9.gif';
 i0.src = imgSrc[0];
 i1.src = imgSrc[1];
 i2.src = imgSrc[2];
 i3.src = imgSrc[3];
 i4.src = imgSrc[4];
 i5.src = imgSrc[5];
 i6.src = imgSrc[6];
 i7.src = imgSrc[7];
 i8.src = imgSrc[8];
 i9.src = imgSrc[9];


JOUR = Math.floor(SEC / 86400);
SEC = SEC % 86400;
HER = Math.floor(SEC / 3600);
SEC = SEC % 3600;
MIN = Math.floor(SEC /60);
SEC = SEC %60;

if (SEC < 10)    SEC = "0"+SEC;
if (MIN < 10)    MIN = "0"+MIN;
if (HER < 10)    HER = "0"+HER;
if (JOUR < 10)    JOUR = "00"+JOUR;
else if (JOUR < 100)   JOUR = "0"+JOUR;
     DinaHeure = ""+ JOUR + HER + MIN + SEC
for(a=0;a<9;a++)
{
// HEURE
obj_image = eval("document.heure_img"+(a+1));
ejs_char = DinaHeure.charAt(a);
obj_image.src = imgSrc[ejs_char];
}
if(document.getElementById)
{
if(rebours > 0)
{
///document.getElementById("rebours_time").innerHTML = JOUR+" <img src=images/chiffres/jours.gif align=absmiddle> "+HER+" <img src=images/chiffres/heure.gif align=absmiddle> "+MIN+" <img src=images/chiffres/min.gif align=absmiddle> "+SEC+" <img src=images/chiffres/sec.gif align=absmiddle>";
}
else
{
///document.getElementById("rebours_time").innerHTML = msg1+" 0 "+msg2;
}
}
else if(document.all)
{
if(rebours > 0)
{
///document.all["rebours_time"].innerHTML = JOUR+" <img src=images/chiffres/jours.gif align=absmiddle> "+HER+" <img src=images/chiffres/heure.gif align=absmiddle> "+MIN+" <img src=images/chiffres/min.gif align=absmiddle> "+SEC+" <img src=images/chiffres/sec.gif align=absmiddle>";
}
else
{
///document.all["rebours_time"].innerHTML = msg1+" 0 "+msg2;
} }
}
window.setInterval("counter()",1000);
