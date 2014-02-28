<?php

//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
///HSL CONVERSION FORMULAS FROM http://serennu.com/colour/rgbtoHsl.php


function rgbToHsl($r, $g, $b) {
    // Input r g b values
    // Output is HSL equivalent as $h, $s and $l — these are again expressed as fractions of 1, like the input values
    $var_r = $r / 255;
    $var_g = $g / 255;
    $var_b = $b / 255;
    $var_min = min($var_r, $var_g, $var_b);
    $var_max = max($var_r, $var_g, $var_b);
    $del_max = $var_max - $var_min;
    $l = ($var_max + $var_min) / 2;
    if ($del_max == 0) {
        $h = 0;
        $s = 0;
    } else {
        if ($l < 0.5) {
            $s = $del_max / ($var_max + $var_min);
        } else {
            $s = $del_max / (2 - $var_max - $var_min);
        };
        $del_r = ((($var_max - $var_r) / 6) + ($del_max / 2)) / $del_max;
        $del_g = ((($var_max - $var_g) / 6) + ($del_max / 2)) / $del_max;
        $del_b = ((($var_max - $var_b) / 6) + ($del_max / 2)) / $del_max;

        if ($var_r == $var_max) {
            $h = $del_b - $del_g;
        } elseif ($var_g == $var_max) {
            $h = (1 / 3) + $del_r - $del_b;
        } elseif ($var_b == $var_max) {
            $h = (2 / 3) + $del_g - $del_r;
        };
        if ($h < 0) {
            $h += 1;
        };
        if ($h > 1) {
            $h -= 1;
        }
    }
    return array($h, $s, $l);
}

function hslToRgb($h2, $s, $l) {

    // Input is HSL value of complementary colour, held in $h2, $s, $l as fractions of 1
    // Output is RGB in normal 255 255 255 format, held in $r, $g, $b
    // Hue is converted using function hueToRgb, shown at the end of this code

    if ($s == 0) {
        $r = $l * 255;
        $g = $l * 255;
        $b = $l * 255;
    } else {
        if ($l < 0.5) {
            $var_2 = $l * (1 + $s);
        } else {
            $var_2 = ($l + $s) - ($s * $l);
        };

        $var_1 = 2 * $l - $var_2;
        $r = 255 * hueToRgb($var_1, $var_2, $h2 + (1 / 3));
        $g = 255 * hueToRgb($var_1, $var_2, $h2);
        $b = 255 * hueToRgb($var_1, $var_2, $h2 - (1 / 3));
    }
    return array($r, $g, $b);
}

function hueToRgb($v1, $v2, $vh) {
// Function to convert hue to RGB, called from above
    if ($vh < 0) {
        $vh += 1;
    };

    if ($vh > 1) {
        $vh -= 1;
    };

    if ((6 * $vh) < 1) {
        return ($v1 + ($v2 - $v1) * 6 * $vh);
    };

    if ((2 * $vh) < 1) {
        return ($v2);
    };

    if ((3 * $vh) < 2) {
        return ($v1 + ($v2 - $v1) * ((2 / 3 - $vh) * 6));
    };

    return ($v1);
}

function hslComplimentary($hex) {

    // convert hex to rgb
    $rgbArray = hexToRgb($hex);
    list($r, $g, $b) = $rgbArray;
    // convert rgb to hsl
    $hslArray = rgbToHsl($r, $g, $b);
    list($h, $s, $l) = $hslArray;


    // Calculate the opposite hue, $h2
    $h2 = $h + 0.5;

    if ($h2 > 1) {
        $h2 -= 1;
    };

    // convert hsl to rgb
    $final_rgb = hslToRgb($h2, $s, $l);

    // convert rgb to hex
    $final_hex = rgbToHex($final_rgb[0], $final_rgb[1], $final_rgb[2]);
    // return new hex
    return $final_hex;
}

function hslAnalogous1($hex) {

    // convert hex to rgb
    $rgbArray = hexToRgb($hex);
    list($r, $g, $b) = $rgbArray;
    // convert rgb to hsl
    $hslArray = rgbToHsl($r, $g, $b);
    list($h, $s, $l) = $hslArray;


    // Calculate the opposite hue, $h2
    $h2 = $h + 0.0833;

    if ($h2 > 1) {
        $h2 -= 1;
    };

    // convert hsl to rgb
    $final_rgb = hslToRgb($h2, $s, $l);

    // convert rgb to hex
    $final_hex = rgbToHex($final_rgb[0], $final_rgb[1], $final_rgb[2]);
    // return new hex
    return $final_hex;
}

function hslAnalogous2($hex) {

    // convert hex to rgb
    $rgbArray = hexToRgb($hex);
    list($r, $g, $b) = $rgbArray;
    // convert rgb to hsl
    $hslArray = rgbToHsl($r, $g, $b);
    list($h, $s, $l) = $hslArray;


    // Calculate the opposite hue, $h2
    $h2 = $h - 0.0833;

    if ($h2 < 1) {
        $h2 += 1;
    };

    // convert hsl to rgb
    $final_rgb = hslToRgb($h2, $s, $l);

    // convert rgb to hex
    $final_hex = rgbToHex($final_rgb[0], $final_rgb[1], $final_rgb[2]);
    // return new hex
    return $final_hex;
}

function hslTriadic1($hex) {

    // convert hex to rgb
    // convert hex to rgb
    $rgbArray = hexToRgb($hex);
    list($r, $g, $b) = $rgbArray;
    // convert rgb to hsl
    $hslArray = rgbToHsl($r, $g, $b);
    list($h, $s, $l) = $hslArray;


    // Calculate the opposite hue, $h2
    $h2 = $h - 0.33;

    if ($h2 < 1) {
        $h2 += 1;
    };

    // convert hsl to rgb
    $final_rgb = hslToRgb($h2, $s, $l);

    // convert rgb to hex
    $final_hex = rgbToHex($final_rgb[0], $final_rgb[1], $final_rgb[2]);
    // return new hex
    return $final_hex;
}

function hslTriadic2($hex) {

    // convert hex to rgb
    $rgbArray = hexToRgb($hex);
    list($r, $g, $b) = $rgbArray;
    // convert rgb to hsl
    $hslArray = rgbToHsl($r, $g, $b);
    list($h, $s, $l) = $hslArray;


    // Calculate the opposite hue, $h2
    $h2 = $h + 0.33;

    if ($h2 > 1) {
        $h2 -= 1;
    };

    // convert hsl to rgb
    $final_rgb = hslToRgb($h2, $s, $l);

    // convert rgb to hex
    $final_hex = rgbToHex($final_rgb[0], $final_rgb[1], $final_rgb[2]);
    // return new hex
    return $final_hex;
}

function hslSplit1($hex) {

    // convert hex to rgb
    $rgbArray = hexToRgb($hex);
    list($r, $g, $b) = $rgbArray;
    // convert rgb to hsl
    $hslArray = rgbToHsl($r, $g, $b);
    list($h, $s, $l) = $hslArray;


    // Calculate the opposite hue, $h2
    $h2 = $h + 0.416;

    if ($h2 > 1) {
        $h2 -= 1;
    };

    // convert hsl to rgb
    $final_rgb = hslToRgb($h2, $s, $l);

    // convert rgb to hex
    $final_hex = rgbToHex($final_rgb[0], $final_rgb[1], $final_rgb[2]);
    // return new hex
    return $final_hex;
}

function hslSplit2($hex) {

    // convert hex to rgb
    $rgbArray = hexToRgb($hex);
    list($r, $g, $b) = $rgbArray;
    // convert rgb to hsl
    $hslArray = rgbToHsl($r, $g, $b);
    list($h, $s, $l) = $hslArray;


    // Calculate the opposite hue, $h2
    $h2 = $h - 0.416;

    if ($h2 < 1) {
        $h2 += 1;
    };

    // convert hsl to rgb
    $final_rgb = hslToRgb($h2, $s, $l);

    // convert rgb to hex
    $final_hex = rgbToHex($final_rgb[0], $final_rgb[1], $final_rgb[2]);
    // return new hex
    return $final_hex;
}

function hsl_shades($hex, $shadeCount) {

    // convert hex to rgb
    $rgbArray = hexToRgb($hex);
    list($r, $g, $b) = $rgbArray;
    // convert rgb to hsl
    $hslArray = rgbToHsl($r, $g, $b);
    list($h, $s, $l) = $hslArray;

    $shades_holder = array(); // array to contain hexcodes of shades
    //  determines how much to increment each shades
    $lightIncrement = ($l - 0) / $shadeCount; // difference between $l and 0 divided by number of shades requested
    for ($i = 0; $i < $shadeCount; $i++) {
        $l2 = $i * $lightIncrement; // starts from pure black
        // convert hsl to rgb
        $final_rgb = hslToRgb($h, $s, $l2);
        // convert rgb to hex
        $final_hex = rgbToHex($final_rgb[0], $final_rgb[1], $final_rgb[2]);
        // return new hex
        $shades_holder[$i] = $final_hex;
    }
    return $shades_holder;
}

function hsl_tints($hex, $tintCount) {

    // convert hex to rgb
    $rgbArray = hexToRgb($hex);
    list($r, $g, $b) = $rgbArray;
    // convert rgb to hsl
    $hslArray = rgbToHsl($r, $g, $b);
    list($h, $s, $l) = $hslArray;

    $tints_holder = array(); // array to contain hexcodes of tints
    //  determines how much to decrement for each tint
    $light_decrement = (1 - $l) / $tintCount; // difference between $l and 0 divided by number of shades requested
    for ($i = 0; $i < $tintCount; $i++) {
        $l2 = 1 - ($i * $light_decrement); //starts from pure white
        // convert hsl to rgb
        $final_rgb = hslToRgb($h, $s, $l2);
        // convert rgb to hex
        $final_hex = rgbToHex($final_rgb[0], $final_rgb[1], $final_rgb[2]);
        // return new hex
        $tints_holder[$i] = $final_hex;
    }
    return $tints_holder;
}

function hsl_tmatch1($hex) {

    // convert hex to rgb
    $rgbArray = hexToRgb($hex);
    list($r, $g, $b) = $rgbArray;
    // convert rgb to hsl
    $hslArray = rgbToHsl($r, $g, $b);
    list($h, $s, $l) = $hslArray;

    // Calculate the opposite hue, $h2
    $h2 = $h + 0.25;

    if ($h2 > 1) {
        $h2 -= 1;
    };

    // convert hsl to rgb
    $final_rgb = hslToRgb($h2, $s, $l);

    // convert rgb to hex
    $final_hex = rgbToHex($final_rgb[0], $final_rgb[1], $final_rgb[2]);
    // return new hex
    return $final_hex;
}

function hsl_tmatch2($hex) {

    // convert hex to rgb
    $rgbArray = hexToRgb($hex);
    list($r, $g, $b) = $rgbArray;
    // convert rgb to hsl
    $hslArray = rgbToHsl($r, $g, $b);
    list($h, $s, $l) = $hslArray;

    // Calculate the opposite hue, $h2
    $h2 = $h - 0.25;

    if ($h2 < 1) {
        $h2 += 1;
    };

    // convert hsl to rgb
    $final_rgb = hslToRgb($h2, $s, $l);

    // convert rgb to hex
    $final_hex = rgbToHex($final_rgb[0], $final_rgb[1], $final_rgb[2]);
    // return new hex
    return $final_hex;
}

function hslIstint($hex, $hex2) { // $hex is main color - check if $hex2 is tint of $hex
// conversion of first hex code to hsl
    // convert hex to rgb
    // convert hex to rgb
    $rgbArray = hexToRgb($hex);
    list($r, $g, $b) = $rgbArray;
    // convert rgb to hsl
    $hslArray = rgbToHsl($r, $g, $b);
    list($h, $s, $l) = $hslArray;

// conversion of second hex code to hsl
    // convert hex to rgb
    $rgbArray2 = hexToRgb($hex2);
    list($r2, $g2, $b2) = $rgbArray2;
    // convert rgb to hsl
    $hslArray2 = rgbToHsl($r2, $g2, $b2);
    list($h2, $s2, $l2) = $hslArray2;

    if ($h == $h2 && $s == $s2 && $l < $l2) { // same colors and hex is darker than hex2
        $percent = $l2 - $l;
        return 100 * $percent;
    } else {
        return false;
    }
}

function hslIsshade($hex, $hex2) { // $hex is main color - check if $hex2 is shade of $hex
// conversion of first hex code to hsl
    // convert hex to rgb
    $rgbArray = hexToRgb($hex);
    list($r, $g, $b) = $rgbArray;
    // convert rgb to hsl
    $hslArray = rgbToHsl($r, $g, $b);
    list($h, $s, $l) = $hslArray;


// conversion of second hex code to hsl
    // convert hex to rgb
    $rgbArray2 = hexToRgb($hex2);
    list($r2, $g2, $b2) = $rgbArray2;
    // convert rgb to hsl
    $hslArray2 = rgbToHsl($r2, $g2, $b2);
    list($h2, $s2, $l2) = $hslArray2;

    if ($h == $h2 && $s == $s2 && $l > $l2) { // same colors and hex is lighter than hex2
        $percent = $l - $l2;
        return 100 * $percent;
    } else {
        return false;
    }
}

/// default value is the ideal match, changing $tolerance_percent changes percent of difference in hues that are included
function hslSame_hue($hex, $hex2, $tolerance_percent = "8.3333") { // compares the h value
    // if the two colors differ by less than 0.083 (30 deg)
    //  they are considered the same color by the 12 color wheel
// conversion of first hex code to hsl
    // convert hex to rgb
    $rgbArray = hexToRgb($hex);
    list($r, $g, $b) = $rgbArray;
    // convert rgb to hsl
    $hslArray = rgbToHsl($r, $g, $b);
    list($h, $s, $l) = $hslArray;


// conversion of second hex code to hsl
    // convert hex to rgb
    $rgbArray2 = hexToRgb($hex2);
    list($r2, $g2, $b2) = $rgbArray2;
    // convert rgb to hsl
    $hslArray2 = rgbToHsl($r2, $g2, $b2);
    list($h2, $s2, $l2) = $hslArray2;

    $tolerance = $tolerance_percent / 200; // divide by 2 since tolerance is calculated positive or negative tolerance
    if (abs($h - $h2) <= $tolerance) {
        return true;
    }
    return false;
}

function hslSame_saturation($hex, $hex2, $tolerance_percent = "12.5") { // compares the h value
    // if the two colors differ by less than 0.083 (30 deg)
    //  they are considered the same color by the 12 color wheel
// conversion of first hex code to hsl
    // convert hex to rgb
    $rgbArray = hexToRgb($hex);
    list($r, $g, $b) = $rgbArray;
    // convert rgb to hsl
    $hslArray = rgbToHsl($r, $g, $b);
    list($h, $s, $l) = $hslArray;


// conversion of second hex code to hsl
    // convert hex to rgb
    $rgbArray2 = hexToRgb($hex2);
    list($r2, $g2, $b2) = $rgbArray2;
    // convert rgb to hsl
    $hslArray2 = rgbToHsl($r2, $g2, $b2);
    list($h2, $s2, $l2) = $hslArray2;

    $tolerance = $tolerance_percent / 100;
    if (abs($s - $s2) <= $tolerance) {
        return true;
    }
    return false;
}

/// the tolerance percent tells how much difference from original tint matching colors are allowed
function hslSameLight($hex, $hex2, $tolerance_percent = "10") { // compares the h value
    // if the two colors differ by less than 0.083 (30 deg)
    //  they are considered the same color by the 12 color wheel
// conversion of first hex code to hsl
    // convert hex to rgb
    // convert hex to rgb
    $rgbArray = hexToRgb($hex);
    list($r, $g, $b) = $rgbArray;
    // convert rgb to hsl
    $hslArray = rgbToHsl($r, $g, $b);
    list($h, $s, $l) = $hslArray;


// conversion of second hex code to hsl
    // convert hex to rgb
    $rgbArray2 = hexToRgb($hex2);
    list($r2, $g2, $b2) = $rgbArray2;
    // convert rgb to hsl
    $hslArray2 = rgbToHsl($r2, $g2, $b2);
    list($h2, $s2, $l2) = $hslArray2;

    $tolerance = $tolerance_percent / 100;
    if (abs($l - $l2) <= $tolerance) {
        return true;
    }
    return false;
}

function hslSameColor($hex, $hex2, $hue_tol = "8.333", $sat_tol = "12.5", $light_tol = "10") {
    if (hslSame_hue($hex, $hex2, $hue_tol) && hslSame_saturation($hex, $hex2, $sat_tol) && hslSameLight($hex, $hex2, $light_tol)) {
        return true;
    }
    return false;
}

function hslIsComplimentary($hex, $hex2, $hue_tol, $sat_tol, $light_tol) {
// checks to see if the second color is analogous to the first
    $comp1 = hslComplimentary($hex);

    // compare these 2 new hexcodes with $hex2 to see if it is scheme match
    if (hslSameColor($comp1, $hex2, $hue_tol, $sat_tol, $light_tol)) {
        return true;
    }
    return false;
    // checks if two color are analogous of each other
}

function hslIsAnalogous($hex, $hex2, $hue_tol, $sat_tol, $light_tol) {
// check if the second color is analogous to the first
    $anal1 = hslAnalogous1($hex);
    $anal2 = hslAnalogous2($hex);

    // compare these 2 new hexcodes with $hex2 to see if it is scheme match
    if (hslSameColor($anal1, $hex2, $hue_tol, $sat_tol, $light_tol) || hslSameColor($anal2, $hex2, $hue_tol, $sat_tol, $light_tol)) {
        return true;
    }
    return false;
    // checks if two color are analogous of each other
}

function hslIsTriadic($hex, $hex2, $hue_tol, $sat_tol, $light_tol) {
// check if the second color is triadic to the first
    $triad1 = hslTriadic1($hex);
    $triad2 = hslTriadic2($hex);

    // compare these 2 new hexcodes with $hex2 to see if it is scheme match
    if (hslSameColor($triad1, $hex2, $hue_tol, $sat_tol, $light_tol) || hslSameColor($triad2, $hex2, $hue_tol, $sat_tol, $light_tol)) {
        return true;
    }
    return false;
    // checks if two color are analogous of each other
}

function hslIsSplit($hex, $hex2, $hue_tol, $sat_tol, $light_tol) {
// check if the second color is split complimentary to the first
    $split1 = hslSplit1($hex);
    $split2 = hslSplit2($hex);

    // compare these 2 new hexcodes with $hex2 to see if it is scheme match
    if (hslSameColor($split1, $hex2, $hue_tol, $sat_tol, $light_tol) || hslSameColor($split2, $hex2, $hue_tol, $sat_tol, $light_tol)) {
        return true;
    }
    return false;
    // checks if two color are analogous of each other
}

function hslIs_shade($hex, $hex2, $hue_tol, $sat_tol, $light_tol) {
    if (hslSame_hue($hex, $hex2, $hue_tol)) {
        return true;
    }
    return false;
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////



function hexToRgb($hex) {
    // Input: html color hexcode
    // Ouput: RGB color equivalent
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    return array($r, $g, $b);
}

function rgbToHex($r, $g, $b) {
    // Input: rgb color values in order
    // Output: html hexcode color
    if ($r == 0)
        $rcode = "00";
    else
        $rcode = TwoToHex($r);
    if ($g == 0)
        $gcode = "00";
    else
        $gcode = TwotoHex($g);
    if ($b == 0)
        $bcode = "00";
    else
        $bcode = TwoToHex($b);
    return $rcode . $gcode . $bcode;
}

function hslToHex($h, $s, $l) {
    list($r, $g, $b) = hslToRgb($h, $s, $l);
    return rgbToHex($r, $g, $b);
}

function hexToHsl($hex) {
    list($r, $g, $b) = hexToRgb($hex);
    return rgbToHsl($r, $g, $b);
}

function TwoToHex($num) {
// takes in a 2 digit number and returns the hex code 
    $tens_digit = floor($num / 16);
    $ones_digit = $num % 16;
    $tensCode = dechex($tens_digit);
    $onesCode = dechex($ones_digit);
    return $tensCode . $onesCode;
}

?>
