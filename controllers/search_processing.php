<?php

session_start();
include('../connection.php');
include('../global_tools.php');
include('../database_functions.php');

$tags_string = $_GET['q'];

if ($tags_string[0] == "@") {
//search user
    header("Location:/search?q=" . $tags_string);
} elseif ($tags_string[0] == "*") {
// searched color
    $tags_string = rawurlencode($tags_string);
    header("Location:/tag?q=" . $tags_string);
} elseif ($tags_string[0] == "#") {
// searched hashtag
    $tags_string = rawurlencode($tags_string);
    header("Location:/tag?q=" . $tags_string);
}
?>
