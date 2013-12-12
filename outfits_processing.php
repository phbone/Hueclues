<?php

session_start();
include('connection.php');
include('database_functions.php');
include('global_objects.php');
include('global_tools.php');

$userid = $_SESSION['userid'];

$action = $_POST['action']; // can add/remove items and delete/save/edit outfits
$itemid = $_POST['itemid'];
$name = mysql_escape_string($_POST['name']);
$outfitid = $_POST['outfitid'];

$user = database_fetch("user", "userid", $userid);
$username = $user['username'];
$current_outfitid = $user['current_outfitid'];

if ($action == "add") { // add item to current outfit
    // add the item (itemid) to the outfit (outfitid)
    $outfit = database_fetch("outfit", "outfitid", $current_outfitid); // get outfit object
    $outfitItemids = array($outfit['itemid1'], $outfit['itemid2'], $outfit['itemid3'], $outfit['itemid4'], $outfit['itemid5'], $outfit['itemid6']);
    for ($i = 0; $i < 6; $i++) {
        // puts item in next empty slot
        if ($outfitItemids[$i] == "0") {
            $outfitItemids[$i] = $itemid;
            break;
        }
    }



    database_update("outfit", "outfitid", $current_outfitid, "", "", "itemid1", $outfitItemids[0], "itemid2", $outfitItemids[1], "itemid3", $outfitItemids[2], "itemid4", $outfitItemids[3], "itemid5", $outfitItemids[4], "itemid6", $outfitItemids[5]);
    

// notify owner of item from $itemid
    $item = database_fetch("item", "itemid", $itemid);
    $owner = database_fetch("user", "userid", $item['userid']);
    
    
// format and send email (this should be made into a function)
// to owner of 
    $to = $owner['email'];
    $subject = "Your item has been stung!";
    $message = emailTemplate($user['name'] . " (" . $user['username'] . ") has just used your item '" . $item['description'] . "' in an <a href='http://hueclues.com/closet/" . $user['username'] . "/outfit'>outfit!</a>");
    $header = "MIME-Version: 1.0" . "\r\n";
    $header .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
    $header .= "From: Hueclues <noreply@hueclues.com>" . "\r\n"
            . 'Reply-To: noreply@hueclues.com' . "\r\n";
    mail($to, $subject, $message, $header);


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
    database_update("outfit", "outfitid", $current_outfitid, "", "", "itemid1", $outfitItemids[0], "itemid2", $outfitItemids[1], "itemid3", $outfitItemids[2], "itemid4", $outfitItemids[3], "itemid5", $outfitItemids[4], "itemid6", $outfitItemids[5]);
    $status = "success";
} else if ($action == "delete") { // delete ENTIRE outfit
    // deletes the outfit (outfitid)
    if ($current_outfitid != "0") {
        database_delete("outfit", "outfitid", $current_outfitid); // delete current outfit from outfit
        database_decrement("user", "userid", $userid, "outfitcount", 1); //
        $previousOutfit = database_fetch("outfit", "userid", $userid);
        if (!$previousOutfit) {
            $previousOutfit['outfitid'] = 0;
        }
        database_update("user", "userid", $userid, "", "", "current_outfitid", $previousOutfit['outfitid']);
        $status = "success";
    }
} else if ($action == "save") { // save current and create a new outfit 
    // save outfit (outfitid) creates new current outfit for user
    database_update("outfit", "outfitid", $current_outfitid, "", "", "name", $name);
    $status = "success";
} else if ($action == "create") {
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
    if ($current_outfitid == 0) {
        // create outfit
        database_insert("outfit", "outfitid", NULL, "userid", $userid, "time", time());
        $newOutfitid = mysql_insert_id();
        database_update("user", "userid", $userid, "", "", "current_outfitid", $newOutfitid);
        database_increment("user", "userid", $userid, "outfitcount", 1);
        $status = "success";
    }
    // returns the items in the current outfit as objects using the array outfit_items
    $outfit = database_fetch("outfit", "outfitid", $current_outfitid);
    $outfit_items[] = returnItem($outfit['itemid1']);
    $outfit_items[] = returnItem($outfit['itemid2']);
    $outfit_items[] = returnItem($outfit['itemid3']);
    $outfit_items[] = returnItem($outfit['itemid4']);
    $outfit_items[] = returnItem($outfit['itemid5']);
    $outfit_items[] = returnItem($outfit['itemid6']);
    $name = $outfit['name'];
}


$return_array = array('notification' => $status, 'objects' => $outfit_items, 'name' => $name, 'username' => $username);
echo json_encode($return_array);
?>
