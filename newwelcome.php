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
                movingWelcome();
            });

            function movingWelcome() {
                // find out how wide the screen is   
                console.log($(window).width());
                console.log($(window).height());
                var bottomArray = new Array();
                var leftArray = new Array();
                var bottom = 0;
                var top = 0;
                var left = -55;
                var i = 0;
                while (bottom < ($(window).height() - 55)) {
                    bottomArray[i] = bottom;
                    leftArray[i] = -55;
                    bottom += 200;
                    i++;
                }
                while (left < ($(window).width())) {
                    leftArray[i] = left;
                    if (i % 2 && leftArray[i]) {
                        bottomArray[i] = 675;
                    }
                    left += 175;
                    i++;
                }
                while (bottom > 0) {
                    bottomArray[i] = bottom;
                    leftArray[i] = "";
                    rightArray[i] = -55;
                    bottom -= 200;
                    i++;
                }
                var topArray = [, , , , , , , , , ];
                for (i = 0; i < 20; i++) {
                    var html = '<div id = "hexagon" style="right:' + rightArray[i] + 'px;bottom:' + bottomArray[i] + 'px;top:' + topArray[i] + 'px;left:' + leftArray[i] + 'px;"><div class = "hexLeft"></div><div class = "hexMid"></div><div class = "hexRight"></div></div>';
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
            #hexTest1{
                top:290px;
                left:-135px;
                width:200px;
                font-size:20px;
                position:relative;
                z-index:3;
                text-align:center;
                color:white;
            }
            #hexTest2{
                top:135px;
                position:relative;
                z-index:3;
                text-align:center;
                color:white;
                cursor:pointer;
                font-size:20px;
            }
            #hexagon1{
                left:-150px;
                top:215px;
                position:absolute;
                width:400px;
                z-index:-1;
            }
            #hexagon2{
                display:block;
                position:relative;
                margin:auto;
                height:200px;
                width:250px;
            }
            #hexagon2:hover, #hexText2:hover{
                cursor:pointer;
            }
            #nextButton{
                left:1130px;
                top:55px;
                width:100px;
                height:592px;
                font-size:25px;
                text-align:center;
                position:absolute;
            }
            #hexagon{
                width:400px;
                position:absolute;
            }
        </style>
    </head>
    <body>      
        <img src="/img/loading.gif" id="loading" />
        <?php commonHeader(); ?>
        <div id="hexagon">
            <div class="hexLeft"></div>
            <div class="hexMid"></div>
            <div class="hexRight"></div>
        </div>

    </body>
</html>
