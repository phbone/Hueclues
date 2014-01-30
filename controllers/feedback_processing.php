<?php

session_start();
include('../connection.php');
include('../database_functions.php');
include('../global_tools.php');

$userid = $_SESSION['userid'];
$feedbackText = $_POST['feedback'];
$user = database_fetch("user", "userid", $userid);
$ourEmail = "contact.hueclues@gmail.com";

$to = $ourEmail;
$subject = "hueclues feedback from " . $user['name'] . " (" . $user['username'] . ")";
$message = emailTemplate($feedbackText);
$header = "MIME-Version: 1.0" . "\r\n";
$header .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
$header .= "From: hueclues <noreply@hueclues.com>" . "\r\n"
        . 'Reply-To: noreply@hueclues.com' . "\r\n";
mail($to, $subject, $message, $header);

$_SESSION['notification'] = "Thanks for your feedback!";
header("Location:/feedback");
?>
