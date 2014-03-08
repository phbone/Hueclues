<?php
session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');
include('global_objects.php');

$userid = $_SESSION['userid'];
$user = database_fetch("user", "userid", $userid);

$outfitid = $_GET['outfitid'];
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initiateTools() ?>
        <link rel="stylesheet" type="text/css" href="/css/global.css" />
        <script type="text/javascript" >
            var userid = '<?php echo $userid ?>';
<?php initiateTypeahead(); ?>
<?php checkNotifications(); ?>


        </script>
        <style>
            div.outfitContainer{
                width:100%;
                margin-left:0px;
            }
        </style>
    </head>
    <body>
        <?php initiateNotification() ?>
        <?php commonHeader() ?>
        <img src="/img/loading.gif" id="loading"/>
        <div class="mainContainer">
            <?php formatOutfit($userid, $outfitid); ?>
        </div>
        <div class="userContainer">
            <?php $useridArray = outfitUsers($outfitid);
            print_r($useridArray);
            for($i=0;$i<6;$i++){
                formatUserSearch($useridArray[$i]);
            }
            ?>

        </div>
    </body>
</html>