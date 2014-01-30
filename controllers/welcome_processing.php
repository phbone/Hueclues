<?php

session_start();
include('../connection.php');
include('../database_functions.php');
include('../global_tools.php');
include('../global_functions.php');

$gender = $_POST['gender']; // m, f
$userid = $_SESSION['userid'];

// female = 0;
// male = 1;

if ($gender == "m") {
    database_update("user", "userid", $userid, "", "", "gender", "1");
    if (!database_count("follow", "userid", "1", "followerid", $userid)) {
        database_insert("follow", "userid", "1", "followerid", $userid);
        database_increment("user", "userid", "1", "followers", 1);
    }
    if (!database_count("follow", "userid", "2", "followerid", $userid)) {
        database_insert("follow", "userid", "2", "followerid", $userid);
        database_increment("user", "userid", "2", "followers", 1);
    }
} else if ($gender == "f") {
    // default is set to 0 so just follow female closets

    if (!database_count("follow", "userid", "3", "followerid", $userid)) {
        database_insert("follow", "userid", "3", "followerid", $userid);
        database_increment("user", "userid", "3", "followers", 1);
    }
    if (!database_count("follow", "userid", "4", "followerid", $userid)) {
        database_insert("follow", "userid", "4", "followerid", $userid);
        database_increment("user", "userid", "4", "followers", 1);
    }
}

echo json_encode(array('notification' => "success"));
?>