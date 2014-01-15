<?php

session_start();
include('connection.php');
include('global_tools.php');
include('database_functions.php');

$tags_string = $_GET['q'];

if ($tags_string[0] != "#") {
    header("Location:/search?q=" . $tags_string);
} else {
    $tags_string = rawurlencode($tags_string);
    header("Location:/tag?q=" . $tags_string);
}
?>
