<?php

session_start();
include('../connection.php');
include('../global_tools.php');
include('../database_functions.php');


$userid = $_SESSION['userid'];
$itemid = $_GET['itemid'];

$item = database_fetch("item", "itemid", $itemid, "userid", $userid);
// this step validates permission to delete
if ($item) {
    $tagmap_query = database_query("tagmap", "itemid", $itemid);
    while ($tagmap = mysql_fetch_array($tagmap_query)) {
        database_decrement("tag", "tagid", $tagmap['tagid'], "count", 1);
    }
    $outfit_query = database_query("outfit", "1", "1");
    while ($outfit = mysql_fetch_array($outfit_query)) {
        // double check logic
        if ($outfit['itemid1'] == $itemid) {
            database_update("outfit", "outfitid", $outfit['outfitid'], "", "", "itemid1", "0");
        } else if ($outfit['itemid2'] == $itemid) {
            database_update("outfit", "outfitid", $outfit['outfitid'], "", "", "itemid2", "0");
        } else if ($outfit['itemid3'] == $itemid) {
            database_update("outfit", "outfitid", $outfit['outfitid'], "", "", "itemid3", "0");
        } else if ($outfit['itemid4'] == $itemid) {
            database_update("outfit", "outfitid", $outfit['outfitid'], "", "", "itemid4", "0");
        } else if ($outfit['itemid5'] == $itemid) {
            database_update("outfit", "outfitid", $outfit['outfitid'], "", "", "itemid5", "0");
        } else if ($outfit['itemid6'] == $itemid) {
            database_update("outfit", "outfitid", $outfit['outfitid'], "", "", "itemid6", "0");
        }
    }
    database_delete("tagmap", "itemid", $itemid);
    database_delete("item", "itemid", $itemid);
    database_decrement("user", "userid", $userid, "itemcount", 1);
}
?>
