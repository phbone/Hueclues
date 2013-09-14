<?php

session_start();
include('connection.php');
include('global_functions.php');
include('database_functions.php');
include('global_tools.php');

$userid = $_SESSION['userid'];
$name = mysql_real_escape_string($_POST['name']);
$password = mysql_real_escape_string($_POST['password']);
$email = mysql_real_escape_string($_POST['email']);

$user = database_fetch("user", "userid", $userid);

if (isset($userid)) {
    if ($name)
        database_update("user", "userid", $userid, "", "", "name", $name);
    if ($email)
        database_update("user", "userid", $userid, "", "", "email", $email);
    if ($password != $user['password']) {
        database_update("user", "userid", $userid, "", "", "password", $password);
        $to = $email;
        $subject = "hueclues Password Change";
        $message = "<html><head></head><body>
        <table width='100%' border='0' cellspacing='0' cellpadding='0' bgcolor='#eeeeee'>
            <tr>
                <td align='center'>
                    <table width='600' border='0' cellspacing='0' cellpadding='0' style='margin-left:auto;margin-right:auto;text-align:left'>
                        <tr>
                            <td height='40'></td>
                        </tr>
                        <tr>
                            <td>
                                <table width='100%' border='0' cellspacing='0' cellpadding='20'>
                                    <tbody>
                                        <tr valign='top'>
                                            <td>
                                                <div style='width:520px;padding:20px 20px 20px 20px;text-align:center;'>
                                                    <img src='http://hueclues.com/img/primary_logo.png' height='105' alt='hueclues' />
                                                </div>
                                            </td>
                                    </tbody>
                                </table>
                                <table width='100%' border='0' cellspacing='0' cellpadding='20'>

                                    <tbody>
                                        <tr valign='top'>
                                            <td bgcolor='#ffffff' style='background-color:rgb(255,255,255);width:345px;padding:20px 20px 20px 20px;font-family:Helvetica Neue,Arial,sans-serif;color:#444;font-size:13px;line-height:18px;'>

                                                <h1 style='font-weight:700;font-size:16px;margin:0px 0px 10px 0px;color:#000;border-bottom:dotted #eee thin;padding-bottom:5px'>Message from hueclues</h1>
                                                <p style='margin:10px 0px 10px 0px'>Dear " . $name . ", You have changed your hueclues password!
                                                    <br>
                                                    <br>
                                                    Your email: " . $email . "
                                                    <br>
                                                    Your password: " . $password . "
                                                    <br>
                                                <p style='margin:10px 0px 10px 0px'>If you ever forget your password, you can retrieve it with your email <a href='http://hueclues.com/index.php?page=password_recovery'>here</a></p>
                                                <br>
                                                <br>
                                                <h1 style='font-weight:700;font-size:16px;margin:0px 0px 10px 0px;color:#000;border-bottom:dotted #eee thin;padding-bottom:5px'>About hueclues</h1> 
                                                <p style='margin:10px 0px 10px 0px'>hueclues helps you match items based on color theory. By taking advantage of our color theory algorithms, you'll be able to make a visual impact on any audience!
                                                    <br>
                                                    <br>
                                                    <br>
                                                    <a href='http://hueclues.com/terms'>terms of service</a>
                                                    <a href='http://hueclues.com/privacy'>user privacy policy<a/></p>
                                                </p>

                                            </td>
                                            <td bgcolor='#ffffff' style='background-color:rgb(255,255,255);width:180px;padding:20px 20px 20px 20px;font-family:Helvetica Neue,Arial,sans-serif;color:#444;font-size:13px;line-height:18px;border-left:1px #eee solid;'>


                                                <h1 style='font-weight:700;font-size:16px;margin:0px 0px 10px 0px;color:#000;border-bottom:dotted #eee thin;padding-bottom:5px'>hueclues, your own color consultant!</h1> 
                                                <p style='margin:10px 0px 10px 0px'>Learn how to Take Full Advantage of hueclues!</p>

                                             
                                                <table cellspacing='0' cellpadding='0' border='0'>
                                                    <tbody>
                                                        <tr>
                                                            <td style='font-weight:bold;font-size:13px;text-decoration:none;color:rgb(51,51,51);background-color:#51a351;font-family:Helvetica Neue,Arial,sans-serif;text-align:center;padding:2px 7px 4px'>
                                                                <a style='display:block;padding:6px 7px 4px;color:white;'>Learn More</a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <br>
                                                <br>
                                                <a  target='_blank' href='http://facebook.com/WorkMarketHQ' title='Check us out on Facebook'>
                                                    <img style='border:none;outline:none;text-decoration:none;' src='http://socialtype.co/assets/119931-matte-grey-square-icon-social-media-logos-facebook-logo.png' width='50' /></a>
                                                <a target='_blank' href='https://twitter.com/#!/workmarket' title='Check us out on Twitter'>
                                                    <img style='border:none;outline:none;text-decoration:none;' src='http://www.realliferunway.com/storage/twitter%20icon%20grey.png?__SQUARESPACE_CACHEVERSION=1332966802874' width='50' />
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    
                        <tr valign='top'>
                            <td>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body></html>";
        $header = "MIME-Version: 1.0" . "\r\n";
        $header .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
        $header .= "From: noreply@hueclues.com";
        $retval = mail($to, $subject, $message, $header);
    } else {
        $_SESSION['account_notification'] = "<span id='success_message'>You have successfully updated your account!</span>";
    }
}
redirectTo("/account");
?>
