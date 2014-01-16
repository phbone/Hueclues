<?php
require_once "/home/default/support/default.php";
$dbh = showDB ();
$cities = array();
$sth = $dbh->prepare("SELECT * FROM purchase_items");
$sth->execute();

 while($row = $sth->fetch(PDO::FETCH_ASSOC)) {
$cities[]=$row;
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
