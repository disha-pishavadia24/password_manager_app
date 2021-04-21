<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
// include database and object files
include_once 'config/database.php';
include_once 'password.php';
//include_once 'objects/category.php';
  
// get database connection
$database = new Database();
$db = $database->getConnection();

// pass connection to objects
$password = new Password($db);
//$category = new Category($db);
// set page headers
$page_title = "Create Password";
include_once "layout_header.php";
include_once "helper.php";

$name_err = $password_err = $url_err = "";

echo "<div class='right-button-margin'>
        <a href='index.php' class='btn btn-default pull-right'>Read Passwords</a>
    </div>";
  
?>
<?php 
// if the form was submitted - PHP OOP CRUD Tutorial
if($_POST){
    if(empty(trim($_POST["name"]))){
        $name_err = "Please enter site name.";
    } 
    //site url validation
    if(empty(trim($_POST["url"]))){
        $url_err = "Please enter site url.";
    }else{
        $url = trim($_POST["url"]);
        if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$url)) {
            $url_err = "Invalid URL";
        }
    }
    //site password validation
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter password.";
    } 
    
    if(empty($name_err) && empty($url_err) && empty($password_err)){
        // set password property values
        $password->name = $_POST['name'];
        $password->url = $_POST['url'];
        $password->password = encrypt_decrypt(trim($_POST['password']));
        $password->user_id = $_SESSION['id'];
    
        // create the password
        if($password->create()){
            echo "<div class='alert alert-success'>password was created.</div>";
        }
    
        // if unable to create the password, tell the user
        else{
            echo "<div class='alert alert-danger'>Unable to create password.</div>";
        }
    }
}
?>
  
<!-- HTML form for creating a password -->
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
  
    <table class='table table-hover table-responsive table-bordered'>
  
        <tr>
            <td>Name</td>
            <td>
                <input type='text' name='name' class='form-control' />
                <span class="text-danger"><?php echo $name_err; ?></span>
            </td>
            
        </tr>
  
        <tr>
            <td>Site Url</td>
            <td>
                <input type='text' name='url' class='form-control' />
                <span class="text-danger"><?php echo $url_err; ?></span>
            </td>
        </tr>
  
        <tr>
            <td>Password</td>
            <td>
                <input type='text' name='password' class='form-control' />
                <span class="text-danger"><?php echo $password_err; ?></span>            
            </td>
        </tr>
          
        <tr>
            <td></td>
            <td>
                <button type="submit" class="btn btn-primary">Create</button>
            </td>
        </tr>
  
    </table>
</form>
<?php
  
// footer
include_once "layout_footer.php";
?>