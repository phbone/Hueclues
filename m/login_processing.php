<?php
session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');


$username = $_POST['loginusername'];
$password = $_POST['loginpassword'];

$result = database_fetch("user", "username", $username, "password", $password);

if ($result) {
    $_SESSION['userid'] = $result['userid'];
    database_update("user", "userid", $_SESSION['userid'], "", "", "last_login_time", time());
    $status = "success";
} else {
    $status = "<span id='error_message'>Username and Password do not match.</span>";
}
$return_array = array('notification' => $status);
echo json_encode($return_array);
?>
