<?php

// function to print an email with the message

function emailTemplate($message) {
    return "<html><head></head><body><table width='100%' border='0' cellspacing='0' cellpadding='0' bgcolor='#eeeeee'>
            <tr><td align='center'>
                    <table width='600' border='0' cellspacing='0' cellpadding='0' style='margin-left:auto;margin-right:auto;text-align:left'>                      
                        <tr><td><table width='100%' border='0' cellspacing='0' cellpadding='20'>
                        <tbody>
                        <img src='http://hueclues.com/img/newlogo.png' height='40' alt='hueclues' style='margin-left:auto;margin-right:auto;'/>
                        </tbody></table><table width='100%' border='0' cellspacing='0' cellpadding='20'>
                                    <tbody><tr valign='top'><td bgcolor='#51BB75' style='background-color:rgb(81,187,117);width:345px;padding:35px 20px 20px 20px;font-family:Helvetica Neue,Arial,sans-serif;color:#fff;font-size:13px;line-height:18px;'>
                                                <h1 style='font-weight:200;font-size:16px;text-align:center;margin:0px 0px 10px 0px;color:#fff;border-bottom:dotted #eee thin;padding-bottom:5px'>Message from hueclues</h1>
                                                <p style='font-weight:100;margin:10px 0px 10px 0px;text-align:center'>" . $message . "
                                             <br><br><br><br><a style='text-decoration:none;color:#555' href='http://hueclues.com/terms'>terms</a>
                                                    <a style='text-decoration:none;color:#555' href='http://hueclues.com/privacy'>privacy policy<a/></p>
                                                </p></td></tr></tbody></table></td></tr>
                        <tr valign='bottom' style='height:50px'><td></td></tr></table>
                </td></tr></table></body></html>";
}
?>
