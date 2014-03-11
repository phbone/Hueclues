<?php

session_start();
include('../connection.php');
include('../global_tools.php');
include('../database_functions.php');


$userid = $_SESSION['userid'];
$itemid = $_POST['itemid'];
$purchaseLink = $_POST['purchaseLink'];

//Add http:// if not already present
$prefix = "http";
if ($purchaseLink && strpos($purchaseLink, $prefix) !== 0) { //this means the prefix is not http
    // if so append it
    $purchaseLink = "http://" . $purchaseLink;
}

database_update("item", "itemid", $itemid, "userid", $userid, "purchaselink", $purchaseLink);

echo json_encode(array("purchaseLink"=>$purchaseLink));
?>
