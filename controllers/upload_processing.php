<?php

session_start();
include('../connection.php');
include('../database_functions.php');
include('../global_tools.php');
include('../s3_config.php');
$userid = $_SESSION['userid'];
$type = $_GET['type'];

$current_time = time();
// Make sure the user actually 
// selected and uploaded a file

$valid_formats = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");



if ($type == "image") {


    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {

        $failError = 0;
        $invalidTypeError = 0;
        $name = $_FILES['images']['name'][$key];
        $tmp = $_FILES['images']['tmp_name'][$key];
        $ext = pathinfo($name, PATHINFO_EXTENSION);

        if (in_array($ext, $valid_formats)) { // check to see if the image is a valid type
            $user = database_fetch("user", "userid", $userid);
            // Temporary file name stored on the server
            $actual_image_name = time() . rand(100, 200) . "." . $ext;
            $im = new Imagick($tmp);
            autoRotateImage($im);
            $im->scaleImage(612, 612, true);
            $imString = $im->getimageblob();
            if ($s3->putObject($imString, $bucket, $actual_image_name, S3::ACL_PUBLIC_READ)) {
                $s3Url = 'http://' . $bucket . '.s3.amazonaws.com/' . $actual_image_name;
                database_insert("image", "imageid", "NULL", "userid", $_SESSION['userid'], "url", $s3Url, "uploadtime", $current_time);
                database_increment("user", "userid", $userid, "filecount", 1);
            } else {
                $failError++;
            }
        } else { // invalid image file type
            $invalidTypeError++;
        }
    }

    if ($failError == 0 && $invalidTypeError == 0) {
        $_SESSION['save_notification'] = "<br><br><span id='error_message'>Please describe the item in the space provided.</span>";
        header("Location:/extraction/file");
        
    } else {
        $_SESSION['upload_notification'] = "<span id='error_message'>Some photos failed to upload, only .jpg, .png, .gif images are accepted</span>";
        header("Location:/upload");
    }
} elseif ($type == "url") {


    $get_method_extension = "?photo_type=url&photo_url=" . $_POST['url'];

// checks what type of url it is based on file extension
    $url = $_POST['url'];
    $url_array = explode(".", $url);
    $url_last = array_slice($url_array, -1, 1);
    $image_type = $url_last[0];

// image is not from history 
    if ($userid && in_array($image_type, $valid_formats)) { // user has logged in 
        $user = database_fetch("user", "userid", $userid);
        if ($user['urlcount'] < $user['allowance']) {
            database_insert("url", "urlid", "NULL", "userid", $userid, "url", $url);  // insert url into the databse
            database_increment("user", "userid", $userid, "urlcount", 1);
            header("Location:/extraction/url");
        } else {
            $_SESSION['upload_notification'] = "<span id='error_message'>You don't have enough url spaces, click <a href=\"/invite.php\">here</a> to find out how you can get more</span>";
            header("Location:/upload");
        }
    } elseif (!$userid) {
        // if not logged in then just pass url forward
        header("Location:/extraction/url");
    } else {
        $_SESSION['upload_notification'] = "<span id='error_message'>Unsupported url. (must be .jpg, .png, or .gif)</span>";
        header("Location:/upload");
    }
}
?>
