<?php

session_start();
include('connection.php');
include('global_tools.php');
include('database_functions.php');

$userid = $_SESSION['userid'];
$itemid = $_GET['itemid'];

if (isset($userid)) {
    $like = database_fetch("want", "userid", $userid, "itemid", $itemid);
    if ($like) {// like exists
        database_delete("want", "userid", $userid, "itemid", $itemid);
        database_decrement("item", "itemid", $itemid, "like_count", 1);
        $status = "unliked";
    } else { // like doesn't exist
        $time = time();
        database_insert("want", "itemid", $itemid, "userid", $userid, "time", $time);
        database_increment("item", "itemid", $itemid, "like_count", 1);
        $error = mysql_error();
        $status = "liked";
    }
}
$item = database_fetch("item", "itemid", $itemid);
$like_count = $item['like_count'];
if (!$like_count)
    $like_count = 0;
echo json_encode(array('status' => $status, "count" => $like_count, "error" =>$error));
?>
