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

    $timeAgo = strtotime('-6 month', time());
    $itemQuery = "SELECT * FROM item WHERE 'time' > '" . $timeAgo . "' ORDER BY 'time'";
    $itemResult = mysql_query($itemQuery);
    $string = "TESTICLES";
    echo $string[2];
    while ($item = mysql_fetch_array($itemResult)) {

        $hexcode = $item['code'];
        $h = $h * 100;
        echo round($h) . "<br/>";
        if (array_key_exists($h, $colors)) {
            $colors[$h]++;
        } else {
            $colors[$h] = 1;
        }
    }


    arsort($colors);
    print_r($colors);
    return $colors;
}

?>
