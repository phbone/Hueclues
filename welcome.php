<?php
session_start();
include('connection.php');
include('global_objects.php');
include('global_tools.php');
include('database_functions.php');

$userid = $_SESSION['userid'];
$user = database_fetch("user", "userid", $userid);
$get = $_SESSION['get'];
$username = $user['username'];
?>
<!doctype html>
<html>
    <head> 
        <?php initiateTools() ?>
        <script>
            var userid = '<?php echo $userid ?>';
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

            function runWelcome(i) {
                $('#hex' + i).animate({opacity: 0.8});
                $('#welcomeText' + i).fadeIn();
                //  $('#hex' + (i - 1)).animate({opacity: 0.1});
                //  $('#welcomeText' + (i - 1)).fadeOut();
            }


            function setupWelcome() {
                // find out how wide the screen is   
                var hexHeight = 199;
                var bottomArray = new Array();
                var leftArray = new Array();
                var vFit = Math.ceil($(window).height() / 200);
                var welcomeMessage = [" ", "", "", "", ""];
                var k = 0;
                var bottom = 0;
                var left = -55;
                var i = 0;
                var col = 0;
                while (left < $(window).width()) {
                    col++;
                    while (bottom < $(window).height() + 100) {
                        bottomArray[i] = bottom;
                        leftArray[i] = left;
                        if (col % 2) {
                            bottomArray[i] -= 100;
                        }
                        i++;
                        bottom += hexHeight;
                    }

                    left += 175;
                    bottom = 0;
                }
                for (i = 0; i < bottomArray.length; i++) {
                    var html = '<div id="hex' + i + '"  class = "hexagon" style="bottom:' + bottomArray[i] + 'px;left:' + leftArray[i] + 'px;">\n\
<div class = "hexLeft"></div><div class = "hexMid"></div><div class = "hexRight"></div></div>';
                    $('body').append(html);
                    if (welcomeMessage[k] && i % vFit == (2 || 3)) {
                        var message = '<span id="welcomeText' + i + '" class="welcomeText" style="bottom:' + bottomArray[i] + 'px;left:' + leftArray[i] + 'px;"> ' + welcomeMessage[k] + '</span>';
                        $('body').append(message);
                        k++;
                    }
                }
                return i;
            }

            function selectGender(gender) {
                $.ajax({
                    type: "POST",
                    url: "/controllers/welcome_processing.php",
                    data: {
                        'gender': gender
                    },
                    success: function(html) {
                        editObject = jQuery.parseJSON(html);
                        if (editObject.notification == "success") {
                            console.log("gender set");
                        }
                        $("#loading").hide();
                    }
                });

            }

            function welcomePages() {
                welcomeStep++;
                if (welcomeStep == 1) {
                    $(".welcomePage").fadeOut();
                    $("#welcomeImage2").fadeIn();
                }
                else if (welcomeStep == 2) {
                    $(".welcomePage").fadeOut();
                    $("#welcomeImage3").fadeIn();
                }
                else if (welcomeStep == 3) {
                    $(".welcomePage").fadeOut();
                    $("#nextButton").prop("disabled", true);
                    $("#nextButton").hide();
                    $("#selectGender").fadeIn();
                } else if (welcomeStep == 4) {
                    $(".welcomePage").fadeOut();
                    $("#nextButton").fadeIn();
                    $("#nextButton").prop("disabled", false);
                    $("#findFriends").fadeIn();
                } else if (welcomeStep = 5) {
                    $(".welcomePage").fadeOut();
                    $("#nextButton").fadeOut();
                    $("#beginHueclues").fadeIn();
                }
            }
            function selectMale() {
                selectGender("m");
                welcomePages();
            }
            function selectFemale() {
                selectGender("f");
                welcomePages();
            }
            function openHueclues() {
                // fade out hexagons from middle
                $(".welcomePage").fadeOut();
                var midWay = Math.round(welcomeHexCount / 2);
                var k = 0;
                var i;
                for (i = midWay; i >= 0; i--) {
                    $("#hex" + i).delay(i * 50).fadeOut();
                    $("#hex" + (Math.abs(midWay + midWay - i))).delay(i * 60).fadeOut();
                }
                setTimeout(Redirect('/hive'),3000);
            }
        </script>
        <style>



            #topText{
                font-family:"Century Gothic";
            }
            .hexLeft{
                border-right: 65px solid #51BB75;
            }

            .hexMid{
                opacity:0.85;
                float: left;
                width: 112px;
                height: 200px;
                background-color:#51BB75;
            }

            .hexRight{
                border-left: 65px solid #51BB75;
            }
            .hexRight, .hexLeft{  
                float: left;
                border-top: 100px solid transparent;
                border-bottom: 100px solid transparent;
                opacity:0.85;
            }
            .hexagon{
                width:400px;
                position:fixed;            
                opacity:0.2;
                z-index:1;
            }
            .welcomePage{
                display:none;
                width:80%;
                height:auto;
                position:absolute;
                left:10%;
                margin-top:125px;
                text-align:center;
                z-index:3;
                font-size:22px;
            }
            #mainHeader{
                display:none;
            }
            #welcomeHeader{
                height:55px;
                top:0px;
                left:0px;
                position:absolute;
                width:100%;
                z-index:3;
                background:url('/img/bg.png');
            }
            #nextButton{
                width:250px;
                margin:auto;
                height:55px;
                font-size:17px;
                display:none;
            }
            #beginHuecluesButton{
                width:auto;
                margin:auto;
                top:100px;
                padding-bottom:50px;
                position:relative;
                background-color:transparent;
                font-size:18px;
                height:250px;
                cursor:pointer;
            }
            #beginHuecluesButton:hover{
                color:white;
            }
            .genderButtons{
                background:url('/img/bg.png');
                width:250px;
                border:0px;
                height:70px;
                font-size:25px;
                margin:10px;
                color:#51BB75;
                margin-top:50px;
                cursor:pointer;
            }
            .HCM{
                width:49%;
                display:inline-block;
                position:absolute;
                top:40px;
            }
            .welcomeImage{
                margin:auto;
                position:relative;
                height:auto;
                display:block;
                margin-top:-50px;
                width:95%;
            }
            .enterHuecluesLogo{
                position:relative;
                width:150px;
                margin:auto;
                display:block;
                top:30px;
                z-index:-1;
            }
            .logoText{
                top:20px;
                margin:auto;
                margin-left:2px;
                display:block;
                position:relative;
                font-weight:bolder;
                font-size:60px;
                color:black;
            }
        </style>
    </head>
    <body>      
        <img src="/img/loading.gif" id="loading" />
        <div id='mainHeader'>
            <?php commonHeader(); ?>
        </div>
        <div id='welcomeHeader'>
            <button id='nextButton' class='greenButton' onclick='welcomePages()'>Next</button>
        </div>

        <div id="welcomeImage1" class="welcomePage">
            <img class="welcomeImage" src="/img/Orientation1.png" />
        </div>

        <div id="welcomeImage2" class="welcomePage">
            <img class="welcomeImage" src="/img/Orientation2.png" />
        </div>

        <div id="welcomeImage3" class="welcomePage">
            <img class="welcomeImage" src="/img/Orientation3.png" />
        </div>

        <div id="selectGender" class="welcomePage">Your gender helps us find clothes for you<br/><br/><br/><br/>
            <button id='menButton' class="genderButtons" onclick='selectMale()'>Male</button>
            <button id='femaleButton' class="genderButtons" onclick='selectFemale()'>Female</button>
        </div>

        <div id="findFriends" class="welcomePage">
            Follow some of our super users!
            <br/><br/><br/>
            <div class="HCM" style="left:0px;">
                <?php
                formatUserSearch(12); //david
                formatUserSearch(48); //shad
                formatUserSearch(25); //phil
                ?>
            </div>
            <div class="HCM">
                <?php
                formatUserSearch(10); //tiara
                formatUserSearch(22); //sarah
                formatUserSearch(56); //ysabelle
                ?>
            </div>
        </div>

        <div id='beginHueclues' class='welcomePage'>
            <button class='greenButton' id='beginHuecluesButton' onclick='openHueclues()'><span class='logoText'>Lets Get Started</span> <img class='enterHuecluesLogo' src='/img/hc_icon_blacksolid.png' /></button>
        </div>
    </body>
</html>
