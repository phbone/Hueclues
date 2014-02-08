<?php
session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');
?>
<!DOCTYPE html >
<html>
    <head>
        <title> hueclues </title>
        <link rel="apple-touch-icon" href="http://hueclues.com/img/hc_icon_blacksolid_square.jpg"/>
        <link rel="apple-touch-icon-precomposed" href="http://hueclues.com/img/hc_icon_blacksolid_square.jpg"/>

        <link rel="image_src" href="http://hueclues.com/img/hc_icon_blacksolid_square.jpg" />
        <meta property="og:image" href="http://hueclues.com/img/hc_icon_blacksolid_square.jpg"/>


        <link rel = 'shortcut icon' type href = '/faviconv2.ico' >
        <meta http-equiv = 'Content-Type' content = 'text/html; charset=utf-8' >
        <script src = 'http://code.jquery.com/jquery-latest.js' ></script>
        <script type='text/javascript' src='/js/global_javascript.js'></script>
        <meta name="description" content=""> 
        <meta name="keywords" content="Color Match Clothing Social" >
        <link rel="stylesheet" href="/fancybox/source/jquery.fancybox.css?" type="text/css" media="screen" />
        <script type="text/javascript" src="/fancybox/source/jquery.fancybox.pack.js?"></script>
        <script type="text/javascript">
            $(document).ready(function(e) {
                /*
            
<?php //checkNotifications() ?>*/
            });


            function signupAjax() {
                $("#loading").show();
                var send_data = $("#signupForm").serialize();
                $.ajax({
                    type: "POST",
                    url: "/controllers/signup_processing.php",
                    data: send_data,
                    success: function(html) {
                        signupObject = jQuery.parseJSON(html);
                        if (signupObject.status == "success") {
                            Redirect('/welcome');
                        }
                        else {
                            $("#notification").html(signupObject.notification);
                            displayNotification(signupObject.notification);
                        }
                        $("#loading").hide();
                    }

                });
            }
        </script>
        <style>
            .greenButton{
                height:30px;
                margin:0px;
                margin-top:5px;
                border:0px;
                color:white;
                font-size:15px;
                background-color:#51BB75;
                -webkit-border-radius: 0px;
                -moz-border-radius: 0px;
                border-radius: 0px;
                padding: 10px 16px;
                vertical-align:middle;
                font-family:"Quicksand";
            }
            .greenButton:hover{
                cursor:pointer;
                -webkit-box-shadow: inset 0 0 40px #808285;
                -moz-box-shadow: inset 0 0 40px #808285;
                box-shadow: inset 0 0 40px #808285;
            }.indexInput{
                height:15px;
                font-family:"Quicksand";
                font-size:13px;
                margin:3px;
                padding:5px 10px;
                border-style:ridge;
                -webkit-border-radius: 0px;
                -moz-border-radius: 0px;
                border-radius: 0px;
                width: 84%;

            }
            .indexInput:focus {
                outline: none;
            }
            #signupFormContainer{
                padding-top:10px;
                padding-bottom:10px;
                padding-left:30px;
                padding-right:15px;
                background: url('/img/bg.png');
                position: relative;
                -webkit-border-radius: 2px;
                -moz-border-radius: 2px;
                border-radius: 2px;
                width:290px;
                margin:auto;
                margin-top:130px;
                right:-130px;
                z-index:2;
                position:absolute;
            }
            #signupAgreement{
                font-size:10px;
                font-family:"Quicksand";
                color:#999;
                position:absolute;
                bottom:10px;
                width:581px;
            }
            #signupLabel, #loginLabel{
                padding:10px 0px;
                margin:auto;
                margin-left:-18px;
                text-align:center;
                font-size:20px;
                color:black;
            }
        </style>
    </head>
    <body>
        <div id="signupFormContainer" style="display:none;">  
            <div id="signupLabel">Sign up to Continue</div>
            <form id="signupForm" action="/controllers/signup_processing.php" method="POST">
                <input type="text" name="signupusername" class="indexInput" placeholder="username" maxlength="15" value="" /><br/>
                <input type="text" name="signupemail" class="indexInput" placeholder ="email"  /><br/>
                <input type="text" name="signupname" class="indexInput" placeholder="name" maxlength="20" /><br/>
                <input type="password" name="signuppassword" class="indexInput" placeholder="password" /><br/>
                <input type="button" onclick="signupAjax();" id="signupButton" class="greenButton" style="margin-top:5px;margin-left:4px;width:266px;" value="JOIN HUECLUES" /><br/>
                <span id="signupAgreement">By signing up, you are agreeing to our' <a href="/terms" target="_blank">terms of use</a></span><br/>
            </form> 
        </div>
    </body>
</html>