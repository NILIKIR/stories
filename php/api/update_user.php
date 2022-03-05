<?php
/*
 * Funkce API Aktualizující uživatele
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

// required to encode json web token
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
include_once 'libs/php-jwt-master/src/Key.php';
include_once 'objects/charakter.php';
use \Firebase\JWT\Key;
use \Firebase\JWT\JWT;

// files needed to connect to database
include_once 'config/database.php';
include_once 'objects/user.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate user object
$user = new User($db);
$charakter = new Charakter($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));

// get jwt
$jwt=isset($data->jwt) ? $data->jwt : "";

// if jwt is not empty
if($jwt){

    // if decode succeed, show user details
    try {

        // decode jwt
        $decoded = JWT::decode($jwt, $key);

        // set user property values
        $user->firstname = $data->firstname;
        $user->lastname = $data->lastname;
        $user->email = $data->email;
        $user->password = $data->password;
        $user->nickname = $data->nickname;
        $user->login = $data->login;
        $user->id = $decoded->data->id;
        $charakter->id_user = $user->id;

        // update the user record
        if($user->update()){
          // we need to re-generate jwt because user details might be different
          $token = array(
             "iat" => $issued_at,
             "exp" => $expiration_time,
             "iss" => $issuer,
             "data" => array(
                 "id" => $user->id,
                 "login" => $user->login
                 "id_charakter" =>$charakter->main_charakter_id(),
             )
          );
          $jwt = JWT::encode($token, $key->getKeyMaterial(), $key->getAlgorithm());

          // set response code
          http_response_code(200);

          // response in json format
          echo json_encode(
                  array(
                      "message" => "user was updated",
                      "jwt" => $jwt,
                      "data" => $token["data"],
                  )
              );
          }

        // message if unable to update user
        else{
        // set response code
        http_response_code(401);

        // show error message
        echo json_encode(array("message" => "unable to update user"));
      }
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
