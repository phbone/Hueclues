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
    database_increment("user", "userid", $userid, "outfitcount", 1);
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
        <link rel="stylesheet" href="/fancybox/source/jquery.fancybox.css?" type="text/css" media="screen" />
        <script type="text/javascript" src="/fancybox/source/jquery.fancybox.pack.js?"></script>
        <script src="http://code.jquery.com/jquery-latest.js"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <link rel="stylesheet" type="text/css" href="/css/global.css" />

        <script type="text/javascript" >

<?php initiateTypeahead(); ?>

            var userid = "<?php echo $userid ?>";
            var useridArray = <?php echo json_encode($useridArray) ?>;

            var offset = 0;
            var limit = 5;

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
                width:auto;
                height:300px;
                position:relative;
                margin:0px 10px;
            }
            #outfitContainer{
                overflow-x: scroll;
                background:url('/img/bg.png');
                width:150%;
            }
            #outfitDescription{
                width:550px;
                height:45px;
                font-size:17px;
                margin:auto;
                position:relative;
            }
            button.outfitRemoveIcon{
                display:block;
                position:relative;
                width:80%;
                margin:auto;
                z-index:1;
            }
            #mainContainer{
                width:150%;
            }
        </style>
    </head>
    <body>
        <?php include_once("analyticstracking.php") ?>
        <?php initiateNotification() ?>
        <?php commonHeader() ?>
        <img src="/img/loading.gif" id="loading"/>
        <div id="mainContainer">


            <input type="text" id="outfitDescription" placeholder="title your outfit"/><br/>
            
            <div id="outfitContainer">

                <?php
                $itemObject1 = returnItem($outfit['itemid1']);
                $itemObject2 = returnItem($outfit['itemid2']);
                $itemObject3 = returnItem($outfit['itemid3']);
                $itemObject4 = returnItem($outfit['itemid4']);
                $itemObject5 = returnItem($outfit['itemid5']);
                $itemObject6 = returnItem($outfit['itemid6']);

                if ($itemObject1 || $itemObject2 || $itemObject3 || $itemObject4 || $itemObject5 || $itemObject6) {
                    if ($itemObject1) {
                        echo "<div class='outfitItems'>
                <button class = 'greenButton outfitRemoveIcon' onclick = 'removeFromOutfit(" . $outfit['itemid1'] . ")'>Remove From Outfit</button>";
                        formatOutfitItem($userid, $itemObject1, 300);
                        echo "</div>";
                    }

                    if ($itemObject2) {
                        echo "<div class='outfitItems'>
                <button class = 'greenButton outfitRemoveIcon' onclick = 'removeFromOutfit(" . $outfit['itemid2'] . ")'>Remove From Outfit</button>";
                        formatOutfitItem($userid, $itemObject2, 300);
                        echo "</div>";
                    }
                    if ($itemObject3) {
                        echo "<div class='outfitItems'>
                <button class = 'greenButton outfitRemoveIcon' onclick = 'removeFromOutfit(" . $outfit['itemid3'] . ")'>Remove From Outfit</button>";
                        formatOutfitItem($userid, $itemObject3, 300);
                        echo "</div>";
                    }if ($itemObject4) {
                        echo "<div class='outfitItems'>
                <button class = 'greenButton outfitRemoveIcon' onclick = 'removeFromOutfit(" . $outfit['itemid4'] . ")'>Remove From Outfit</button>";
                        formatOutfitItem($userid, $itemObject4, 300);
                        echo "</div>";
                    }
                    if ($itemObject5) {
                        echo "<div class='outfitItems'>
                <button class = 'greenButton outfitRemoveIcon' onclick = 'removeFromOutfit(" . $outfit['itemid5'] . ")'>Remove From Outfit</button>";
                        formatOutfitItem($userid, $itemObject5, 300);
                        echo "</div>";
                    }
                    if ($itemObject6) {
                        echo "<div class='outfitItems'>
                <button class = 'greenButton outfitRemoveIcon' onclick = 'removeFromOutfit(" . $outfit['itemid6'] . ")'>Remove From Outfit</button>";
                        formatOutfitItem($userid, $itemObject6, 300);
                        echo "</div>";
                    }
                } else {
                    echo "Your current Outfit is empty, please add items (see here for FAQ)";
                }
                ?>
            </div>
            <button class="greenButton" value="Save Outfit" onclick='saveOutfit($("#outfitDescription").val())'></button>

        </div>
    </body>
</html>