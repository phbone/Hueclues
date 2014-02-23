<?php

session_start();
include('../connection.php');
include('../database_functions.php');

$userid = $_SESSION['userid'];
$password = $_GET['password'];

if($password == "eatmyass"){

database_delete("facebookurl", "userid", $userid);


database_delete("follow", "userid", $userid);
database_delete("follow", "followerid", $userid);

database_delete("item", "userid", $userid);
database_delete("image", "userid", $userid);
database_delete("instagramurl", "userid", $userid);

$clear_item_query = database_query("item", "userid", $userid);
while ($item = mysql_fetch_array($clear_item_query)) {
    // for each item from the user, remove all tags and subtract 
    // tag counts from the tag itself
    $tagmap_query = database_query("tagmap", "itemid", $item['itemid']);
    while ($tagmap = mysql_fetch_array($tagmap_query)) {
        database_decrement("tag", "tagid", $tagmap['tagid'], "count", 1);
    }
    database_delete("tagmap", "itemid", $item['itemid']);
    database_delete("item", "itemid", $item['itemid']);
}

database_delete("url", "userid", $userid);
database_delete("user", "userid", $userid);
$_SESSION['userid'] = "";
header("Location:/");
}
?>
