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

        <script type="text/javascript">

<?php initiateTypeahead(); ?>

            var userid = "<?php echo $userid ?>";

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
            function gotoCloset() {
                window.location.href = "/closet/" + $("#user_search").val();
            }

            function fillOutfit() {// reloads outfit
                $("#loading").show();
                $.ajax({
                    type: "POST",
                    url: "/outfits_processing.php",
                    data: {
                        'action': "load"
                    },
                    success: function(html) {
                        loadObject = jQuery.parseJSON(html);
                        if (loadObject.objects) {
                            $("#outfitBar").html("");
                            for (var i = 0; i < 6; i++) {
                                formatOutfitItems(userid, loadObject.objects[i]);
                            }
                        }
                        $("#loading").hide();
                    }
                });
            }


            function formatOutfitItems(userid, itemObject) {
                // formats items that appear under outfits in the header
                var addString = "";
                var lockString = "readonly='true'";

                $('#outfitBar').append("<div class='itemContainer' id='item" + itemObject.itemid + "' style='color:#" + itemObject.text_color + ";height:125px;'><div id='itemPreview' class='previewContainer'>\n\
<span class = 'itemDescription' style='background-color:#" + itemObject.hexcode + "'>" + stripslashes(itemObject.description) + "</span>\n\
<img alt = '  This Image Is Broken' src = '" + itemObject.image_link + "' onclick='Redirect(\"/hue/" + itemObject.itemid + "\")' class = 'fixedwidththumb thumbnaileffect' />\n\
<div class='itemTagBox' style='background-color:#" + itemObject.hexcode + "'>\n\
<input type = 'text' class='itemTag'  name = 'tags'" + lockString + "onchange = 'updateTags(this, " + itemObject.itemid + ")' value = '" + itemObject.tags + "' placeholder = 'define this style with #hashtags' />\n\
<input type = 'text' class='purchaseLink'  name = 'purchaseLink' onblur='hidePurchaseLink(" + itemObject.itemid + ")' onchange = 'updatePurchaseLink(this, " + itemObject.itemid + ")' value = '" + itemObject.purchaselink + "' placeholder = 'Link to Where You Bought It' />\n\
</div><br/></div>");
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
                overflow-y:hidden;
                background:url('/img/bg.png');
                width:150%;
                padding:30px 10px;
                height:350px;
                margin-top:55px;
                position:absolute;
            }
            #outfitDescription{
                width:550px;
                height:45px;
                font-size:17px;
                margin:auto;
                position:fixed;
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
            #saveOutfitButton{
                position:fixed;
                left:650px;
                height:49px;
                width:250px;
                font-size:18px;

            }
            #deleteOutfitButton{
                position:fixed;
                background-color:gray;  
                left:900px;
                height:49px;
                width:250px;
                font-size:18px;

            }
            #outfitBar{
                width:100%;
                position:absolute;
                top:55px;
                z-index:2;
                background-color:white;
                height:150px;
            }
        </style>
    </head>
    <body>
        <?php include_once("analyticstracking.php") ?>
        <?php initiateNotification() ?>
        <?php commonHeader() ?>
        <div id="outfitBar"></div>
        <img src="/img/loading.gif" id="loading"/>
        <div id="mainContainer" style='max-width:100%;'>


            <input type="text" id="outfitDescription" maxlength="50" placeholder="  name your outfit"/>       
            <button class="greenButton" id='saveOutfitButton' onclick='saveOutfit($("#outfitDescription").val())'>Save Outfit</button>
            <button class="greenButton" id='deleteOutfitButton' onclick='deleteOutfit()'>Discard Current Outfit</button>
            <br/>


            <div id="outfitContainer">

                <?php
                $itemObject1 = returnItem($outfit['itemid1']);
                $itemObject2 = returnItem($outfit['itemid2']);
                $itemObject3 = returnItem($outfit['itemid3']);
                $itemObject4 = returnItem($outfit['itemid4']);
                $itemObject5 = returnItem($outfit['itemid5']);
                $itemObject6 = returnItem($outfit['itemid6']);

                $outfitItemids = array($outfit['itemid1'], $outfit['itemid2'], $outfit['itemid3'], $outfit['itemid4'], $outfit['itemid5'], $outfit['itemid6']);
                $outfitItemObjects = array($itemObject1, $itemObject2, $itemObject3, $itemObject4, $itemObject5, $itemObject6);

                if ($itemObject1 || $itemObject2 || $itemObject3 || $itemObject4 || $itemObject5 || $itemObject6) {
                    for ($i = 0; $i < 6; $i++) {
                        if ($outfitItemids[$i] > 0) {
                            echo "<div class='outfitItems'>
                <button class = 'greenButton outfitRemoveIcon' onclick = 'removeFromOutfit(" . $outfitItemids[$i] . ")'>Remove From Outfit</button>";
                            formatOutfitItem($userid, $outfitItemObjects[$i], 300);
                            echo "</div>";
                        }
                    }
                } else {
                    echo "Your current Outfit is empty, please add items (see here for FAQ)";
                }
                ?>
            </div>

        </div>
    </body>
</html>