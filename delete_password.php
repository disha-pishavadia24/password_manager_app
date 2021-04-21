<?php
include_once 'helper.php';
// check if value was posted
if($_POST){
  
    // include database and object file
    include_once 'config/database.php';
    include_once 'password.php';
  
    // get database connection
    $database = new Database();
    $db = $database->getConnection();
  
    // prepare password object
    $password = new password($db);
      
    // set password id to be deleted
    $password->id = encrypt_decrypt($_POST['object_id'],'decrypt');
      
    // delete the password
    if($password->delete()){
        echo "Object was deleted.";
    }
      
    // if unable to delete the password
    else{
        echo "Unable to delete object.";
    }
}
?>