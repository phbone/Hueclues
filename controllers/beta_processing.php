<?php
session_start();
include('../connection.php');
include('../database_functions.php');
include('../global_tools.php');
include('../global_objects.php');


$email = $_POST['betaEmail'];

database_insert("beta", "emailid", "", "email", $email);

$notification = "<span id='error_message'>Your email has been saved, We'll be in touch with you soon!</span>";
$return_array = array('notification' => $notification);
echo json_encode($return_array);
?>
