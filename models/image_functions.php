<?php

/*
 * Image manipulation, image editing, etc functions dealing with the pictures themselves
 * 
 */

function getImageLink($itemid) {
    // given an itemid, return the image link
    $item = database_fetch("item", "itemid", $itemid);
    
    $origin = $item['image_origin'];
    $urlid = $item['urlid'];
    $imageid = $item['imageid'];
    
   
    if ($origin == "0") { // native url
        $url = database_fetch("url", "urlid", $urlid);
        $imagelink = $url['url'];
    } else if ($origin == "1") { // facebook url
        $url = database_fetch("facebookurl", "urlid", $urlid);
        $imagelink = $url['url'];
    } else if ($origin == "2") { // Instagram url
        $url = database_fetch("instagramurl", "urlid", $urlid);
        $imagelink = $url['url'];
    } else if ($origin == "3") {
        $file = database_fetch("image", "imageid", $imageid);
        $imagelink = $file['url'];
    }
    return $imagelink;
}

function autoRotateImage($image) {
    // automatically rotates images to proper orientation;
    $orientation = $image->getImageOrientation();

    switch ($orientation) {
        case imagick::ORIENTATION_BOTTOMRIGHT:
            $image->rotateimage("#000", 180); // rotate 180 degrees 
            break;

        case imagick::ORIENTATION_RIGHTTOP:
            $image->rotateimage("#000", 90); // rotate 90 degrees CW 
            break;

        case imagick::ORIENTATION_LEFTBOTTOM:
            $image->rotateimage("#000", -90); // rotate 90 degrees CCW 
            break;
    }

    // Now that it's auto-rotated, make sure the EXIF data is correct in case the EXIF gets saved with the image! 
    $image->setImageOrientation(imagick::ORIENTATION_TOPLEFT);
}

?>
