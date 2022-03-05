<?php
/*
 * Objekt skoku pro php
 * Projekt: STORIES
 * Vytvořil: Janek
 */

 // 'condition' object
 class Condition{

   //database connection...
   private $conn;
   private $table_name = "conditions";

   //object proparties
   public $id;
   public $condition;
   public $count;
   public $item;
   public $error;
   public $id_jump;
   public $inventory;

   //constructor
   public function __construct($db){
     $this->conn = $db;
     //write_to_log("CONDITION", "IS CONSTRUCTED", "");

   }

   //funkce ověřující platnost podmínky
   public function is_valid(){//$inventory je array prvků objektu item
     foreach ($this->inventory as $item) {
       //write_to_log("CONDITION is valid concerned item", json_encode($item) , "");
       if ($item->id_item == $this->item) {
         switch ($this->condition) {
           case 1:
             //write_to_log("CONDITION is valid needs at least", $this->count , "");
             if ($item->count_item - $this->count >=0) {
               return true;
             }
             else{
               //write_to_log("CONDITION is valid not enough items", "" , "");
               $this->error = "Pro uskutečnění dané podmínky není dostatečný počet předmětů";
               return false;
             }
             break;
           case 2:
             //write_to_log("CONDITION is valid can have at most", $this->count , "");
             if ($item->count_item - $this->count < 0) {
               return true;
             }
             else{
               //write_to_log("CONDITION is valid too much items", "" , "");
               $this->error = "Pro uskutečnění dané podmínky je příliš vysoký počet předmětů";
               return false;
             }
             break;

           default:
             //write_to_log("CONDITION is valid unknown condition", "" , "");
             $this->error = "zadaná podmínka není naprogramovaná";
             return false;
             break;
         }
       }
     }
     switch ($this->condition) {
        case 2:
         //write_to_log("CONDITION is valid can have at most", $this->count , "");
         return true;
         break;

       default:
         //write_to_log("CONDITION is valid unknown condition", "" , "");
         $this->error = "zadaná podmínka není naprogramovaná a nebo není dost předmětů v inventáři";
         return false;
         break;
     }
   }

   public function has_valid_conditions(){
     $query = "SELECT CONDITIONS.CONDITION AS condition_type,
                       CONDITIONS.ID_ITEM AS id_item_needed,
                       CONDITIONS.COUNT AS count_item_needed
                FROM   CONDITIONS INNER JOIN  CONDITIONS_PAR ON
                       CONDITIONS.ID_CONDITION=CONDITIONS_PAR.ID_CONDITION
                WHERE  ID_JUMP=:ID_JUMP";
     //write_to_log("CONDITION has valid conditions query", $query , "");
     $stmt = $this->conn->prepare($query);
     $this->id_jump=htmlspecialchars(strip_tags($this->id_jump));
     $stmt->bindParam(':ID_JUMP', $this->id_jump);
     //write_to_log("CONDITION has valid conditions query data", ':ID_JUMP'.$this->id_jump , "");
     if($stmt->execute()){//spuštění mysqli query
       //write_to_log("CONDITION has valid conditions query", 'successfull' , "");
       while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {//zpracování mysqli query
         //write_to_log("CONDITION has valid conditions current condition", json_encode($row) , "");
         //přiřazování proměnných
         $this->condition = $row['condition_type'];
         $this->item = $row['id_item_needed'];
         $this->count = $row['count_item_needed'];
         if(!$this->is_valid()){
           //write_to_log("CONDITION is not valid", "" , "");
           return false;
         }
       }
     }
     else {
       //write_to_log("CONDITION has valid conditions query", "unsuccessfull" , "error");
       $this->error = "chyba při načítání podmínek probíhajícího skoku";
       return false;
     };
     //write_to_log("CONDITION has valid conditions", "successfull" , "");
     return true;
   }
 }
 ?>
