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

?>
