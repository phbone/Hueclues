<?php

session_start();
include('connection.php');
include('database_functions.php');
include('algorithms.php');
include('global_tools.php');
include('global_objects.php');




$userid = $_SESSION['userid'];
$photo_file_type = $_GET['photo_type'];
$photo_url_link = $_GET['photo_url'];
$photo_file_imageid = $_GET['photo_imageid'];


if ($photo_file_type == "url") {
    $image_url = $photo_url_link;
} else {
    $image = database_fetch("image", "imageid", $photo_file_imageid);
    $image_url = $image['url'];
}

if ($photo_file_type == "url") {
    $imageData = file_get_contents($image_url);
    $image_type = getExtension($image_url);
    $base64_image = "data:image/" . $image_type . ";base64," . base64_encode($imageData);
    $image_string = file_get_contents($photo_url_link);
} elseif ($photo_file_type == "file" && $userid) {
    $image_database = database_fetch("image", "imageid", $photo_file_imageid);
    $image_type = getExtension($image_database['url']);
    $base64_image = "data:image/" . $image_type . ";base64," . base64_encode(file_get_contents($image_database['url']));
    $image_string = file_get_contents($image_database['url']);
}
$image = imagecreatefromstring($image_string);
$width = imagesx($image);
$height = imagesy($image);
$original_ratio = ($width / $height);
$maxwidth = "275";
$maxheight = "375";
$width_ratio = ($width / $maxwidth);
$height_ratio = ($height / $maxheight);


if ($width_ratio > 1) {
    $width = $width / $width_ratio;
    $height = ($width / $original_ratio);
} else if ($height_ratio > 1) {
    $height = $height / $height_ratio;
    $width = $height * $original_ratio;
}

$drawing_height = $maxheight / 2 - $height / 2;
$drawing_width = $maxwidth / 2 - $width / 2;

$canvas_object = array(
    'image_type' => $photo_file_type,
    'image_url' => $image_url,
    'imageid' => $photo_file_imageid,
    'image_string' => $base64_image,
    'width' => $width,
    'height' => $height,
    'original_ratio' => $original_ratio,
    'max_width' => $maxwidth,
    'max_height' => $maxheight,
    'width_ratio' => $width_ratio,
    'height_ratio' => $height_ratio,
    'drawing_height' => $drawing_height,
    'drawing_width' => $drawing_width
);


echo json_encode($canvas_object);
?>
