<?php
function write_to_log($name, $value, $property){
  include_once 'config/database.php';
  $database = new Database();
  $db = $database->getConnection();
  $query = "INSERT INTO DEBUG (NAME, VALUE, PROPERTY) VALUES ('".$name."','".$value."','".$property."')";
  $stmt = $db->prepare($query);
  $stmt->execute();
}
 ?>
