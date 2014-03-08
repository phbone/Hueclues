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
                margin-top:150px;

            }
            div.userContainer{
                width:30%;
                margin-left:50px;
                display:inline-block;
            }
            .bigOutfitName{
                display:block;
                margin:auto;
                text-align:center;
                font-size:25px;
                margin-bottom:20px;
            }
        </style>
    </head>
    <body>
        <?php initiateNotification() ?>
        <?php commonHeader() ?>
        <img src="/img/loading.gif" id="loading"/>

        <div class="mainContainer">
            <?php
            echo "<span class='bigOutfitName'>".$outfit['name']."</span>";
            $i = 0;
            echo "<div style='margin:auto'>";
            while ($itemidArray[$i]) {
                $itemObject = returnItem($itemidArray[$i]);
                formatAppSmallItem($userid, $itemObject);
                $i++;
            }
            echo "</div>";
            ?>
        </div>

    </body>
</html>