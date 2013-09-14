<?php

session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');


$email = $_POST['recovery_email'];

$user = database_fetch("user", "email", $email);
$name = $user['name'];
$password = $user['password'];
if ($user) {
    $to = $user['email'];
    $subject = "hueclues Password Recovery";
    $message = emailTemplate("Hey there ".$name."! We heard you've lost your password, here it is --".$password."--");

    $header = "MIME-Version: 1.0" . "\r\n";
    $header .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
    $header .= "From: noreply@hueclues.com";
    $retval = mail($to, $subject, $message, $header);
    if ($retval == true) {
        $_SESSION['password_recovery_notification'] = "<span id='error_message'>We've sent you an email. If you don't receive it within a few minutes, check your email's spam and junk filters.</span>";
    } 
} else {
    $_SESSION['password_recovery_notification'] = "<span id='error_mesage'>This email address has not been registered. <a href='http://hueclues.com/'>Click here to sign up for hueclues.</a></span>";
}
header("Location:/");
?>
