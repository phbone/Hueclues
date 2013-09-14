<?php

session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');

$userid = $_SESSION['userid'];
$facebook_urls = $_POST['facebook_urls'];
$instagram_urls = $_POST['instagram_urls'];

if ($facebook_urls) {
    $tab = "facebook";
} else if ($instagram_urls) {
    $tab = "instagram";
}

$facebook_urls = explode(",", $facebook_urls);
$i = 0;
while ($facebook_urls[$i]) {
    database_insert("facebookurl", "urlid", "NULL", "userid", $userid, "url", $facebook_urls[$i]);
    database_increment("user", "userid", $userid, "fbcount", 1);
    $i++;
}

$instagram_urls = explode(",", $instagram_urls);
$i = 0;
while ($instagram_urls[$i]) {
    database_insert("instagramurl", "urlid", "NULL", "userid", $userid, "url", $instagram_urls[$i]);
    database_increment("user", "userid", $userid, "igcount", 1);
    $i++;
}

header("Location:/extraction/" . $tab);
?>
