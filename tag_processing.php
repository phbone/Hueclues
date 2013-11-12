<?php

session_start();
include('connection.php');
include('global_tools.php');
include('database_functions.php');


$userid = $_SESSION['userid'];
$itemid = $_POST['itemid'];
$tags = $_POST['tags'];



$tags = strtolower($tags);
$tags = mysql_real_escape_string($tags);
$tags = str_replace(" ", "", $tags);
$tags_array = explode("#", $tags);
array_shift($tags_array);
$previous_tags_array = array();
$tag_object = array();

// HANDLE PREVIOUS TAGS
$previous_tags_query = database_query("tagmap", "itemid", $itemid);
while ($previous_tags = mysql_fetch_array($previous_tags_query)){
    $tag = database_fetch("tag", "tagid", $previous_tags['tagid']);
  // currently unused
    array_push($previous_tags_array, $tags['name']);
    
    if(in_array($tag['name'], $tags_array)){
        // the current tag is already on the item, and the user has kept it 
        // do nothing
    }
    else{
        // user has entered new tags which doens't include this previous tag
        database_decrement("tag", "tagid", $tag['tagid'], "count", "1");
        database_delete("tagmap", "tagid", $tag['tagid'], "itemid", $itemid);
    }
}

// HANDLES NEW TAGS
for ($i = 0; $i < count($tags_array); $i++) {
//check if tag already exists
    $existence = database_fetch("tag", "name", $tags_array[$i]);
    $tag = database_fetch("tag", "name", $tags_array[$i]);
//check if item is already tagged
    $tagged = database_fetch("tagmap", "itemid", $itemid, "tagid", $tag['tagid']);
    if ($existence && !$tagged) {
// tag exists, but isn't associated with item
        $tagid = $existence['tagid'];
        database_insert("tagmap", "tagmapid", NULL, "itemid", $itemid, "tagid", $tagid, "time", time());
        database_increment("tag", "name", $tags_array[$i], "count", 1);
        array_push($tag_object, $tags_array[$i]);
    } else if (!$existence) {
// tag is new
        database_insert("tag", "tagid", NULL, "name", $tags_array[$i], "count", "0", "time_created", time());
        $tagid = mysql_insert_id();
        database_insert("tagmap", "tagmapid", NULL, "itemid", $itemid, "tagid", $tagid, "time", time());
        database_increment("tag", "tagid", $tagid, "count", 1);
        array_push($tag_object, $tags_array[$i]);
    } else if($existence && $tagged){
        array_push($tag_object, $tags_array[$i]); // tag already exists so include it in the object to be passed back
    }
}
echo json_encode($tag_object);
?>
