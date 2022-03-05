/*
 * Základní script
 * Projekt: STORIES
 * Vytvořil: Janek
 */

 window.onload = function(){
   onLoadVar(); /*přiřazování proměnných*/
   onResize();  /*rozhazování obsahu na příslušné místo*/
   window.onresize = onResize;

   displayStories();
 }

 /*Odeslání informací potřebných pro přihlášení a nastavení funkce pro návratové hodnoty*/
 function login() {

 }

 /*Zobrazení všech příběhů dostupných k přehrání*/
 function displayStories() {
   var pom = new stories();
 }
