<?php
/*
 * Funkce API Ukazující příběhy
 * Projekt: STORIES
 * Vytvořil: Janek
 */
 include "objects/log.php";

 header("Access-Control-Allow-Origin: http://localhost/příběhy/");
 header("Content-Type: application/json; charset=UTF-8");
 header("Access-Control-Allow-Methods: POST");
 header("Access-Control-Max-Age: 3600");
 header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

 // required to decode jwt
 include_once 'config/core.php';
 include_once 'config/database.php';
 include_once 'libs/php-jwt-master/src/BeforeValidException.php';
 include_once 'libs/php-jwt-master/src/ExpiredException.php';
 include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
 include_once 'libs/php-jwt-master/src/JWT.php';
 include_once 'libs/php-jwt-master/src/Key.php';
 include_once 'objects/story.php';
 include_once 'objects/charakter.php';
 include_once 'objects/user.php';
 use \Firebase\JWT\JWT;
 use \Firebase\JWT\Key;

// get database connection
$database = new Database();
$db = $database->getConnection();

//vytvoř instanci třídy Story
$stories = new Story($db);

//vytvoř instanci třídy Story
$charakter = new Charakter($db);

//vytvoř instanci třídy Story
$user = new User($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));

// get jwt
$jwt=isset($data->jwt) ? $data->jwt : exit(json_encode(array( "message" => "Access denied.", "error" => "NO TOKEN SENT")));

if($jwt){
  // if decode succeed, do your job
  try {

    // decode jwt a přiřazení zakódovaných informací
    $decoded = JWT::decode($jwt, $key);
    $user->id= $decoded->data->id;
    $user->login= $decoded->data->login;
    $charakter->id_user = $user->id;
    $stories->id_charakter = $decoded->data->id_charakter;
    $charakter->id = $decoded->data->id_charakter;

    //vlož do $story všechny příběhy podle dostupných kritérií
    $stories = $stories->show();
    // set response code
    http_response_code(200);

    $token = array(
       "iat" => $issued_at,
       "exp" => $expiration_time,
       "iss" => $issuer,
       "data" => array(
           "id" => $user->id,
           "login" => $user->login,
           "id_charakter" => $decoded->data->id_charakter,
       )
    );

    $stories_pom;
    $i = 0;
    foreach ($stories as $story) {
      $stories_pom[$i]["name"]=$story->name;
      $stories_pom[$i]["anotation"]=$story->anotation;
      $i++;
    }

    //generate new key
    $jwt = JWT::encode($token, $key->getKeyMaterial(), $key->getAlgorithm());

    // response in json format
    echo json_encode(
      array(
        "stories" => $stories,
        "jwt" => $jwt,
        "message" => "stories sent",
        "data" => $token["data"],
      )
    );
  }
  // if decode fails, it means jwt is invalid
  catch (Exception $e){
    // set response code
    http_response_code(401);
    // show error message
    //write_to_log("next_paragraph Access denied", $e->getMessage(), "error");
    echo json_encode(array(
      "message" => "access denied",
      "error" => $e->getMessage()
    ));
  }
}
// show error message if jwt is empty
else{
  // set response code
  http_response_code(401);
  //write_to_log("next_paragraph Access denied", "", "error");
  // tell the user access denied
  echo json_encode(array("message" => "access denied"));
}

?>
