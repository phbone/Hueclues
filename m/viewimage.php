<?php

session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');

$userid = $_SESSION['userid'];
$imageid = $_GET['imageid'];

$item = database_fetch("item", "imageid", $imageid);
//this ensures its an item
if ($item) {
    //item exists thus picture is public
    $image = database_fetch("image", "imageid", $imageid);
} else if (!$item) {
    $image = database_fetch("image", "userid", $userid, "imageid", $imageid);
}
echo file_get_contents($image['url']);
?>
