<?php
session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');
include('global_objects.php');
$userid = $_SESSION['userid'];
$user = database_fetch("user", "userid", $userid);
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initiateTools() ?>
        <link rel="stylesheet" href="/fancybox/source/jquery.fancybox.css?" type="text/css" media="screen" />
        <script type="text/javascript" src="/fancybox/source/jquery.fancybox.pack.js?"></script>
        <script>
<?php initiateTypeahead(); ?>
            var userid = "<?php echo $userid ?>";
            $(document).ready(function(e) {
<?php checkNotifications(); ?>
            });


        </script>
        <style>
            #feedbackForm{

            }
            #feedbackContainer{
                width:70%;
                margin:auto;
                position:relative;
                margin-top:150px;
                font-size:15px;
                overflow:hidden;
            }
            #feedbackButton{
                right:0px;
                position:absolute;
                width:200px;
                background-color:#51bb75;
                height:60px;
                color:white;
                border:none;
                font-size:23px;
                cursor:pointer;
            }
        </style>
    </head>
    <body>
        <?php initiateNotification() ?>
        <?php commonHeader(); ?>
        <div class="divider" style="margin-top:-50px">
            <hr class="left" style="width:39%"/>
            <span id="mainHeading">FEEDBACK</span>
            <hr class="right"style="width:39%" />
        </div>
        <div id="feedbackContainer">

            <br/>
            <form id="feedbackForm" action="/controllers/feedback_processing.php" method="POST">
                <input id="feedbackButton" class="greenButton" type="submit" />
            </form>
            <textarea rows="25" cols="80" name="feedback" style="border:none;font-size:20px;" form="feedbackForm" placeholder="Tell us about the issue"></textarea>
        </div>
    </body>
</html>
