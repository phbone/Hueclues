<?php

/* Functions dealing with stinging, which is searching by color
 * 
 * 
 */

function stingColor($hexcode){
 // find all clothing attached to the input color
    $h = str_split($hexcode);
    $stingRst = mysql_query("SELECT * FROM item WHERE code LIKE '%$h[0]_$h[2]_$h[4]_%'");
    while ($item = mysql_fetch_array($stingRst)){
        $itemObject = new item_object;
        $itemObject = returnItem($item['itemid']);
        formatItem($itemObject);
    }
    
}



?>