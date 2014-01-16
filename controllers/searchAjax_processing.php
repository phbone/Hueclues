<?php

session_start();
include('connection.php');
include('global_tools.php');
include('database_functions.php');


$query = $_POST['q'];

// hashtag search
if(preg_match('/#/',$query)){
    
}else{
    // user search
    
}



$term = trim(strip_tags($_GET['term']));

$matches = array();
foreach($cities as $city){
if((stripos($city['SKU'], $term) !== false) || (stripos($city['FAMILY'], $term) !== false) || (stripos($city['DESCRIPTION'], $term) !== false)){
    // Add the necessary "value" and "label" fields and append to result set
    $city['value1'] = $city['SKU'];
    $city['value2'] = $city['FAMILY'];
    $city['value3'] = $city['DESCRIPTION'];
    $city['label'] = "{$city['FAMILY']} - {$city['DESCRIPTION']} ({$city['SKU']})";
    $matches[] = $city;
}
}
$matches = array_slice($matches, 0, 100);
print json_encode($matches);
?>
