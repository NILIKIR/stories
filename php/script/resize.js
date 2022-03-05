/*
 * funkce používané při změně velikosti prohlíženého dokumentu
 * Projekt: STORIES
 * Vytvořil: Janek
 */

 function onResize() {
   height = window.innerHeight;
   width = window.innerWidth;

   left.style.width = width*left_ratio+'px';
   logo.style.height = height*top_ratio+'px';
   content_menu.style.height = height*content_ratio+'px';
   function_buttons_left.style.height = height*bottom_ratio+'px';

   middle.style.width = width*middle_ratio+'px';
   top_menu.style.height = height*top_ratio+'px';
   content.style.height = height*content_ratio+'px';
   content_footer.style.height = height*bottom_ratio+'px';

   right.style.width = width*right_ratio+'px';
   user.style.height = height*top_ratio+'px';
   right_content.style.height = height*content_ratio+'px';
   function_buttons_right.style.height = height*bottom_ratio+'px';
 }
