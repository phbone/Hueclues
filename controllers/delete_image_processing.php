<?php

session_start();
include('../connection.php');
include('../database_functions.php');
include('../lib/s3_config.php');

$userid = $_SESSION['userid'];

// 0 = native url
// 1 = faceboook url
// 2 = instagram url
// 3 = file

$type = $_GET['origin'];
$imageid = $_GET['imageid'];
$urlid = $_GET['urlid'];

// delete any items, associated with image
if ($type == "3" && isset($imageid) && $imageid != 0) {//////////////////////////////////////// IMAGE FILE
    $item_query = database_query("item", "userid", $userid, "imageid", $imageid);
    while ($item = mysql_fetch_array($item_query)) { // for each item that uses the url
        $tagmap_query = database_query("tagmap", "itemid", $item['itemid']);
        while ($tagmap = mysql_fetch_array($tagmap_query)) { // for each tag on that item
            database_decrement("tag", "tagid", $tagmap['tagid'], "count", 1); // remove the tag count
        }
        $itemid = $item['itemid'];
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
        database_delete("tagmap", "itemid", $itemid); // delete the tags from the item
        database_delete("item", "userid", $userid, "itemid", $itemid); // delete the item from database
        database_decrement("user", "userid", $userid, "itemcount", 1); // decrease item count in user profile
    }

    $image = database_fetch("image", "imageid", $imageid);
    $imageUrlArray = explode("/", $image['url']);
    $imageUrl = end($imageUrlArray);
    S3::deleteObject($bucket, $imageUrl);

    database_delete("image","userid", $userid, "imageid", $imageid); // delete the url
    database_decrement("user", "userid", $userid, "filecount", 1);
} else {
// user is trying to delete a url
    if ($type == "0" && isset($urlid) && $urlid != 0) {//////////////////////////////////// NATIVELY INPUT URL
        $url = database_fetch("url", "urlid", $urlid); // currently useless
        $item_query = database_query("item", "userid", $userid, "image_origin", "0", "urlid", $urlid);
        while ($item = mysql_fetch_array($item_query)) { // for each item that uses the url
            $tagmap_query = database_query("tagmap", "itemid", $item['itemid']);
            while ($tagmap = mysql_fetch_array($tagmap_query)) { // for each tag on that item
                database_decrement("tag", "tagid", $tagmap['tagid'], "count", 1); // remove the tag count
            }
            $itemid = $item['itemid'];
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
            database_delete("tagmap", "itemid", $item['itemid']); // delete the tags from the item
            database_delete("item", "userid", $userid, "itemid", $item['itemid']); // delete the item from the url
            database_decrement("user", "userid", $userid, "itemcount", 1); // decrease item count in user profile
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
            $itemid = $item['itemid'];
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
            $itemid = $item['itemid'];
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
            database_delete("tagmap", "itemid", $item['itemid']); // delete the tags from the item
            database_delete("item", "userid", $userid, "itemid", $item['itemid']); // delete the item from the url
            database_decrement("user", "userid", $userid, "itemcount", 1); // decrease item count in user profile
        }
        database_delete("instagramurl", "urlid", $url['urlid']); // delete the url
        database_decrement("user", "userid", $userid, "igcount", 1);
    }
}
?>
