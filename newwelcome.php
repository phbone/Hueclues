<?php
session_start();
include('connection.php');
include('global_objects.php');
include('global_tools.php');
include('database_functions.php');

$userid = $_SESSION['userid'];
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
            
            $(document).ready(function(e) {
                setupWelcome();
                runWelcome();
            });

            function runWelcome() {
                var i = 0;
                while (i) {
                    $('hex' + i).animate('opacity', 1);
                    $('hex' + i - 1).animate('opacity', 0.4).delay(5000);
                }
            }

            function setupWelcome() {
                // find out how wide the screen is   
                console.log(vFit);
                console.log(hFit);
                var hexHeight = 199;
                var bottomArray = new Array();
                var leftArray = new Array();
                var bottom = 0;
                var left = -90;
                var i = 0;
// create left side of shell               
                while (bottom < ($(window).height() - 55)) {
                    bottomArray[i] = bottom;
                    leftArray[i] = left;
                    bottom += hexHeight;
                    i++;
                }
                bottom -= hexHeight;
                i--;
// create top side of shell

                var k = 0;
                while (left < ($(window).width())) {

                    leftArray[i] = left;
                    bottomArray[i] = bottom;
                    if (k % 2) {
                        bottomArray[i] += 100;
                    }
                    left += 175;
                    i++;
                    k++;
                }

                left -= 175;
                bottom = bottomArray[i - 1];
// create right side of shell
                while (bottom > -200) {
                    bottomArray[i] = bottom;
                    leftArray[i] = left;
                    bottom -= hexHeight;
                    i++;
                }
                for (i = 0; i < bottomArray.length; i++) {
                    var html = '<div id="hex' + i + '"  class = "hexagon" style="bottom:' + bottomArray[i] + 'px;left:' + leftArray[i] + 'px;"><div class = "hexLeft"></div><div class = "hexMid"></div><div class = "hexRight"></div></div>';
                    $('body').append(html);
                }
            }
        </script>
        <style>
            #welcomeHeading{
                font-size:40px;
                font-family:"Century Gothic";
                color:#58595B;
            }
            .tutorialImage{
                display:none;
                left:0px;
                top:55px;
                height:595px;
                width:1130px;
                position:absolute;
                margin:auto;
            }
            #welcomeTutorial{
                width:1130px;
                height:auto;
                margin:auto;
                position:relative;
            }
            #welcomeContainer{
                width:100%;
                height:100%;
                position:relative;
                margin:auto;
            }
            .divider hr {
                width:31%;
            }

            #welcomeStart{
                top:175px;
                left:0px;
                width:100%;
                position:absolute;
            }
            #welcomeFollow{
                top:-250px;
                left:525px;
                position:absolute;
                opacity:0.8;
            }
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
                opacity:0.6;
            }
        </style>
    </head>
    <body>      
        <img src="/img/loading.gif" id="loading" />
        <?php commonHeader(); ?>

    </body>
</html>
