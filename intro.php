<?php
session_start();
include('connection.php');
include('global_objects.php');
include('global_tools.php');
include('database_functions.php');
?>
<!doctype html>
<html>
    <head> 
        <?php initiateTools() ?>
        <script type='text/javascript' src='/js/welcomeAnimation.js'></script>
        <link rel="stylesheet" href="/css/welcomeAnimation.css" type="text/css"/>
        <script>
            var num = 1;
            var followCount = 5;
            var welcomePage = 0;
            var welcomeIndex = 0;
            var welcomeStep = 0;
            var welcomeHexCount = "";

            $(document).ready(function(e) {
                bindActions();
                welcomeHexCount = setupWelcome();
                var intervalId = setInterval(function() {
                    runWelcome(welcomeIndex);
                    welcomeIndex++;
                    if (welcomeIndex > welcomeHexCount) {
                        clearInterval(intervalId);
                    }
                }, 100);

                setTimeout(function() {
                    $(".welcomePage").fadeOut();
                    $("#welcomeImage1").fadeIn();
                    $("#nextButton").css("display", "block");
                }, welcomeHexCount * 100);


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
            #welcomeImage1{
                position:absolute;
                z-index:3;
                left:100px;
                top:150px;
            }
            #signupFormContainer{
                padding-top:10px;
                padding-bottom:20px;
                padding-left:50px;
                padding-right:33px;
                background: url('/img/bg.png');
                position: relative;
                -webkit-border-radius: 2px;
                -moz-border-radius: 2px;
                border-radius: 2px;
                width:290px;
                margin:auto;
                margin-top:-55px;
            }
            .indexInput{
                height:35px;
                font-family:"Quicksand";
                font-size:20px;
                margin:3px;
                padding:5px 10px;
                border-style:ridge;
                -webkit-border-radius: 0px;
                -moz-border-radius: 0px;
                border-radius: 0px;
                width: 84%;
            }

        </style>
    </head>
    <body>
        <img src="/img/loading.gif" id="loading" />
        <div id="welcomeImage1" class="welcomePage">
            <img class="welcomeImage" src="/img/orientationlandingpage.png" />
        </div>

        <div id="signupFormContainer" style="">  
            <img src="/img/betalogo.png" id="betaLogo"/>
            <div id="signupLabel">Want in? Sign up below</div>
            <form id="signupForm" action="/controllers/signup_processing.php" method="POST">
                <input type="text" name="signupusername" class="indexInput" placeholder="username" maxlength="15" value="" /><br/>
                <input type="text" name="signupemail" class="indexInput" placeholder ="email"  /><br/>
                <input type="text" name="signupname" class="indexInput" placeholder="name" maxlength="20" /><br/>
                <input type="password" name="signuppassword" class="indexInput" placeholder="password" /><br/>
                <input type="button" onclick="signupAjax();" id="signupButton" class="greenButton" style="margin-left:4px;width:266px;" value="JOIN HUECLUES" /><br/>
                <span id="signupAgreement">By signing up, you are agreeing to our' <a href="/terms" target="_blank">terms of use</a></span><br/>
            </form> 
        </div> 
    </body>
</html>