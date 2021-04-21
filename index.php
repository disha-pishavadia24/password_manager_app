<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
// page given in URL parameter, default page is one
$page = isset($_GET['page']) ? $_GET['page'] : 1;
  
// set number of records per page
$records_per_page = 5;
  
// calculate for the query LIMIT clause
$from_record_num = ($records_per_page * $page) - $records_per_page;
  
// include database and object files
include_once 'config/database.php';
include_once 'password.php';
include_once 'helper.php'; 
// instantiate database and objects
$database = new Database();
$db = $database->getConnection();
  
$password = new Password($db);
  
// query passwords
$stmt = $password->readAll($from_record_num, $records_per_page);
$num = $stmt->rowCount();
// set page header
$page_title = "Manage Passwords";
include_once "layout_header.php";?>
  
<div class='right-button-margin'>
    <a href='create_password.php' class='btn btn-default pull-right'>Create Password</a>
</div>

 <?php 
 // display the passwords if there are any
if($num>0){
    
    echo "<table class='table table-hover table-responsive table-bordered'>";
        echo "<tr>";
            echo "<th>Site Name</th>";
            echo "<th>Site Url</th>";
            echo "<th>Password</th>";           
            echo "<th>Actions</th>";
        echo "</tr>";
  
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
  
            extract($row);
            $uid = encrypt_decrypt($id);
            $show_pass = encrypt_decrypt($password,'decrypt');
            echo "<tr>";
                echo "<td>{$name}</td>";
                echo "<td>{$url}</td>";
                echo "<td>{$show_pass}</td>";
                echo "<td>";
                echo "<a href='update_password.php?id={$uid}' class='btn btn-info left-margin'>
                        <span class='glyphicon glyphicon-edit'></span> Edit
                    </a>              
                    <a delete-id='{$uid}' class='btn btn-danger delete-object'>
                        <span class='glyphicon glyphicon-remove'></span> Delete
                    </a>";
                echo "</td>";
  
            echo "</tr>";
  
        }
  
    echo "</table>";
  
    // paging buttons will be here
        // the page where this paging is used
    $page_url = "index.php?";
    
    // count all passwords in the database to calculate total pages
    $passwordObj = new Password($db);
    $total_rows = $passwordObj->countAll();
    
    // paging buttons here
    include_once 'paging.php';
}
  
// tell the user there are no passwords
else{
    echo "<div class='alert alert-info'>No passwords found.</div>";
}
// set page footer
include_once "layout_footer.php";
?>