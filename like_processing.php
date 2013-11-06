<?php

session_start();
include('connection.php');
include('global_tools.php');
include('database_functions.php');

$userid = $_SESSION['userid'];
$itemid = $_GET['itemid'];

if (isset($userid)) {
    database_insert("like", "userid", $userid, "itemid", $itemid, "time", time());
}
echo json_encode(array('status' => "liked"));
?>
