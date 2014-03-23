<?php

session_start();
include('../connection.php');
include('../database_functions.php');
include('../global_tools.php');
include('../global_objects.php');

// Input parameters to paginate
$offset = $_GET['offset'];
$limit = 2;
$color = $_GET['color'];
$userid = $_SESSION['userid'];

$friendArray = getFollowing($userid);
$colorArray = colorSuggest($color);
$matchedItemArray = array();


// create a query that fetches 3 items to each of the matching
// look through ppl user follows
// select items that match each of the color schemes

for ($i = 0; $i < count($colorArray); $i++) {
    $h = str_split($colorArray[$i]);
    $r = $h[0];
    $g = $h[2];
    $b = $h[4];
    $stingQuery = "SELECT * FROM item WHERE userid IN (" . implode(",", array_map('intval', $friendArray)) . ") AND code LIKE '%{$r}_{$g}_{$b}_%' ORDER BY time DESC LIMIT " . $offset . ", " . $limit;
    $stingRst = mysql_query($stingQuery);
    while ($matchedItem = mysql_fetch_array($stingRst)) {
        // adds $limit(num) items matching with $colorArray[$i](hex) to the list 
        $matchedItemArray[] = returnItem($matchedItem['itemid']);
        $error = $matchedItem;
    }
}
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
$return_array = array('results' => $matchedItemArray, 'error' => $stingQuery);
echo json_encode($return_array);
?>
