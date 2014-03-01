<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function formatNotification($notificationid) {
    // takes in a notificationid and formats the notification

    $notification = database_fetch("notification", "notificationid", $notificationid);
    $type = $notification['type'];
    $fromUserid = $notification['from_userid'];
    $fromUser = database_fetch("user", "userid", $fromUserid);
    $fromUsername = $fromUser['username'];
    $itemid = $notification['itemid'];
    $item = database_fetch("item", "itemid", $itemid);
    $itemDesc = $item['description'];
    $seen = $notification['seen'];
    

    if ($type == "0") {
        // notify user for liking an item

        $message = "<a href='/closet/$fromUsername'>$fromUsername</a> liked your item <a href='/item/$itemid'>$itemDesc</a>";
    } else if ($type == "1") {
        //notify user for following
        $message = "<a href='/closet/$fromUsername'>$fromUsername</a>is now following you";
    } else if ($type == "2") {
        //notify user for matching
        $message = "<a href='/closet/$fromUsername'>$fromUsername</a>matched your item <a href='/item/$itemid'>$itemDesc</a>";
    } else if ($type == "3") {
        //notify user item used in outfit
        $message = "<a href='/closet/$fromUsername'>$fromUsername</a>is using item $itemDesc in an outfit";
    }
    echo "<div class='notificationBox'>".$message."</div>";
}

?>
