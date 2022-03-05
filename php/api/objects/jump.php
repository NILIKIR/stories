<?php
/*
 * Objekt skoku pro php
 * Projekt: STORIES
 * Vytvořil: Janek
 */

 // 'jump' object
 class Jump{

   //database connection...
   private $conn;
   private $table_name = "jumps";

   //object proparties
   public $id;
   public $label;
   public $id_paragraph_to;
   public $id_paragraph_from;
   public $id_paragraph_from_type;
   public $item_gotten;
   public $condition;
   public $error;
   public $inventory; //array prvků objektu item
   public $id_charakter;
   public $id_story;

   //constructor
   public function __construct($db){
     $this->conn = $db;
     include_once 'item.php';
     include_once 'condition.php';

     //write_to_log("JUMP", "IS CONSTRUCTED", "");
   }

   //Provedení skoku (včetně testu podmínky a započítání předmětů...)
   public function execute_jump(){
     //kontrola podmínek
     $this->condition = new Condition($this->conn);
     $this->condition->inventory = $this->inventory;
     $this->condition->id_jump = $this->id;
     //write_to_log("jump execute_jump has_valid_conditions", json_encode($this->condition), "");
     if (!$this->condition->has_valid_conditions()) {
       $this->error = $this->condition->error;
       //write_to_log("jump execute_jump !has_valid_conditions", $this->error, "error");

       return false;
     }

     //Aktualizování inventáře (jak na straně uživatele tak na straně serveru)
     $query = "SELECT ITEMS_PAR_JUMP.ID_ITEM AS id_item_gotten,
                      ITEMS_PAR_JUMP.COUNT AS count_item_gotten
                 FROM ITEMS_PAR_JUMP
                WHERE ID_JUMP=:ID_JUMP";
     //write_to_log("jump actualize_inventory query", $query, "");

     $stmt = $this->conn->prepare($query);
     $this->id=htmlspecialchars(strip_tags($this->id));
     $stmt->bindParam(':ID_JUMP', $this->id);
     //write_to_log("jump actualize_inventory query params", ':ID_JUMP'.$this->id, "");
     if($stmt->execute()){//spuštění mysqli query
       while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {//zpracování mysqli query
         //přiřazování proměnných

         //write_to_log("jump actualize_inventory item_to_actualize", json_encode($row), "");
         $this->item_gotten = new Item($this->conn);
         $this->item_gotten->id_item = $row['id_item_gotten'];
         $this->item_gotten->count_item = $row['count_item_gotten'];
         $this->item_gotten->id_charakter = $this->id_charakter;
         //write_to_log("jump actualize_inventory item_to_actualize", json_encode($this->item_gotten), "");
         $this->inventory = $this->item_gotten->actualize_inventory($this->inventory);
         if(!($this->inventory)){
           $this->error = $this->item_gotten->error;
           //write_to_log("jump actualize_inventory !actualize_inventory", ($this->error), "error");
           return false;
         }
       }
       //write_to_log("jump actualize_stories_par_charakter", json_encode($this), "");
       if(!$this->actualize_stories_par_charakter()){
         //write_to_log("jump actualize_inventory !actualize_stories_par_charakter", ($this->error), "error");
         return false;
       }
     }
     else {
       $this->error = "chyba při načítání předmětů";
       //write_to_log("jump actualize_inventory database", ($this->error), "error");
       return false;
     }
     return $this->inventory;
   }

   public function actualize_stories_par_charakter()
   {
     if ($this->id_paragraph_from_type == 1){
       $query = "INSERT INTO STORIES_PAR_CHARAKTER
                              (ID_STORY, ID_CHARAKTER, ID_PARAGRAPH)
                       VALUES (:ID_STORY, :ID_CHARAKTER, :ID_PARAGRAPH)";

     }
     else{
       $query = "UPDATE STORIES_PAR_CHARAKTER
                    SET ID_PARAGRAPH = :ID_PARAGRAPH
                  WHERE ID_CHARAKTER = :ID_CHARAKTER
                    AND ID_STORY = :ID_STORY";
     }
     //write_to_log("jump actualize_stories_par_charakter query", $query, "");
     $stmt = $this->conn->prepare($query);
     $this->id_paragraph_from=htmlspecialchars(strip_tags($this->id_paragraph_from));
     $stmt->bindParam(':ID_PARAGRAPH', $this->id_paragraph_from);
     $this->id_charakter=htmlspecialchars(strip_tags($this->id_charakter));
     $stmt->bindParam(':ID_CHARAKTER', $this->id_charakter);
     $this->id_story=htmlspecialchars(strip_tags($this->id_story));
     $stmt->bindParam(':ID_STORY', $this->id_story);

     //write_to_log("jump actualize_stories_par_charakter query variables", ':ID_PARAGRAPH'.$this->id_paragraph_from.':ID_CHARAKTER'.$this->id_charakter.':ID_STORY'.$this->id_story, "");
     if($stmt->execute()){
       //write_to_log("jump actualize_stories_par_charakter query done", "", "");
       return true;
     }
     $this->error = "Nepodařilo se aktualizovat skok";
       //write_to_log("jump actualize_stories_par_charakter query database", "", "error");
     return false;
   }

   //najití všech skoků z aktuálního odstavce, ke kterým má uživatel přístup
   public function available_jumps(){
     $pom = null; //pole skoků vracené funkcí
     $query = "SELECT ID_JUMP AS id, LABEL AS label
                FROM JUMPS WHERE ID_PARAGRAPH_FROM = :ID_PARAGRAPH_FROM";
     //write_to_log("jump available_jumps query", $query, "");
     $stmt = $this->conn->prepare($query);
     $this->id_paragraph_from=htmlspecialchars(strip_tags($this->id_paragraph_from));
     $stmt->bindParam(':ID_PARAGRAPH_FROM', $this->id_paragraph_from);
     //write_to_log("jump available_jumps query variables", ':ID_PARAGRAPH_FROM'.$this->id_paragraph_from, "");
     if($stmt->execute()){//spuštění mysqli query
       $i=0;
       while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {//zpracování mysqli query
         //write_to_log("jump available_jumps query done worked on jump", json_encode($row), "");
         //přiřazování proměnných
         $this->id = $row['id'];
         $this->label = htmlspecialchars_decode($row['label']);
         $this->condition = new Condition($this->conn);
         $this->condition->inventory = $this->inventory;
         $this->condition->id_jump = $this->id;

         //write_to_log("jump available_jumps condition of jump before check", json_encode($this->condition), "");
         if(!$this->condition->has_valid_conditions()){
           //write_to_log("jump available_jumps condition not valid", "", "");
         }
         else {//Zapsání do pole, které bude následně vráceno jako return hodnota
           $pom[$i]["id"] = $this->id;
           $pom[$i]["label"] = $this->label;
           $i++;
           //write_to_log("jump available_jumps condition valid jump sent", json_encode($pom[$i]), "");
         }
       }
     }
     else {
       $this->error = "chyba při načítání podmínek načítaných skoků";
       return false;
     }
     if ($pom == null) {

       return $this->no_jumps();
     }
     $this->inventory = $this->inventory["0"]->fill_inventory();
     //write_to_log("next_paragraph inventory data", json_encode($this->inventory), "");
     return $pom;
   }

   //ukládá data, říkající, že pro danou osobu neexistuje žádný skok vedoucí z tohoto odstavce
   public function no_jumps(){
     $this->delete_stories_par_charakter();

     //write_to_log("next_paragraph inventory data", json_encode($this->inventory), "");

     return array("id" => 0, "label" => "there is no jump available for this paragraph");
   }

   public function delete_stories_par_charakter()
   {
     //delete previous saves
     $query = "DELETE FROM STORIES_PAR_CHARAKTER WHERE ID_CHARAKTER = :ID_CHARAKTER AND ID_STORY = :ID_STORY";
     //write_to_log("jump delete_stories_par_charakter query", $query, "");
     $stmt = $this->conn->prepare($query);
     $this->id_charakter=htmlspecialchars(strip_tags($this->id_charakter));
     $stmt->bindParam(':ID_CHARAKTER', $this->id_charakter);
     $this->id_story=htmlspecialchars(strip_tags($this->id_story));
     $stmt->bindParam(':ID_STORY', $this->id_story);

     //write_to_log("jump delete_stories_par_charakter query 1 params", ':ID_STORY'.$this->id_story.':ID_CHARAKTER'.$this->id_charakter, "");

     if(!$stmt->execute())   {//write_to_log("jump delete_stories_par_charakter query 1 done", $query, "error");
     }//else  write_to_log("jump delete_stories_par_charakter query 1 done", "", "");

     //delete items...
     $query = "SELECT ID_ITEM AS id_item FROM ITEMS_PAR_STORIES WHERE ID_STORY = :ID_STORY";

     //write_to_log("jump delete_stories_par_charakter query 2", $query, "");
     $stmt = $this->conn->prepare($query);
     $this->id_story=htmlspecialchars(strip_tags($this->id_story));
     $stmt->bindParam(':ID_STORY', $this->id_story);

     //write_to_log("jump delete_stories_par_charakter query 2 params", ':ID_STORY'.$this->id_story, "");

     if(!$stmt->execute())  { //write_to_log("jump delete_stories_par_charakter query 2 done", "", "error");
     }//else  write_to_log("jump delete_stories_par_charakter query 2 done", "", "");

     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
       $query = "DELETE FROM ITEMS_PAR_CHARAKTER WHERE ID_CHARAKTER = :ID_CHARAKTER AND ID_ITEM = :ID_ITEM";
       //write_to_log("jump delete_stories_par_charakter query 3", $query, "");
       $stmt = $this->conn->prepare($query);
       $this->id_charakter=htmlspecialchars(strip_tags($this->id_charakter));
       $stmt->bindParam(':ID_CHARAKTER', $this->id_charakter);
       $row["id_item"]=htmlspecialchars(strip_tags($row["id_item"]));
       $stmt->bindParam(':ID_ITEM', $row["id_item"]);

       //write_to_log("jump delete_stories_par_charakter query 3 params", ':ID_ITEM'.$row["id_item"].':ID_CHARAKTER'.$this->id_charakter, "");

       if(!$stmt->execute())   {//write_to_log("jump delete_stories_par_charakter query 3 done", $query, "error");
       }//else  write_to_log("jump delete_stories_par_charakter query 3 done", "", "");
     }
   }
 }
 ?>
