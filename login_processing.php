<?php

session_start();
include('connection.php');
include('database_functions.php');


$username = $_POST['loginusername'];
$password = $_POST['loginpassword'];
$status = "";
$result = database_fetch("user", "username", $username, "password", $password);

if ($result) {
    $_SESSION['userid'] = $result['userid'];
    setcookie("userid", $_SESSION['userid'], time() + 60 * 60 * 24 * 60);
    setcookie("username", $username, time() + 60 * 60 * 24 * 60);
    setcookie("password", $password, time() + 60 * 60 * 24 * 60);
    database_update("user", "userid", $_SESSION['userid'], "", "", "last_login_time", time());
    $status = "success";
} else {
    $status = "<span id='error_message'>Username and Password do not match.</span>";
}

$return_array = array('notification' => $status);
echo json_encode($return_array);
?>
