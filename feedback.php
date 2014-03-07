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
                width:39%;
                height:200px;
                margin:auto;
                margin-top:100px;
            }
            #feedbackContainer{
                width:40%;
                margin:auto;
                position:relative;
                margin-top:150px;
                font-size:15px;
                overflow:hidden;
            }
            #feedbackButton{
                margin:auto;
                display:block;
                margin-top:-100px;
                width:100%;
                background-color:#51bb75;
                height:60px;
                color:white;
                border:none;
                font-size:20px;
                cursor:pointer;
            }
            textarea{
                outline:none;
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

            
            <textarea rows="20" cols="43" name="feedback" style="border:none;font-size:20px;background:url('/img/bg.png')" form="feedbackForm" placeholder="Tell us what we can improve"></textarea>
        
        </div>
        <form id="feedbackForm" action="/controllers/feedback_processing.php" method="POST">
                <input id="feedbackButton" class="greenButton" type="submit" />
            </form>
    </body>
</html>
