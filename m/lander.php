<?php
session_start();
include('connection.php');
include('database_functions.php');
?>

<html>
    <head><title>hueclues</title> 
        <meta name="description" content="fashion help inspiration what to wear "> 
        <meta name="keywords" content="what women wear fashion style decide" >
        <meta http-equiv="content-type" content="text/html;charset=UTF-8"> 

        <script type="text/javascript" >

            function delayedRedirect(){
                window.location = "/";
            }
        </script>
    </head>
    <body onload="setTimeout('delayedRedirect()', 2000)">
        <div style="display:none">
            hueclues is the social network that will help you decide what to wear, without getting fashion help, give global fashion inspiration, 
            follow closets of friends, dress for the occasion, show off your style, 
            <?php
            $tag_query = "SELECT * FROM tag WHERE `count` > 0";
            $tag_result = mysql_query($tag_query);
            while ($tag = mysql_fetch_array($tag_result)) {
                echo "#" . $tag['name'] . "clothing";
            }
            ?>
        </div>
    </body>
</html>