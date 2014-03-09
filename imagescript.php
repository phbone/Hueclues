<?php
session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');
 


$query = "SELECT * FROM item WHERE 1";
$rst = mysql_query($query);
while($item = mysql_fetch_array($rst)){
    $itemObject = returnItem($item['itemid']);
    
    echo $item['itemid'];
    list($width, $height) = getimagesize($itemObject->image_link);
    $sizeRatio = $width/$height;
    //database_update("item", "itemid", $item['itemid'], "","", "sizeRatio", $sizeRatio);
}
?>
