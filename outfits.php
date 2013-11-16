<?php
session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');
include('global_objects.php');

$userid = $_SESSION['userid'];
$user = database_fetch("user", "userid", $userid);
$current_outfitid = $user['current_outfitid'];
if ($current_outfitid == "0") {
    database_insert("outfit", "outfitid", NULL, "userid", $userid, "time", time());
    $newOutfitid = mysql_insert_id();
    database_update("user", "userid", $userid, "", "", "current_outfitid", $newOutfitid);
    database_increment("user", "outfitcount", 1);
}
$outfit = database_fetch("outfit", "outfitid", $current_outfitid);
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initiateTools() ?>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <script src="/js/global_javascript.js" type="text/javascript" charset="utf-8" ></script>
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <script src="http://code.jquery.com/jquery-latest.js"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <link rel="stylesheet" type="text/css" href="/css/global.css" />

        <script type="text/javascript" >

<?php initiateTypeahead(); ?>

            var userid = "<?php echo $userid ?>";
            var useridArray = <?php echo json_encode($useridArray) ?>;

            var offset = 0;
            var limit = 5;
            var database = 'item';
            if (<?php echo $item_count; ?> > 0) {
                var enablePagination = "1";
            }
            else {
                var enablePagination = "0";
            }

            $(document).ready(function(e) {
                bindActions();
                initiatePagination(database, useridArray);
                $('#filterInput').keyup(function() {
                    filterItems($('#filterInput').val())
                });
            });

            function submitForm(formid) {
                document.getElementById(formid).submit();
            }


            function flipRequest(id) {
                if (id == "followers") {
                    $("#followers").fadeIn();
                    $("#following").hide();
                    $("#top").hide();
                }
                else if (id == "following") {
                    $("#following").fadeIn();
                    $("#followers").hide();
                    $("#top").hide();
                }
                else if (id == "top") {
                    $("#top").fadeIn();
                    $("#following").hide();
                    $("#followers").hide();
                }
            }
            function flipView(id) {
                // switches to item or outfits

            }
            function gotoCloset() {
                window.location.href = "/closet/" + $("#user_search").val();
            }
        </script>
        <style>
            .smallItemContainer:hover{
                cursor: pointer;
            }
            .outfitItems{
                display:inline-block;
                
            }
        </style>
    </head>
    <body>
        <?php include_once("analyticstracking.php") ?>
        <?php initiateNotification() ?>
        <?php commonHeader() ?>
        <img src="/img/loading.gif" id="loading"/>
        <div id="mainContainer">


            <input type="text" id="outfitDescription" placeholder="title your outfit"/>

            <?php
            $itemObject1 = returnItem($outfit['itemid1']);
            $itemObject2 = returnItem($outfit['itemid2']);
            $itemObject3 = returnItem($outfit['itemid3']);
            $itemObject4 = returnItem($outfit['itemid4']);
            $itemObject5 = returnItem($outfit['itemid5']);
            $itemObject6 = returnItem($outfit['itemid6']);

            if ($itemObject1 || $itemObject2 || $itemObject3 || $itemObject4 || $itemObject5 || $itemObject6) {

                echo "<div class='outfitItems'>";
                formatOutfitItem($userid, $itemObject1);
                echo "</div>";
                echo "<div class='outfitItems'>";
                formatOutfitItem($userid, $itemObject2);
                echo "</div>";
                echo "<div class='outfitItems'>";
                formatOutfitItem($userid, $itemObject3);
                echo "</div>";
                echo "<div class='outfitItems'>";
                formatOutfitItem($userid, $itemObject4);
                echo "</div>";
                echo "<div class='outfitItems'>";
                formatOutfitItem($userid, $itemObject5);
                echo "</div>";
                echo "<div class='outfitItems'>";
                formatOutfitItem($userid, $itemObject6);
                echo "</div>";
            } else {
                echo "Your current Outfit is empty, please add items (see here for FAQ)";
            }
            ?>


        </div>
    </body>
</html>