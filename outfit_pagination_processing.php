<?php
session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');
include('global_objects.php');

$offset = $_GET['offset'];
$database = $_GET['database'];
$limit = $_GET['limit'];
$userid = $_SESSION['userid'];
// feed in the array of userids, the query will select items by those users
// in order of time updated
$useridArray = $_GET['useridArray'];

//SELECT * FROM item WHERE item['userid'] is inside of $friend_array ORDER BY date uploaded
// this could be a database function itself
$outfit_query = "SELECT * FROM " . $database . " WHERE userid IN (" . implode(",", array_map('intval', $useridArray)) . ") ORDER BY time DESC LIMIT " . $offset . ", " . $limit;

// for select by tags, change to array of tagids

// for select by matches, change to array of itemids

$outfit_result = mysql_query($outfit_query);
while ($outfit = mysql_fetch_array($outfit_result)) {
    $update_objects[] = returnOutfit($outfit['outfitid']);
}

$return_array = array('updates' => $update_objects);
echo json_encode($return_array);
?>
