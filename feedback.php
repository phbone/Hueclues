<?php
session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');

$notification = $_SESSION['upload_notification'];
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
            }
        </style>
    </head>
    <body>
        <?php initiateNotification() ?>
        <?php commonHeader(); ?>
        <div id="feedbackContainer">
            <div class="divider">
                <hr class="left"/>
                <span id="mainHeading">NEW ITEMS</span>
                <hr class="right" />
            </div>
            <br/>
            <form id="feedbackForm" action="/controllers/feedback_processing.php" method="POST">
                <input id="feedbackButton" class="greenButton" type="submit" />
            </form>
            <textarea rows="6" cols="80" name="feedback" form="feedbackForm">Tell us about the issue</textarea>
        </div>
    </body>
</html>
