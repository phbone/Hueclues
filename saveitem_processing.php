<?php

session_start();
include('connection.php');
include('database_functions.php');

$photo_file_type = $_POST['photo_type'];
$photo_url = $_POST['photo_url'];
$photo_file_imageid = $_POST['photo_imageid'];
$url_origin = $_POST['url_origin'];

$code = $_POST['code'];
$desc = mysql_real_escape_string($_POST['description']);
$tags = mysql_real_escape_string($_POST['tags']);
$userid = $_SESSION['userid'];

$tags = str_replace(" ", "", $tags);
$tags_array = explode("#", $tags);
array_shift($tags_array);


$time = time();




if ($code != "" && $desc != "") { // all fields filled in 
    if (strlen($code) != 6) { // validate color code
        $_SESSION['save_notification'] = "<span id='error_message'>This is not a valid color code</span><br/><br/>";
    }

    $user = database_fetch("user", "userid", $userid);
    // depending on if the image was a saved file or a url link, save queries are different
    if ($photo_file_type == "url" && $url_origin == "0") { // native url
        $url = database_fetch("url", "url", $photo_url);
        $photo_urlid = $url['urlid'];
        database_insert("item", "itemid", "NULL", "userid", $userid, "urlid", $photo_urlid, "image_origin", "0", "code", $code, "description", $desc, "time", $time);
        $itemid = mysql_insert_id();
    } else if ($photo_file_type == "url" && $url_origin == "1") { // facebook url
        $facebookurl = database_fetch("facebookurl", "url", $photo_url);
        $photo_urlid = $facebookurl['urlid'];
        database_insert("item", "itemid", "NULL", "userid", $userid, "urlid", $photo_urlid, "image_origin", "1", "code", $code, "description", $desc, "time", $time);
        $itemid = mysql_insert_id();
    } else if ($photo_file_type == "url" && $url_origin == "2") { // facebook url
        $instagramurl = database_fetch("instagramurl", "url", $photo_url);
        $photo_urlid = $instagramurl['urlid'];
        database_insert("item", "itemid", "NULL", "userid", $userid, "urlid", $photo_urlid, "image_origin", "2", "code", $code, "description", $desc, "time", $time);
        $itemid = mysql_insert_id();
    } else if ($photo_file_type == "file") {
        database_insert("item", "itemid", "NULL", "userid", $userid, "imageid", $photo_file_imageid, "image_origin", "3", "code", $code, "description", $desc, "time", $time);
        $itemid = mysql_insert_id();
    }

    database_increment("user", "userid", $userid, "itemcount", 1); // increases the number of swatches saved
    $_SESSION['save_notification'] = "<span id='success_message'><br>Item added to closet! <br/><a class='notificationLinks' href='/closet'>See It In Closet</a><br><a class='notificationLinks' href='' onclick='addMore()'>Add More</a></span>";
} else if ($code == "") {
    $_SESSION['save_notification'] = "<br><br><span id='error_message'>Please select an item color by clicking the uploaded image.</span>";
} else if ($desc == "") {
    $_SESSION['save_notification'] = "<br><br><span id='error_message'>Please describe the item in the space provided.</span>";
}

for ($i = 0; $i < count($tags_array); $i++) {
    //check if tag already exists
    $existence = database_fetch("tag", "name", $tags_array[$i]);

    if ($existence) {
        $tagid = $existence['tagid'];
    } else {
        // tag is new
        database_insert("tag", "tagid", NULL, "name", $tags_array[$i], "count", "0");
        $tagid = mysql_insert_id();
    }
    database_insert("tagmap", "tagmapid", NULL, "itemid", $itemid, "tagid", $tagid);
    database_increment("tag", "tagid", $tagid, "count", 1);
}

$return_array = array('status' => $_SESSION['save_notification']);
echo json_encode($return_array);
?>
