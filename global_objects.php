<?php

function returnItem($itemid) {
//// Input: itemid INT
//// given an itemid, return an object with the
//// user
//// image link
//// color code
//// description
//// save time
//// NOTE REQUIRES DATABASE FUNCTIONS TO WORK

    $item = database_fetch("item", "itemid", $itemid);
    $user = database_fetch("user", "userid", $item['userid']);
    $item_object = new item_object;
    $item_object->owner_id = $item['userid'];
    $item_object->owner_name = $user['name'];
    $item_object->owner_username = $user['username'];
    $item_object->owner_picture = $user['picture'];
    $item_object->owner_followers = $user['followers'];
    $item_object->hexcode = $item['code'];
    $item_object->save_time = $item['time'];
    $item_object->description = $item['description'];
    $item_object->image_origin = $item['image_origin'];
    $item_object->itemid = $item['itemid'];
    $item_object->purchaselink = str_replace(' ', '', $item['purchaselink']);
    ;
    // get all the tags of the item and send them in the format
    //#first#tag#goes#on
    $tag_string = "";
    $tagmap_query = database_query("tagmap", "itemid", $item['itemid']);
    while ($tagmap = mysql_fetch_array($tagmap_query)) {
        $tag = database_fetch("tag", "tagid", $tagmap['tagid']);
        $tag_string = $tag_string . "#" . $tag['name'];
    }
    // the string formatted for searching by tag
    $item_object->search_string = str_replace("#", "%23", $tag_string);
    $item_object->tags = $tag_string;
    if ($item['imageid']) { // item is an image file
        $image = database_fetch("image", "imageid", $item['imageid']);
        $item_object->image_link = $image['url'];
    } else {  //item is an url
        if ($item['image_origin'] == "0") {
            $url = database_fetch("url", "urlid", $item['urlid']);
            $item_object->image_link = $url['url'];
        } else if ($item['image_origin'] == "1") {
            $facebookurl = database_fetch("facebookurl", "urlid", $item['urlid']);
            $item_object->image_link = $facebookurl['url'];
        } else if ($item['image_origin'] == "2") {
            $instagramurl = database_fetch("instagramurl", "urlid", $item['urlid']);
            $item_object->image_link = $instagramurl['url'];
        }
    }
    return $item_object;
}

function userItems(&$userid) {
//// Input: userid array of INTs
//// Output: array of item objects, ordered from most to least recent
    $all_items = array();
    $item_query = "SELECT * FROM item ORDER BY time DESC";
    $item_result = mysql_query($item_query);
    while ($item = mysql_fetch_array($item_result)) {
        if (in_array($item['userid'], $userid)) {
            $item_object = new item_object();
            $item_object = returnItem($item['itemid']);
            array_push($all_items, $item_object);
        }
    }
    return $all_items;
}

function storeMatch($store_itemid, &$scheme_color_array, $hue_tol, $sat_tol, $light_tol, $schemeMatch, $requires_algorithms_file = "") {
    // INPUT: id of store item, 2 color array, tolerances, the scheme that this item corresponds to
    // 
    // determines priority for each item, returns a match_object, with the itemid and the priority

    $priority = 0;
    //scheme_color_array is an array [0] = input color, [1] and [2] are scheme colors
    $item = database_fetch("storeitem", "itemid", $store_itemid);
    $saved_color_array = array($item['code1'], $item['code2'], $item['code3']);
    for ($scheme_index = 1; $scheme_index < count($scheme_color_array); $scheme_index++) {
        for ($save_index = 0; $save_index < count($saved_color_array); $save_index++) {
            if (hsl_same_color($saved_color_array[$save_index], $scheme_color_array[$scheme_index], $hue_tol, $sat_tol, $light_tol)) {
                $priority++;
            }
        }
    }
    $store_match_object = new store_match_object();
    $store_match_object->itemid = $item['itemid'];
    $store_match_object->scheme = $schemeMatch;
    $store_match_object->colors = $saved_color_array;
    $store_match_object->description = $item['description'];
    $store_match_object->gender = $item['gender'];
    $store_match_object->price = $item['price'];
    // creates a percentage between 0% and 100% for 
    $store_match_object->priority = round(($priority * 100) / 6);
    $store_match_object->url = $item['url'];
    $store_match_object->purchaselink = $item['purchaselink'];
    // returns the store item object, with all the useful fields
    // priority is determined, each object priority determines when it should
    // be displayed, in according to degree of match
    return $store_match_object;
}

class item_object {

    public $owner_id = "";
    public $owner_name = "";
    public $owner_username = "";
    public $owner_picture = "";
    public $owner_followers = "";
    public $purchaselink = "";
    public $itemid = "";
    public $image_link = "";
    public $hexcode = "";
    public $tags = "";
    public $description = "";
    public $save_time = "";
    public $image_origin = "";
    public $search_string = "";
    public $item_tags_string = "";
    public $association = "";
    public $matchScheme = "";

}

class store_match_object {

    // the itemid of the store item
    // the priority, i.e degree of match
    // 1 being lowest (1 color matches) 3 being highest (3 colors match)
    public $itemid = "";
    public $description = "";
    public $gender = "";
    public $purchaselink = "";
    public $colors = array();
    public $url = "";
    public $priority = "";
    public $price = "";
    public $scheme = ""; // ana, tri, comp, spl, sha

}

class colorObject {

    // the itemid of the store item
    // the priority, i.e degree of match
    // 1 being lowest (1 color matches) 3 being highest (3 colors match)
    public $hex = "";
    public $comp = "";
    public $ana1 = "";
    public $ana2 = "";
    public $spl1 = "";
    public $spl2 = "";
    public $tri1 = "";
    public $tri2 = "";
    public $sha1 = "";
    public $sha2 = "";

}

class matchObject {

    // match object returns itemid with associations:
    //      whether the item is 
    //      from following, closet, or store

    public $priority = ""; // how well item matches
    public $matchingItemid = "";
    public $itemSource = ""; // closet, following, or store
    public $scheme = ""; // analogous(ana), complimentary(comp)

    // shades(sha), split(spl), triadic (tri)
}
?> 

