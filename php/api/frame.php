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
 include_once 'objects/charakter.php';
 include_once 'objects/user.php';
 use \Firebase\JWT\JWT;
 use \Firebase\JWT\Key;

// get database connection
$database = new Database();
$db = $database->getConnection();

//vytvoř instanci třídy Story
$user = new User($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));

// get jwt
$jwt=isset($data->jwt) ? $data->jwt : exit(json_encode(array( "message" => "Access denied.", "error" => "NO TOKEN SENT")));

if($jwt){
  // if decode succeed, do your job
  try {
    // decode jwt
    $decoded = JWT::decode($jwt, $key);
    $user->id= $decoded->data->id;
    $user->login= $decoded->data->login;

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
    $jwt = JWT::encode($token, $key->getKeyMaterial(), $key->getAlgorithm());
    // set response code
    http_response_code(200);
    // response in json format
    echo json_encode(
      array(
        "jwt" => $jwt,
        "data" => $token["data"],
      )
    );
  }

  // if decode fails, it means jwt is invalid
  catch (Exception $e){
    // set response code
    http_response_code(401);
    // show error message
    echo json_encode(array(
      "message" => "Access denied.",
      "error" => $e->getMessage()
    ));
  }
}
// show error message if jwt is empty
else{
  // set response code
  http_response_code(401);
  // tell the user access denied
  echo json_encode(array("message" => "Access denied."));
}

?>
