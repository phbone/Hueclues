<?php

/*
 * Contains functions dealing with 
 * 
 * -items
 * -getting all items that match with an item
 * -getting items from closets user is following
 * -small items
 * -store items
 * 
 * 
 *  
 */

function formatAppItem($userid, $itemObject, $height = "", $delete = "on") {
    $loggedIn = isset($_SESSION['userid']);
    $owns_item = ($userid == $itemObject->owner_id);
    $item_tags = array();
    $tagmap_query = database_query("tagmap", "itemid", $itemObject->itemid);
    $like = database_fetch("like", "userid", $userid, "itemid", $itemObject->itemid);
    $canEdit = "";
    $purchaseDisabled = "";


    if ($delete = "on" && $owns_item) {
        // by default the icon is on for item owner
        $deleteIcon = "<a class = 'itemAction trashIcon' onclick = 'removeItem(" . $itemObject->itemid . ")'><i class='itemActionImage fa fa-times-circle'></i></a>";
    } else {
        $deleteIcon = "";
    }
    while ($tagmap = mysql_fetch_array($tagmap_query)) {
        $tag = database_fetch("tag", "tagid", $tagmap['tagid']);
        $tagString .= "<a class='hashtag' href='/tag?q=%23" . $tag['name'] . "'>#" . $tag['name'] . "</a>";
    }


    if ($owns_item) {
        $purchaseString = "onclick=\"togglePurchaseLink(" . $itemObject->itemid . ")\"";
        $canEdit = "<i class='fa fa-edit editIcon' onclick='toggleEditTags(this," . $itemObject->itemid . ")'></i>";
    } else {
        $purchaseString = "onclick=\"findButton(" . $itemObject->purchaselink . ")\"";
        if (!$itemObject->purchaselink) {

            $purchaseDisabled = " style='color:#808285;font-color:#808285;'";
        }
    }
    // format likes
    if ($itemObject->likedbyuser == "liked" || $owns_item) {
        $likeString = " liked' ></i><span class='likeText'>" . $itemObject->like_count . "</span>";
    } else if ($itemObject->likedbyuser == "unliked") {
        $likeString = "' ></i><span class='likeText'>like</span> ";
    }

    echo "<div class='appItemContainer' id='item" . $itemObject->itemid . "'style='color:#" . $itemObject->text_color . "' > 
        
    <div class='appItemOwnerContainer'><div id='user" . $itemObject->owner_id . "' class='itemUserContainer'>
            <a href = '/closet/" . $itemObject->owner_username . "' class='appUserLink'>
                <img class='appUserPicture' src='" . $itemObject->owner_picture . "'></img>
                <div class='appUserText'>" . $itemObject->owner_username . "
               </div>
            </a>
            </div>
            </div>  
    <a class = 'itemAction outfitIcon' id = 'tag_search' onclick='addToOutfit(" . $itemObject->itemid . ")'><i class='itemActionImage fa fa-plus' title='match by tags'></i> to outfit</a>
    <a class = 'itemAction beeIcon' id = 'color_search' href = '/hue/" . $itemObject->itemid . "' ><img class='itemActionImage' title='match by color'  src='/img/bee" . $itemObject->text_color . ".png'></img> match</a>
    <a class = 'itemAction purchaseIcon' " . $purchaseDisabled . $purchaseString . "><i class='itemActionImage fa fa-search' title='this user can give a source link'  style='font-size:20px'></i> find</a>
    <a class = 'itemAction likeIcon' id = 'like' onclick='likeButton(" . $itemObject->itemid . ")'><i title='like this'  style='font-size:20px' class='itemActionImage fa fa-heart" . $likeString . "</a>    
    <img alt = '  This Image Is Broken' src = '" . $itemObject->image_link . "' onclick=\"Redirect('/hue/" . $itemObject->itemid . "')\" class = 'fixedwidththumb thumbnaileffect' style='height:" . (($height) ? $height . "px;width:auto" : "") . "' />
    <span class = 'itemDescription' style='background-color:#" . $itemObject->hexcode . "'>" . stripslashes($itemObject->description) . "</span>" . $deleteIcon . "
    
    <div class='itemTagBox' style='background-color:#" . $itemObject->hexcode . "'>
      <div class='hashtagContainer' placeholder = 'define this style with #hashtags'>" . $tagString . $canEdit . "<hr class='hashtagLine'/></div>
          <input type = 'text' class='purchaseLink'  name = 'purchaseLink' onblur='hidePurchaseLink(" . $itemObject->itemid . ")' onchange = 'updatePurchaseLink(this, " . $itemObject->itemid . ")' value = '" . $itemObject->purchaselink . "' placeholder = 'link to buy/find item' />     
    </div>
</div>";
}

function formatAppSmallItem($userid, $itemObject, $height = 150, $width = "", $inputColor = "") {

    $tagmap_query = database_query("tagmap", "itemid", $itemObject->itemid);


    if ($itemObject->sizeRatio == 0) {
        // if the image has a size and an input color was given.
        $itemObject->sizeRatio = 1;
    }

    if ($width) {
        $imgHeight = $width / $itemObject->sizeRatio;
        $imgWidth = $width;
        $itemHeight = $imgHeight + 75;
    } else {
        // 
        $itemHeight = $height + 75;
        $imgHeight = $height;
        $imgWidth = $height * $itemObject->sizeRatio;
    }

    while ($tagmap = mysql_fetch_array($tagmap_query)) {
        $tag = database_fetch("tag", "tagid", $tagmap['tagid']);
        $tagString .= "<a class='hashtag' href='/tag?q=%23" . $tag['name'] . "'>#" . $tag['name'] . "</a>";
    }

    // if itemobject is empty format blank tag
    if (!$itemObject->itemid) {
        $itemObject->owner_picture = "/img/hc_icon_blacksolid_square.png";
        $colorsArray = colorSuggest($inputColor);
        $randKey = array_rand($colorsArray);
        if ($colorsArray[$randKey] != "000000") {
            $redirectHtml = "onclick=\"Redirect('/sting?q=$colorsArray[$randKey]')\"";
            $itemObject->owner_username = "search #$colorsArray[$randKey]";
        }
    } else {
        $redirectHtml = "onclick=\"Redirect('/hue/$itemObject->itemid')\"";
    }

    if ($itemObject && $inputColor) {
        echo "
        <div class='appSmallItemContainer' id='item" . $itemObject->itemid . "'style='color:#" . $itemObject->text_color . ";height:" . $itemHeight . "px;width:" . $imgWidth . "px' > 
    <div class='appItemOwnerContainer' onclick=\"Redirect('/closet/$itemObject->owner_username')\"><div id='user" . $itemObject->owner_id . "' class='itemUserContainer'>
           <img class='appUserPicture' src='" . $itemObject->owner_picture . "'></img>
                <div class='appUserText'>" . $itemObject->owner_username . "
               </div>
            </div>
            </div>  
    <img alt = '  This Image Is Broken' style='background:#$colorsArray[$randKey];height:" . $imgHeight . "px' class = 'appSmallItemImage'src = '" . $itemObject->image_link . "' $redirectHtml/>
    <span class = 'appSmallItemDesc' style='background-color:#" . $itemObject->hexcode . "' $redirectHtml >" . stripslashes($itemObject->description) . "</span>
    <div class='itemTagBox' style='background-color:#" . $itemObject->hexcode . "'>
      <div class='hashtagContainer' placeholder = 'define this style with #hashtags'>" . $tagString . "<hr class='hashtagLine'/></div>
    </div>
</div>";
    }
}

function formatItem($userid, $itemObject, $height = "", $delete = "on") {
    $loggedIn = isset($_SESSION['userid']);
    $owns_item = ($userid == $itemObject->owner_id);
    $item_tags = array();
    $tagmap_query = database_query("tagmap", "itemid", $itemObject->itemid);
    $like = database_fetch("like", "userid", $userid, "itemid", $itemObject->itemid);
    $canEdit = "";
    $purchaseDisabled = "";


    if ($delete = "on" && $owns_item) {
        // by default the icon is on for item owner
        $deleteIcon = "<a class = 'itemAction trashIcon' onclick = 'removeItem(" . $itemObject->itemid . ")'><i class='itemActionImage fa fa-times-circle'></i></a>";
    } else {
        $deleteIcon = "";
    }
    while ($tagmap = mysql_fetch_array($tagmap_query)) {
        $tag = database_fetch("tag", "tagid", $tagmap['tagid']);
        $tagString .= "<a class='hashtag' href='/tag?q=%23" . $tag['name'] . "'>#" . $tag['name'] . "</a>";
    }


    if ($owns_item) {
        $purchaseString = "onclick=\"togglePurchaseLink(" . $itemObject->itemid . ")\"";
        $canEdit = "<i class='fa fa-edit editIcon' onclick='toggleEditTags(this," . $itemObject->itemid . ")'></i>";
    } else {
        $purchaseString = "onclick=\"findButton(" . $itemObject->purchaselink . ")\"";
        if (!$itemObject->purchaselink) {

            $purchaseDisabled = " style='color:#808285;font-color:#808285;'";
        }
    }
    // format likes
    if ($itemObject->likedbyuser == "liked" || $owns_item) {
        $likeString = " liked' ></i><span class='likeText'>" . $itemObject->like_count . "</span>";
    } else if ($itemObject->likedbyuser == "unliked") {
        $likeString = "' ></i><span class='likeText'>like</span> ";
    }

    echo "<div class='itemContainer' id='item" . $itemObject->itemid . "'style='color:#" . $itemObject->text_color . "' > 
    <div id='itemPreview' class='previewContainer'><div id='user" . $itemObject->owner_id . "' class='itemUserContainer'>
            <a href = '/closet/" . $itemObject->owner_username . "' class='userPreview'>
                <img class='userPicture' src='" . $itemObject->owner_picture . "'></img>
                <div class='userText'>" . $itemObject->owner_username . "
                <br/><span class='followerCount'>" . $itemObject->owner_followers . " followers</span></div>
            </a></div></div>  
    <a class = 'itemAction outfitIcon' id = 'tag_search' onclick='addToOutfit(" . $itemObject->itemid . ")'><i class='itemActionImage fa fa-plus' title='match by tags'></i> to outfit</a>
    <a class = 'itemAction beeIcon' id = 'color_search' href = '/hue/" . $itemObject->itemid . "' ><img class='itemActionImage' title='match by color'  src='/img/bee" . $itemObject->text_color . ".png'></img> match</a>
    <a class = 'itemAction purchaseIcon' " . $purchaseDisabled . $purchaseString . "><i class='itemActionImage fa fa-search' title='this user can give a source link'  style='font-size:20px'></i> find</a>
    <a class = 'itemAction likeIcon' id = 'like' onclick='likeButton(" . $itemObject->itemid . ")'><i title='like this'  style='font-size:20px' class='itemActionImage fa fa-heart" . $likeString . "</a>    
    <img alt = '  This Image Is Broken' src = '" . $itemObject->image_link . "' onclick=\"Redirect('/hue/" . $itemObject->itemid . "')\" class = 'fixedwidththumb thumbnaileffect' style='height:" . (($height) ? $height . "px;width:auto" : "") . "' />
    <span class = 'itemDescription' style='background-color:#" . $itemObject->hexcode . "'>" . stripslashes($itemObject->description) . "</span>" . $deleteIcon . "
    
    <div class='itemTagBox' style='background-color:#" . $itemObject->hexcode . "'>
      <div class='hashtagContainer' placeholder = 'define this style with #hashtags'>" . $tagString . $canEdit . "<hr class='hashtagLine'/></div>
          <input type = 'text' class='purchaseLink'  name = 'purchaseLink' onblur='hidePurchaseLink(" . $itemObject->itemid . ")' onchange = 'updatePurchaseLink(this, " . $itemObject->itemid . ")' value = '" . $itemObject->purchaselink . "' placeholder = 'link to buy/find item' />     
    </div>
</div>";
}

function returnAllItemsFromFollowing($user_id, $field = "") {
    // returns item objects from all of the people $user_id is following
    $followingArray = Array();
    $followingItems = Array();
    $follow_query = database_query("follow", "followerid", $user_id);
    while ($follow = mysql_fetch_array($follow_query)) {
        $followingArray[] = $follow['userid']; // list of userids of following
    }

    $item_query = database_query("item", "1", "1");
    while ($item = mysql_fetch_array($item_query)) {
        if (in_array($item['userid'], $followingArray)) {
            if ($field) {
                $followingItems[] = $item[$field];
            } else {
                $followingItems[] = $item;
            }
        }
    }
    return $followingItems;
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
    $sat_tol = 20;
    $light_tol = 70;
    $hue_tol = 8.3;

    $userItems = array(); // items that are from other users/ or yourself
    $storeItems = array(); // items from the store

    $item = database_fetch("item", "itemid", $itemid);
    $inputColor = $item['code'];
    $user = database_fetch("user", "userid", $userid);


    $colorObj = colorsMatching($inputColor);
// [comp, comp, ana1, ana2, tri1, tri2, sha1, sha2, spl1, spl2]

    $schemeCount = array(0, 0, 0, 0);
    $schemeNames = array("comp", "comp", "ana", "ana", "tri", "tri", "sha", "sha"/* ,"spl1","spl2" */);
    $colorMatches = array($colorObj->comp, $colorObj->comp, $colorObj->ana1, $colorObj->ana2, $colorObj->tri1, $colorObj->tri2, $colorObj->sha1, $colorObj->sha2/* , $colorObj->spl1, $colorObj->spl2 */);



    $followItemids = array(); // holds a list of unique itemids of items that match for following 
    $userItemids = array(); // holds a list of unique itemids of items that match for closet


    if ($userid) {

        $followingArray = Array();
        $follow_query = database_query("follow", "followerid", $userid);
        while ($follow = mysql_fetch_array($follow_query)) {
            $followingUser = database_fetch("user", "userid", $follow['userid']); // person user(logged in) is following

            if ($user['gender'] == $followingUser['gender'] || $followingUser['gender'] == "2") {
                $followingArray[] = $follow['userid']; // list of userids of following
            }
        }



        $item_query = database_query("item", "1", "1");
        while ($item = mysql_fetch_array($item_query)) {
            // go through each item one by one 
            if ($item['userid'] == $userid) {//item belongs to user
                $itemColor = $item['code'];
                for ($sch = 0; $sch < 8; $sch+=2) {
// goes through it by scheme

                    if ($sch < 6) {
                        $checkSame1 = hsl_same_color($itemColor, $colorMatches[$sch], $hue_tol, $sat_tol, $light_tol);
                        $checkSame2 = hsl_same_color($itemColor, $colorMatches[$sch + 1], $hue_tol, $sat_tol, $light_tol);
                    } else { // for shades and tints
                        $checkSame1 = hsl_same_hue($itemColor, $colorMatches[$sch], $hue_tol);
                        $checkSame2 = hsl_same_hue($itemColor, $colorMatches[$sch + 1], $hue_tol);
                    }
                    if ($item['itemid'] != $itemid) {
                        if ($checkSame1 || $checkSame2) {// current item matches with 1 of the 2 colors in the scheme
                            $currentItemid = array_search($item['itemid'], $userItemids);
                            if (in_array($item['itemid'], $userItemids)) {
                                $userItems[$currentItemid]->scheme .= " " . $schemeNames[$sch];
                            } else {
                                $matchObject = new matchObject();
                                $matchObject->source = "closet";
                                $matchObject->scheme = $schemeNames[$sch];
                                $matchObject->itemid = $item['itemid'];
                                $userItemids[] = $item['itemid'];
                                $userItems[] = $matchObject;
                            }
                            $schemeCount[$sch / 2]++;
                        }
                    }
                }
            }

            if (in_array($item['userid'], $followingArray)) {
// this item belongs someone the user is following
                $itemColor = $item['code'];
                for ($sch = 0; $sch < 8; $sch+=2) {
                    if ($sch < 6) {
                        $checkSame1 = hsl_same_color($itemColor, $colorMatches[$sch], $hue_tol, $sat_tol, $light_tol);
                        $checkSame2 = hsl_same_color($itemColor, $colorMatches[$sch + 1], $hue_tol, $sat_tol, $light_tol);
                    } else { // for shades and tints
                        $checkSame1 = hsl_same_hue($itemColor, $colorMatches[$sch], $hue_tol);
                        $checkSame2 = hsl_same_hue($itemColor, $colorMatches[$sch + 1], $hue_tol);
                    } if ($checkSame1 || $checkSame2) {// the current item matches 1 of the 2 colors in the scheme
                        if ($item['itemid'] != $itemid) { // item cannot match itself
/// check if the itemid already exists, if so add the current scheme to that data
                            $currentItemid = array_search($item['itemid'], $followItemids);
                            if (in_array($item['itemid'], $followItemids)) {
                                $userItems[$currentItemid]->scheme .= " " . $schemeNames[$sch];
                            } else {
// otherwise count and create new object
                                $matchObject = new matchObject();
                                $matchObject->source = "following";
                                $matchObject->scheme = $schemeNames[$sch];
                                $matchObject->itemid = $item['itemid'];
                                $followItemids[] = $item['itemid'];
                                $userItems[] = $matchObject;
                            }
                            $schemeCount[$sch / 2]++;
                        }
                    }
                }
            }
        }



// sort through matches from the STORE
        $storeitem_query = database_query("storeitem", "gender", $user['gender']);
        while ($storeitem = mysql_fetch_array($storeitem_query)) {

            $description = $storeitem['description'];
            $saved_color1 = $storeitem['code1'];
            $saved_color2 = $storeitem['code2'];
            $saved_color3 = $storeitem['code3'];
            for ($sch = 0; $sch < 8; $sch+=2) {

/// CHANGE 100 TO APPROPRIATE LEVEL BEFORE LAUNCH
/// CASE: The user has given a color/scheme and views items depending on match priority
//  Check if any of the 3 item colors corresponds to and of the 3 scheme colors
//  Separate based on priority
                $currentColors = array($colorMatches[$sch], $colorMatches[$sch + 1]);
                $storeObj = storeMatch($storeitem['itemid'], $currentColors, $hue_tol, $sat_tol, $light_tol, $schemeNames[$sch]);
                if ($storeObj) {
                    $storeItems[] = $storeObj;
                    $schemeCount[$sch / 2]++;
                }
            }
        }
    }

    $compCount = $schemeCount[0];
    $anaCount = $schemeCount[1];
    $shaCount = $schemeCount[3];
    $triCount = $schemeCount[2];

    $returnArray = array(
        'anaCount' => $anaCount,
        'shaCount' => $shaCount,
        'triCount' => $triCount,
        'compCount' => $compCount,
        'userItems' => $userItems,
        'storeItems' => $storeItems);

    return($returnArray);
}

function formatStoreItem($match_object) {
    echo "<div id='storeItem$match_object->itemid' class='storeMatch " . $match_object->gender . "'>
<div class='storeBar1' style='background-color:#" . $match_object->colors[0] . "'></div>
<div class='storeBar2' style='background-color:#" . $match_object->colors[1] . "'></div>
<div class='storeBar3' style='background-color:#" . $match_object->colors[2] . "'></div>
<span class='storeTitle'><span class='storePrice' title='Color Match Percentage'>$" . $match_object->price . "</span>  " . stripslashes($match_object->description) . "</span>              
<img alt='  This Image Is Broken' src='" . $match_object->url . "' class='fixedwidththumb thumbnaileffect' /><br/><br/>                                        
<a class='storeLink' href='" . $match_object->purchaselink . "' target='_blank' class='storeUrl'>View Item In Store</a>
</div>";
}

function formatSmallItem($userid, $itemObject, $width = "", $itemLink = "") {
    // this item has no user preview
    if ($itemObject->owner_id) {
        $owns_item = ($userid == $itemObject->owner_id);
        $item_tags = array();
        $tagmap_query = database_query("tagmap", "itemid", $itemObject->itemid);
        while ($tagmap = mysql_fetch_array($tagmap_query)) {
            $tag = database_fetch("tag", "tagid", $tagmap['tagid']);
            array_push($item_tags, $tag['name']);
        }
        $item_tags_string = implode(" #", $item_tags);
        if ($item_tags_string) {
            $item_tags_string = "#" . $item_tags_string;
        }
        if ($owns_item) {
            $purchaseString = "onclick=\"togglePurchaseLink(" . $itemObject->itemid . ")\"";
        } else {
            $purchaseDisabled = " ";
            if ($itemObject->purchaselink) {
                $purchaseString = "href='" . $itemObject->purchaselink . "' target='_blank'";
            } else {
                $purchaseDisabled = " style='color:#808285;font-color:#808285;'";
                $purchaseString = "href='javascript:void(0)'";
            }
        }
        $search_string = str_replace("#", "%23", $item_tags_string);

        if ($itemLink == "off") {
            $itemLink = "";
        } else if (!$itemLink) { //
            $itemLink = "/hue/" . $itemObject->itemid;
        }
        echo "<div class='smallItemContainer' id='item" . $itemObject->itemid . "'style='color:#" . $itemObject->text_color . ";width:" . (($width) ? $width . "px;" : "") . "' >
        <a class = 'smallItemAction tagIcon' id = 'tag_search' href = '/tag?q=" . $search_string . "' ><img class='itemActionImage' title='match by tags' src='/img/tag.png'></img> search</a>
    <a class = 'smallItemAction beeIcon' id = 'color_search' href = '/hue/" . $itemObject->itemid . "' ><img class='itemActionImage' title='match by color'  src='/img/bee.png'></img> match</a>
    <a class = 'smallItemAction purchaseIcon' " . $purchaseDisabled . " id = 'color_search' " . $purchaseString . " >
    <i class='smallItemActionImage fa fa-search' title='this user can give a source link'></i> explore</a>
    <img alt = '  This Image Is Broken' src = '" . $itemObject->image_link . "' onclick=\"Redirect('$itemLink')\" class = 'fixedwidththumb thumbnaileffect' style='width:" . (($width) ? $width . "px;height:auto" : "") . "' />
    <span class = 'smallItemDescription' style='background-color:#" . $itemObject->hexcode . ";width:" . (($width) ? $width . "px;height:auto" : "") . "'>" . stripslashes($itemObject->description) . "</span>
    <div class='itemTagBox' style='background-color:#" . $itemObject->hexcode . ";width:" . (($width) ? $width . "px;height:auto" : "") . "'>
        <input type = 'text' class='itemTag'  name = 'tags'" . ((!$owns_item) ? "readonly = 'true'" : "") . " onchange = 'updateTags(this, " . $itemObject->itemid . ")' value = '" . $item_tags_string . "' placeholder = 'define this style with #hashtags' />
        <input type = 'text' class='purchaseLink'  name = 'purchaseLink' onblur='hidePurchaseLink(" . $itemObject->itemid . ")' onchange = 'updatePurchaseLink(this, " . $itemObject->itemid . ")' value = '" . $itemObject->purchaselink . "' placeholder = 'link to buy/find item' />     
    </div>
</div>";
    }
}

?>
