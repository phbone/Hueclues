<?php
session_start();
include ('connection.php');
include('database_functions.php');
include('global_tools.php');

if (isset($_SESSION['userid'])) {
    redirectTo("/hive");
}
?>
<!DOCTYPE html>
<html>
    <head>  
        <link rel="apple-touch-icon" href="/apple-touch-icon.png"/>
        <link rel="apple-touch-icon-precomposed" href="/apple-touch-icon-precomposed.png"/>

        <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0;" />
        <meta name="apple-mobile-web-app-capable" content="yes">

        <meta name="apple-mobile-web-app-status-bar-style" content="black" />
        <link rel="stylesheet" href="/css/mobile.css">

        <link rel="stylesheet" href="/css/add2home.css">
        <script type='application/javascript' src='/js/add2home.js'></script>
        <script src="http://code.jquery.com/jquery-latest.js"></script> 

        <link rel="stylesheet" href="/fancybox/source/jquery.fancybox.css" type="text/css" media="screen" />
        <script type="text/javascript" src="/fancybox/source/jquery.fancybox.pack.js"></script>
        <script type="text/javascript" src="js/global_javascript.js"></script>

        <script>

            function loginAjax() {
                $("#loading").show();
                var send_data = $("#loginForm").serialize();
                $.ajax({
                    type: "POST",
                    url: "/login_processing.php",
                    data: send_data,
                    success: function(html) {
                        loginObject = jQuery.parseJSON(html);
                        if (loginObject.notification == "success") {
                            Redirect("/hive");
                        }
                        else {
                            $("#notification").html(loginObject.notification);
                            displayNotification(loginObject.notification);
                            $("#body").show();
                        }
                        $("#loading").hide();
                    }

                });
            }
            $(document).ready(function(e) {
                if (localStorage.username && localStorage.password) {
                    $("#loading").show();
                    $.ajax({
                        type: "POST",
                        url: "/login_processing.php",
                        data: {'loginusername': localStorage.username,
                            'loginpassword': localStorage.password},
                        success: function(html) {
                            loginObject = jQuery.parseJSON(html);
                            if (loginObject.notification == "success") {
                                Redirect("/hive");
                            }
                            else {
                                $("#notification").html(loginObject.notification);
                                displayNotification(loginObject.notification);
                            }
                            $("#loading").hide();
                        }

                    });
                }
                else {
                    $("#body").fadeIn();
                }
                if (window.navigator.standalone) {
                    $("#mobileApp").fadeIn();
                } else {
                    // browser

                    $("#mobileSite").fadeIn();
                }
<?php checkNotifications(); ?>

                var addToHomeconfig = {
                    animationsIn: 'bubble',
                    animationOut: 'drop',
                    lifespan: 10000,
                    expire: 0,
                    touchIcon: true,
                    message: "Add me here <strong>%device</strong>. the action icon is '%icon'."};
            });

            function signupAjax() {
                $("#loading").show();
                var send_data = $("#signupForm").serialize();
                $.ajax({
                    type: "POST",
                    url: "/signup_processing.php",
                    data: send_data,
                    success: function(html) {
                        signupObject = jQuery.parseJSON(html);
                        if (signupObject.status == "success") {
                            Redirect('/hive');
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

            input, textarea {
                -webkit-appearance: none;
                -webkit-border-radius: 0;
                margin:0px;
            }
            @font-face {
                font-family: 'Quicksand';
                font-style: normal;
                font-weight: normal;
                src: local('Quicksand'), url("/font/Quicksand.otf") format("opentype");
            }

            @font-face {
                font-family: 'Quicksand Bold';
                font-style: normal;
                font-weight: normal;
                src: local('Quicksand Bold'), url("/font/Quicksand_bold.otf") format("opentype");
            }

            @font-face {
                font-family: 'Century Gothic';
                font-style: normal;
                font-weight: normal;
                src: local('Century Gothic'), url("/font/Century Gothic.ttf") format("opentype");
            }

            body{
                background: url('/img/wood-small.jpg');
                background-repeat: no-repeat;
                background-attachment: fixed;
                background-size: cover;
            }
            #formcontainer{
                margin-top:60px;
                padding:10px;
                background-color:#fbfbfb;
                box-shadow: 2px 2px 5px #545453;
                right:0px;
                opacity:0.7;
                border-radius:2px;
                position:relative;
            }
            .greenButton{
                padding-top:3px;
                padding-bottom:3px;
                padding-left:10px;
                padding-right:10px;
                display:inline-block;
                text-align:center;
                background-color:#51BB75;
                border-radius:3px;
                -webkit-border-radius: 3px;
                -moz-border-radius: 3px;
                border:0px;
                height:44px;
                color:white;
                font-size:15px;
                font-family:"Quicksand";
                vertical-align:middle;
            }
            .greenButton:hover{
                background-color:#808285;
                cursor: pointer;
            }
            .indexInput{
                height:35px;
                font-family:"Quicksand";
                font-size:18px;
                margin:3px;
                width:92%;
                text-indent:2px;
                padding:5px 0px;
                vertical-align:middle;
                text-indent:3px;
                border-style:ridge;
                border-radius:3px;
            }
            .indexInput:focus {
                outline: none;
            }

            #title{
                background-color:#f5f5f5;
                opacity:0.7;
                width:95%;
                margin:auto;
                color: #050504;
                text-shadow: #ffffff 0px 1px 0px;
                height:50px;
                position:absolute;
                top:0px;
                font-size: 32px;
            }
            #notification{
                position:relative;
                margin:auto;
                font-family:"Quicksand";
                font-family:35px;
                text-align:center;
            }
            #logo{
                height:35px;
                position:absolute;
                top:0px;
                left:0px;
            }
            #loading{
                width:200px;
                z-index:3;
                position:fixed;
                left:65px;
                top:150px;
                display:none;
            }
            #agreementPrompt{
                font-family:"Quicksand";
                font-color:#51BB75;
            }
            #mobileContainer{
                margin:1px;
                width:100%;
                position:relative;
            }
        </style>
    </head>
    <body id="body" style="display:none;">
        <div id="mobileApp" style="display:none;">
            <img src="/img/loading.gif" id="loading" />
            <?php initiateNotification(); ?>
            <div id='title'>
                <img id="logo" src="/img/huecluesLogo.png"></img>
            </div>

            <div id="mobileContainer">
                <div id="formcontainer">
                    <form id="loginForm" action="login_processing.php" method="POST">
                        <input type="text" autocomplete="off" name="loginusername" class="indexInput" placeholder ="username" /><br/>
                        <input type="password" name="loginpassword" class="indexInput" style="width:65%;height:35px;margin-right:0px;" placeholder="password" autocomplete="off" />
                        <input type="button" onclick="loginAjax()" class="greenButton" style="width:25%;margin-left:0px;" value="LOG IN"/>
                    </form>
                </div>


                <div id="formcontainer" style="margin-top:20px;">
                    <form id="signupForm" action="/signup_processing.php" method="POST">
                        <input type="text" name="signupusername" class="indexInput" placeholder="username"  maxlength="15" value="" /><br/>
                        <input type="text" name="signupemail" class="indexInput" placeholder ="email" value="<?php ?>" autocomplete="off" /><br/>
                        <input type="text" name="signupname" class="indexInput" placeholder="full name"  maxlength="20" autocomplete="off" /><br/>
                        <input type="password" name="signuppassword" class="indexInput" placeholder="password" autocomplete="off" /><br/>
                        <input type="button" onclick="signupAjax();" id="useragreementbutton" class="greenButton" style="margin:3px;width:92%;" value="SIGN UP FOR HUECLUES" /><br/><br/><br/>
                        <span id="agreementPrompt">By signing up, you are agreeing to our' <a href="/terms.php" target="_blank">terms of use</a></span><br/>
                    </form> 
                </div> 
            </div>

        </div>
        <div id="mobileSite" style="display:none;">
            <img src="/img/huecluesLogo.png" style="display:block;width:75%;margin:auto;position:relative"></img>
            <span style="font-size:20px;color:#51BB75;font-family:'Quicksand';padding:15px;font-weight:bold;">For the best experience, add our mobile App to your home screen below!</span>
        </div>
    </body>
</html>