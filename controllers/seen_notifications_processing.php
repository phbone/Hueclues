<?php

session_start();
include('../connection.php');
include('../database_functions.php');

$userid = $_SESSION['userid']; // get the current user id
// update all unseen notifications and make them seen
database_update("notification", "userid", $userid, "seen", "0", "seen", "1");
?>