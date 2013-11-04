<?php

$dbhost = 'localhost';
$dbuser = 'root';
$dbpw = 'Recursion23@#';
$db = 'hueclues_main';

$conn = mysql_connect($dbhost, $dbuser, $dbpw);
mysql_select_db($db);
session_start();
$_SESSION['userid'] = $_COOKIE['userid'];
?>
