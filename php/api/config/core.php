<?php
include_once './libs/php-jwt-master/src/Key.php';
use \Firebase\JWT\Key;
// show error reporting
error_reporting(E_ALL);

// set your default time-zone
date_default_timezone_set('Europe/Prague');

// variables used for jwt
$key = new Key("Klíček","HS512");
$issued_at = time();
$expiration_time = $issued_at + (60 * 60); // valid for 1 hour
$issuer = "http://localhost/rest-api-authentication-example/";
?>
