<?php

session_start();
include('connection.php');
include('database_functions.php');
include('header.php');
include('facebook_connect.php');

$userid = $_SESSION['userid'];
$facebook_user = $_SESSION['facebook_user'];
$photo_count = $_POST['photo_count'];
$photo_count = 100;
if ($user_profile) {

    $facebook_photos = $facebook->api('/me/photos?limit=' . $photo_count);

    $htmlreturn_string = "";
    $i = 0;
//when "how many pictures do you want " shows and no pictures load, the error occurs here
// otherwise error occurs in not having the  scope (permission) to access photos
    foreach ($facebook_photos['data'] as $value) {

        $fb_top_offset = floor($i / 5) * 210 + 50;
        $fb_left_offset = ($i % 5) * 210;
        // the hidden inputs may be unnecessary
        $htmlreturn_string = $htmlreturn_string . "
                                <div class='thumbnail_frame' id='fb_frame" . $i . "' style=\"top:" . $fb_top_offset . "px;left:" . $fb_left_offset . "px; \">
                                <input type=\"hidden\" value=\"" . $value['source'] . "\" id='fb_url".$i."' name=\"photo_url\" />
                                <input type=\"image\" alt=\"   This link is broken\" src=\"" . $value['source'] . "\" class=\"thumbnaileffect\" id='fb_image" . $i . "'onclick='addFacebookImage(" . $i . ")' /> 
                                    </div>";
        $i++;
    }
} else {
    //$htmlreturn_string = "Error in accessing the User Profile, please Reload this page and try again";
}

echo json_encode(array('response' => $htmlreturn_string));
?>
