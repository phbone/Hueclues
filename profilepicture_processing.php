<?php

session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');
include('global_functions.php');
include('s3_config.php');

$userid = $_SESSION['userid'];
$name = $_FILES['image']['name'];
$size = $_FILES['image']['size'];
$tmp = $_FILES['image']['tmp_name'];
$ext = getExtension($name);
$valid_formats = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");


if (in_array($ext, $valid_formats)) {

//Rename image name. 
    $actual_image_name = time() . "." . $ext;

    //scale to 100 by 100
    list($scaleWidth, $scaleHeight) = smartScale($tmp, 150, 150);
    list($width, $height) = getimagesize($tmp);

    $newImg = imagecreatetruecolor($scaleWidth, $scaleHeight);
    switch ($ext) {
        case 'jpg':
            $srcImg = imagecreatefromjpeg($tmp);
            break;
        case 'jpeg':
            $srcImg = imagecreatefromjpeg($tmp);
            break;
        case 'gif':
            $srcImg = imagecreatefromgif($tmp);
            break;
        case 'png':
            $srcImg = imagecreatefrompng($tmp);
            break;
    }
    imagecopyresized($newImg, $srcImg, 0, 0, 0, 0, $scaleWidth, $scaleHeight, $width, $height);

    switch ($ext) {
        case 'jpg':
            imagejpeg($newImg, $tmp);
            break;
        case 'jpeg':
            imagejpeg($newImg, $tmp);
            break;
        case 'gif':
            imagegif($newImg, $tmp);
            break;
        case 'png':
            imagepng($newImg, $tmp);
            break;
    }

    
    if ($s3->putObjectFile($tmp, $bucket, $actual_image_name, S3::ACL_PUBLIC_READ)) {
// if new profile picture is created delete old picture
        $user = database_fetch("user", "userid", $userid);
        $imageUrlArray = explode("/", $user['picture']);
        $imageUrl = end($imageUrlArray);
        S3::deleteObject($bucket, $imageUrl);
// get the link and update the new profile picture
        $s3file = 'http://' . $bucket . '.s3.amazonaws.com/' . $actual_image_name;
        database_update("user", "userid", $userid, "", "", "picture", $s3file);
        
    }
}
header("Location:/account");
?>