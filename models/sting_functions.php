<?php

/* Functions dealing with stinging, which is searching by color
 * 
 * 
 */

function stingColor($hexcode){
 // find all clothing attached to the input color
    $h = str_split($hexcode);
    $stingRst = mysql_query("SELECT * FROM item WHERE code LIKE '".$h[0]."%".$h[2]."%".$h[4]."%'");
    while ($item = mysql_fetch_array(mysql_query)){
        formatItem(returnItem($item['itemid']));
    }
    
}



?>