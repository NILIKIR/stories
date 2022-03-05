<?php
/*
 * prohlížení příběhů
 * Projekt: STORIES
 * Vytvořil: Janek
 */


 include_once('../config/database.php');
 $database = new Database();
 $db = $database->getConnection();

 /*Vypisování příběhů hráči*/
 $story = NULL;
 if (array_key_exists("story",$_GET)) {
   $story = $_GET['story'];
 }
 if ($story!=NULL) {
   $sql = "SELECT STORIES.ID_STORIES, STORY FROM `STORIES`WHERE FINISHED = 1 AND PUBLISHED = 1";

   $stmt = $db->prepare($sql);
   send_data($stmt);
   exit();
 }

 /*Vypisování odstavců a jejich vlastností hráči*/
 $paragraph = NULL;
 if (array_key_exists("paragraph",$_GET)) {
   $paragraph = $_GET['paragraph'];
 }
 if ($paragraph!=NULL) {
    $sql = "SELECT NAME_PARAGRAPH, PARAGRAPH FROM PARAGRAPHS WHERE ID_PARAGRAPH =".$paragraph;
    $stmt = $db->prepare($sql);
    send_data($stmt);
    $sql   = "SELECT ITEMS.ID_ITEM AS ID_ITEM_GOTTEN, ITEMS.NAME_ITEM AS NAME_ITEM_GOTEN,
    ITEMS_PAR_JUMP.COUNT AS COUNT_ITEM_GOTEN, ITEMS.PROPERTIES_ITEM AS PROPERTIES_ITEM_GOTTEN, LABEL, ID_PARAGRAPH_TO,
    conditions.CONDITION, conditions.COUNT AS COUNT_NEEDED, conditions.ID_ITEM AS ID_ITEM_NEEDED
    FROM `ITEMS_PAR_JUMP`
    RIGHT JOIN `JUMPS` ON ITEMS_PAR_JUMP.ID_JUMP=JUMPS.ID_JUMP
    LEFT JOIN `ITEMS` ON ITEMS_PAR_JUMP.ID_ITEM=ITEMS.ID_ITEM
    LEFT JOIN `CONDITIONS_PAR` ON CONDITIONS_PAR.ID_JUMP=JUMPS.ID_JUMP
    LEFT JOIN `CONDITIONS` ON CONDITIONS_PAR.ID_CONDITION=CONDITIONS.ID_CONDITION
    WHERE `ID_PARAGRAPH_FROM` = ".$paragraph;
    $stmt = $db->prepare($sql);
    send_data($stmt);
    exit();
  }

 /*funkce odesílající data*/
 function send_data($stmt){
   if ($stmt->execute()){
     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
       foreach ($row as $key => $value) {
         if ($value != NULL){
           echo $key . ": " . $value.";";
         }
       }
       echo "<br>";
     }
   }else{
     echo $stmt->error;
   }
 }
?>
