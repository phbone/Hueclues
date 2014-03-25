<?php

/* Functions dealing with stinging, which is searching by color
 * 
 * 
 */

function stingColor($color6bit) {
    /*** 
    Finds and returns all clothing attached 6-bit input color by reducing the all colors in database to a 6-bit RGB space
    ***/
    // Slpit the given 6-bit into the 3 components
    $r = $color6bit[0];
    $g = $color6bit[1];
    $b = $color6bit[2];
    
    // Create the color matching condition for every components
    $redCondition = "ROUND((CONVERT(CONV(SUBSTR('code', 1, 2), 16, 10), UNSIGNED))*(8/255)) = ".$r;
    $greenCondition = "ROUND((CONVERT(CONV(SUBSTR('code', 3, 2), 16, 10), UNSIGNED))*(8/255)) = ".$g;  
    $blueCondition = "ROUND((CONVERT(CONV(SUBSTR('code', 5, 2), 16, 10), UNSIGNED))*(8/255)) = ".$b;
    // The full color matching condition
    $colorCondition = $redCondition . " AND " . $greenCondition . " AND " . $blueCondition;
    $query = "SELECT * FROM item WHERE ".$colorCondition;
    
    $result = mysql_query($query);
    
    $similarItems = array(); // initiate the return array
    // extraxt the item ids
    while($item = mysql_fetch_array($result)){
        $similarItems[] = $item['itemid'];
    }
    
    return $similarItems;
}
    
//    $h = str_split($hexcode);
//    $r = $h[0];
//    $g = $h[2];
//    $b = $h[4];
//    $stingQry = "SELECT * FROM item WHERE code LIKE '%{$r}_{$g}_{$b}_%'";
//    $stingRst = mysql_query($stingQry);
//    while ($item = mysql_fetch_array($stingRst)) {
//        $itemObject = new item_object;
//        $itemObject = returnItem($item['itemid']);
//        formatItem($userid, $itemObject);
//    }
//}
//
//function stingCount($hexcode){
//    // counts number of clothing with this color
//    $h = str_split($hexcode);
//    $r = $h[0];
//    $g = $h[2];
//    $b = $h[4];
//    $stingQry = "SELECT * FROM item WHERE code LIKE '%{$r}_{$g}_{$b}_%'";
//    $stingRst = mysql_query($stingQry);
//    $stingCount = mysql_num_rows($stingRst);
//    echo $stingCount;



?>
