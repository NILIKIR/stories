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

 if ($_SERVER['HTTP_HOST']==='localhost') $url_key = "http://localhost/stories/";
 else $url_key = "https://stories.ents.cz/";

 if (array_key_exists("login", $_POST)&&array_key_exists("password", $_POST)) {
   $curl = curl_init();
   $url = $url_key."api/login.php";
   $fields = json_encode(array("login" => $_POST["login"],"password" => $_POST["password"]));

   curl_setopt($curl, CURLOPT_URL, $url);
   curl_setopt($curl, CURLOPT_POST, true);
   curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, $url);

   $data = curl_exec($curl);
   $data = json_decode($data);

   if (isset($data->error)){
     echo "Špatná kombinace jména a hesla";
   }
   else{
     setcookie("jwt", $data->jwt);
     setcookie("login", $_POST["login"]);
     header("Location: ".$url_key."index.php");

   }
 }

 if (array_key_exists("jwt", $_COOKIE)) {
   $curl = curl_init();
   $url = $url_key."api/reload_token.php";
   $fields = json_encode(array("jwt" => $_COOKIE["jwt"]));

   curl_setopt($curl, CURLOPT_URL, $url);
   curl_setopt($curl, CURLOPT_POST, true);
   curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, $url);

   $data = curl_exec($curl);
   $data = json_decode($data);

   if (isset($data->error)){
     echo "Vaše přihlášení vypršelo";
   }
   else{
     setcookie("jwt", $data->jwt);
     setcookie("login", $_COOKIE["login"]);
     header("Location: ".$url_key."index.php");
   }
 }


 ?>

 <!doctype html>
 <html>

	 <head>

		 <title>Příběhy - Přihlášení</title>
     <meta charset="UTF-8">
     <meta name="description" content="Příběhy">
     <meta name="keywords" content="Příběhy">
     <meta name="author" content="Jan Slovák">

     <link rel='stylesheet' href='style/style.css'>
     <script type="text/javascript">
       function showPassword() {
         var x = document.getElementById("password_text");
         if (x.type === "password") {
           x.type = "text";
         } else {
           x.type = "password";
         }
       }
     </script>

 	 </head>

   <body>
     <form id="login_form" method="post" action="login.php">
       <label for="login">Login:</label>
       <input type="text" class="form_controll" id="login_text" name="login" placeholder="enter login" value="Mike" required><br>
       <label for="password">Password:</label>
       <input type="password" class="form_controll" id="password_text" name="password" placeholder="Password" value="555" required><br>
       <label for="show_password">Show password:</label>
       <input type="checkbox" class="form_controll" id="show_password" name="show_password" onclick="showPassword()"><br>
       <input type="submit" value="Login">
     </form>
   </body>
 </html>
