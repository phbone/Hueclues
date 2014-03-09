<?php
session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');
include('global_objects.php');


$query = "SELECT * FROM item WHERE itemid > 0";
$rst = mysql_query($query);
while($item = mysql_fetch_array($rst)){
    $itemObject = returnItem($item['itemid']);
    
    list($width, $height) = getimagesize($itemObject->image_link);
    $sizeRatio = $width/$height;
    echo $sizeRatio . "<br/>";
    database_update("item", "itemid", $item['itemid'], "","", "sizeRatio", $sizeRatio);
}
?>
