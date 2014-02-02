<?php
session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');

if ($_SESSION['userid']) {
    header("Location:/hive");
} else if (!$_SESSION['userid'] && isset($_COOKIE['userid'])) {
    $user = database_fetch("user", "username", $_COOKIE['username'], "password", $_COOKIE['password']);
    if ($_COOKIE['userid'] == $user['userid']) { // make sure the username/pass matches the userid cookie
        $_SESSION['userid'] = $_COOKIE['userid'];
        header("Location:/hive");
    }
}

/* if (is_mobile()) {
  // switch this to hueclues.com
  header("Location:http://m.hueclues.com");
  } */
if (!$_GET['page']) {
    $page_jump = "user_login";
} else {
    $page_jump = $_GET['page'];
}

function oneLiner() {
    echo "where style and color come to play";
}
?>
<!DOCTYPE html >
<html>
    <head>
        <title> hueclues </title>
        <link rel = 'shortcut icon' type href = '/faviconv2.ico' >
        <meta http-equiv = 'Content-Type' content = 'text/html; charset=utf-8' >
        <script src = 'http://code.jquery.com/jquery-latest.js' ></script>
        <script type='text/javascript' src='/js/global_javascript.js'></script>
        <script type='text/javascript' src='/js/welcomeAnimation.js'></script>
        <link rel="stylesheet" href="/css/welcomeAnimation.css" type="text/css"/>
        <meta name="description" content="<?php oneLiner(); ?>"> 
        <meta name="keywords" content="Color Match Clothing Social" >
        <link rel="stylesheet" href="/fancybox/source/jquery.fancybox.css?" type="text/css" media="screen" />
        <script type="text/javascript" src="/fancybox/source/jquery.fancybox.pack.js?"></script>
        <script type="text/javascript">

            ////////////////////////////////////////GETS BROWSER TYPE//////////////////////////////////////////
            var isOpera = !!(window.opera && window.opera.version); // Opera 8.0+
            var isFirefox = testCSS('MozBoxSizing'); // FF 0.8+
            var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
            // At least Safari 3+: "[object HTMLElementConstructor]"
            var isChrome = !isSafari && testCSS('WebkitTransform'); // Chrome 1+
            var isIE = /*@cc_on!@*/false || testCSS('msTransform'); // At least IE6

            function testCSS(prop) {
                return prop in document.documentElement.style;
            }
            ////////////////////////////////////////GETS BROWSER TYPE//////////////////////////////////////////


            var num = 1;
            var followCount = 5;
            var welcomePage = 0;
            var welcomeIndex = 0;
            var welcomeStep = 0;
            var welcomeHexCount = "";


            $(document).ready(function(e) {
<?php checkNotifications() ?>

                flipTab('<?php echo $page_jump ?>');
                $('<img/>').attr('src', '/img/wood.jpg').load(function() {
                    $('body').fadeIn();
                });

                $(".indexInput").keyup(function(event) {
                    if (event.keyCode == 13) {
                        var formId = $(this).closest("form").attr("id");
                        if (formId == "loginForm") {
                            $("#loginButton").click();
                        }
                        else if (formId == "signupForm") {
                            $("#signupButton").click();
                        }
                        else if (formId == "passwordForm") {
                            $("#passwordButton").click();
                        }
                    }
                });

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
                    $("#signupFormContainer").fadeIn();
                    $("#loginFormContainer").fadeIn();
                }, welcomeHexCount * 100);
            });

            function flipTab(id) {
                if (isIE) {
                    $("#unsupported").show();
                } else {
                    $("#supported").show();
                }
                if (id == "password_recovery") {
                    $("#welcomeImage1").fadeOut();
                }
                $('#' + id + 'tab').addClass('active');
                $('.flippages').hide();
                $('#' + id + '_page').fadeIn();
            }

            function loginAjax() {
                $("#loading").show();
                var send_data = $("#loginForm").serialize();
                $.ajax({
                    type: "POST",
                    url: "/controllers/login_processing.php",
                    data: send_data,
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

            html{
                height:100%;
                width:98%;
                padding:0px;
                margin:0px;
                color: #51bb75;
                font-family:"Quicksand";
            }
            body{
                background: url('/img/wood.jpg');
                background-repeat: no-repeat;
                background-attachment: fixed;
                background-size: cover;
            }
            .flippages{
                display:none;
                width:850px;
                top:90px;
                margin:auto;
                position: relative;
            }
            #welcomeImage1{
                position:absolute;
                z-index:3;
                left:50px;
                top:150px;
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
                right:-150px;
                z-index:2;
                position:absolute;
            }
            .indexInput{
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
            }
            #logoContainer{
                text-align:center;
                position:relative;
                margin:auto;
                z-index:2;

            }
            #loginFormContainer, #passwordFormContainer{
                padding-top:15px;
                padding-left:30px;
                padding-bottom:10px;
                padding-right:15px;
                background:url('/img/bg.png');
                position: relative;
                -webkit-border-radius: 2px;
                -moz-border-radius: 2px;
                border-radius: 2px;
                width:290px;
                margin:auto;
                z-index:2;
            }
            #loginFormContainer{
                top:-25px;
                right:-150px;
                position:absolute;
                display:none;
            }

            .active{ 
                border-color: #BBBBBB;
                -webkit-border-radius: 3px;
                -moz-border-radius: 3px;
                border-radius: 3px;
                color:white;
                text-shadow: 1px 2px 2px #BBBBBB;
            }

            #signupAgreement{
                font-size:10px;
                font-family:"Quicksand";
                color:#999;
                position:absolute;
                bottom:10px;
                width:581px;
            }
            a#passwordLink{
                cursor:pointer;
                margin-left:2px;
                font-size:12px;
            }
            a#passwordLink:hover{
                text-decoration: underline;
            }
            #notification{
                position:relative;
                margin:auto;
                font-family:"Quicksand";
                text-align:center;
                top:70px;
                font-size:35px;
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
                src: local('Century Gothic'), url("/font/Century_Gothic.ttf");
            }

            #unsupported{
                top:200px;
                position:relative;
                text-align:center;
                font-size:25px;
            }
            #welcomeDescription{
                font-family:"Quicksand";
                font-size:15px;
                color:black;
                width:710px;
                text-align:center;
                margin:auto;
            }
            input{
                vertical-align:middle;
            }
            #loading{
                width:307px;
                z-index:3;
                position:fixed;
                left:550px;
                top:232px;
                display:none;
            }
            #betaForm{
                width:450px;
                margin:auto;
            }
            #betaLogo{
                top:-100px;
                position:absolute;;
                height:36px;
                left:-50px;
                width:41px;
                display:block;
                z-index:2;
            }
            #passwordButton{
                position:relative;
                margin:auto;
                display:block;
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
    <body id="body" style="display:none">
        <?php initiateNotification(); ?>


        <img src="/img/loading.gif" id="loading" />
        <div id="welcomeImage1" class="welcomePage" style="display:none;">
            <img class="welcomeImage" src="/img/orientationlandingpage.png" style="height:400px;"/>
        </div>


        <div id="logoContainer">
            <img src="/img/newlogobeta.png" />
            <div id="welcomeDescription">
                <?php oneLiner(); ?>
            </div>
        </div>




        <div id="unsupported" style="display:none">
            hueclues does not support Internet Explorer, please open hueclues in 
            google chrome or firefox to continue.
        </div>
        <div id="supported" style="display:none">
            <div id="user_login_page" class="flippages">
                
                <div id="loginFormContainer" style="">
                    <div id="loginLabel">Already a user? Login</div>
                    <form id="loginForm" action="/controllers/login_processing.php" method="POST">
                        <input type="text" name="loginusername" class="indexInput" placeholder ="username" /><br/>
                        <input type="password" name="loginpassword" class="indexInput" style="width:142px;" placeholder="password" />
                        <input type="button" id="loginButton" onclick="loginAjax()" class="greenButton" style="padding:10px 20px;" value="LOG IN"/>
                    </form>                    
                    <a id="passwordLink" onclick="flipTab('password_recovery')">Lost Password?</a>
                </div>
                
                <div id="signupFormContainer" style="display:none;">  
                    <div id="signupLabel">Want in? Sign up below</div>
                    <form id="signupForm" action="/controllers/signup_processing.php" method="POST">
                        <input type="text" name="signupusername" class="indexInput" placeholder="username" maxlength="15" value="" /><br/>
                        <input type="text" name="signupemail" class="indexInput" placeholder ="email"  /><br/>
                        <input type="text" name="signupname" class="indexInput" placeholder="name" maxlength="20" /><br/>
                        <input type="password" name="signuppassword" class="indexInput" placeholder="password" /><br/>
                        <input type="button" onclick="signupAjax();" id="signupButton" class="greenButton" style="margin-top:5px;margin-left:4px;width:266px;" value="JOIN HUECLUES" /><br/>
                        <span id="signupAgreement">By signing up our' <a href="/terms" target="_blank">terms of use</a></span><br/>
                    </form> 
                </div> 

            </div>    
            <div id="password_recovery_page" class="flippages">
                <div id="passwordFormContainer">
                    <form id="passwordForm" method="POST" action="/controllers/recoverPassword_processing.php">
                        <input type="text" class="indexInput" name="recovery_email" placeholder="Enter your Email" /><br/>
                        <input type="submit" class="greenButton" id="passwordButton" value="Recover" style=""/>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>

