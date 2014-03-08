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
                width:100%;
                right:0px;
            }
            div.mainContainer{
                width:60%;
                display:inline-block;
                margin-right:0px;
            }
            div.userContainer{
                width:30%;
                margin-left:50px;
                display:inline-block;
            }
        </style>
    </head>
    <body>
        <?php initiateNotification() ?>
        <?php commonHeader() ?>
        <img src="/img/loading.gif" id="loading"/>
        <div class="userContainer">
            
            <?php $useridArray = outfitUsers($outfitid);
            while($useridArray[$i]){
                formatUserSearch($useridArray[$i]);
                echo "<br/>";
                $i++;
            }
            ?>

        </div>
        <div class="mainContainer">
            <?php formatOutfit($userid, $outfitid); ?>
        </div>
        
    </body>
</html>