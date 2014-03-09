<?php

/*
 * Image manipulation, image editing, etc functions dealing with the pictures themselves
 * 
 */


function imageDimension($url) {
    $headers = array(
        "Range: bytes=0-32768"
    );

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    curl_close($curl);


    $im = imagecreatefromstring($data);

    $width = imagesx($im);
    $height = imagesy($im);

    return $width / $height;
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
