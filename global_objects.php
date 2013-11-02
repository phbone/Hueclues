<?php

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
    public $itemid = "";
    public $source = ""; // closet, following, or store
    public $scheme = ""; // analogous(ana), complimentary(comp)

    // shades(sha), split(spl), triadic (tri)
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

function returnAllMatchingItems($userid, $itemid) {
// INPUT: an itemid of any item
// OUTPUT: all itemid which create color matches with that itemid
// as well as associations:
//      following, closet, or store?
//      which scheme?
// in the form of "a Match Object"
// 
// 
// tolerance is for how specific color matches are
    $sat_tol = 12.5;
    $light_tol = 10;
    $hue_tol = 8.333;

    $userItems = array(); // items that are from other users/ or yourself
    $storeItems = array(); // items from the store

    $itemObject = returnItem($itemid);
    $inputColor = $itemObject->hexcode;

    $compCount = 0;
    $anaCount = 0;
    $splCount = 0;
    $shaCount = 0;
    $triCount = 0;

    $colorObj = colorsMatching($inputColor);
    // [hex, comp, ana1, ana2, tri1, tri2, sha1, sha2, spl1, spl2]

    $schemeNames = array("comp", "comp", "ana", "ana", "tri", "tri", "sha", "sha"/* ,"spl1","spl2" */);
    $colorMatches = array($colorObj->comp, $colorObj->comp, $colorObj->ana1, $colorObj->ana2, $colorObj->tri1, $colorObj->tri2, $colorObj->sha1, $colorObj->sha2/* , $colorObj->spl1, $colorObj->spl2 */);



    $followItemids = array(); // holds a list of unique itemids of items that match for following 
    $userItemids = array(); // holds a list of unique itemids of items that match for closet
    for ($sch = 0; $sch < count($colorMatches); $sch+=2) {
        // goes through it by scheme


        if ($userid) {
            // sort through items from USERS
            $itemQuery = database_query("item", "userid", $userid);
            while ($item = mysql_fetch_array($itemQuery)) {
                $itemColor = $item['code'];




                if ($schemeNames[$sch] == "comp") {
                    $checkSame1 = hsl_is_complimentary($itemColor, $colorMatches[$sch], $hue_tol, $sat_tol, $light_tol);
                    $checkSame2 = hsl_is_complimentary($itemColor, $colorMatches[$sch + 1], $hue_tol, $sat_tol, $light_tol);
                } else if ($schemeNames[$sch] == "ana") {
                    $checkSame1 = hsl_is_analogous($itemColor, $colorMatches[$sch], $hue_tol, $sat_tol, $light_tol);
                    $checkSame2 = hsl_is_analogous($itemColor, $colorMatches[$sch + 1], $hue_tol, $sat_tol, $light_tol);
                } else if ($schemeNames[$sch] == "tri") {
                    $checkSame1 = hsl_is_triadic($itemColor, $colorMatches[$sch], $hue_tol, $sat_tol, $light_tol);
                    $checkSame2 = hsl_is_triadic($itemColor, $colorMatches[$sch + 1], $hue_tol, $sat_tol, $light_tol);
                } else if ($schemeNames[$sch] == "sha") {
                    $checkSame1 = hsl_same_color($itemColor, $colorMatches[$sch], $hue_tol, $sat_tol, $light_tol);
                    $checkSame2 = hsl_same_color($itemColor, $colorMatches[$sch + 1], $hue_tol, $sat_tol, $light_tol);
                }


                if ($checkSame1 || $checkSame2) {// current item matches with 1 of the 2 colors in the scheme
                    if ($item['itemid'] != $itemid) {


                        $currentItemid = array_search($item['itemid'], $userItemids);
                        if (in_array($item['itemid'], $userItemids)) {
                            $userItems[$currentItemid]->scheme .= " " . $schemeNames[$sch];
                        } else {
                            $matchObject = new matchObject();
                            $matchObject->source = "closet";
                            $matchObject->scheme = $schemeNames[$sch];
                            $matchObject->itemid = $item['itemid'];
                            $userItems[] = $matchObject;
                        }
                        if ($schemeNames[$sch] == "comp") {
                            $compCount++;
                        } else if ($schemeNames[$sch] == "ana") {
                            $anaCount++;
                        } else if ($schemeNames[$sch] == "tri") {
                            $triCount++;
                        } else if ($schemeNames[$sch] == "sha") {
                            $shaCount++;
                        }
                    }
                }
            }
            $followingItems = returnAllItemsFromFollowing($userid);

            for ($i = 0; $i < sizeof($followingItems); $i++) {
                $itemColor = $followingItems[$i]['code'];
                if ($schemeNames[$sch] == "comp") {
                    $checkSame1 = hsl_is_complimentary($itemColor, $colorMatches[$sch], $hue_tol, $sat_tol, $light_tol);
                    $checkSame2 = hsl_is_complimentary($itemColor, $colorMatches[$sch + 1], $hue_tol, $sat_tol, $light_tol);
                } else if ($schemeNames[$sch] == "ana") {
                    $checkSame1 = hsl_is_analogous($itemColor, $colorMatches[$sch], $hue_tol, $sat_tol, $light_tol);
                    $checkSame2 = hsl_is_analogous($itemColor, $colorMatches[$sch + 1], $hue_tol, $sat_tol, $light_tol);
                } else if ($schemeNames[$sch] == "tri") {
                    $checkSame1 = hsl_is_triadic($itemColor, $colorMatches[$sch], $hue_tol, $sat_tol, $light_tol);
                    $checkSame2 = hsl_is_triadic($itemColor, $colorMatches[$sch + 1], $hue_tol, $sat_tol, $light_tol);
                } else if ($schemeNames[$sch] == "sha") {
                    $checkSame1 = hsl_same_color($itemColor, $colorMatches[$sch], $hue_tol, $sat_tol, $light_tol);
                    $checkSame2 = hsl_same_color($itemColor, $colorMatches[$sch + 1], $hue_tol, $sat_tol, $light_tol);
                }

                if ($checkSame1 || $checkSame2) {// the current item matches 1 of the 2 colors in the scheme
                    if ($followingItems[$i]['itemid'] != $itemid) { // item cannot match itself
/// check if the itemid already exists, if so add the current scheme to that data
                        $currentItemid = array_search($followingItems[$i]['itemid'], $followItemids);
                        if (in_array($followingItems[$i]['itemid'], $followItemids)) {
                            $userItems[$currentItemid]->scheme .= " " . $schemeNames[$sch];
                        } else {
// otherwise count and create new object
                            $matchObject = new matchObject();
                            $matchObject->source = "following";
                            $matchObject->scheme = $schemeNames[$sch];
                            $matchObject->itemid = $followingItems[$i]['itemid'];
                            $followItemids[] = $followingItems[$i]['itemid'];
                            $userItems[] = $matchObject;
                        }


                        if ($schemeNames[$sch] == "comp") {
                            $compCount++;
                        } else if ($schemeNames[$sch] == "ana") {
                            $anaCount++;
                        } else if ($schemeNames[$sch] == "tri") {
                            $triCount++;
                        } else if ($schemeNames[$sch] == "sha") {
                            $shaCount++;
                        }
                    }
                }
            }



            // sort through matches from the STORE
            $storeitem_query = mysql_query("SELECT * FROM storeitem WHERE itemid > 0");
            while ($storeitem = mysql_fetch_array($storeitem_query)) {

                $description = $storeitem['description'];
                $saved_color1 = $storeitem['code1'];
                $saved_color2 = $storeitem['code2'];
                $saved_color3 = $storeitem['code3'];


                if ($inputColor) {
                    /// CHANGE 100 TO APPROPRIATE LEVEL BEFORE LAUNCH
                    /// CASE: The user has given a color/scheme and views items depending on match priority
                    //  Check if any of the 3 item colors corresponds to and of the 3 scheme colors
                    //  Separate based on priority
                    $currentColors = array($colorMatches[$sch], $colorMatches[$sch + 1]);
                    $storeObj = storeMatch($storeitem['itemid'], $currentColors, $hue_tol, $sat_tol, $light_tol, $schemeNames[$sch]);
                    if ($storeObj) {
                        $storeItems[] = $storeObj;
                    }
                } else {
                    // CASE: no color has been chose, so show all items;
                    $storeObj = new store_match_object();
                    $storeObj->itemid = $storeitem['itemid'];
                    $storeObj->colors = array($saved_color1, $saved_color2, $saved_color3);
                    $storeObj->description = $description;
                    $storeObj->priority = 1;
                    $storeObj->scheme = $schemeNames[$sch];
                    $storeObj->gender = $storeitem['gender'];
                    $storeObj->purchaselink = $storeitem['purchaselink'];
                    $storeObj->url = $storeitem['url'];
                    $storeItems[] = $storeObj;
                }

                if ($schemeNames[$sch] == "comp") {
                    $compCount++;
                } else if ($schemeNames[$sch] == "ana") {
                    $anaCount++;
                } else if ($schemeNames[$sch] == "tri") {
                    $triCount++;
                } else if ($schemeNames[$sch] == "sha") {
                    $shaCount++;
                }
            }
        }
    }

    function cmp($a, $b) {
        // array low -> high
        // priority high -> low
        // reverse comparison string
        return strcmp($b->priority, $a->priority);
    }

    if ($inputColor) {
        // sort according to degree of match(priority) if there was a color entered
        usort($storeItems, "cmp");
    }


    $returnArray = array('anaCount' => $anaCount,
        'splCount' => $splCount,
        'shaCount' => $shaCount,
        'triCount' => $triCount,
        'compCount' => $compCount,
        'userItems' => $userItems,
        'storeItems' => $storeItems);

    return($returnArray);
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
?> 

