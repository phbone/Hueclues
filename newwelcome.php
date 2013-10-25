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
            var welcomeIndex = 0;
            $(document).ready(function(e) {
                var welcomeHexCount = setupWelcome();
                setInterval(function() {
                    runWelcome(welcomeIndex % welcomeHexCount);
                    welcomeIndex++;
                }, 1000);

            });

            function runWelcome(i) {
                $('#hex' + i).animate({opacity: 0.8});
                $('#hex' + (i - 1)).animate({opacity: 0.1});

            }

            function setupWelcome() {
                // find out how wide the screen is   
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
                return i;
            }
        </script>
        <style>

            .divider hr {
                width:31%;
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
                opacity:0.2;
            }
        </style>
    </head>
    <body>      
        <img src="/img/loading.gif" id="loading" />
        <?php commonHeader(); ?>

    </body>
</html>
