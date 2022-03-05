<?php
/*
 * Objekt odstavce pro php
 * Projekt: STORIES
 * Vytvořil: Janek
 */

// 'paragraph' object
class Paragraph{

  //database connection...
  private $conn;
  private $table_name = "paragraphs";

  //public object properties
  public $id;        //id_odstavce
  public $name;      //jméno odstavce
  public $text;      //text odstavce...
  public $type;      //druh odstavce, blíže popsáno v doc.txt v rootu
  public $jumps_from;//skoky ze současného odstavce
  public $error;     //message, viditelná zvenku, ve které je zapsáno, proč aktuální funkce selhala
  public $jump_to;   //skok na současná odstavec
  public $inventory; //instance inventáře...
  public $id_story;  //id příběhu, ke kterému odstavec patří
  public $id_charakter;


  //constructor
  public function __construct($db){
    $this->conn = $db;
    include_once 'jump.php';
    include_once 'condition.php';
    include_once 'item.php';
    //write_to_log("PARAGRAPH", "IS CONSTRUCTED", "");
  }

  public function select_story()
  {
    $query = "SELECT PARAGRAPHS.ID_PARAGRAPH AS id,
                     PARAGRAPHS.NAME_PARAGRAPH AS name,
                     PARAGRAPHS.PARAGRAPH AS text,
                     PARAGRAPHS.ID_PARAGRAPH_TYPE AS type
                FROM PARAGRAPHS INNER JOIN
                     STORIES_PAR_CHARAKTER ON PARAGRAPHS.ID_PARAGRAPH = STORIES_PAR_CHARAKTER.ID_PARAGRAPH
               WHERE STORIES_PAR_CHARAKTER.ID_CHARAKTER=:ID_CHARAKTER AND
                     STORIES_PAR_CHARAKTER.ID_STORY=:ID_STORY";

    $stmt = $this->conn->prepare($query);
    $this->id_story=htmlspecialchars(strip_tags($this->id_story));
    $this->id_charakter=htmlspecialchars(strip_tags($this->id_charakter));
    $stmt->bindParam(':ID_STORY', $this->id_story);
    $stmt->bindParam(':ID_CHARAKTER', $this->id_charakter);

    if($stmt->execute()){//spuštění mysqli query
      if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {//zpracování mysqli query
        //write_to_log("paragraph available_paragraph query data", json_encode($row), "");
        $this->id = $row['id'];
        $this->name = htmlspecialchars_decode($row['name']);
        $this->text = htmlspecialchars_decode($row['text']);
        $this->type = $row['type'];
        //write_to_log("paragraph available_paragraph jump_to before actualize_stories_par_charakter", json_encode($this->jump_to), "");
      }
    }

    $this->inventory = new Item($this->conn);
    $this->inventory->id_story = $this->id_story;
    $this->inventory->id_charakter = $this->id_charakter;

    $this->inventory = $this->inventory->fill_inventory();

    $this->jumps_from = new Jump($this->conn);
    $this->jumps_from->id_paragraph_from = $this->id;
    $this->jumps_from->inventory = $this->inventory;

    $this->jumps_from = $this->jumps_from->available_jumps();

    return true;

  }

  //Vybírání odstavce podle skoku na něj vedoucího a ověřování, že jde o odstavec, následující skoku, který úživatel mohl využít
  public function select_paragraph($load){
      if($this->jump_to->id||$load){


        //write_to_log("paragraph jump!=0", "", "");

        //Select odstavce a ověřování, zda uživatel mohl skočit z odstavce, ze kterého na něj skákal
        if (!$load){
          if (!$this->available_paragraph()) {
            return false;
            //write_to_log("paragraph !select_paragraph", "", "");
          }

          //Provedení skoku (včetně testu podmínky a započítání předmětů...)
          $this->jump_to->inventory = $this->inventory;
          $this->jump_to->id_paragraph_from_type = $this->type;
          $this->jump_to->id_story = $this->id_story;
          $this->jump_to->id_charakter = $this->id_charakter;
          $this->jump_to->id_paragraph_from = $this->id;

          //write_to_log("paragraph select_paragraph jump before jump", json_encode($this->jump_to), "");

          $this->inventory = $this->jump_to->execute_jump();



          if (!($this->inventory)){
            $this->error = $this->jump_to->error;

            //write_to_log("paragraph select_paragraph after failed jump", "", "error");

            return false;
          }

          //write_to_log("paragraph select_paragraph after executed jump", json_encode($this->inventory), "");
        }






        //najití všech skoků z aktuálního odstavce, ke kterým má uživatel přístup
        switch ($this->type) {
          case 1||2: //první a nebo obyčejný odstavec
            $jump = new Jump($this->conn);
            $jump->inventory = $this->inventory;
            $jump->id_charakter = $this->id_charakter;
            $jump->id_paragraph_from = $this->id;
            $jump->id_story = $this->id_story;

            //write_to_log("paragraph select_paragraph paragraph type 1, 2 before available_jumps", json_encode($jump), "");

            $this->jumps_from = $jump->available_jumps();//return hodnota -> array 0+ skocích...

            //write_to_log("paragraph select_paragraph paragraph type 1, 2 after available_jumps", json_encode($this->jumps_from), "");
            return true;
            break;
          case 3||4: //jeden ze zakončovacích odstavců
            $this->jumps_from = new Jump($this->conn);
            $jump->id_charakter = $this->id_charakter;
            $jump->id_paragraph_from = $this->id;
            $jump->id_story = $this->id_story;

            //write_to_log("paragraph select_paragraph paragraph type 3, 4 before no_jumps", json_encode($jump), "");

            $this->jumps_from = $jump->no_jumps();//return hodnota -> array 0
            //write_to_log("paragraph select_paragraph paragraph type 3, 4 after no_jumps", json_encode($this->jumps_from), "");
            return true;
          default:   //neznámý typ odstavce
            //write_to_log("paragraph select_paragraph unknown paragraph type", json_encode($this->jumps_from), "");
            $this->error = "unknown paragraph type";
            return false;
            break;
          }
        }
        else{
          //write_to_log("paragraph select_paragraph jump=0", "", "");
          if (!$this->first_paragraph()) {
            //write_to_log("paragraph select_paragraph !first_paragraph", $this->error, "error");
            return false;
          }
          $jump = new Jump($this->conn);
          $jump->inventory = $this->inventory;
          $jump->id_paragraph_from = $this->id;
          $jump->id_charakter = $this->id_charakter;
          $jump->id_story = $this->id_story;

          //write_to_log("paragraph select_paragraph jump before available_jumps", json_encode($jump), "");

          $this->jumps_from = $jump->available_jumps();//return hodnota -> array 0+ skocích...

          //write_to_log("paragraph select_paragraph jumps after available_paragraph", json_encode($this->jumps_from), "");
          return true;
        }
    }


  //Select odstavce a ověřování, zda uživatel mohl skočit z odstavce, ze kterého na něj skákal
  private function available_paragraph(){
    $query = "SELECT PARAGRAPHS.ID_PARAGRAPH AS id,
                     PARAGRAPHS.NAME_PARAGRAPH AS name,
                     PARAGRAPHS.PARAGRAPH AS text,
                     PARAGRAPHS.ID_PARAGRAPH_TYPE AS type
                FROM PARAGRAPHS INNER JOIN
                     JUMPS ON PARAGRAPHS.ID_PARAGRAPH=JUMPS.ID_PARAGRAPH_TO INNER JOIN
                     STORIES_PAR_CHARAKTER ON JUMPS.ID_PARAGRAPH_FROM = STORIES_PAR_CHARAKTER.ID_PARAGRAPH
               WHERE STORIES_PAR_CHARAKTER.ID_CHARAKTER=:ID_CHARAKTER AND
                     JUMPS.ID_JUMP=:ID_JUMP";
    //write_to_log("paragraph available_paragraph query", $query, "");
    $stmt = $this->conn->prepare($query);
    $this->jump_to->id=htmlspecialchars(strip_tags($this->jump_to->id));
    $this->id_charakter=htmlspecialchars(strip_tags($this->id_charakter));
    $stmt->bindParam(':ID_JUMP', $this->jump_to->id);
    $stmt->bindParam(':ID_CHARAKTER', $this->id_charakter);

    //write_to_log("paragraph available_paragraph query variables", ':ID_JUMP'.$this->jump_to->id.':ID_CHARAKTER'.$this->id_charakter, "");

    if($stmt->execute()){//spuštění mysqli query
      if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {//zpracování mysqli query
        //write_to_log("paragraph available_paragraph query data", json_encode($row), "");
        $this->id = $row['id'];
        $this->name = htmlspecialchars_decode($row['name']);
        $this->text = htmlspecialchars_decode($row['text']);
        $this->type = $row['type'];

        $this->jump_to->id_paragraph_from_type = $this->type;
        $this->jump_to->id_charakter = $this->id_charakter;
        $this->jump_to->id_paragraph_from = $this->id;
        $this->jump_to->id_story = $this->id_story;

        //write_to_log("paragraph available_paragraph jump_to before actualize_stories_par_charakter", json_encode($this->jump_to), "");

        $this->jump_to->actualize_stories_par_charakter();
      }
      else{
        //write_to_log("paragraph available_paragraph query no data", "", "error");
        $this->error = "neexistuje odstavec, na který by zadaná postava, vlastněná zadaným uživatelem mohla skočit zadaným skokem";
        return false;
      }
    }
    else{
      //write_to_log("paragraph available_paragraph query database error", "", "error");
      $this->error = "Chyba při načítání odstavců:
      ".$stmt->error;
      return false;
    }
    return true;
  }

  private function first_paragraph(){
      $query = "SELECT PARAGRAPHS.ID_PARAGRAPH AS id,
                       PARAGRAPHS.NAME_PARAGRAPH AS name,
                       PARAGRAPHS.PARAGRAPH AS text,
                       PARAGRAPHS.ID_PARAGRAPH_TYPE AS type
                  FROM PARAGRAPHS
                 WHERE ID_STORY = :ID_STORY
                   AND ID_PARAGRAPH_TYPE = 1";

      //write_to_log("paragraph first_paragraph query database query", $query, "");
      $stmt = $this->conn->prepare($query);
      $this->id_story=htmlspecialchars(strip_tags($this->id_story));
      $stmt->bindParam(':ID_STORY', $this->id_story);
      //write_to_log("paragraph first_paragraph query variables", ':ID_STORY'.$this->id_story, "");
      if($stmt->execute()){//spuštění mysqli query
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {//zpracování mysqli query
          //write_to_log("paragraph first_paragraph query data", json_encode($row), "");
          $this->id = $row['id'];
          $this->name = htmlspecialchars_decode($row['name']);
          $this->text = htmlspecialchars_decode($row['text']);
          $this->type = $row['type'];

          $this->jump_to->id_paragraph_from_type = $this->type;
          $this->jump_to->id_charakter = $this->id_charakter;
          $this->jump_to->id_paragraph_from = $this->id;
          $this->jump_to->id_story = $this->id_story;

          //write_to_log("paragraph first_paragraph jump_to before actualize_stories_par_charakter and delete_stories_par_charakter", json_encode($this->jump_to), "");

          $this->jump_to->delete_stories_par_charakter();
          $this->jump_to->actualize_stories_par_charakter();
          $this->inventory = $this->inventory[0]->fill_inventory();

          //write_to_log("paragraph first_paragraph after delete inventory", json_encode($this->inventory), "");

        }
        else{
          $this->error = "neexistuje první odstavec v zadaném příběhu";
          //write_to_log("paragraph first_paragraph query no paragraph", $this->error, "error");
          return false;
        }
      }
      else{
        $this->error = "Chyba při načítání odstavců:
         ".$stmt->error;
         //write_to_log("paragraph first_paragraph query database error", $this->error, "error");
        return false;
      }
      return true;
  }
}
?>
