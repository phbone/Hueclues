<?php

/* Functions dealing with stinging, which is searching by color
 * 
 * 
 */

function stingColor($hexcode) {
    // find all clothing attached to the input color
    // algorithmn not specific, uses only every 10's digit in hexcodes
    $h = str_split($hexcode);
    $r = $h[0];
    $g = $h[2];
    $b = $h[4];
    $stingQry = "SELECT * FROM item WHERE code LIKE '%{$r}_{$g}_{$b}_%'";
    $stingRst = mysql_query($stingQry);
    while ($item = mysql_fetch_array($stingRst)) {
        $itemObject = new item_object;
        $itemObject = returnItem($item['itemid']);
        formatItem($userid, $itemObject);
    }
}

function stingCount($hexcode){
    // counts number of clothing with this color
    $h = str_split($hexcode);
    $r = $h[0];
    $g = $h[2];
    $b = $h[4];
    $stingQry = "SELECT * FROM item WHERE code LIKE '%{$r}_{$g}_{$b}_%'";
    $stingRst = mysql_query($stingQry);
    $stingCount = mysql_num_rows($stingRst);
    echo $stingCount;
}


?>
