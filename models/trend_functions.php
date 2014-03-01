<?php

/* Functions dealing with stinging, which is searching by color
 * 
 * 
 */
include('../algorithmns.php');

function hueCount() {
    // list of hexcodes to process by hue value
    // granularity no used 

    $colors = array();
    $trending = array();
    $items = array();
    $timeAgo = strtotime('-6 month', time());
    $itemQuery = "SELECT * FROM item WHERE 'time' > '" . $timeAgo . "' ORDER BY 'time'";
    $itemResult = mysql_query($itemQuery);
    while ($item = mysql_fetch_array($itemResult)) {

        $hex = $item['code'];
        $key = $hex[0] . $hex[2] . $hex[4];

        if (array_key_exists($key, $colors)) {
            $colors[$key]++;
        } else {
            $colors[$key] = 1;
        }
    }


    arsort($colors);
    for ($i = 0; $i < 15; $i++) {
        $max = max($colors); // $max == 7
        print_r(array_keys($colors, $max));
        
    }

    return $colors;
}

?>
