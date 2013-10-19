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
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <link rel="stylesheet" type="text/css" href="/css/global.css" />
        <style>
            #errorMessage{
                width:60%;
                font-size:25px;
                margin:auto;
                background: url('/img/bg.png');
                top:250px;
                height:125px;
                padding-top:100px;
                text-align:center;
                position:relative;
            }
        </style>
    </head>

    <body>
        <div id="errorMessage">
            Looks like the url you entered has been removed or doesn't exist!
        </div>
    </body>
</html>