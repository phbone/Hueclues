<?php

session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');
include('global_objects.php');


$query = "SELECT * FROM item WHERE itemid > 0";
$rst = mysql_query($query);
while ($item = mysql_fetch_array($rst)) {

    /*
      // recalculates all the item size ratios
      $itemObject = returnItem($item['itemid']);
      list($width, $height) = getimagesize($itemObject->image_link);
      $sizeRatio = round($width / $height, 2);
      if ($sizeRatio == 0) {
      $sizeRatio = 1;
      }
      database_update("item", "itemid", $item['itemid'], "", "", "sizeRatio", $sizeRatio);
     */


    // adds http to all links
    if ($item['purchaselink']) { // make sure there is a link
        // if link exists and doesn't have http add it
        if (strpos($item['purchaselink'], 'http') !== false) {
            // delete any empty find links
            if($item['purchaselink']=="http://"){
                database_update("item", "itemid", $item['itemid'], "", "", "purchaselink", "");
            }
            // do nothing already has http
        } else {
            $link = substr_replace($item['purchaselink'], 'http://', 0, 0);
            database_update("item", "itemid", $item['itemid'], "", "", "purchaselink", $link);
        }
    }
}
?>
