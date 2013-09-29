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

    // crop image 
    $im = new Imagick($tmp);
    $im->scaleImage(200, 200, true);
    $im->cropimage(150, 150, 25, 0);
    $imString = $im->getimageblob();

    if ($s3->putObjectFile($imString, $bucket, $actual_image_name, S3::ACL_PUBLIC_READ)) {
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