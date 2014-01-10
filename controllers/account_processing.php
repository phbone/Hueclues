<?php

session_start();
include('../connection.php');
include('../global_tools.php');
include('../database_functions.php');

$userid = $_SESSION['userid'];
$name = mysql_real_escape_string($_POST['name']);
$password = mysql_real_escape_string($_POST['password']);
$email = mysql_real_escape_string($_POST['email']);
$bio = mysql_real_escape_string($_POST['bio']);
$user = database_fetch("user", "userid", $userid);

if (isset($userid)) {

    if ($name)
        database_update("user", "userid", $userid, "", "", "name", $name);
    if ($bio)
        database_update("user", "userid", $userid, "", "", "bio", $bio);
    if ($email)
        database_update("user", "userid", $userid, "", "", "email", $email);
    if ($password != $user['password']) {
        database_update("user", "userid", $userid, "", "", "password", $password);
        $_SESSION['account_notification'] = "<span id='success_message'>You have successfully updated your account!</span>";

        $to = $email;
        $subject = "hueclues Password Change";
        $message = emailTemplate("Hey there, " . $name . " it looks like you've changed your password to " . $password . ", keep it safe!");
        $header = "MIME-Version: 1.0" . "\r\n";
        $header .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
        $header .= "From: noreply@hueclues.com";
        mail($to, $subject, $message, $header);
    }
}
header("Location:/account");
?>
