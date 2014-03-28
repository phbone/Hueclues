<?php
/************************************************************************************************
 * This file prints the most recent 100 items ordered by color to visually inspect color trends *
 ************************************************************************************************/

session_start();
include('../connection.php');
include('../database_functions.php');
include('../global_tools.php');
include('../global_objects.php');

$userid = $_SESSION['userid'];

$query = "SELECT * FROM item";
$result = mysql_query($query);

$colors = array();

while($item = mysql_fetch_array($result)){
    $hex = $item['code'];
    $r = strval(round(hexdec(substr($hex, 1, 2))*(3/255)));
    $g = strval(round(hexdec(substr($hex, 3, 2))*(3/255)));
    $b = strval(round(hexdec(substr($hex, 5, 2))*(3/255)));
    $color6bit = $r . $g . $b;
    
    if(!in_array($color6bit, $colors)){
        $colors[] = $color6bit;
    }
}


echo count($colors);

?>