<?php

session_start();
include('connection.php');
include('database_functions.php');
include('global_functions.php');
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
$facebook_count = count($facebook_urls);
for ($i = 0; $i < $facebook_count; $i++) {
    database_insert("facebookurl", "urlid", "NULL", "userid", $userid, "url", $facebook_urls[$i]);
    database_increment("user", "userid", $userid, "fbcount", $facebook_count);
}

$instagram_urls = explode(",", $instagram_urls);
$instagram_count = count($instagram_urls);
for ($i = 0; $i < $instagram_count; $i++) {
    database_insert("instagramurl", "urlid", "NULL", "userid", $userid, "url", $instagram_urls[$i]);
    database_increment("user", "userid", $userid, "igcount", $instagram_count);
}

redirectTo("/extraction/" . $tab);
?>
