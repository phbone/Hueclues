<?php
session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');
include('global_objects.php');

$userid = $_SESSION['userid'];
$user = database_fetch("user", "userid", $userid);
$outfitid = $_GET['outfitid'];
$outfit = database_fetch("outfit", "outfitid", $outfitid);
$itemidArray = array($outfit['itemid1'], $outfit['itemid2'], $outfit['itemid3'], $outfit['itemid4'], $outfit['itemid5'], $outfit['itemid6']);
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
                width:80%;
                margin:auto;
                
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
            <?php echo $outfit['name'];?>
        <div class="mainContainer">
            <?php
            $i = 0;
            while ($itemidArray[$i]) {
                $itemObject = returnItem($itemidArray[$i]);
                formatItem($userid, $itemObject);
                $i++;
            }
            ?>
        </div>

    </body>
</html>