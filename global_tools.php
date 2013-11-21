<?php

include('algorithms.php');

function getImagetype($imageType) {
    // input: return value from exif_imagetype()
//// DETERMINE PROPER HEADER AND IMAGE TYPE FOR IMAGE DEPENDING ON DATABASE TYPE 
    if ($imageType == 1) {
        $ext = "gif";
    } else if ($imageType == 2) {
        $ext = "jpeg";
    } else if ($imageType == 3) {
        $ext = "png";
    }
    return $imageType;
}

function isPrime($num) {
    if ($num == 1)
        return false;
    //2 is prime (the only even number that is prime)
    if ($num == 2)
        return true;
    /**
     * if the number is divisible by two, then it's not prime and it's no longer
     * needed to check other even numbers
     */
    if ($num % 2 == 0) {
        return false;
    }
    /**
     * Checks the odd numbers. If any of them is a factor, then it returns false.
     * The sqrt can be an aproximation, hence just for the sake of
     * security, one rounds it to the next highest integer value.
     */
    for ($i = 3; $i <= ceil(sqrt($num)); $i = $i + 2) {
        if ($num % $i == 0)
            return false;
    }
    return true;
}

function emailTemplate($message) {
    return "<html><head></head><body><table width='100%' border='0' cellspacing='0' cellpadding='0' bgcolor='#eeeeee'>
            <tr><td align='center'>
                    <table width='600' border='0' cellspacing='0' cellpadding='0' style='margin-left:auto;margin-right:auto;text-align:left'>                      
                        <tr><td><table width='100%' border='0' cellspacing='0' cellpadding='20'>
                        <tbody><tr valign='top'><td><div style='width:520px;padding:20px 20px 20px 20px;text-align:center;'>
                        <img src='http://hueclues.com/img/huecluesLogo.png' height='105' alt='hueclues' />
                       </div></td></tbody></table><table width='100%' border='0' cellspacing='0' cellpadding='20'>
                                    <tbody><tr valign='top'><td bgcolor='#51BB75' style='background-color:rgb(81,187,117);width:345px;padding:20px 20px 20px 20px;font-family:Helvetica Neue,Arial,sans-serif;color:#fff;font-size:13px;line-height:18px;'>
                                                <h1 style='font-weight:700;font-size:16px;margin:0px 0px 10px 0px;color:#fff;border-bottom:dotted #eee thin;padding-bottom:5px'>Message from hueclues</h1>
                                                <p style='margin:10px 0px 10px 0px'>" . $message . "
                                             <br><br><br><br><a style='text-decoration:none;color:#555' href='http://hueclues.com/terms'>terms</a>
                                                    <a style='text-decoration:none;color:#555' href='http://hueclues.com/privacy'>privacy policy<a/></p>
                                                </p></td></tr></tbody></table></td></tr>
                        <tr valign='bottom' style='height:50px'><td></td></tr></table>
                </td></tr></table></body></html>";
}

function typeaheadTags() {
    $result = mysql_query("SELECT * FROM tag where count > 1");
    $tag_array = array();
    while ($tags = mysql_fetch_array($result)) {
        $tag_array[] = "#" . $tags['name'];
    }
    return $tag_array;
}

function typeaheadUsers() {
    $result = mysql_query("SELECT * FROM user where followers > 1");
    $user_array = array();
    while ($user = mysql_fetch_array($result)) {
        $user_array[] = $user['username'];
    }
    return $user_array;
}

function initiateTypeahead() {
    echo "var typeahead_tags = " . json_encode(typeaheadTags()) . "; var typeahead_users = " . json_encode(typeaheadUsers()) . ";" .
    "var typeahead_src = typeahead_tags.concat(typeahead_users);
$(function() {
$( '#searchInput' ).autocomplete({
source: typeahead_src           
});
});";
}

function initiateNotification() {
    echo " <a href='#notification' id='fancyNotification' style='display:none'></a>
    <div style = 'display:none'>
    <div id = 'notification'></div></div>";
}

function getExtension($str) {
    $i = strrpos($str, ".");
    if (!$i) {
        return "";
    }

    $l = strlen($str) - $i;
    $ext = substr($str, $i + 1, $l);
    return $ext;
}

function checkNotifications() {
    echo "var notification = \"" .
    $_SESSION['account_notification'] .
    $_SESSION['login_notification'] .
    $_SESSION['signup_notification'] .
    $_SESSION['password_recovery_notification'] .
    $_SESSION['upload_notification'] . "\";";
    // clear notifications
    $_SESSION['account_notification'] = "";
    $_SESSION['login_notification'] = "";
    $_SESSION['signup_notification'] = "";
    $_SESSION['password_recovery_notification'] = "";
    $_SESSION['upload_notification'] = "";

    echo "displayNotification(notification);";
}

function initiateTools() {
    metaTags();
    echo "<title>hueclues</title>";
    echo "<link rel = 'icon' type = 'image/png' href = '/img/favicon.ico'>";
    echo "<link rel = 'shortcut icon' type href = '/favicon.ico'>";
    echo "<meta http-equiv = 'Content-Type' content = 'text/html; charset=utf-8'>";
    echo "<link rel = 'stylesheet' href = '/css/font-awesome.css'>";
}

function metaTags() {
    if (strpos($_SERVER['REQUEST_URI'], 'closet') !== false) {
        $url = explode("/", $_SERVER['REQUEST_URI']);
        $username = end($url);
        // get closet info
        $user = database_fetch("user", "username", $username);
        $item = database_order_fetch("item", "userid", $user['userid'], "", "", "time");
        $itemObj = returnItem($item['itemid']);
        echo "<meta name='description' content='hueclues lets you easily promote, manage, and select clothing'> 
     <meta property='og:image' content='" . $itemObj->image_link . "'/>
     <meta property='og:title' content=\"" . $itemObj->owner_username . "'s Closet\" />
     <meta property='og:site_name' content='hueclues'/>
     <meta property='og:type' content=''/>";
    } else {
        echo "<meta name='description' content='hueclues lets you easily promote, manage, and select clothing'> 
<meta property='og:image' content='http://hueclues.com/img/hc_icon_new.png'/>
     <meta property='og:title' content='Hueclues'/>
     <meta property='og:site_name' content='hueclues'/>
     <meta property='og:type' content=''/>";
    }
}

function echoClear($notification_name) {
    echo $_SESSION[$notification_name];
    $_SESSION[$notification_name] = "";
}

function commonHeader() {
    if ($_SESSION['userid']) {
        $user = database_fetch("user", "userid", $_SESSION['userid']);
        echo "
    <div id='navigationbar'><h1 id = 'title'>
    <a href='/' id='logoLink'><img id = 'logo' src = '/img/huecluesLogo.png' /></a>

    <a href = '/home' class = 'navigationText'><img class='navigationIcon' src = '/img/home.png'></img>HOME</a>
    <a href = '/closet' class = 'navigationText'><img class='navigationIcon' src = '/img/closet.png'></img>CLOSET</a>
    <a href = '/hive' class = 'navigationText'><img class='navigationIcon' src = '/img/hive.png'></img>HIVE</a>
<a class = 'navigationText' onclick='toggleOutfit()'><img class='navigationIcon' src = '/img/hanger.png'></img></a>

    <form action = '/search_processing.php' id = 'searchForm' method = 'GET' style = 'display:inline-block'>
    <div class = 'input-append' style = 'display:inline;'>
    <input id = 'searchInput' autocomplete = 'off' type = 'text' name = 'q' placeholder = ' search user or #tag' />
    <button type = 'submit' id = 'searchButton'></button>
    </div>
    </form>
<div id='condensedMenu' onclick='Redirect(\"/closet\")' onmouseover='headerMenu(\"on\")' onmouseout='headerMenu(\"off\")'>
    <img class='selfPicture' src='" . $user['picture'] . "' onclick='Redirect('/account')'></img>
    <span class='selfName' style='margin-top:10px;font-size:15px'>" . $user['name'] . "</span>
    <div id='collapsedMenu'>
    <a href = '/extraction' class = 'navigationImage'><img title='Uploaded Images' style = 'height:16px;' src = '/img/cameraGreen.png'></img>  Upload</a>
    <a href = '/account' class = 'navigationImage'><img title = 'Account' style = 'height:20px' src = '/img/gear.png'></img> Account</a>
    <a href = '/logout.php' class = 'navigationImage'><i title = 'Logout' style = 'font-size:25px;text-decoration:none;color:#58595B;' class = 'icon-off'></i> Logout</a>
</div>
</div>
</h1></div>
<div id='outfitBar' style='display:none'>
 </div>";
    } else {
        echo "<div id = 'navigationbar'><h1 id = 'title'>
        <a href = '/' id = 'logoLink'><img id = 'logo' src = '/img/huecluesLogo.png' /></a></h1></div>";
    }
}

function socialMedia() {
    echo "https://www.facebook.com/sharer/sharer.php?t=hueclues&u=http://hueclues.com/closet/thesunnyos";
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
            if ($itemObject->purchaselink) {
                $purchaseDisabled = "";
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
        $itemLink = "/hue/" . $itemObject->itemid;
        echo "<div class='smallItemContainer' id='item" . $itemObject->itemid . "'style='color:#" . $itemObject->text_color . ";width:" . (($width) ? $width . "px;" : "") . "' >
    <span class = 'itemDescription' style='background-color:#" . $itemObject->hexcode . ";width:" . (($width) ? $width . "px;height:auto" : "") . "'>" . stripslashes($itemObject->description) . "</span>" . (($owns_item) ? "<a class = 'itemAction trashIcon' onclick = 'removeItem(" . $itemObject->itemid . ")'><i class='itemActionImage icon-remove-sign'></i></a>" : "") . "
    <a class = 'itemAction tagIcon' id = 'tag_search' href = '/tag?q=" . $search_string . "' ><img class='itemActionImage' title='match by tags' src='/img/tag.png'></img> search</a>
    <a class = 'itemAction beeIcon' id = 'color_search' href = '/hue/" . $itemObject->itemid . "' ><img class='itemActionImage' title='match by color'  src='/img/bee.png'></img> match</a>
    <a class = 'itemAction purchaseIcon' " . $purchaseDisabled . " id = 'color_search' " . $purchaseString . " >
    <i class='itemActionImage icon-search' title='get this link'></i> explore</a>
    <img alt = '  This Image Is Broken' src = '" . $itemObject->image_link . "' onclick=\"Redirect('$itemLink')\" class = 'fixedwidththumb thumbnaileffect' style='width:" . (($width) ? $width . "px;height:auto" : "") . "' />
    <br/>
    <div class='itemTagBox' style='background-color:#" . $itemObject->hexcode . ";width:" . (($width) ? $width . "px;height:auto" : "") . "'>
        <input type = 'text' class='itemTag'  name = 'tags'" . ((!$owns_item) ? "readonly = 'true'" : "") . " onchange = 'updateTags(this, " . $itemObject->itemid . ")' value = '" . $item_tags_string . "' placeholder = 'define this style with #hashtags' />
        <input type = 'text' class='purchaseLink'  name = 'purchaseLink' onblur='hidePurchaseLink(" . $itemObject->itemid . ")' onchange = 'updatePurchaseLink(this, " . $itemObject->itemid . ")' value = '" . $itemObject->purchaselink . "' placeholder = 'link to buy/find item' />     
    </div>
    <br/>
</div>";
    }
}

function formatOutfitItem($userid, $itemObject, $height = "", $itemLink = "") { // by default clicking directs to item 
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
            if ($itemObject->purchaselink) {
                $purchaseDisabled = "";
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
        $itemLink = "/hue/" . $itemObject->itemid;
        echo "<div class='outfitItemContainer' id='item" . $itemObject->itemid . "'style='color:#" . $itemObject->text_color . ";height:" . (($height) ? $height . "px;width:auto" : "") . "' >
        <img alt = '  This Image Is Broken' src = '" . $itemObject->image_link . "' onclick=\"Redirect('$itemLink')\" class = 'outfitImage' />
    <div class='outfitItemTagBox' style='background-color:#" . $itemObject->hexcode . ";'>
        <input type = 'text' class='itemTag'  name = 'tags'" . ((!$owns_item) ? "readonly = 'true'" : "") . " onchange = 'updateTags(this, " . $itemObject->itemid . ")' value = '" . $item_tags_string . "' placeholder = 'define this style with #hashtags' />
        <input type = 'text' class='purchaseLink'  name = 'purchaseLink' onblur='hidePurchaseLink(" . $itemObject->itemid . ")' onchange = 'updatePurchaseLink(this, " . $itemObject->itemid . ")' value = '" . $itemObject->purchaselink . "' placeholder = 'link to buy/find item' />     
    </div>
    <br/>
</div>";
    }
}

function formatItem($userid, $itemObject, $height = "") {
    $owns_item = ($userid == $itemObject->owner_id);
    $item_tags = array();
    $tagmap_query = database_query("tagmap", "itemid", $itemObject->itemid);
    $like = database_fetch("like", "userid", $userid, "itemid", $itemObject->itemid);
    while ($tagmap = mysql_fetch_array($tagmap_query)) {
        $tag = database_fetch("tag", "tagid", $tagmap['tagid']);
        array_push($item_tags, $tag['name']);
    }
    $item_tags_string = implode(" #", $item_tags);
    if ($item_tags_string) {
        $item_tags_string = "#" . $item_tags_string;
    }
    $purchaseDisabled = "";
    if ($owns_item) {
        $purchaseString = "onclick=\"togglePurchaseLink(" . $itemObject->itemid . ")\"";
    } else {
        if ($itemObject->purchaselink) {
            $purchaseString = "href='" . $itemObject->purchaselink . "' target='_blank'";
        } else {
            $purchaseDisabled = " style='color:#808285;font-color:#808285;'";
            $purchaseString = "href='javascript:void(0)'";
        }
    }
    $search_string = str_replace("#", "%23", $item_tags_string);

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
    <span class = 'itemDescription' style='background-color:#" . $itemObject->hexcode . "'>" . stripslashes($itemObject->description) . "</span>
        " . (($owns_item) ? "<a class = 'itemAction trashIcon' onclick = 'removeItem(" . $itemObject->itemid . ")'><i class='itemActionImage icon-remove-sign'></i></a>" : "") . "
    <a class = 'itemAction outfitIcon' id = 'tag_search' onclick='addToOutfit(" . $itemObject->itemid . ")'><i class='itemActionImage icon-plus' title='match by tags'></i> to outfit</a>
    <a class = 'itemAction beeIcon' id = 'color_search' href = '/hue/" . $itemObject->itemid . "' ><img class='itemActionImage' title='match by color'  src='/img/bee.png'></img> match</a>
    <a class = 'itemAction purchaseIcon' " . $purchaseDisabled . $purchaseString . " ><i class='itemActionImage icon-search' title='get this link'  style='font-size:20px'></i> explore</a>
    <a class = 'itemAction likeIcon' id = 'like' onclick='likeButton(" . $itemObject->itemid . ")'><i title='like this'  style='font-size:20px' class='itemActionImage icon-heart" . $likeString . "</a>    
    <img alt = '  This Image Is Broken' src = '" . $itemObject->image_link . "' onclick=\"Redirect('/hue/" . $itemObject->itemid . "')\" class = 'fixedwidththumb thumbnaileffect' style='height:" . (($height) ? $height . "px;width:auto" : "") . "' />

    <div class='itemTagBox' style='background-color:#" . $itemObject->hexcode . "'>
        <input type = 'text' class='itemTag'  name = 'tags'" . ((!$owns_item) ? "readonly = 'true'" : "") . " onchange = 'updateTags(this, " . $itemObject->itemid . ")' value = '" . $item_tags_string . "' placeholder = 'define this style with #hashtags' />
        <input type = 'text' class='purchaseLink'  name = 'purchaseLink' onblur='hidePurchaseLink(" . $itemObject->itemid . ")' onchange = 'updatePurchaseLink(this, " . $itemObject->itemid . ")' value = '" . $itemObject->purchaselink . "' placeholder = 'link to buy/find item' />     
    </div>
    <br/>
</div>";
}

function autoRotateImage($image) {
    $orientation = $image->getImageOrientation();

    switch ($orientation) {
        case imagick::ORIENTATION_BOTTOMRIGHT:
            $image->rotateimage("#000", 180); // rotate 180 degrees 
            break;

        case imagick::ORIENTATION_RIGHTTOP:
            $image->rotateimage("#000", 90); // rotate 90 degrees CW 
            break;

        case imagick::ORIENTATION_LEFTBOTTOM:
            $image->rotateimage("#000", -90); // rotate 90 degrees CCW 
            break;
    }

    // Now that it's auto-rotated, make sure the EXIF data is correct in case the EXIF gets saved with the image! 
    $image->setImageOrientation(imagick::ORIENTATION_TOPLEFT);
}

function formatOutfit($userid, $outfitid) {
    // takes in the outfit id and returns outfit Object
    $outfitObject = returnOutfit($outfitid);
    $item1 = returnItem($outfitObject->itemid1);
    $item2 = returnItem($outfitObject->itemid2);
    $item3 = returnItem($outfitObject->itemid3);
    $item4 = returnItem($outfitObject->itemid4);
    $item5 = returnItem($outfitObject->itemid5);
    $item6 = returnItem($outfitObject->itemid6);
    if (!$outfitObject->name) {
        $outfitObject->name = "Untitled Outfit";
    }
    echo "<div class='outfitContainer' id='outfit" . $outfitObject->outfitid . "'>";
    echo "<div class='outfitRow' align='center'>";
    echo "<span class='outfitName'>" . $outfitObject->name . "  <i class='icon-edit cursor' onclick='editOutfit(" . $outfitObject->outfitid . ")'></i></span><br/><br/>";
    echo "<div class='outfitItemPreview'>";
    formatOutfitItem($userid, $item1, 175);
    echo "</div>";
    echo "<div class='outfitItemPreview'>";
    formatOutfitItem($userid, $item2, 175);
    echo "</div>";
    echo "<div class='outfitItemPreview'>";
    formatOutfitItem($userid, $item3, 175);
    echo "</div></div>";
    echo "<br/>";
    echo "<div class='outfitRow' align='center'>";
    echo "<div class='outfitItemPreview'>";
    formatOutfitItem($userid, $item4, 175);
    echo "</div>";
    echo "<div class='outfitItemPreview'>";
    formatOutfitItem($userid, $item5, 175);
    echo "</div>";
    echo "<div class='outfitItemPreview'>";
    formatOutfitItem($userid, $item6, 175);
    echo "</div></div>";
}

function formatUser($userid, $otherUserid) {
// Input: passes in the userid of the logged in user and userid of the other users
// Ouput: User preview, with follow/unfollow options
    if ($userid != $otherUserid) {
        $user = database_fetch("user", "userid", $otherUserid);
        echo "<div id='user" . $otherUserid . "' class='userContainer'>
    <a href = '/closet/" . $user['username'] . "' class='userPreview'>
       <img class='followUserPicture' src='" . $user['picture'] . "'></img>
        <div class='followUserText'>" . $user['username'] . "
            <br/><span class='followerCount'>" . $user['followers'] . " followers</span></div></a>        
    <button id='followaction" . $user['userid'] . "' class='greenFollowButton " . ((database_fetch("follow ", "userid", $user['userid'], "followerid", $userid)) ? 'clicked' : '') . "'
            onclick='followButton(" . $user['userid'] . ")'>" . ((database_fetch("follow ", "userid", $user['userid'], "followerid", $userid)) ? "following" : "follow") . "</button><br/>
</div>";
    }
}

function is_mobile() {
// Get the user agent
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
// Create an array of known mobile user agents
// This list is from the 21 October 2010 WURFL File.
// Most mobile devices send a pretty standard string that can be covered by
// one of these.  I believe I have found all the agents (as of the date above)
// that do not and have included them below.  If you use this function, you 
// should periodically check your list against the WURFL file, available at:
// http://wurfl.sourceforge.net/
    $mobile_agents = Array(
        "240x320",
        "acer",
        "acoon",
        "acs-",
        "abacho",
        "ahong",
        "airness",
        "alcatel",
        "amoi",
        "android",
        "anywhereyougo.com",
        "applewebkit/525",
        "applewebkit/532",
        "asus",
        "audio",
        "au-mic",
        "avantogo",
        "becker",
        "benq",
        "bilbo",
        "bird",
        "blackberry",
        "blazer",
        "bleu",
        "cdm-",
        "compal",
        "coolpad",
        "danger",
        "dbtel",
        "dopod",
        "elaine",
        "eric",
        "etouch",
        "fly ",
        "fly_",
        "fly-",
        "go.web",
        "goodaccess",
        "gradiente",
        "grundig",
        "haier",
        "hedy",
        "hitachi",
        "htc",
        "huawei",
        "hutchison",
        "inno",
        "ipad",
        "ipaq",
        "ipod",
        "jbrowser",
        "kddi",
        "kgt",
        "kwc",
        "lenovo",
        "lg ",
        "lg2",
        "lg3",
        "lg4",
        "lg5",
        "lg7",
        "lg8",
        "lg9",
        "lg-",
        "lge-",
        "lge9",
        "longcos",
        "maemo",
        "mercator",
        "meridian",
        "micromax",
        "midp",
        "mini",
        "mitsu",
        "mmm",
        "mmp",
        "mobi",
        "mot-",
        "moto",
        "nec-",
        "netfront",
        "newgen",
        "nexian",
        "nf-browser",
        "nintendo",
        "nitro",
        "nokia",
        "nook",
        "novarra",
        "obigo",
        "palm",
        "panasonic",
        "pantech",
        "philips",
        "phone",
        "pg-",
        "playstation",
        "pocket",
        "pt-",
        "qc-",
        "qtek",
        "rover",
        "sagem",
        "sama",
        "samu",
        "sanyo",
        "samsung",
        "sch-",
        "scooter",
        "sec-",
        "sendo",
        "sgh-",
        "sharp",
        "siemens",
        "sie-",
        "softbank",
        "sony",
        "spice",
        "sprint",
        "spv",
        "symbian",
        "tablet",
        "talkabout",
        "tcl-",
        "teleca",
        "telit",
        "tianyu",
        "tim-",
        "toshiba",
        "tsm",
        "up.browser",
        "utec",
        "utstar",
        "verykool",
        "virgin",
        "vk-",
        "voda",
        "voxtel",
        "vx",
        "wap",
        "wellco",
        "wig browser",
        "wii",
        "windows ce",
        "wireless",
        "xda",
        "xde",
        "zte"
    );

// Pre-set $is_mobile to false.
    $is_mobile = false;
// Cycle through the list in $mobile_agents to see if any of them
// appear in $user_agent.
    foreach ($mobile_agents as $device) {
// Check each element in $mobile_agents to see if it appears in
// $user_agent.  If it does, set $is_mobile to true.
        if (stristr($user_agent, $device)) {
            $is_mobile = true;
// break out of the foreach, we don't need to test
// any more once we get a true value.
            break;
        }
    }
    return $is_mobile;
}

?>
