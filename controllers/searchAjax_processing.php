<?php

session_start();
include('connection.php');
include('global_tools.php');
include('database_functions.php');


$query = $_POST['q'];
$queryWord = str_replace("#", "", $query);
$searchArray = array();

if (preg_match('/#/', $query)) {
    // hashtag search
    $searchResults = database_like_results("tag", "name", $queryWord, 15);
    while($tag = mysql_fetch_array($searchResults)) {
        $searchArray[] = $tag['name'] . "(" . $tag['count'] . ")";
    }
} else {
    // user search
    $searchResults = database_like_results("user", "username", $queryWord, 15);
    while($user = mysql_fetch_array($searchResults)) {
        $searchArray[] = $user['username'];
    }
}


echo json_encode(array('response' => $searchArray));
?>
