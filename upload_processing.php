<?php

session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');
include('s3_config.php');
$userid = $_SESSION['userid'];
$type = $_GET['type'];

$current_time = time();
// Make sure the user actually 
// selected and uploaded a file

if ($type == "image") {

    $name = $_FILES['image']['name'];
    $size = $_FILES['image']['size'];
    $tmp = $_FILES['image']['tmp_name'];


    $ext = getExtension($name);
    if ($ext == "jpg" || $ext == "jpeg" || $ext == "jpe" || $ext == "png" || $ext == "gif") { // check to see if the image is a valid type
        $user = database_fetch("user", "userid", $userid);
        // Temporary file name stored on the server
        $actual_image_name = time() . rand(100, 200) . "." . $ext;


        list($scaleWidth, $scaleHeight) = smartScale($tmp, 612, 612);
        list($width, $height) = getimagesize($tmp);

        $imgString = file_get_contents($tmp);
        $srcImg = imagecreatefromstring($imgString);
        $newImg = imagecreatetruecolor($scaleWidth, $scaleHeight);

        imagecopyresampled($newImg, $srcImg, 0, 0, 0, 0, $scaleWidth, $scaleHeight, $width, $height);


        // gibberish is because of here
        switch ($ext) {
            case 'jpg':
                imagejpeg($newImg, $tmpImagePath);
                break;
            case 'jpeg':
                imagejpeg($newImg, $tmpImagePath);
                break;
            case 'gif':
                imagegif($newImg, $tmpImagePath);
                break;
            case 'png':
                imagepng($newImg, $tmpImagePath);
                break;
        }
        if ($s3->putObjectFile($tmpImagePath, $bucket, $actual_image_name, S3::ACL_PUBLIC_READ)) {

            $s3Url = 'http://' . $bucket . '.s3.amazonaws.com/' . $actual_image_name;
            database_insert("image", "imageid", "NULL", "userid", $_SESSION['userid'], "url", $s3Url, "uploadtime", $current_time);
            database_increment("user", "userid", $userid, "filecount", 1);

            header("Location:/extraction/file");
        } else {
            $_SESSION['upload_notification'] = "<span id='error_message'>Upload Failed</span>";
            header("Location:/upload");
        }
    } else { // invalid image file type
        $_SESSION['upload_notification'] = "<span id='error_message'>Incorrect upload file type. (must be jpg, png or gif)</span>";
        header("Location:/upload");
    }
} elseif ($type == "url") {


    $get_method_extension = "?photo_type=url&photo_url=" . $_POST['url'];

// checks what type of url it is based on file extension
    $url = $_POST['url'];
    $url_array = explode(".", $url);
    $url_last = array_slice($url_array, -1, 1);
    $image_type = $url_last[0];
    $valid_type = 0;
//

    if ($image_type == "jpg" || $image_type == "png" || $image_type == "gif" || $image_type == "jpeg") {
        $valid_type = 1;
    }

// image is not from history 
    if ($userid && $valid_type == 1) { // user has logged in 
        $user = database_fetch("user", "userid", $userid);
        if ($user['urlcount'] < $user['allowance']) {
            database_insert("url", "urlid", "NULL", "userid", $userid, "url", $url);  // insert url into the databse
            database_increment("user", "userid", $userid, "urlcount", 1);
            header("Location:/extraction/url");
        } else {
            $_SESSION['upload_notification'] = "<span id='error_message'>You don't have enough url spaces, click <a href=\"/invite.php\">here</a> to find out how you can get more</span>";
            header("Location:/upload");
        }
    } elseif (!$valid_type) {
        $_SESSION['upload_notification'] = "<span id='error_message'>Unsupported url. (must be .jpg, .png, or .gif)</span>";
        header("Location:/upload");
    } elseif (!$userid) {
        // if not logged in then just pass url forward
        header("Location:/extraction/url");
    }
}
?>