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



?>
