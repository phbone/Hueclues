<?php

session_start();
include('connection.php');
include('database_functions.php');
include('global_objects.php');
include('global_tools.php');

$userid = $_SESSION['userid'];

$action = $_POST['action']; // can add/remove items and delete/save/edit outfits
$itemid = $_POST['itemid'];
$outfitName = $_POST['name'];
$outfitid = $_POST['outfitid'];

$user = database_fetch("user", "userid", $userid);
$current_outfitid = $user['current_outfitid'];

if ($action == "add") { // add item to current outfit
    // add the item (itemid) to the outfit (outfitid)
    $outfit = database_fetch("outfit", "outfitid", $current_outfitid); // get outfit object
    $outfitItemids = array($outfit['itemid1'], $outfit['itemid2'], $outfit['itemid3'], $outfit['itemid4'], $outfit['itemid5'], $outfit['itemid6']);
    for ($i = 0; $i < 6; $i++) {
        if ($outfitItemids[$i] == "0") {
            $outfitItemids[$i] = $itemid;
            break;
        }
    }
    database_update("outfit", "outfitid", $current_outfitid, "", "", "name", $outfitName, "itemid1", $outfitItemids[0], "itemid2", $outfitItemids[1], "itemid3", $outfitItemids[2], "itemid4", $outfitItemids[3], "itemid5", $outfitItemids[4], "itemid6", $outfitItemids[5]);
    $status = "success";
} else if ($action == "remove") { // remove item from current outfit
    // remove the item (itemid) to the outfit (outfitid)
    $outfit = database_fetch("outfit", "outfitid", $current_outfitid); // get outfit object
    $outfitItemids = array($outfit['itemid1'], $outfit['itemid2'], $outfit['itemid3'], $outfit['itemid4'], $outfit['itemid5'], $outfit['itemid6']);
    for ($i = 0; $i < 6; $i++) {
        if ($outfitItemids[$i] == $itemid) {
            $outfitItemids[$i] = "0";
            break;
        }
    }
    database_update("outfit", "outfitid", $current_outfitid, "", "", "name", $outfitName, "itemid1", $outfitItemids[0], "itemid2", $outfitItemids[1], "itemid3", $outfitItemids[2], "itemid4", $outfitItemids[3], "itemid5", $outfitItemids[4], "itemid6", $outfitItemids[5]);
    $status = "success";
} else if ($action == "delete") { // delete ENTIRE outfit
    // deletes the outfit (outfitid)
    database_delete("outfit", "outfitid", $current_outfitid);
    database_decrement("user", "userid", $userid, "outfitcount", 1);

    //creates new current
    $previousOutfit = database_fetch("outfit", "userid", $userid);

    database_update("user", "userid", $userid, "", "", "current_outfitid", $previousOutfit['outfitid']);
    $status = "success";
} else if ($action == "save") { // save current and create a new outfit 
    // save outfit (outfitid) creates new current outfit for user
    database_update("outfit", "outfitid", $current_outfitid, "name", $outfitName);
    database_insert("outfit", "outfitid", NULL, "userid", $userid, "time", time());
    $newOutfitid = mysql_insert_id();
    database_update("user", "userid", $userid, "", "", "current_outfitid", $newOutfitid);
    database_increment("user", "userid", $userid, "outfitcount", 1);
    $status = "success";
} else if ($action == "edit") {
    // edit mode for outfit (outfitid) 
    database_update("user", "userid", $userid, "", "", "current_outfitid", $outfitid);
    $status = "success";
} else if ($action == "load") {
    // returns the items in the current outfit as objects using the array outfit_items
    $outfit = database_fetch("outfit", "outfitid", $current_outfitid);
    $outfit_items[] = returnItem($outfit['itemid1']);
    $outfit_items[] = returnItem($outfit['itemid2']);
    $outfit_items[] = returnItem($outfit['itemid3']);
    $outfit_items[] = returnItem($outfit['itemid4']);
    $outfit_items[] = returnItem($outfit['itemid5']);
    $outfit_items[] = returnItem($outfit['itemid6']);
}


$return_array = array('notification' => $status, 'objects' => $outfit_items);
echo json_encode($return_array);
?>
