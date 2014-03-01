<?php

/* Functions dealing with stinging, which is searching by color
 * 
 * 
 */



function colorPalette($itemList, $granularity = 5) {
        $granularity = max(1, abs((int) $granularity));
        $colors = array();
        $length = count($itemList);
       
        for ($i = 0; $i < $length; $i++) {
                $hexcode = $itemList[$i];
                list($h, $s, $l) = hexToHsl($hexcode);
                if (array_key_exists($h, $colors)) {
                    $colors[$thisRGB]++;
                } else {
                    $colors[$thisRGB] = 1;
            }
        }
        arsort($colors);
        return $colors;
    }

?>
