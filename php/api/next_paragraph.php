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
 include_once 'objects/jump.php';
 include_once 'objects/paragraph.php';
 include_once 'objects/item.php';
 use \Firebase\JWT\JWT;
 use \Firebase\JWT\Key;

//write_to_log("next_paragraph dependencies", "done", "");

// get database connection
$database = new Database();
$db = $database->getConnection();

//vytvoř instanci třídy User, Item, Jump a Paragraph
$user = new User($db);
$inventory = new Item($db);
$paragraph = new Paragraph($db);
// get posted data
$data = json_decode(file_get_contents("php://input"));
// get jwt
$jwt=isset($data->jwt) ? $data->jwt : exit(json_encode(array( "message" => "Access denied.", "error" => "NO TOKEN SENT")));
$id_story=isset($data->id_story) ? $data->id_story : exit(json_encode(array( "message" => "Access denied.", "error" => "NO STORY SENT")));
$id_jump=isset($data->id_jump) ? $data->id_jump : "0";

 //write_to_log("next_paragraph incomming data", json_encode($data), "");

if($jwt){
  // if decode succeed, do your job
  try {
    // decode jwt and populate data
    $decoded = JWT::decode($jwt, $key);
    $user->id= $decoded->data->id;
    $user->login = $decoded->data->login;
    $user->charakter = $decoded->data->id_charakter;

    //write_to_log("next_paragraph jwt data", json_encode($decoded), "");

    //populate inventory
    $inventory->id_story = $id_story;
    $inventory->id_charakter = $user->charakter;
    $inventory = $inventory->fill_inventory();

    //write_to_log("next_paragraph before sent inventory", json_encode($inventory), "");

    //populate paragraph
    $paragraph->jump_to = new Jump($db);
    $paragraph->jump_to->id = $id_jump;
    $paragraph->inventory = $inventory;
    $paragraph->id_charakter = $user->charakter;
    $paragraph->id_story = $id_story;

    //write_to_log("next_paragraph paragraph init", json_encode($paragraph), "");

    if (!$paragraph->select_paragraph(false)){
      //write_to_log("next_paragraph select_paragraph", $paragraph->error, "error");
      exit(json_encode(array( "message" => "Access denied.", "error" => $paragraph->error)));
    }


    $inventory_pom;
    $i = 0;
    foreach ($paragraph->inventory as $item) {
      $inventory_pom[$i]["name_item"]=$item->name_item;
      $inventory_pom[$i]["properties_item"]=$item->properties_item;
      $inventory_pom[$i]["count_item"]=$item->count_item;
      $i++;
    }

    $paragraph_pom["jumps"] = $paragraph->jumps_from;
    $paragraph_pom["text"] = $paragraph->text;
    $paragraph_pom["name"] = $paragraph->name;

    //write_to_log("next_paragraph before sent inventory data", json_encode($inventory_pom), "");

    // we need to re-generate jwt because user details might be different
    $token = array(
         "iat" => $issued_at,
         "exp" => $expiration_time,
         "iss" => $issuer,
         "data" => array(
             "id" => $user->id,
             "login" => $user->login,
             "id_charakter" => $user->charakter,
         )
      );
    $jwt = JWT::encode($token, $key->getKeyMaterial(), $key->getAlgorithm());
    // set response code
    http_response_code(200);
    // response in json format
    echo json_encode(
      array(
        "message" => "story sent",
        "jwt" => $jwt,
        "paragraph" => $paragraph_pom,
        "inventory" => $inventory_pom,
        "id_story" => $paragraph->id_story,
        "data" => $token["data"],
      )
    );

    //write_to_log("next_paragraph after sent inventory", "", "");
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
