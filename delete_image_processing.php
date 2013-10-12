<?php

session_start();
include('connection.php');
include('database_functions.php');
include('s3_config.php');

$userid = $_SESSION['userid'];

// 0 = native url
// 1 = faceboook url
// 2 = instagram url
// 3 = file

$type = $_GET['origin'];
$imageid = $_GET['imageid'];
$urlid = $_GET['urlid'];

// delete any items, associated with image
if ($type == "3") {//////////////////////////////////////// IMAGE FILE
    $item_query = database_query("item", "userid", $userid, "imageid", $imageid);
    while ($item = mysql_fetch_array($item_query)) { // for each item that uses the url
        $tagmap_query = database_query("tagmap", "itemid", $item['itemid']);
        while ($tagmap = mysql_fetch_array($tagmap_query)) { // for each tag on that item
            database_decrement("tag", "tagid", $tagmap['tagid'], "count", 1); // remove the tag count
        }
        database_delete("tagmap", "itemid", $item['itemid']); // delete the tags from the item
        database_delete("item", "userid", $userid, "itemid", $item['itemid']); // delete the item from database
        database_decrement("user", "itemcount", 1); // decrease item count in user profile
    }

    $image = database_fetch("image", "imageid", $imageid);
    $imageUrlArray = explode("/", $image['url']);
    $imageUrl = end($imageUrlArray);
    S3::deleteObject($bucket, $imageUrl);

    database_delete("image", "imageid", $imageid); // delete the url
    database_decrement("user", "userid", $userid, "filecount", 1);
} else {
// user is trying to delete a url
    if ($type == "0") {//////////////////////////////////// NATIVELY INPUT URL
        $url = database_fetch("url", "urlid", $urlid); // currently useless
        $item_query = database_query("item", "userid", $userid, "image_origin", "0", "urlid", $urlid);
        while ($item = mysql_fetch_array($item_query)) { // for each item that uses the url
            $tagmap_query = database_query("tagmap", "itemid", $item['itemid']);
            while ($tagmap = mysql_fetch_array($tagmap_query)) { // for each tag on that item
                database_decrement("tag", "tagid", $tagmap['tagid'], "count", 1); // remove the tag count
            }
            database_delete("tagmap", "itemid", $item['itemid']); // delete the tags from the item
            database_delete("item", "userid", $userid, "itemid", $item['itemid']); // delete the item from the url
            database_decrement("user", "itemcount", 1); // decrease item count in user profile
        }
        database_delete("url", "urlid", $url['urlid']); // delete the url
        database_decrement("user", "userid", $userid, "urlcount", 1);
    } else if ($type == "1") {////////////////////////////////////////////////// FACEBOOK URL
        $url = database_fetch("facebookurl", "userid", $userid, "urlid", $urlid); // currently useless
        $item_query = database_query("item", "userid", $userid, "image_origin", "1", "urlid", $urlid);
        while ($item = mysql_fetch_array($item_query)) { // for each item that uses the url
            $tagmap_query = database_query("tagmap", "itemid", $item['itemid']);
            while ($tagmap = mysql_fetch_array($tagmap_query)) { // for each tag on that item
                database_decrement("tag", "tagid", $tagmap['tagid'], "count", 1); // remove the tag count
            }
            database_delete("tagmap", "itemid", $item['itemid']); // delete the tags from the item
            database_delete("item", "userid", $userid, "itemid", $item['itemid']); // delete the item from the url
            database_decrement("user", "itemcount", 1); // decrease item count in user profile
        }
        database_delete("facebookurl", "urlid", $url['urlid']); // delete the url
        database_decrement("user", "userid", $userid, "fbcount", 1);
    } else if ($type == "2") {//////////////////////////////////////////////// INSTAGRAM URL
        $url = database_fetch("instagramurl", "urlid", $urlid); // currently useless
        $item_query = database_query("item", "userid", $userid, "image_origin", "2", "urlid", $urlid);
        while ($item = mysql_fetch_array($item_query)) { // for each item that uses the url
            $tagmap_query = database_query("tagmap", "itemid", $item['itemid']);
            while ($tagmap = mysql_fetch_array($tagmap_query)) { // for each tag on that item
                database_decrement("tag", "tagid", $tagmap['tagid'], "count", 1); // remove the tag count
            }
            database_delete("tagmap", "itemid", $item['itemid']); // delete the tags from the item
            database_delete("item", "userid", $userid, "itemid", $item['itemid']); // delete the item from the url
            database_decrement("user", "itemcount", 1); // decrease item count in user profile
        }
        database_delete("instagramurl", "urlid", $url['urlid']); // delete the url
        database_decrement("user", "userid", $userid, "igcount", 1);
    }
}
?>
