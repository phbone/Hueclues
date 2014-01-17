<?php

session_start();
include('../connection.php');
include('../global_tools.php');
include('../database_functions.php');


$query = $_POST['q'];
$queryWord = str_replace("#", "", $query);
$searchArray = array();
$count = 0; // only return up to 15 results
if (preg_match('/#/', $query)) {
    // hashtag search
    $searchResults = database_like_results("tag", "name", $queryWord);
    while (($tag = mysql_fetch_array($searchResults)) && $count < 15) {
        $searchArray[] = "#" . $tag['name'] . "(" . $tag['count'] . ")";
        $count++;
    }
} else {
    // user search
    $searchResults = database_like_results("user", "username", $queryWord);
    while (($user = mysql_fetch_array($searchResults)) && $count < 15) {
        $searchArray[] = $user['username'];
        $count++;
    }
}


echo json_encode(array('response' => $searchArray));
?>
