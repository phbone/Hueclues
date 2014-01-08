<?php

include('algorithms.php');
include('item_functions.php');
include('outfit_functions.php');

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
                        <tbody>
                        <img src='http://hueclues.com/img/huecluesLogo.png' height='40' alt='hueclues' />
                        </tbody></table><table width='100%' border='0' cellspacing='0' cellpadding='20'>
                                    <tbody><tr valign='top'><td bgcolor='#51BB75' style='background-color:rgb(81,187,117);width:345px;padding:35px 20px 20px 20px;font-family:Helvetica Neue,Arial,sans-serif;color:#fff;font-size:13px;line-height:18px;'>
                                                <h1 style='font-weight:200;font-size:16px;text-align:center;margin:0px 0px 10px 0px;color:#fff;border-bottom:dotted #eee thin;padding-bottom:5px'>Message from hueclues</h1>
                                                <p style='font-weight:100;margin:10px 0px 10px 0px;text-align:center'>" . $message . "
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
    <a href = '/closet/" . $user['username'] . "' class = 'navigationText'><img class='navigationIcon' src = '/img/closet.png'></img>CLOSET</a>
    <a href = '/hive' class = 'navigationText'><img class='navigationIcon' src = '/img/hive.png'></img>HIVE</a>
<a id='outfitNavigation' class = 'navigationText' onclick='toggleOutfit()' alt='see current outfit'><img class='navigationIcon' src = '/img/hanger.png'></img>OUTFIT</a>

    <form action = '/search_processing.php' id = 'searchForm' method = 'GET' style = 'display:inline-block'>
    <div class = 'input-append' style = 'display:inline;'>
    <input id = 'searchInput' autocomplete = 'off' type = 'text' name = 'q' placeholder = ' search user or #tag' />
    <button type = 'submit' id = 'searchButton'></button>
    </div>
    </form>
<div id='condensedMenu' onclick='Redirect(\"/closet/" . $user['username'] . "\")' onmouseover='headerMenu(\"on\")' onmouseout='headerMenu(\"off\")'>
    <img class='selfPicture' src='" . $user['picture'] . "'></img>
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


function formatHashtag($tag) {
    return "<a class='hashtag' href='/tag?q=%23" . $tag . "'>#" . $tag . "</a>";
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

            if ($user['gender'] == $followingUser['gender']) {
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

?>
