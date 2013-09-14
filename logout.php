<?php

session_start();
setcookie('userid', "", time()-100);
setcookie('username', "", time()-100);
setcookie('password', "", time()-100);
$_SESSION['userid'] = "";
session_destroy();
header("Location:/");
?>
