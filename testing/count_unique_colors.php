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
    $r = strval(round(hexdec(substr($hex, 1, 2))*(7/255)));
    $g = strval(round(hexdec(substr($hex, 3, 2))*(7/255)));
    $b = strval(round(hexdec(substr($hex, 5, 2))*(7/255)));
    $color6bit = $r . $g . $b;
    
    if(isset($colors[$color6bit])){
        $colors[$color6bit]++;
    }
    else{
        $colors[$color6bit] = 1;
    }
}

arsort($colors);

foreach($colors as $key => $value){
    echo $key. "=>". $value ."<br>";
}

echo "<br><br><br>".count($colors);

?>