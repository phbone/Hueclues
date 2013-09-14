<?php

session_start();
include('connection.php');
include('global_tools.php');
include('database_functions.php');
include('algorithms.php');

$tags_string = $_GET['q'];

if ($tags_string[0] != "#") {
    header("Location:/closet/" . $tags_string);
} else {
    $tags_string = str_replace("#", "%23", $tags_string);
    $tags_string = str_replace(" ", "%20", $tags_string);
    header("Location:/tag?q=" . $tags_string);
}
?>
