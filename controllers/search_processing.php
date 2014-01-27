<?php

session_start();
include('../connection.php');
include('../global_tools.php');
include('../database_functions.php');

$query = $_GET['q'];


if ($query[0] != "#") {
    header("Location:/search?q=" . $query);
} else {
    if (preg_match_all('/#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?\b/', $query)) {
        header("Location:/sting?q=" . str_replace("#", "", $query));
    } else {
        header("Location:/tag?q=" . rawurlencode($query));
    }
}
?>
