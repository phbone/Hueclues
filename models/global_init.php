<?php

// contains various tools and functions that every viewable page initializes with
// functions that retrieve the data for search bar ajax
function typeaheadTags() {
    $result = mysql_query("SELECT * FROM tag where count > 1");
    $tag_array = array();
    while ($tags = mysql_fetch_array($result)) {
        $tag_array[] = "#" . $tags['name'];
    }
    return $tag_array;
}

function typeaheadUsers() {
    $result = mysql_query("SELECT * FROM user where followers > 0");
    $user_array = array();
    while ($user = mysql_fetch_array($result)) {
        $user_array[] = $user['username'];
    }
    return $user_array;
}

function initiateTypeahead() {
    /* functions that handles typeahead in search bar */
    echo "var typeahead_tags = " . json_encode(typeaheadTags()) . "; var typeahead_users = " . json_encode(typeaheadUsers()) . ";" .
    "var typeahead_src;// = typeahead_tags.concat(typeahead_users);
$(function() {
$( '#searchInput' ).keyup(function(){
searchAjax($('#searchInput').val());
});
});";
}

function initiateNotification() {
    echo " <a href='#notification' id='fancyNotification' style='display:none'></a>
    <div style = 'display:none'>
    <div id = 'notification'></div></div>";
}

function initiateTools() {
    /* prints the file dependencies on the html that every page needs */
    /* example, putting global css/js, etc */
    metaTags();
    echo "<title>hueclues</title>";
    echo "<link rel = 'shortcut icon' type href = '/faviconv2.ico'>";
    echo "<meta http-equiv = 'Content-Type' content = 'text/html; charset=utf-8'>";
    echo "<link rel='apple-touch-icon' href='http://hueclues.com/img/hc_icon_blacksolid_square.jpg'/>";
    echo "<link rel='apple-touch-icon-precomposed' href='http://hueclues.com/img/hc_icon_blacksolid_square.jpg'/>";
    echo "<link rel='image_src' href='http://hueclues.com/img/hc_icon_blacksolid_square.jpg' />";
    echo "<meta property='og:image' href='http://hueclues.com/img/hc_icon_blacksolid_square.jpg'/>";

//    echo "<link rel = 'stylesheet' href = '/css/font-awesome.css'>";
    echo "<link rel = 'stylesheet' href = '/fontawesome/css/font-awesome.min.css'>";
    echo "<script src='http://code.jquery.com/jquery-latest.js'></script>";
    echo "<link rel='stylesheet' type='text/css' href='/css/globalv5.css' />";
    echo "<script type='text/javascript' src='/js/global_javascript.js'></script>";
    echo "<link rel='stylesheet' href='http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css' />";
    echo "<script src='http://code.jquery.com/ui/1.10.3/jquery-ui.js'></script>";
}

function commonHeader() {
    if ($_SESSION['userid']) {
        $user = database_fetch("user", "userid", $_SESSION['userid']);
        echo "
    <div id='navigationbar'><h1 id = 'title'>
    <a href='/' id='logoLink'><img id = 'logo' src = '/img/newlogo.png' /></a>

    <a href = '/hive' class = 'navigationText'><img class='navigationIcon' src = '/img/hive.png'></img>HIVE</a>
    <a href = '/closet/" . $user['username'] . "' class = 'navigationText'><img class='navigationIcon' src = '/img/closet.png'></img>CLOSET</a>
<a id='outfitNavigation' class = 'navigationText' onclick='toggleOutfit()' alt='see current outfit'><img class='navigationIcon' src = '/img/hanger.png'></img>OUTFIT</a>

    <form action = '/controllers/search_processing.php' id = 'searchForm' method = 'GET' style = 'display:inline-block'>
    <div class = 'input-append' style = 'display:inline;'>
    <input id = 'searchInput' autocomplete = 'off' type = 'text' name = 'q' placeholder = 'search user,color,#tag' />
    <button type = 'submit' id = 'searchButton'></button>
    </div>
    </form>
<div id='condensedMenu' onclick='Redirect(\"/closet/" . $user['username'] . "\")' onmouseover='headerMenu(\"on\")' onmouseout='headerMenu(\"off\")'>
    <img class='selfPicture' src='" . $user['picture'] . "'></img>
    <span class='selfName' style='margin-top:5px;font-size:15px;display:inline-block'>" . $user['name'] . "</span>
    <div id='collapsedMenu'>
    <a href = '/extraction' class = 'navigationImage'><img title='Uploaded Images' style = 'height:16px;' src = '/img/cameraGreen.png'></img>  Upload</a>
    <a href = '/account' class = 'navigationImage'><img title = 'Account' style = 'height:20px' src = '/img/gear.png'></img> Account</a>
    <a href = '/faq' class = 'navigationImage'><i title = 'FAQ' class = 'fa fa-question'></i> FAQs</a>
    <a href = '/feedback' class = 'navigationImage'><i title = 'Feedback' class = 'fa fa-thumbs-up'></i> Feedback</a>
    <a href = '/logout' class = 'navigationImage'><i title = 'Logout' class = 'fa fa-power-off'></i> Logout</a>
</div>
</div>
</h1></div>
<div id='outfitBar' style='display:none'>
<div id='headerOutfitContainer'>
</div>
 </div>";
    } else {
        echo "<div id = 'navigationbar'><h1 id = 'title'>
            <a href = '/' id = 'logoLink'><img id = 'logo' src = '/img/newlogo.png' /></a>
            <div id='signUp' onclick='openSignup()'>Signup First</div>
            <div id='signUpForm'>
            <form action='/controllers/signup_processing.php'>
            <input type='text' />
            <input type='text' name='' />
            </form>
            </div>
            </h1></div>";
    }
}

function metaTags() {
// Preparing the meta tags for each page
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

function checkNotifications() {
    /*
     * this function is called on every page that has a pop up message or notificationin this function
     */
    echo "var notification = \"" .
    $_SESSION['account_notification'] .
    $_SESSION['login_notification'] .
    $_SESSION['signup_notification'] .
    $_SESSION['password_recovery_notification'] .
    $_SESSION['notification'] .
    $_SESSION['upload_notification'] . "\";";
    // clear notifications
    $_SESSION['account_notification'] = "";
    $_SESSION['login_notification'] = "";
    $_SESSION['signup_notification'] = "";
    $_SESSION['password_recovery_notification'] = "";
    $_SESSION['upload_notification'] = "";
    $_SESSION['notification'] = "";

    echo "displayNotification(notification);";
}

function is_mobile() {
// Get the user device
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
// Create an array of known mobile user agents
// This list is from the 21 October 2010 WURFL File.
// Most mobile devices send a pretty standard string that can be covered by
// one of these.  I believe I have found all the agents (as of the date above)
// that do not and have included them below.  If you use this function, you 
// should periodically check your list against the WURFL file, available at:
// http://wurfl.sourceforge.net/
    $mobile_agents = Array(
        "240x320", "acer", "acoon", "acs-", "abacho", "ahong", "airness", "alcatel",
        "amoi", "android", "anywhereyougo.com", "applewebkit/525", "applewebkit/532", "asus", "audio", "au-mic", "avantogo", "becker", "benq", "bilbo", "bird", "blackberry", "blazer", "bleu", "cdm-", "compal", "coolpad", "danger", "dbtel", "dopod", "elaine", "eric",
        "etouch", "fly ", "fly_", "fly-", "go.web", "goodaccess", "gradiente", "grundig", "haier", "hedy", "hitachi", "htc", "huawei", "hutchison", "inno", "ipad",
        "ipaq", "ipod", "jbrowser", "kddi", "kgt", "kwc", "lenovo", "lg ", "lg2", "lg3", "lg4", "lg5", "lg7", "lg8", "lg9", "lg-", "lge-", "lge9", "longcos", "maemo", "mercator",
        "meridian", "micromax", "midp", "mini", "mitsu", "mmm", "mmp", "mobi", "mot-", "moto", "nec-", "netfront", "newgen", "nexian", "nf-browser", "nintendo",
        "nitro", "nokia", "nook", "novarra", "obigo", "palm", "panasonic", "pantech", "philips", "phone", "pg-", "playstation", "pocket", "pt-", "qc-", "qtek", "rover", "sagem", "sama", "samu", "sanyo", "samsung", "sch-", "scooter", "sec-", "sendo", "sgh-", "sharp", "siemens", "sie-", "softbank", "sony", "spice", "sprint", "spv", "symbian", "tablet", "talkabout", "tcl-", "teleca", "telit",
        "tianyu", "tim-", "toshiba", "tsm", "up.browser", "utec", "utstar", "verykool", "virgin", "vk-", "voda", "voxtel", "vx", "wap", "wellco", "wig browser", "wii", "windows ce", "wireless", "xda", "xde", "zte");

// Pre-set $is_mobile to false.
    $is_mobile = false;
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
