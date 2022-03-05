<?php
 include "objects/log.php";
// required headers
header("Access-Control-Allow-Origin: http://localhost/příběhy/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// required to decode jwt
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
include_once 'libs/php-jwt-master/src/Key.php';
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

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

        // set response code
        http_response_code(200);

        // show user details
        echo json_encode(array(
            "message" => "access granted",
            "data" => $decoded->data,
        ));

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
