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
        </script>
    </head>
    <body>
        <img src="/img/loading.gif" id="loading" />
        <div id='mainHeader'>
            <?php commonHeader(); ?>
        </div>
        <div id="welcomeImage1" class="welcomePage">
            <img class="welcomeImage" src="/img/orientationlandingpage.png" />
        </div>

    </body>
</html>