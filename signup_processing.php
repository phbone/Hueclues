<?php

session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');

$email = $_POST['signupemail'];
$password = $_POST['signuppassword'];
$username = substr($_POST['signupusername'], 0, 15);
$status = "failed";
$notification = "";

function validate_username($str) {
    return preg_match('/^[A-Za-z0-9_]+$/', $str);
}

if ($email && $password && $username) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $notification = "<span id='error_message'>Invalid email address.</span>";
    } elseif (database_count("user", "email", $email) > 0) {
        $notification = "<span id='error_message'>This email is already registered.</span>";
    } elseif (!validate_username($username)) {
        $notification = "<span id='error_message'>Invalid username! Alphanumerics only.</span>";
    } elseif (database_count("user", "username", $username) > 0 || !$username) {
        $notification = "<span id='error_message'>This username is taken.</span>";
    } elseif (strlen($password) < 6) {
        $notification = "<span id='error_message'>Password must be 6 or more characters.</span>";
    } else {


        $email = mysql_real_escape_string($email);
        $password = $password;
        $name = $_POST['signupname'];
        $allowance = 100;
        $gender = $_POST['signupgender'];

//  INSERT ENTRY INTO DATABASE
        database_insert("user", "userid", "NULL", "username", $username, "email", $email, "password", $password, "name", $name, "onboardtime", time(), "allowance", $allowance);
        $time = time();
        $user = database_fetch("user", "email", $email);
        $_SESSION['userid'] = $user['userid'];

        database_update("user", "userid", $_SESSION['userid'], "", "", "last_login_time", $time);

        $to = $email;
        $subject = "hueclues Account Creation!";
        $message = emailTemplate("Congratulations " . $name . ", <br/> You have successfully signed up for hueclues! <br/> If you would like to see what hueclues has to offer please take a tour of the site by clicking the following link: <a style='color:#555;' href='http://hueclues.com/welcome'></a>");
        $header = "MIME-Version: 1.0" . "\r\n";
        $header .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
        $header .= "From: Hueclues <noreply@hueclues.com>" . "\r\n"
                . 'Reply-To: noreply@hueclues.com' . "\r\n";
        mail($to, $subject, $message, $header);
        $status = "success";
    }
} else {
    $notification = "<span id='error_message'>Must complete all fields.</span>";
}
$return_array = array('status' => $status, 'notification' => $notification);
echo json_encode($return_array);
?>
