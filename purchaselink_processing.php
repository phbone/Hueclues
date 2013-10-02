<?php

session_start();
include('connection.php');
include('global_tools.php');
include('database_functions.php');
include('algorithms.php');


$userid = $_SESSION['userid'];
$itemid = $_POST['itemid'];
$purchaseLink = $_POST['purchaseLink'];


database_update("item", "itemid", $itemid, "", "", "purchaselink", $purchaseLink);


echo json_encode(array("purchaseLink"=>$purchaseLink));
?>
