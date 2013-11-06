<?php

session_start();
include('connection.php');
include('global_tools.php');
include('database_functions.php');

$userid = $_SESSION['userid'];
$itemid = $_GET['itemid'];

if (isset($userid)) {
    $like = database_fetch("like", "userid", $userid, "itemid", $itemid);
    if ($like) {// like exists
        database_delete("like", "userid", $userid, "itemid", $itemid);
        database_decrement("item", "itemid", $itemid, "like_count", 1);
        $status = "unliked";
    } else { // like doesn't exist
        database_insert("like", "userid", $userid, "itemid", $itemid, "time", time());
        database_increment("item", "itemid", $itemid, "like_count", 1);
        $status = "liked";
    }
}
$item = database_fetch("item", "itemid", $itemid);
$like_count = $item['like_count'];
echo json_encode(array('status' => $status, "count" =>$like_count));
?>
