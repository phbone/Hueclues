<?php
session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');
include('global_objects.php');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initiateTools() ?>
        <script type="text/javascript" src="/js/global_javascript.js"></script>
        <script type="text/javascript" src="/js/facebook.js"></script>
        <script src="//connect.facebook.net/en_US/all.js"></script>
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <link rel="stylesheet" href="/fancybox/source/jquery.fancybox.css?" type="text/css" media="screen" />
        <script type="text/javascript" src="/fancybox/source/jquery.fancybox.pack.js?"></script>
        <link rel="stylesheet" type="text/css" href="/css/global.css" />
        <style>
            #404error{
                width:60%;
                font-size:25px;
                margin:auto;
                top:250px;
                text-align:center;
                position:relative;
            }
        </style>
    </head>

    <body>
        <div id="404error">
            Looks like the url you entered has been removed or doesn't exist!
        </div>
    </body>
</html>