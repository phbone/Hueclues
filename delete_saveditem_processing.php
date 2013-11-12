<?php

session_start();
include('connection.php');
include('global_tools.php');
include('database_functions.php');


$userid = $_SESSION['userid'];
$itemid = $_GET['itemid'];

$item = database_fetch("item", "itemid", $itemid, "userid", $userid);
// this step validates permission to delete
if ($item) {
    $tagmap_query = database_query("tagmap", "itemid", $itemid);
    while ($tagmap = mysql_fetch_array($tagmap_query)) {
        database_decrement("tag", "tagid", $tagmap['tagid'], "count", 1);
    }
    database_delete("tagmap", "itemid", $itemid);
    database_delete("item", "itemid", $itemid);
    database_decrement("user", "userid", $userid, "itemcount", 1);
}
?>
