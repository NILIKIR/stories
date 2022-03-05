<?php
/*
 * Objekt uživatele pro php
 * Projekt: STORIES
 * Vytvořil: Janek
 */

// 'user' object
class User{

    // database connection and table name
    private $conn;
    private $table_name = "users";

    // object properties
    public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $password;
    public $nickname;
    public $login;
    public $charakter;

    // constructor
    public function __construct($db){
        $this->conn = $db;

    }

    // create new user record
    public function create(){

        // insert query
        $query = "INSERT INTO USERS SET
                    name_user = :firstname,
                    surname_user = :lastname,
                    email_user = :email,
                    nickname_user = :nickname,
                    login = :login,
                    password = :password";

        // prepare the query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->firstname=htmlspecialchars(strip_tags($this->firstname));
        $this->lastname=htmlspecialchars(strip_tags($this->lastname));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->password=htmlspecialchars(strip_tags($this->password));
        $this->nickname=htmlspecialchars(strip_tags($this->nickname));
        $this->login=htmlspecialchars(strip_tags($this->login));

        // bind the values
        $stmt->bindParam(':firstname', $this->firstname);
        $stmt->bindParam(':lastname', $this->lastname);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':nickname', $this->nickname);
        $stmt->bindParam(':login', $this->login);

        // hash the password before saving to database
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $password_hash);

        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    // check if given email exist in the database
    public function loginExists(){

        // query to check if email exists
        $query = "SELECT id_user, nickname_user, password
                FROM USERS WHERE login = :LOGIN";

        // prepare the query
        $stmt = $this->conn->prepare( $query );

        // sanitize
        $this->login=htmlspecialchars(strip_tags($this->login));

        // bind given email value
        $stmt->bindParam(":LOGIN", $this->login);

        // execute the query
        $stmt->execute();

        // get number of rows
        $num = $stmt->rowCount();

        // if login exists, assign values to object properties for easy access and use for php sessions
        if($num>0){

            // get record details / values
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // assign values to object properties
            $this->id = $row['id_user'];
            $this->nickname = htmlspecialchars_decode($row['nickname_user']);
            $this->password = htmlspecialchars_decode($row['password']);

            // return true because email exists in the database
            return true;
        }

        // return false if email does not exist in the database
        return false;
    }

    // update a user record
    public function update(){

      // if password needs to be updated
      $password_set=!empty($this->password) ? ", password = :password" : "";

      // if no posted password, do not update the password
      $query = "UPDATE USERS SET
                  name_user = :firstname,
                  surname_user = :lastname,
                  email_user = :email,
                  nickname_user = :login,
                  login = :nickname
                  {$password_set}
              WHERE id_user = :id";

      // prepare the query
      $stmt = $this->conn->prepare($query);

      // sanitize
      $this->firstname=htmlspecialchars(strip_tags($this->firstname));
      $this->lastname=htmlspecialchars(strip_tags($this->lastname));
      $this->email=htmlspecialchars(strip_tags($this->email));
      $this->login=htmlspecialchars(strip_tags($this->login));
      $this->nickname=htmlspecialchars(strip_tags($this->nickname));

      // bind the values from the form
      $stmt->bindParam(':firstname', $this->firstname);
      $stmt->bindParam(':lastname', $this->lastname);
      $stmt->bindParam(':email', $this->email);
      $stmt->bindParam(':nickname', $this->nickname);
      $stmt->bindParam(':login', $this->login);
      $stmt->bindParam(':id', $this->id);

      // hash the password before saving to database
      if(!empty($this->password)){
          $this->password=htmlspecialchars(strip_tags($this->password));
          $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
          $stmt->bindParam(':password', $password_hash);
      }

      // unique ID of record to be edited
      $stmt->bindParam(':id', $this->id);

      // execute the query
      if($stmt->execute()){
        return true;
      }

      return false;
      }
}
