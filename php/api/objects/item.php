<?php
/*
 * Objekt skoku pro php
 * Projekt: STORIES
 * Vytvořil: Janek
 */

 // 'item' object
 class Item{

   //database connection...
   private $conn;
   private $table_name = "items";

   //object proparties
   public $id_item;
   public $name_item;
   public $properties_item;
   public $count_item;
   public $id_charakter;
   public $error;
   public $id_story;

   //constructor
   public function __construct($db){
     $this->conn = $db;
     include_once 'item.php';

     //write_to_log("ITEM", "IS CONSTRUCTED", "");
   }

   //nahraje na server aktuální verzi předmětu pro daného hráče
   public function actualize_inventory($inventory){
     foreach ($inventory as $item) {
       //write_to_log("ITEM actualize inventory", json_encode($item), "");
       if(($this->id_item == $item->id_item)&&($this->count_item + $item->count_item >= 0)){
         //write_to_log("ITEM actualize inventory", "there is enough needed item in the inventory", "");


         $item->count_item = $this->count_item + $item->count_item;

         //write_to_log("ITEM actualize inventory", json_encode($item), "");

         $query = "UPDATE ITEMS_PAR_CHARAKTER
                       SET COUNT = :COUNT
                     WHERE ID_CHARAKTER = :ID_CHARAKTER
                       AND ID_ITEM = :ID_ITEM";
         //write_to_log("ITEM actualize inventory query", $query, "");
         $stmt = $this->conn->prepare($query);
         $this->id_item=htmlspecialchars(strip_tags($this->id_item));
         $this->count_item=htmlspecialchars(strip_tags($this->count_item));
         $this->id_charakter=htmlspecialchars(strip_tags($this->id_charakter));
         $stmt->bindParam(':COUNT', $item->count_item);
         $stmt->bindParam(':ID_CHARAKTER', $item->id_charakter);
         $stmt->bindParam(':ID_ITEM', $item->id_item);

         //write_to_log("ITEM actualize inventory query data", ':COUNT'.$item->count_item.':ID_CHARAKTER'.$item->id_charakter.':ID_ITEM'.$item->id_item, "");
         if($stmt->execute()){//spuštění mysqli query
           //write_to_log("ITEM actualize inventory", "database query succeded", "");
           return $inventory;
         }
         else {
           //write_to_log("ITEM actualize inventory", "database", "error");
           $this->error = "chyba při aktualizování inventáře";
           return false;
         }
       }
     }

     //write_to_log("ITEM actualize inventory", "there was not enough needed item in the inventory", "");
     if($this->count_item>=0){



       //write_to_log("ITEM actualize inventory zaktualizovaný předmět", json_encode($inventory[$i]), "");

       $query = "INSERT INTO ITEMS_PAR_CHARAKTER
                             (COUNT, ID_CHARAKTER, ID_ITEM)
                      VALUES (:COUNT, :ID_CHARAKTER, :ID_ITEM)";
       //write_to_log("ITEM actualize inventory query", $query, "");
       $stmt = $this->conn->prepare($query);
       $this->id_item=htmlspecialchars(strip_tags($this->id_item));
       $this->count_item=htmlspecialchars(strip_tags($this->count_item));
       $this->id_charakter=htmlspecialchars(strip_tags($this->id_charakter));
       $stmt->bindParam(':COUNT', $this->count_item);
       $stmt->bindParam(':ID_CHARAKTER', $this->id_charakter);
       $stmt->bindParam(':ID_ITEM', $this->id_item);
       //write_to_log("ITEM actualize inventory query data", ':COUNT'.$item->count_item.':ID_CHARAKTER'.$item->id_charakter.':ID_ITEM'.$item->id_item, "");
       if($stmt->execute()){//spuštění mysqli query//write_to_log("ITEM actualize inventory query data", ':COUNT'.$item->count_item.':ID_CHARAKTER'.$item->id_charakter.':ID_ITEM'.$item->id_item, "");
         write_to_log("ITEM actualize inventory query database", "successfull" , "");
         //write_to_log("ITEM actualize inventory", "there was not the item in the inventory", "");
         $query = "SELECT ITEMS_PAR_CHARAKTER.ID_ITEM AS id_item,
                           ITEMS_PAR_CHARAKTER.COUNT AS count_item,
                           ITEMS.NAME_ITEM AS name_item,
                           ITEMS.PROPERTIES_ITEM AS properties_item
                      FROM ITEMS
                INNER JOIN ITEMS_PAR_CHARAKTER ON ITEMS_PAR_CHARAKTER.ID_ITEM=ITEMS.ID_ITEM
                     WHERE ITEMS_PAR_CHARAKTER.ID_ITEMS_PAR_CHARAKTER = :id_items_par_charakter";
         $stmt = $this->conn->prepare($query);

         $id_items_par_charakter=htmlspecialchars(strip_tags($this->conn->lastInsertId()));
         $stmt->bindParam(':id_items_par_charakter', $id_items_par_charakter);
         if($stmt->execute()){
           $i = count($inventory)-1;
           echo $i;
           //write_to_log("ITEM actualize inventory počet předmětů v inventáři", $i, "");
           if ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
             $inventory[$i]= new Item($this->conn);
             $inventory[$i]->count_item = $row["count_item"];
             $inventory[$i]->id_item = $row["id_item"];
             $inventory[$i]->name_item = htmlspecialchars_decode($row["name_item"]);
             $inventory[$i]->properties_item = htmlspecialchars_decode($row["properties_item"]);
           }
         }
         return $inventory;
       }
       else {
         //write_to_log("ITEM actualize inventory query database", "unsuccessfull" , "error");
         $this->error = "chyba při přidání předmětu do inventáře";
         return false;
       }
     }else{
       //write_to_log("ITEM actualize inventory", "not enough items in the inventory" , "error");
       $this->error = "Pokus o odebrání předmětů z inventáře, ve kterém tento předmět není nebyl úspěšný";
       return $false;
     }
   }


   //vloží do inventáře všechny předměty mající souvislost s právě hraným příběhem
   public function fill_inventory(){
     $pom = array();
     $query = "SELECT ITEMS_PAR_CHARAKTER.ID_ITEM AS id_item,
                       ITEMS_PAR_CHARAKTER.COUNT AS count_item,
                       ITEMS.NAME_ITEM AS name_item,
                       ITEMS.PROPERTIES_ITEM AS properties_item
                  FROM ITEMS
            INNER JOIN ITEMS_PAR_CHARAKTER ON ITEMS_PAR_CHARAKTER.ID_ITEM=ITEMS.ID_ITEM
            INNER JOIN ITEMS_PAR_STORIES ON ITEMS_PAR_STORIES.ID_ITEM=ITEMS.ID_ITEM
                 WHERE ID_STORY = :ID_STORY
                   AND ID_CHARAKTER = :ID_CHARAKTER";
     //write_to_log("ITEM fill inventory query", $query , "");
     $stmt = $this->conn->prepare($query);
     $this->id_charakter=htmlspecialchars(strip_tags($this->id_charakter));
     $this->id_story=htmlspecialchars(strip_tags($this->id_story));
     $stmt->bindParam(':ID_STORY', $this->id_story);
     $stmt->bindParam(':ID_CHARAKTER', $this->id_charakter);
     //write_to_log("ITEM fill inventory query data", ':ID_CHARAKTER'.$this->id_charakter.':ID_STORY'.$this->id_story , "");
     if($stmt->execute()){//spuštění mysqli query
       //write_to_log("ITEM fill inventory query", 'successfull' , "");
       $i=0;
       while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
         $pom[$i] = new Item($this->conn);
         $pom[$i]->id_item = $row['id_item'];
         $pom[$i]->name_item = htmlspecialchars_decode($row['name_item']);
         $pom[$i]->properties_item = htmlspecialchars_decode($row['properties_item']);
         $pom[$i]->count_item = $row['count_item'];
         $pom[$i]->id_charakter = $this->id_charakter;
         $i++;
         //write_to_log("ITEM fill inventory item".$i, json_encode($pom) , "");
       }
     }
     else {
       //write_to_log("ITEM fill inventory query", "unsuccessfull" , "error");
       $this->error = "chyba při načítání předmětů do inventáře";
       return false;
     }
     if (!array_key_exists("0", $pom)) $pom["0"] = new Item($this->conn);
     //write_to_log("ITEM fill inventory first item", json_encode($pom["0"]) , "");
     return $pom;
   }
 }
 ?>
