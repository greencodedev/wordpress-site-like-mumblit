/**
 * bigpipe js
 * Plugins for Wowonder
 * @author PpG - LdrMx
 */
 
 /* creating bigpipe function */ 
 bigPipe = function(){};
 
 /* add cache for no repeat scripts */
 var CacheJSArray = [];
 var CacheCSSArray = [];
 var CacheHTMLArray = [];
 
 /* dding position in website */
 bigPipe.onPageletArrive = function(BP) {

/*check if exist content of only load css and js*/
if(BP.container_id){
if(BP.unique){ if($.inArray(BP.container_id, CacheHTMLArray) != -1){ return; } }

 /* get template comprim */
if(BP.type){ var Bp = jQuery.parseJSON($("#"+BP.container_id).html().replace('<!-- ','').replace(' -->','')); }
else 
{ var Bp = $("#"+BP.container_id).html().replace('<!-- ','').replace(' -->',''); }

 /* check auto position in, after, before add more sites of website */
if(BP.append){ $('#'+BP.append).append(Bp); }
else if(BP.appendTo){ $(Bp).appendTo('#'+BP.appendTo); }
else if(BP.prepend){ $('#'+BP.prepend).prepend(Bp); }
else if(BP.before){ $('#'+BP.before).before(Bp); }
else if(BP.after){ $('#'+BP.after).after(Bp); }
else{ $('#'+BP.content).html(Bp); } 
 if(BP.unique){ CacheHTMLArray.push(BP.container_id); }
 /* remove template comprimmer */
 $("#"+BP.container_id).remove(); 
 }

/* check if exist auto js and send to function  */
if(BP.js){ bigPipe._JS(BP.js); } 

/* check if exist auto css and send to function */
if(BP.css){ bigPipe._CCS(BP.css); }

}

/*creation function for each CCS*/
bigPipe._CCS = function(CSS){
var myArray = CSS;
$.each(myArray, function(i, value){
/* check if exist prev css*/
if ($.inArray(value, CacheCSSArray) == -1){ 
var st = document.createElement("link");
st.type = "text/css";
st.rel = "stylesheet";
st.href = websiteUrl+"/assets/lider/css/"+value+".css";
/* adding css to page */
document.getElementsByTagName("head")[0].appendChild(st);
/* caching */
CacheCSSArray.push(value); }
 });
 }
 
/*creation function for each JS*/
bigPipe._JS = function(JS){
var myArray = JS;
$.each(myArray, function(i, value){
/* check if exist prev js*/
if ($.inArray(value, CacheJSArray) == -1){ 
var st = document.createElement("script");
st.type = "text/javascript";
st.src = websiteUrl+"/assets/lider/js/"+value+".js";
/* adding js to page */
document.getElementsByTagName("head")[0].appendChild(st);
/* caching */
CacheJSArray.push(value); }
 });
 
 }