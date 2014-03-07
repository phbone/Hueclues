<?php

function formatNotification($notificationid) {
    // takes in a notificationid and formats the notification

    $notification = database_fetch("notification", "notificationid", $notificationid);
    $type = $notification['type'];
    $fromUserid = $notification['from_userid'];
    $fromUser = database_fetch("user", "userid", $fromUserid);
    $fromUsername = $fromUser['username'];
    // this itemid becomes outfitid for type 3
    $itemid = $notification['itemid'];
    $item = database_fetch("item", "itemid", $itemid);
    $itemDesc = $item['description'];
    $seen = $notification['seen'];


    if ($type == "0") {
        // notify user for liking an item
        $itemObject = returnItem($itemid);
        $message = "<a class='notificationBox' href='/item/$itemid'><img src='" . $itemObject->image_link . "' class='notificationPicture' /><br>$fromUsername liked your item</a>";
    } else if ($type == "1") {
        //notify user for following
        $message = "<a class='notificationBox' href='/closet/$fromUsername'><img src='" . $fromUser['picture'] . "' class='notificationPicture' /><br>$fromUsername is now following you</a>";
    } else if ($type == "2") {
        //notify user for matching
        $itemObject = returnItem($itemid);
        $message = "<a class='notificationBox' href='/hue/$itemid'><img src='" . $itemObject->image_link . "' class='notificationPicture' /><br>$fromUsername matched your item</a>";
    } else if ($type == "3") {
        //notify user item used in outfit
        $outfitid = $itemid;
        $itemObject = returnItem($itemid);
        $message = "<a class='notificationBox' href='/outfit/$outfitid'><img src='" . $fromUser['picture'] . "' class='notificationPicture' /><br>$fromUsername is using your item in an outfit</a>";
    }
    echo $message;
}

?>
