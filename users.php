<?php

include_once 'helper.php';
class User{
  
    // database connection and table name
    private $conn;
    private $table_name = "users";
  
    // object properties
    public $id;
    public $username;  
    public $password;
  
    public function __construct($db){
        $this->conn = $db;
    }

    function isUserExist(){
        // Prepare a select statement
        $query = "SELECT id FROM users WHERE username = ?";

        if($stmt = $this->conn->prepare( $query )){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(1, $this->username);       
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
               
                // Check if username exists
                if($stmt->rowCount() == 1){
                    return true;
                }
            }
            return false;
        }         
        return false;
    }

    function create(){
  
        //write query
        $query = "INSERT INTO
                    " . $this->table_name . "
                SET
                    username=:username, password=:password";
  
        $stmt = $this->conn->prepare($query);
  
        // posted values
        $this->username=htmlspecialchars(strip_tags($this->username));
        $this->password=htmlspecialchars(strip_tags($this->password));
  
        // bind values 
        $stmt->bindParam(":username", $this->username);        
        $stmt->bindParam(":password", $this->password);
        
  
        if($stmt->execute()){
            return true;
        }else{
            return false;
        }
  
    }

    function login(){
        
            // Prepare a select statement
            $query = "SELECT id, username, password FROM  " . $this->table_name . " WHERE username = ?";
            
            if($stmt = $this->conn->prepare( $query )){
                // Bind variables to the prepared statement as parameters
                $stmt->bindParam(1, $this->username);
                             
                // Set parameters
                $param_username = $this->username;
                
                // Attempt to execute the prepared statement
                if($stmt->execute()){
                    // Store result
                   // $stmt->store_result();
                    
                    // Check if username exists, if yes then verify password
                    if($stmt->rowCount() == 1){                    
                        // Bind result variables
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
      
                        $id = $row['id'];
                        $hashed_password = $row['password'];
                        $username = $row['username'];
                        //$stmt->bind_result($id, $username, $hashed_password);
                        //if($stmt->fetch()){
                            if(password_verify($this->password, $hashed_password)){
                                // Password is correct, so start a new session
                                session_start();
                                
                                // Store data in session variables
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = encrypt_decrypt($id);
                                $_SESSION["username"] = $username;                            
                                return true;
                                // Redirect user to welcome page
                               
                            } else{
                                // Password is not valid, display a generic error message
                                $login_err = "Invalid username or password.";
                            }
                        //}
                    } else{
                        // Username doesn't exist, display a generic error message
                        $login_err = "Invalid username or password.";
                    }
                } else{
                    $login_err = "Oops! Something went wrong. Please try again later.";
                }
                return $login_err;
                // Close statement
                //$stmt->close();
            }
        
    }
}