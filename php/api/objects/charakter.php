<?php
/*
 * Objekt postavy pro php
 * Projekt: STORIES
 * Vytvořil: Janek
 */

 // 'charakter' object
 class Charakter{

   //database connection...
   private $conn;
   private $table_name = "charakters";

   //object proparties
   public $id;
   public $name;
   public $properties;
   public $id_user;

   //constructor
   public function __construct($db){

     $this->conn = $db;
     include_once 'story.php';
     //write_to_log("CHARAKTER", "IS CONSTRUCTED", "");
   }

   public function main_charakter_id(){

     $query = "SELECT id_charakter AS id FROM CHARAKTERS WHERE ID_USER=:ID_USER";


     //write_to_log("Charakter main_charakter_id query", $query, "");
     $stmt = $this->conn->prepare($query);
     $this->id_user=htmlspecialchars(strip_tags($this->id_user));
     $stmt->bindParam(':ID_USER', $this->id_user);

     //write_to_log("Charakter main_charakter_id query params", ':ID_USER'.$this->id_user, "");
     if($stmt->execute()){//spuštění mysqli query
       if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {//zpracování mysqli query

         //write_to_log("Charakter main_charakter_id query data", json_encode($row), "");
         return $row["id"];
       }
     }
     //write_to_log("Charakter main_charakter_id query database connection", "", "error");
     return false;
   }

   //create a character record
   public function create(){
     // insert query
     $query = "INSERT INTO CHARAKTERS SET
                 ID_USER = :ID_USER,
                 PROPERTIES_CHARAKTER = :properties_charakter,
                 NAME_CHARAKTER = :name_charakter";
     //write_to_log("Charakter main_charakter_id query", $query, "");
     // prepare the query
     $stmt = $this->conn->prepare($query);
     // sanitize
     $this->name=htmlspecialchars(strip_tags($this->name));
     $this->properties=htmlspecialchars(strip_tags($this->properties));
     $this->id_user=htmlspecialchars(strip_tags($this->id_user));
     // bind the values
     $stmt->bindParam(':ID_USER', $this->id_user);
     $stmt->bindParam(':PROPERTIES_CHARAKTER', $this->properties);
     $stmt->bindParam(':NAME_CHARAKTER', $this->name);
     //write_to_log("Charakter create query params", ':ID_USER'.$this->id_user.':PROPERTIES_CHARAKTER'.$this->properties.':NAME_CHARAKTER'.$this->name, "");
     // execute the query, also check if query was successful
     if($stmt->execute()){
         //write_to_log("Charakter create done", "", "");
         return true;
     }
     //write_to_log("Charakter create database", "", "error");

     return false;
   }

   //update a charakter record
   public function update(){
     $query = "UPDATE CHARAKTERS SET
                 NAME_CHARAKTER = :NAME_CHARAKTER,
                 PROPERTIES_CHARAKTER = :PROPERTIES_CHARAKTER
             WHERE ID_CHARAKTER = :ID_CHARAKTER";
     //write_to_log("Charakter update query", $query, "");
     // prepare the query
     $stmt = $this->conn->prepare($query);
     // sanitize
     $this->name=htmlspecialchars(strip_tags($this->name));
     $this->id=htmlspecialchars(strip_tags($this->id));
     $this->properties=htmlspecialchars(strip_tags($this->properties));
     // bind the values
     $stmt->bindParam(':PROPERTIES_CHARAKTER', $this->properties);
     $stmt->bindParam(':ID_CHARAKTER', $this->id);
     $stmt->bindParam(':NAME_CHARAKTER', $this->name);
     //write_to_log("Charakter update query params", ':NAME_CHARAKTER'.$this->name.':ID_CHARAKTER'.$this->id.':PROPERTIES_CHARAKTER'.$this->name, "");
     // execute the query
     if($stmt->execute()){
       //write_to_log("Charakter update done", "", "");
       return true;
     }
     return false;
       //write_to_log("Charakter update database", "", "error");
     }
 }



 ?>
