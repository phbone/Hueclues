<?php
session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');
include('global_objects.php');

$userid = $_SESSION['userid'];
$user = database_fetch("user", "userid", $userid);

$outfitid = $_GET['outfitid'];
$i = 0;
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
                width:60%;
                right:0px;
                margin-left:250px;
            }
            div.userContainer{
                width:20%;
                left:0px;
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
            while($useridArray[$i]){
                formatUserSearch($useridArray[$i]);
                echo "<br/>";
                $i++;
            }
            ?>

        </div>
    </body>
</html>