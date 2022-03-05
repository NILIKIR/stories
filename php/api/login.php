<?php
/*
 * Funkce API Přihlašující uživatele
 * Projekt: STORIES
 * Vytvořil: Janek
 */

  include "objects/log.php";
// required headers
header("Access-Control-Allow-Origin: http://localhost/příběhy/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// files needed to connect to database
include_once 'config/database.php';
include_once 'objects/user.php';
include_once 'objects/charakter.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate user and charakter object
$user = new User($db);
$charakter = new Charakter($db);
// get posted data
$data = json_decode(file_get_contents("php://input"));

// set product property values
$user->login=isset($data->login) ? $data->login : exit(json_encode(array( "message" => "Access denied.", "error" => "NO LOGIN SENT")));
$login_exists = $user->loginExists();

// generate json web token
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';

// check if email exists and if password is correct
if($login_exists && password_verify($data->password, $user->password)){

    $charakter->id_user = $user->id;

    $token = array(
       "iat" => $issued_at,
       "exp" => $expiration_time,
       "iss" => $issuer,
       "data" => array(
           "id" => $user->id,
           "login" => $user->login,
           "id_charakter" =>$charakter->main_charakter_id()
       )
    );

    // set response code
    http_response_code(200);

    // generate jwt
    $jwt = JWT::encode($token, $key->getKeyMaterial(), $key->getAlgorithm());
    echo json_encode(
            array(
                "message" => "successful login.",
                "jwt" => $jwt,
                "data" => $token["data"],
            )
        );
      }

// login failed
else{

    // set response code
    http_response_code(401);

    // tell the user login failed
    echo json_encode(array("message" => "login failed."));
}
?>
