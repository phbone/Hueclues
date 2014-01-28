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
?>
<!DOCTYPE html>
<html>
    <head>
        <title>hueclues</title>
        <link rel = 'icon' type = 'image/png' href = '/img/favicon.ico'>
        <link rel = 'shortcut icon' type href = '/favicon.ico'>
        <meta http-equiv = 'Content-Type' content = 'text/html; charset=utf-8'>
        <link rel = 'stylesheet' href = '/css/font-awesome.css'>
        <script src='http://code.jquery.com/jquery-latest.js'></script>
        <script type='text/javascript' src='/js/global_javascript.js'></script>
        <meta name="description" content="hueclues lets you easily promote, manage, and select clothing"> 
        <meta name="keywords" content="Color Theory Clothing Matching Closet" >
        <link rel="stylesheet" href="/fancybox/source/jquery.fancybox.css?" type="text/css" media="screen" />
        <script type="text/javascript" src="/fancybox/source/jquery.fancybox.pack.js?"></script>
        <script type="text/javascript">

            ////////////////////////////////////////GETS BROWSER TYPE//////////////////////////////////////////
            var isOpera = !!(window.opera && window.opera.version);  // Opera 8.0+
            var isFirefox = testCSS('MozBoxSizing');                 // FF 0.8+
            var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
            // At least Safari 3+: "[object HTMLElementConstructor]"
            var isChrome = !isSafari && testCSS('WebkitTransform');  // Chrome 1+
            var isIE = /*@cc_on!@*/false || testCSS('msTransform');  // At least IE6

            function testCSS(prop) {
                return prop in document.documentElement.style;
            }
            ////////////////////////////////////////GETS BROWSER TYPE//////////////////////////////////////////


            $(document).ready(function(e) {
<?php checkNotifications() ?>

                flipTab('<?php echo $page_jump ?>');
                $('<img/>').attr('src', '/img/wood.jpg').load(function() {
                    $('body').fadeIn();
                });

                $(".indexInput").keyup(function(event) {
                    if (event.keyCode == 13) {
                        $("#loginButton").click();
                    }
                });

            });

            function flipTab(id) {
                if (isIE) {
                    $("#unsupported").show();
                } else {
                    $("#supported").show();
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

            function betaSignup() {
                $("#loading").show();
                var send_data = $("#betaForm").serialize();
                $.ajax({
                    type: "POST",
                    url: "/beta_processing.php",
                    data: send_data,
                    success: function(html) {
                        betaObject = jQuery.parseJSON(html);
                        $("#notification").html(betaObject.notification);
                        displayNotification(betaObject.notification);
                        $("#loading").hide();
                        $("#betaButton").attr("disabled", true);
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
                color: #004600;
                font-family:"Quicksand";
            }
            body{
                background: url('/img/wood.jpg');
                background-repeat: no-repeat;
                background-attachment: fixed;
                background-size: cover;
            }
            ::-webkit-input-placeholder { /* WebKit browsers */
                color:black;
            }
            :-moz-placeholder { /* Mozilla Firefox 4 to 18 */
                color:black;
            }
            ::-moz-placeholder { /* Mozilla Firefox 19+ */
                color:black;
            }
            :-ms-input-placeholder { /* Internet Explorer 10+ */
                color:black;
            }

            .flippages{
                display:none;
                width:850px;
                top:90px;
                margin:auto;
                position: relative;
            }

            .indexInput{
                height:35px;
                font-family:"Quicksand";
                font-size:20px;
                margin:3px;
                padding:5px 10px;
                border-style:ridge;
                -webkit-border-radius: 3px;
                -moz-border-radius: 3px;
                border-radius: 3px;
            }
            .indexInput:focus {
                outline: none;
            }
            .greenButton{
                height:44px;
                margin:0px;
                border:0px;
                color:white;
                font-size:19px;
                background-color:#51BB75;
                -webkit-border-radius: 0px;
                -moz-border-radius: 0px;
                border-radius: 0px;
                padding: 13px 16px;
                vertical-align:middle;
                font-family:"Quicksand";
            }
            .greenButton:hover{
                cursor:pointer;
                -webkit-box-shadow: inset 0 0 40px #808285;
                -moz-box-shadow: inset 0 0 40px #808285;
                box-shadow: inset 0 0 40px #808285;
            }
            #title{
                background-color:#ffffff;
                padding:0px;
                margin:0px;
                color: #fbfbfb;
                text-shadow: #ffffff 0px 1px 0px;
                height:50px;
                opacity:0.6;
                width:100%;
                position:fixed;
                left:0px;
                top:0px;
            }
            #title:hover{
                color: #333;
                cursor:pointer;
            }
            #formcontainer1{
                padding-top:35px;
                padding-bottom:20px;
                padding-left:50px;
                padding-right:33px;
                opacity:1;
                background:url('/img/bg.png');
                position: absolute;
                right:-226px;
                -webkit-border-radius: 2px;
                -moz-border-radius: 2px;
                border-radius: 2px;
                width:290px;
            }
            #formcontainer2{
                padding-top:5px;
                padding-bottom:5px;
                padding-left:50px;
                padding-right:33px;
                opacity:1;
                background:url('/img/bg.png');
                position: absolute;
                right:0px;
                font-size:19px;
                -webkit-border-radius: 2px;
                -moz-border-radius: 2px;
                border-radius: 2px;
                width:290px;
            }
            #formcontainer3{
                padding-top:10px;
                padding-bottom:20px;
                padding-left:50px;
                padding-right:33px;
                background-color:white;
                opacity:0.7;
                position: absolute;
                right:-226px;
                -webkit-border-radius: 2px;
                -moz-border-radius: 2px;
                border-radius: 2px;
                width:290px;
            }
            .active{ 
                border-color: #BBBBBB;
                -webkit-border-radius: 3px;
                -moz-border-radius: 3px;
                border-radius: 3px;
                color:white;
                text-shadow: 1px 2px 2px #BBBBBB;
            }
            #info{
                margin:auto;
                width: 188px;
                bottom: 0px;
                position: relative;
                color: #888888;
                opacity:0.8;
            }
            #infolink{
                color:#6BB159;
                font-size:10px;
                text-decoration: none;
                margin-left:5px;
            }
            #infolink:hover{
                color: #004600;
                text-decoration: underline;
                cursor: pointer;
            }

            #agreement_prompt{
                font-size:10px;
                font-family:"Quicksand";
                color:#999;
                position:absolute;
                bottom:10px;
                width:581px;
            }
            #logo{
                z-index:2;
                display:block;
                margin:auto;
                top:100px;
                height:100px;
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
            }
            #middle_logo{
                top:50px;
                width:350px;
                position:relative;
                display:block;
                margin:0 auto;
            }
            #welcomeMessage{
                font-family:"Century Gothic";
                font-size:30px;
                color:#58595B;
                display:block;
                width:365px;
                text-align:center;
                margin:auto;


            }
            #welcomeDescription{
                font-family:"Quicksand";
                font-size:18px;
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
            #betaPrompt{
                font-size:25px;
                padding:35px 50px;
                background:url('/img/bg.png');
                text-align:center;
                color:#51bb75;
                width:450px;
                display:block;
                margin:auto;
                top:75px;
                position:relative;
            }
            #betaForm{
                width:450px;
                margin:auto;
            }
            .navigationText{
                width:65px;
                position:absolute;
                right:-225px;
                top:-100px;
                font-family:"Century Gothic";
                font-size:20px;
                height:22px;
                padding:15px;
                background-color:transparent;
                text-decoration: none;
                color:#58595B;
                text-align: center;
            }
            .navigationText:hover{
                background-color:white;
                text-decoration:none;
                color:#51BB75;
                cursor:pointer;
            }
            
        </style>
    </head>
    <body id="body" style="display:none">
        <img src="/img/loading.gif" id="loading" />
        <?php initiateNotification(); ?>

        <div id="unsupported" style="display:none">
            <center>
                <img id="middle_logo" src="/img/newlogo.png" ></img>
                <br/>
                
                <span class="alert alert-success">hueclues is currently unavailable on Internet Explorer, please open hueclues in 
                    google chrome or firefox to continue.</span>
            </center>
        </div>
        <div id="supported" style="display:none">
            <h1 id="title"></h1> 

           
            <div id="user_login_page" class="flippages"> 
                <div class="navigationText" onclick="$('#formcontainer1').toggle();">Login</div>
                <img src="/img/newlogo.png" id="logo"/><br/><br/>
                <span id="welcomeMessage">WELCOME TO HUECLUES!</span><br/><br/>
                <div id="welcomeDescription">
                    hueclues uses pictures of your clothing to shop, match and manage your style.
                </div>
                <div id="formcontainer1" style="top:-48px;display:none;">
                    <form id="loginForm" action="/controllers/login_processing.php" method="POST">
                        <input type="text" name="loginusername" class="indexInput" placeholder ="username" /><br/>
                        <input type="password" name="loginpassword" class="indexInput" style="width:142px;" placeholder="password" />
                        <input type="button" id="loginButton" onclick="loginAjax()" class="greenButton" value="LOG IN"/>
                    </form>                    <a id="infolink" onclick="flipTab('password_recovery')">Lost Password</a>
                </div>
                <span id='betaPrompt'>-Hueclues is in Private Beta- <br>Leave your email for an Invite!<br/><br/>
                    <form id="betaForm" action="/controllers/beta_processing.php" method="POST">
                        <input type="text" name="betaEmail" class="indexInput" placeholder ="email" />
                        <input type="button" id="betaButton" onclick="betaSignup()" class="greenButton" value="Send"/>
                    </form>
                </span>
            </div>    
            <div id="password_recovery_page" class="flippages">
                <img src="/img/newlogo.png" id="logo"/>
                <div id="formcontainer1">
                    <form method="POST" action="password_recovery.php">
                        <input type="text" class="indexInput" name="recovery_email" placeholder="Enter your Email" /><br/>
                        <input type="submit" class="greenButton" value="Recover"/>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>