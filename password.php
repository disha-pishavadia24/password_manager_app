<?php

include_once 'helper.php';
class Password{
  
    // database connection and table name
    private $conn;
    private $table_name = "user_passwords";
  
    // object properties
    public $id;
    public $name;
    public $url;
    public $password;
    public $user_id;
    public $timestamp;
  
    public function __construct($db){
        $this->conn = $db;
    }
  
    // create password
    function create(){
  
        //write query
        $query = "INSERT INTO
                    " . $this->table_name . "
                SET
                    name=:name, url=:url, password=:password, user_id=:user_id, created=:created";
  
        $stmt = $this->conn->prepare($query);
  
        // posted values
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->url=htmlspecialchars(strip_tags($this->url));
        $this->password=htmlspecialchars(strip_tags($this->password));
        $this->user_id=htmlspecialchars(strip_tags($this->user_id));
  
        // to get time-stamp for 'created' field
        $this->timestamp = date('Y-m-d H:i:s');
  
        // bind values 
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":url", $this->url);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":created", $this->timestamp);
  
        if($stmt->execute()){
            return true;
        }else{
            return false;
        }
  
    }

    function update(){
  
        $query = "UPDATE
                    " . $this->table_name . "
                SET
                    name = :name,
                    password = :password,
                    url = :url,
                    user_id  = :user_id
                WHERE
                    id = :id";
      
        $stmt = $this->conn->prepare($query);
      
        // posted values
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->password=htmlspecialchars(strip_tags($this->password));
        $this->url=htmlspecialchars(strip_tags($this->url));
        $this->user_id=htmlspecialchars(strip_tags($this->user_id));
        $this->id=htmlspecialchars(strip_tags($this->id));
      
        // bind parameters
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':url', $this->url);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':id', $this->id);
      
        // execute the query
        if($stmt->execute()){
            return true;
        }
      
        return false;
          
    }

    
    function delete(){
    
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
    
        if($result = $stmt->execute()){
            return true;
        }else{
            return false;
        }
    }


    function readOne(){
  
        $query = "SELECT
                    name, password, url, user_id
                FROM
                    " . $this->table_name . "
                WHERE
                    id = ?
                LIMIT
                    0,1";
      
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
      
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
      
        $this->name = $row['name'];
        $this->password = $row['password'];
        $this->url = $row['url'];
        $this->user_id = $row['user_id'];
    }

    function readAll($from_record_num, $records_per_page){
  
        $query = "SELECT
                    id, name, url, password, user_id
                FROM
                    " . $this->table_name . "
                ORDER BY
                    name ASC
                LIMIT
                    {$from_record_num}, {$records_per_page}";
      
        $stmt = $this->conn->prepare( $query );
        $stmt->execute();
      
        return $stmt;
    }

    public function countAll(){
  
        $query = "SELECT id FROM " . $this->table_name . "";
      
        $stmt = $this->conn->prepare( $query );
        $stmt->execute();
      
        $num = $stmt->rowCount();
      
        return $num;
    }
}
?>