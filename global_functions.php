<?php


function getGender($code) {
    // input: 0, 1, or 2
    // maps the numbers to gender 
    // 0 = m,  1 = f, 2 = u
    if ($code == "0") {
        return "m";
    } else if ($code == "1") {
        return "f";
    } else if ($code == "2") {
        return "u";
    }
}

function fontColor($hex) {
    // use appropriate text color for any item color
    list($r, $g, $b) = hex_2_rgb($hex);
    list($h, $s, $l) = rgb_2_hsl($r, $g, $b);
    if (round($l) == 1) {
        return "000000"; // use a black text color for brighter bgs
    } else if (round($l) == 0) {
        return "FFFFFF"; // uses a white text color for darker bgs
    }
}

function getImagetype($imageType) {
    // input: return value from exif_imagetype()
//// DETERMINE PROPER HEADER AND IMAGE TYPE FOR IMAGE DEPENDING ON DATABASE TYPE 
    if ($imageType == 1) {
        $ext = "gif";
    } else if ($imageType == 2) {
        $ext = "jpeg";
    } else if ($imageType == 3) {
        $ext = "png";
    }
    return $imageType;
}


function getExtension($str) {
    // get the image extension
    $i = strrpos($str, ".");
    if (!$i) {
        return "";
    }

    $l = strlen($str) - $i;
    $ext = substr($str, $i + 1, $l);
    return $ext;
}

function isPrime($num) {
    // check if a number is prime, this is only used for the private beta keys
    if ($num == 1)
        return false;
    //2 is prime (the only even number that is prime)
    if ($num == 2)
        return true;
    /**
     * if the number is divisible by two, then it's not prime and it's no longer
     * needed to check other even numbers
     */
    if ($num % 2 == 0) {
        return false;
    }
    /**
     * Checks the odd numbers. If any of them is a factor, then it returns false.
     * The sqrt can be an aproximation, hence just for the sake of
     * security, one rounds it to the next highest integer value.
     */
    for ($i = 3; $i <= ceil(sqrt($num)); $i = $i + 2) {
        if ($num % $i == 0)
            return false;
    }
    return true;
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
