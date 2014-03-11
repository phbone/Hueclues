<?php

/* Functions dealing with stinging, which is searching by color
 * 
 * 
 */
include('../algorithmns.php');

function trendingHex() {
    // list of hexcodes to process by hue value
    // granularity no used 

    $colors = array();
    $trending = array();
    $items = array();
    $timeAgo = strtotime('-4 month', time());
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
    $trending[] = current(array_keys($colors));
    $count = 0;
    foreach ($colors as $key => $val) {
        $key = strval($key);
        $hex = $key[0] . "0" . $key[1] . "0" . $key[2] . "0";
        $text = fontColor($hex);
        // weeds out some really dark colors
        if ($key[0]>1 && $key[1]>1 && $key[2] > 1) {
            echo "<span class='colorTags' onclick=\"viewItemsTaggedWith('$hex')\" style='background-color:#$hex;'> #" . $hex . "</span><br/>";
            $trending[] = $hex;
            // count 15 tags
            if ($count > 4) {
                break;
            }
            $count++;
        }
    }
    return $trending;
}



function trendingItemsColor($trendingHex) {

    for ($i = 0; $i < count($trendingHex); $i++) {
        // select 10 tags with the most 
        echo "<div class='taggedItems " . $trendingHex[$i] . "'>";
        stingColor($trendingHex[$i]);
        echo "</div>";
    }
}

function trendingItems($trendingTags, $friend_array) {
    $existingItems = array();
    for ($i = 0; $i < count($trendingTags); $i++) {
        // select 10 tags with the most
        $tagResult = database_query("tagmap", "tagid", $trendingTags[$i]);
        while ($tagmap = mysql_fetch_array($tagResult)) {
            $item = database_fetch("item", "itemid", $tagmap['itemid']);

            // prevents an item appearing multiple times from having 2 trending tags
            // prevents any items from friends
            if (!in_array($tagmap['itemid'], $existingItems) && !in_array($item['userid'], $friend_array)) {
                $item_object = returnItem($tagmap['itemid']);
                echo $tagmap['itemid'];
                $tags = str_replace("#", " ", $item_object->tags);
                echo "<div class='taggedItems" . $tags . "'>";
                formatItem($userid, $item_object);
                echo "</div>";
                $existingItems[] = $tagmap['itemid'];
            }
        }
    }
}

function trendingTags() {

    $trendingItems = array();
    $trendingTags = array();
    $timeAgo = strtotime('-4 month', time());
    // join sql combines tagmap and item tables on itemid, select ones up to a month old
    $itemQuery = "SELECT * FROM tagmap LEFT JOIN item on item.itemid = tagmap.itemid WHERE 'tagmap.time' > '" . $timeAgo . "' ORDER BY 'tagmap.time'";
    $itemResult = mysql_query($itemQuery);
    while ($itemTagmap = mysql_fetch_array($itemResult)) {
        if (!in_array($itemTagmap['userid'], $friend_array)) {
            $trendingTags[] = $itemTagmap['tagid'];
        }
    }

    $trendingTagSort = array_count_values($trendingTags); //Counts the values in the array, returns associatve array
    arsort($trendingTagSort); //Sort it from highest to lowest
    $trendingTagDict = array_keys($trendingTagSort); //Split the array so we can find the most occuring key
    //The most occuring value is $trendingTagKey[0][1] with $trendingTagKey[0][0] occurences.";

    $arrayLength = count($trendingTagDict);
    $tagCount = $arrayLength;
    if ($arrayLength > 10) {
        $tagCount = 10;
    }
    $trendingTags = array();

    for ($i = 0; $i < $tagCount; $i++) {

        if (count($trendingTagDict) == count(array_unique($trendingTagDict))) {
            $tag = database_fetch("tag", "tagid", $trendingTagDict[$i]);
        } else {
            $tag = database_fetch("tag", "tagid", $trendingTagDict[$i][1]);
        }
        echo "<span class='tagLinks' onclick=\"viewItemsTaggedWith('" . $tag['name'] . "')\">#" . $tag['name'] . "</span><br/>";
        $trendingTags[] = $tag['tagid'];
    }
    return $trendingTags;
}

?>
