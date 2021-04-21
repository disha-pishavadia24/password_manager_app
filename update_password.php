<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
include_once 'helper.php';
include_once 'config/database.php';
include_once 'password.php';
// set page header
$page_title = "Update Password";
include_once "layout_header.php";

// get ID of the password to be edited
$id = isset($_GET['id']) ? encrypt_decrypt($_GET['id'],'decrypt') : die('ERROR: missing ID.');
 
$name_err = $password_err = $url_err = "";

// get database connection
$database = new Database();
$db = $database->getConnection();
  
// prepare objects
$password = new password($db);
$password->id = $id;
$password->readOne();  

?>
<div class='right-button-margin'>
    <a href='index.php' class='btn btn-default pull-right'>Read Passwords</a>
</div>

  
<?php 
// if the form was submitted
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
        $password->password = encrypt_decrypt($_POST['password']);
        $password->url = $_POST['url'];
        $password->user_id = $_SESSION['id'];
        $password->id = $id;

        //echo $id; die;
        // update the password
        if($password->update()){
            echo "<div class='alert alert-success alert-dismissable'>";
                echo "Record was updated.";
            echo "</div>";
        }
    
        // if unable to update the password, tell the user
        else{
            echo "<div class='alert alert-danger alert-dismissable'>";
                echo "Unable to update password.";
            echo "</div>";
        }
    }
}
?>
<?php $uid = encrypt_decrypt($id);?>  
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id={$uid}");?>" method="post">
    <table class='table table-hover table-responsive table-bordered'>
  
        <tr>
            <td>Site Name</td>
            <td>
                <input type='text' name='name' value='<?php echo $password->name; ?>' class='form-control' />
                <span class="text-danger"><?php echo $name_err; ?></span>
            </td>
        </tr>

        <tr>
            <td>Site Url</td>
            <td>
                <input type='text' name='url' value='<?php echo $password->url; ?>' class='form-control'>
                <span class="text-danger"><?php echo $url_err; ?></span>            
            </td>
        </tr>
  
        <tr>
            <td>Password</td>
            <td>
                <input type='text' name='password' value='<?php echo encrypt_decrypt($password->password,'decrypt'); ?>' class='form-control' />
                <span class="text-danger"><?php echo $password_err; ?></span>
            </td>
        </tr>
  
        
  
        <tr>
            <td></td>
            <td>
                <button type="submit" class="btn btn-primary">Update</button>
            </td>
        </tr>
  
    </table>
</form>
<?php
// set page footer
include_once "layout_footer.php";
?>