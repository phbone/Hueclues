<?php
session_start();
include('connection.php');
include('global_objects.php');
include('global_tools.php');
include('database_functions.php');

$userid = $_SESSION['userid'];
$get = $_SESSION['get'];
?>
<!doctype html>
<html>
    <head> 
        <?php initiateTools() ?>
        <script src="http://code.jquery.com/jquery-latest.js"></script>
        <script src="/js/global_javascript.js" type="text/javascript" charset="utf-8" ></script>
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <link rel="stylesheet" type="text/css" href="/css/global.css" />
        <script>
            var userid = '<?php echo $userid ?>';
            var num = 1;
            var followCount = 5;
            var welcomePage = 0;
            var welcomeIndex = 0;
            $(document).ready(function(e) {
                bindActions();
                var welcomeHexCount = setupWelcome();
                var intervalId = setInterval(function() {
                    runWelcome(welcomeIndex);
                    welcomeIndex++;
                    if (welcomeIndex > welcomeHexCount) {
                        clearInterval(intervalId);
                    }
                }, 100);
                $("#itemHolder").fadeIn();
                $(".itemContainer").trigger("mouseover");
                $("#skipWelcome").delay(1500).fadeIn();
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
                    url: "/welcome_processing.php",
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

            function welcomePages(id) {
                if (id == 1) {
                    $(".welcomePage").fadeOut();
                    $("#welcomeImage").fadeIn();
                } else if (id == 2) {
                    $(".welcomePage").fadeOut();
                    $("#selectGender").fadeIn();
                } else if (id == 3) {
                    $(".welcomePage").fadeOut();
                    $("#findFriends").fadeIn();
                } else if (id = 4) {
                    $(".welcomePage").fadeOut();
                }
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
        </style>
    </head>
    <body>      
        <img src="/img/loading.gif" id="loading" />
        <?php commonHeader(); ?>
        <div id="welcomeImage" class="welcomePage">
            welcome image
        </div>
        <div id="selectGender" class="welcomePage">Select your gender
            <button id='menButton'></button>
            <button id='femaleButton'></button>
        </div>
        <div id="findFriends" class="welcomePage">
            find your friends!
        </div>

    </body>
</html>
