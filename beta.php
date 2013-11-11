<?php
session_start();
require_once 'connection.php';
include('database_functions.php');
include('global_tools.php');

if ($_SESSION['userid']) {
    header("Location:/home");
} else if (!$_SESSION['userid'] && isset($_COOKIE['userid'])) {
    $user = database_fetch("user", "username", $_COOKIE['username'], "password", $_COOKIE['password']);
    if ($_COOKIE['userid'] == $user['userid']) { // make sure the username/pass matches the userid cookie
        $_SESSION['userid'] = $_COOKIE['userid'];
        header("Location:/home");
    }
}

if (is_mobile()) {
    // switch this to hueclues.com
    header("Location:http://m.hueclues.com");
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initiateTools() ?>
        <meta name="description" content="hue clues fashion help inspiration what to wear "> 
        <meta name="keywords" content="what women wear fashion style decide" >
        <script src="http://code.jquery.com/jquery-latest.js"></script>
        <script type="text/javascript" src="/js/global_javascript.js?"></script>
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
                flipTab('user_login');
                $('<img/>').attr('src', '/img/wood.jpg').load(function() {
                    $('body').fadeIn();
                });
                $(".indexInput").keyup(function(event) {
                    if (event.keyCode == 13) {
                        $("#signupButton").click();
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
                width:850px;
                top:90px;
                margin:auto;
                position: relative;
            }

            .indexInput{
                height:35px;
                font-family:"Quicksand";
                font-size:20px;
                margin:4px;
                width:258px;
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
                margin:3px;
                border:0px;
                color:white;
                font-size:19px;
                background-color:#51BB75;
                -webkit-border-radius: 3px;
                -moz-border-radius: 3px;
                border-radius: 3px;
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
                background-color:white;
                opacity:0.7;
                position: absolute;
                right:0px;
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
                background-color:white;
                opacity:0.7;
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
                top:-7px;
                height:46px;
                margin:auto;
                display:block;
                position:relative;
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
                display:block;
                margin:auto;
                text-align:center;
                color:#58595B;
            }
            #welcomeDescription{
                font-family:"Quicksand";
                font-size:18px;
                color:black;
                width:700px;
                text-align:center;
                margin:auto;
                display:block;
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
        </style>
    </head>
    <body id="body" style="display:none">
        <img src="/img/loading.gif" id="loading" />
        <?php initiateNotification(); ?>
<img src="/img/huecluesLogo.png" id="logo"/><br/>
        <div id="unsupported" style="display:none">
            <center>
                <img id="middle_logo" src="/img/huecluesLogo.png" ></img>
                <span class="alert alert-success">hueclues is currently unavailable on Internet Explorer, please open hueclues in 
                    google chrome or firefox to continue.</span>
            </center>
        </div>
        <div id="supported" style="display:none">
            <h1 id="title"></h1> 
            <div id="user_login_page" class="flippages"> 
                
                <span id="welcomeMessage">WELCOME TO HUECLUES!</span><br/><br/>
                <div id="welcomeDescription">
                    hueclues uses pictures of your clothing to shop, match and manage your style.
                </div>

                <?php
                $key = $_GET['key'];
                if (strlen($key / 23) == 8 && isPrime($key / 23)) {
                    ?>
                    <div id="formcontainer3" style="margin:auto;position:relative;margin-top:45px;">  
                        <div style="padding:10px 0px;margin:auto;text-align:center;font-size:20px;">SIGN UP HERE</div>
                        <form id="signupForm" action="/signup_processing.php" method="POST">
                            <input type="text" name="signupusername" class="indexInput" placeholder="username" maxlength="15" value="" /><br/>
                            <input type="text" name="signupemail" class="indexInput" placeholder ="email" value="<?php ?>" /><br/>
                            <input type="text" name="signupname" class="indexInput" placeholder="full name" maxlength="20" /><br/>
                            <input type="password" name="signuppassword" class="indexInput" placeholder="password" /><br/>
                            <input type="button" onclick="signupAjax();" id="signupButton" class="greenButton" style="margin-left:4px;width:280px;" value="SIGN UP FOR HUECLUES" /><br/>
                            <span id="agreement_prompt">By signing up, you are agreeing to our' <a href="/terms" target="_blank">terms of use</a></span><br/>
                        </form> 
                    </div> 
                <?php } ?>
            </div>   
        </div>
    </body>
</html>