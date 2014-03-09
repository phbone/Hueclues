<?php

include('global_functions.php');

class item_object {

    public $owner_id = "";
    public $owner_name = "";
    public $owner_username = "";
    public $owner_picture = "";
    public $owner_followers = "";
    public $purchaselink = "";
    public $gender = ""; // m, f or u
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
    public $like_count = "";
    public $likedbyuser = "";
    public $text_color = "";
    public $sizeRatio = ""; // width / height

}

class outfit_object {

    public $owner_id = "";
    public $outfitid = "";
    public $name = "";
    public $time = "";
    public $itemcount = "";
    public $item1 = "";
    public $item2 = "";
    public $item3 = "";
    public $item4 = "";
    public $item5 = "";
    public $item6 = "";

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
    public $itemid = "";
    public $source = ""; // closet, following, or store
    public $scheme = ""; // analogous(ana), complimentary(comp)

// shades(sha), split(spl), triadic (tri)
}

function returnOutfit($outfitid) {
    $outfit_object = new outfit_object;
    $outfit = database_fetch("outfit", "outfitid", $outfitid);
    $outfit_object->outfitid = $outfitid;
    $outfit_object->time = $outfit['time'];
    $outfit_object->name = $outfit['name'];
    $outfit_object->itemcount = $outfit['itemcount'];
    $outfit_object->owner_id = $outfit['userid'];
    $outfit_object->item1 = new item_object;
    $outfit_object->item1 = returnItem($outfit['itemid1']);
    $outfit_object->item2 = new item_object;
    $outfit_object->item2 = returnItem($outfit['itemid2']);
    $outfit_object->item3 = new item_object;
    $outfit_object->item3 = returnItem($outfit['itemid3']);
    $outfit_object->item4 = new item_object;
    $outfit_object->item4 = returnItem($outfit['itemid4']);
    $outfit_object->item5 = new item_object;
    $outfit_object->item5 = returnItem($outfit['itemid5']);
    $outfit_object->item6 = new item_object;
    $outfit_object->item6 = returnItem($outfit['itemid6']);

    return $outfit_object;
}

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
    $like = database_fetch("want", "userid", $_SESSION['userid'], "itemid", $itemid);
    $item_object = new item_object;
    $item_object->owner_id = $item['userid'];
    $item_object->owner_name = $user['name'];
    $item_object->owner_username = $user['username'];
    $item_object->owner_picture = $user['picture'];
    $item_object->owner_followers = $user['followers'];
    $item_object->gender = getGender($user['gender']);
    $item_object->hexcode = $item['code'];
    $item_object->save_time = $item['time'];
    $item_object->description = $item['description'];
    $item_object->image_origin = $item['image_origin'];
    $item_object->itemid = $item['itemid'];
    $item_object->like_count = $item['like_count'];
    $item_object->purchaselink = str_replace(' ', '', $item['purchaselink']);
    if ($item['code']) {
        $item_object->text_color = fontColor($item['code']);
    } else {
        $item_object->text_color = "000000";
    }
    if ($like) {
        $item_object->likedbyuser = "liked";
    } else {
        $item_object->likedbyuser = "unliked";
    }
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
    // calculate the ratio between width and height of image
    $item_object->sizeRatio = imageDimension($item_object->image_link);
    if (!$item_object->sizeRatio) {
        $item_object->sizeRatio = 1; // broken images will be squares
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
    for ($scheme_index = 0; $scheme_index < count($scheme_color_array); $scheme_index++) {
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
    if ($priority == 0) {
        return false;
    }
    return $store_match_object;
}

function colorsMatching($hex) {
// INPUT: a hexcode    
// OUPUT: returns all colors that match the input color 


    $colorObj = new colorObject();
    $shades = hsl_shades($hex, 10);
    $tints = hsl_tints($hex, 10);

    $colorObj->ana1 = hsl_analogous1($hex);
    $colorObj->ana2 = hsl_analogous2($hex);
    $colorObj->tri1 = hsl_triadic1($hex);
    $colorObj->tri2 = hsl_triadic2($hex);
    $colorObj->spl1 = hsl_split1($hex);
    $colorObj->spl2 = hsl_split2($hex);
    $colorObj->comp = hsl_complimentary($hex);
    $colorObj->sha1 = $shades[4];
    $colorObj->sha2 = $tints[4];
    $colorObj->hex = $hex;
    return $colorObj;
}

function colorSuggest($hex) {
    $colorArray = array();
    $colorArray[] = hsl_analogous1($hex);
    $colorArray[] = hsl_analogous2($hex);
    $colorArray[] = hsl_triadic1($hex);
    $colorArray[] = hsl_triadic2($hex);
    $colorArray[] = hsl_split1($hex);
    $colorArray[] = hsl_split2($hex);
    return $colorArray;
}
?> 

