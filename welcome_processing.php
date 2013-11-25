<?php

session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');
include('global_functions.php');
include('s3_config.php');

$gender = $_POST['gender']; // m, f
$userid = $_SESSION['userid'];

// female = 0;
// male = 1;

if ($gender == "m") {
    database_update("user", "userid", $userid, "", "", "gender", "1");
} else if ($gender == "f") {
    // default is set to 0 so do nothing
}

echo json_encode(array('notification'=>"success"));
?>