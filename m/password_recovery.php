<?php

session_start();
include('connection.php');
include('database_functions.php');
include('global_functions.php');
include('global_tools.php');


$email = $_POST['recovery_email'];

$user = database_fetch("user", "email", $email);
$name = $user['name'];
$password = $user['password'];
if ($user) {
    $to = $user['email'];
    $subject = "hueclues Password Recovery";
    $message = "<html><head></head><body></a>
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
                                                <p style='margin:10px 0px 10px 0px'>Dear " . $name . ", It seems you have forgotten your password, well here it is!
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
                                                    <a href='http://hueclues.com/terms.php'>terms of service</a>
                                                    <a href='http://hueclues.com/privacy.php'>user privacy policy<a/></p>
                                                </p>

                                            </td>
                                            <td bgcolor='#ffffff' style='background-color:rgb(255,255,255);width:180px;padding:20px 20px 20px 20px;font-family:Helvetica Neue,Arial,sans-serif;color:#444;font-size:13px;line-height:18px;border-left:1px #eee solid;'>


                                                <h1 style='font-weight:700;font-size:16px;margin:0px 0px 10px 0px;color:#000;border-bottom:dotted #eee thin;padding-bottom:5px'>hueclues, your own color consultant!</h1> 
                                                <p style='margin:10px 0px 10px 0px'>Learn how to Take Full Advantage of hueclues!</p>

                                                <ul style='padding:0px 0px 0px 20px; '>
                                                    <li>Attention Grabbing Outfits<a href=''></a></li>
                                                    <li>Dress to Stand Out<a href=''></a></li>
                                                    <li>Spacial Designs<a href=''></a></li>
                                                    <li>Logos, Emblems and Icons<a href=''></a></li>
                                                    <li>Visual Layouts<a href=''></a></li>
                                                    <li>Color Consulting<a href=''></a></li>
                                                </ul>  
                                                <table cellspacing='0' cellpadding='0' border='0'>
                                                    <tbody>
                                                        <tr>
                                                            <td style='font-weight:bold;font-size:13px;text-decoration:none;color:rgb(51,51,51);background-color:#51a351;font-family:Helvetica Neue,Arial,sans-serif;text-align:center;padding:2px 7px 4px'>
                                                                <a style='display:block;padding:6px 7px 4px'>Learn More</a>

                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                                <br>
                                                <br>


                                                <a  target='_blank' href='http://facebook.com/hueclues' title='Check us out on Facebook'>
                                                    <img style='border:none;outline:none;text-decoration:none;' src='http://socialtype.co/assets/119931-matte-grey-square-icon-social-media-logos-facebook-logo.png' width='50' /></a>
                                                <a target='_blank' href='https://twitter.com/hueclues' title='Check us out on Twitter'>
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
    if ($retval == true) {
        $_SESSION['password_recovery_notification'] = "<span id='error_message'>We've sent you an email. If you don't receive it within a few minutes, check your email's spam and junk filters.</span>";
    }
} else {
    $_SESSION['password_recovery_notification'] = "<span id='error_mesage'>This email address has not been registered. <a href='http://hueclues.com/'>Click here to sign up for hueclues.</a></span>";
}
redirectTo("/recover");
?>
