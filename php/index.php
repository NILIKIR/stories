<?php
/*
 * Jediná stránka zobrazovaná uživateli/adminovi
 * Projekt: STORIES
 * Vytvořil: Janek
 */

 if(!isSet($_SERVER['HTTPS'])){ /*Kontrolova, zda je připojení HTTPS*/
    header("Location: https://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
 }
 session_start();/*Připojování k session*/

 $paragraph =null;
 $stories =null;
 $inventory= null;
 $jumps =null;

 if ($_SERVER['HTTP_HOST']==='localhost') $url_key = "http://localhost/stories/";
 else $url_key = "https://stories.ents.cz/";
 if(array_key_exists("jwt", $_COOKIE)) {
   if(isset($_GET["id_story"])){
     if (isset($_GET["id_jump"])) {
       $url_2 = $url_key."api/next_paragraph.php";
       $fields_2 = json_encode(array("jwt" => $_COOKIE["jwt"], "id_story"=> $_GET["id_story"], "id_jump"=> $_GET["id_jump"]));
     }
     $url = $url_key."api/select_story.php";
     $fields = json_encode(array("jwt" => $_COOKIE["jwt"], "id_story"=> $_GET["id_story"]));
   }
   else  {
     $url = $url_key."api/show_stories.php";
     $fields = json_encode(array("jwt" => $_COOKIE["jwt"]));
   }
 }
 if (array_key_exists("jwt", $_COOKIE)) {
   $curl = curl_init();

   curl_setopt($curl, CURLOPT_URL, $url);
   curl_setopt($curl, CURLOPT_POST, true);
   curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, $url);

   $data = curl_exec($curl);

   if (isset($fields_2)) {
     $curl_2 = curl_init();

     curl_setopt($curl_2, CURLOPT_URL, $url_2);
     curl_setopt($curl_2, CURLOPT_POST, true);
     curl_setopt($curl_2, CURLOPT_POSTFIELDS, $fields_2);
     curl_setopt($curl_2, CURLOPT_RETURNTRANSFER, $url);

     $data_2 = curl_exec($curl_2);

     if (isset(json_decode($data_2)->jwt)) {
       $data = $data_2;
     }
   }



   $data = json_decode($data);

     if (isset($data->jwt)&&isset($data->data)){
       setcookie("jwt", $data->jwt);
       setcookie("login", $_COOKIE["login"]);
       $logged["id_user"] = $data->data->id;
       $logged["id_charakter"] = $data->data->id_charakter;

       if(isset($data->paragraph)){
         $paragraph["name"] = $data->paragraph->name;
         $paragraph["text"] = $data->paragraph->text;
         $story["id"] = $_GET["id_story"];

         $i = 0;
         if (is_array($data->paragraph->jumps)) {
           foreach ($data->paragraph->jumps as $jump) {
             $jumps[$i]["id"] = isset($jump->id)?$jump->id:null;
             $jumps[$i]["label"] = isset($jump->label)?$jump->label:null;
             $i++;
           }
         }
       }
       if(isset($data->inventory)){
         foreach ($data->inventory as $item) {
           $inventory[$i]["properties"] = $item->properties_item;
           $inventory[$i]["name"] = $item->name_item;
           $inventory[$i]["count"] = $item->count_item;
           $i++;
         }
       }
       if(isset($data->stories)){
         $i = 0;
         foreach ($data->stories as $story) {
           if(isset($story->id_current_paragraph)) $stories[$i]["paragraph_id"] = $story->id_current_paragraph;
           $stories[$i]["id"] = $story->id;
           $stories[$i]["name"] = $story->name;
           $stories[$i]["anotation"] = $story->anotation;
           $i++;
         }
       }
     }
   }


   if ($_SERVER['HTTP_HOST']==='localhost') $url_key = "https://localhost/stories/";
   else $url_key = "https://stories.ents.cz/";
 ?>

 <!doctype html>
 <html>

	 <head>

		 <title>Příběhy</title>
     <meta charset="UTF-8">
     <meta name="description" content="Příběhy">
     <meta name="keywords" content="Příběhy">
     <meta name="author" content="Jan Slovák">

     <script type="text/javascript" src="script/variables.js"></script>
     <script type="text/javascript" src="script/script.js"></script>
     <script type="text/javascript" src="script/resize.js"></script>
     <link rel='stylesheet' href='style/style.css'>

 	 </head>

   <body>
     <span id="back">
       <span id="left">
         <span id="logo"><img src="images/data/sword-189-798886.png"></span>
         <span id="content_menu"></span>
         <span id="function_buttons_left"></span>
       </span>
       <span id="middle">
         <span id="top_menu"></span>
         <span id="content">
           <?php
           if (isset($logged)){
             if (array_key_exists("id_story",$_GET)) {
               echo "<div> Paragraph name: ".$paragraph["name"]."</div><div> Paragraph text: ".$paragraph["text"]."</div>";
               if (isset($jumps)){
                 foreach ($jumps as $jump) {
                   echo"<a href='".$url_key."index.php?id_story=".$story["id"]."&id_jump=".$jump["id"]."'><button><div>Text skoku: ".$jump["label"]."</div></button></a>";
                 }
               }
               else echo "<a href='".$url_key."index.php'><button><div>Zpět na výběr příběhů</div></button></a>";
             }
             else{
               foreach ($stories as $story) {
                 if (isset($story["paragraph_id"])) echo "<a href='".$url_key."index.php?id_story=".$story["id"]."'><button><div>Jméno příběhu: ".$story["name"]."</div><div>Anotace příběhu: ".$story["anotation"]."</div><div>Začít z uložené pozice</div></button></a>";
                 else echo"<a href='".$url_key."index.php?id_story=".$story["id"]."&id_jump=0'><button><div>Jméno příběhu: ".$story["name"]."</div><div>Anotace příběhu: ".$story["anotation"]."</div><div>Začít odzačátku</div></button></a>";
               }
             }
           }
           else {
             echo "Potřebujete být přihlášen, abyste mohl hrát";
           }
            ?> </span>
         <span id="content_footer"></span>
       </span>
       <span id="right">
         <span id="user">
           <?php
           if (isset($logged)) echo $_COOKIE["login"];
           else echo "<a href=".$url_key."login.php>Přihlásit se</a>";
            ?> </span>
         <span id="right_content">
           <?php
           if (isset($logged)){if (isset($inventory)){

             foreach ($inventory as $item) {
               echo "<div><div>Jméno předmětu: ".$item["name"]."</div><div>Počet předmětu: ".$item["count"]."</div><div>Vlastnosti předmětu: ".$item["properties"]."</div></div>";
               }
             }
           }
           else {
             echo "Potřebujete být přihlášen, abyste viděl inventář";
           }
            ?> </span>
         <span id="function_buttons_right"></span>
       </span>
     </span>
   </body>
 </html>
