<?php
/*
 * Objekt příběhu pro php
 * Projekt: STORIES
 * Vytvořil: Janek
 */

 // 'story' object
 class Story{

   //database connection...
   private $conn;
   private $table_name = "stories";

   //object proparties
   public $id;
   public $name;
   public $anotation;
   public $finished;
   public $published;
   public $id_current_paragraph;
   public $id_charakter;
   public $error;

   //constructor
   function __construct($db){
     $this->conn = $db;
     include_once 'paragraph.php';

   }

   //Vyber příběh a odstavec, který se má aktuálně načíst a ověří, zda má daný charakter přístup k příběhu...
   public function select(){
     $query = "SELECT STORIES.FINISHED AS finished,
                      STORIES.PUBLISHED AS published,
                      STORIES.ANOTATION AS anotation,
                      STORIES.STORY AS name,
                      STORIES_PAR_CHARAKTER.ID_PARAGRAPH AS id_paragraph
              FROM STORIES LEFT JOIN STORIES_PAR_CHARAKTER ON STORIES.ID_STORIES = STORIES_PAR_CHARAKTER.ID_STORY
                           LEFT JOIN CHARAKTERS ON STORIES_PAR_CHARAKTER.ID_CHARAKTER = CHARAKTERS.ID_CHARAKTER
              WHERE STORIES_PAR_CHARAKTER.ID_CHARAKTER = :ID_CHARAKTER
              AND   STORIES_PAR_CHARAKTER.ID_STORIES = :ID_STORY";
     $stmt = $this->conn->prepare($query);
     $this->id=htmlspecialchars(strip_tags($this->id));
     $this->charakter=htmlspecialchars(strip_tags($this->charakter));
     $stmt->bindParam(':ID_STORY', $this->id);
     $stmt->bindParam(':ID_CHARAKTER', $this->charakter);
     if($stmt->execute()){//pokud mysqli funkce vrátí error funkce vrací false
       if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {//pokud mysqli vrátí méně než jeden řádek, funkce vrací false
         $this->id = $id_new_story;
         $this->name = htmlspecialchars_decode($row['name']);
         $this->published = $row['published'];
         $this->finished = $row['finished'];
         $this->anotation = htmlspecialchars_decode($row['anotation']);
         $this->id_current_paragraph = $row['id_paragraph'];
         return true;
       }
     }
     return false;
   }

   //Výběr všech publikovaných a dokončených příběhů
   public function show(){
     $query = "SELECT STORIES.FINISHED AS finished,
                      STORIES.PUBLISHED AS published,
                      STORIES.ANOTATION AS anotation,
                      STORIES.STORY AS name,
                      STORIES_PAR_CHARAKTER.ID_PARAGRAPH AS id_current_paragraph,
                      STORIES.ID_STORIES AS id
                 FROM STORIES LEFT JOIN STORIES_PAR_CHARAKTER ON STORIES.ID_STORIES = STORIES_PAR_CHARAKTER.ID_STORY
                WHERE STORIES.PUBLISHED = 1
                  AND STORIES.FINISHED = 1
                  AND (STORIES_PAR_CHARAKTER.ID_CHARAKTER = :ID_CHARAKTER OR STORIES_PAR_CHARAKTER.ID_CHARAKTER IS NULL)
             ORDER BY STORIES.STORY ASC;";
     $stmt = $this->conn->prepare($query);
     $this->id_charakter=htmlspecialchars(strip_tags($this->id_charakter));
     $stmt->bindParam(':ID_CHARAKTER', $this->id_charakter);
     if($stmt->execute()){//pokud mysqli funkce vrátí error funkce vrací false
       $pom = array();
       $pom_2;
       for ($i=0; ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ; $i++) {
         $pom[$i] = new Story($this->conn);
         $pom[$i]->id = $row["id"];
         $pom[$i]->name = htmlspecialchars_decode($row['name']);
         $pom[$i]->published = $row['published'];
         $pom[$i]->finished = $row['finished'];
         $pom[$i]->anotation = htmlspecialchars_decode($row['anotation']);
         $pom[$i]->id_current_paragraph = $row['id_current_paragraph'];
       }
       return $pom;
     }
     return false;
   }
 }

?>
