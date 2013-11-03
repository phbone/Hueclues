<?php
session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');
include('global_objects.php');
include('algorithms.php');

$itemid = $_GET['itemid'];
$itemObject = returnItem($itemid);
$inputColor = $itemObject->hexcode;
// tolerance is for how specific color matches are
$saturation_tolerance = 100;
$light_tolerance = 100;
$hue_tolerance = 8.33;

$userid = $_SESSION['userid'];

$colorObject = colorsMatching($inputColor);
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initiateTools() ?>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <script type="text/javascript" src="/js/global_javascript.js"></script>
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <link rel="stylesheet" type="text/css" href="/css/global.css" />
        <link rel="stylesheet" type="text/css" href="/css/newhue.css" />
        <script type="text/javascript">
            //tells you whether the tabs are pressed or not
<?php initiateTypeahead(); ?>


            function toggleCheckboxes() {
                if ($("#closetBox").is(':checked')) {
                    $(".closet").fadeIn();
                }
                else {
                    $(".closet").hide();
                }
                if ($("#followingBox").is(':checked')) {
                    $(".following").fadeIn();
                }
                else {
                    $(".following").hide();
                }
                if ($("#storeBox").is(':checked')) {
                    $(".store").fadeIn();
                }
                else {
                    $(".store").hide();
                }
            }
            var userid = '<?php echo $userid ?>';
            $(document).ready(function(e) {
                bindActions();
                genderFilter(2);
                enableSelectBoxes();
                $('#filterInput').keyup(function() {
                    filterItems($('#filterInput').val())
                });
                $(".selected").html("Filter By:");
            });

            function genderFilter(gender) {
                // gender:
                // 0 = female
                // 1 = male
                // 2 = unisex
                if (gender == 0) {
                    $(".1").slideUp();
                    $(".0").slideDown();
                }
                else if (gender == 1) {
                    $(".0").slideUp();
                    $(".1").slideDown();
                }
                else if (gender == 2) {
                    $(".1").slideDown();
                    $(".0").slideDown();
                }
            }
            function changeScheme(scheme) {
                $("#itemSort").fadeIn();
                toggleCheckboxes();
                $(".matched").hide();
                $("." + scheme).fadeIn();
            }
        </script>
        <style>
        </style>
    </head>
    <body>
        <img src="/img/loading.gif" id="loading" />
        <?php commonHeader(); ?>


        <div id="matchContainer">
            <div id="side_container">
                <div class="picture_box">
                    <?php
                    formatSmallItem($userid, $itemObject, 300);
                    ?> 
                    <ul class="matchButtons">
                        <input type="checkbox" checked="checked" id="closetBox" class="matchCheckbox" onchange="toggleCheckboxes()"><label>MY CLOSET MATCHES</label>
                        <br/>
                        <input type="checkbox" checked="checked" id="followingBox" class="matchCheckbox" onchange="toggleCheckboxes()"><label>FOLLOWING MATCHES</label>
                        <br/>
                        <input type="checkbox" checked="checked" id="storeBox" class="matchCheckbox" onchange="toggleCheckboxes()"><label>STORE MATCHES</label>

                    </ul>
                </div>
            </div>


            <div id="main_container" id="item_display">
                <div id="itemSort">
                    <input type='text' id='filterInput' placeholder="(Sort by keyword) i.e pockets"></input>
                    <br/>
                    <?php
                    $matchingItems = returnAllMatchingItems($userid, $itemid);
                    $compCount = $matchingItems['compCount'];
                    $anaCount = $matchingItems['anaCount'];
                    $splCount = $matchingItems['splCount'];
                    $shaCount = $matchingItems['shaCount'];
                    $triCount = $matchingItems['triCount'];

                    $userItems = $matchingItems['userItems'];
                    $storeItems = $matchingItems['storeItems'];

                    for ($i = 0; $i < count($userItems); $i++) {
                        echo "<div class='" . $userItems[$i]->source . "'><div class='matched " . $userItems[$i]->scheme . "'>";
                        formatItem($userid, returnItem($userItems[$i]->itemid));
                        echo "</div></div>";
                    }
                    for ($i = 0; $i < count($storeItems); $i++) {
                        echo "<div class='store'><div class='matched " . $storeItems[$i]->scheme . "'>";

                        formatStoreItem($storeItems[$i]);
                        echo "</div></div>";
                    }
                    ?>
                </div>
            </div>
        </div>


        <table id="matchpanel">
            <tr class="matchSchemeColumn">
                <td class="hovereffect" id="shadey_scheme" onclick="changeScheme('sha')" onmouseover="showDescription('shadey_scheme')" onmouseout="hideDescription()">
                    <span class="schemeName">BATTISTA (<?php echo $shaCount; ?>)</span><br/>          
                    <div class="schemeContainer">

                        <div class="hexLeft"  style="border-right-color: #<?php echo $colorObject->sha1; ?>"></div>
                        <div class="hexMid"  style="background-color: #<?php echo $colorObject->sha1; ?>"></div>
                        <div class="hexRight"  style="border-left-color: #<?php echo $colorObject->sha1; ?>"></div>


                        <div class="hexLeft"  style="border-right-color: #<?php echo $inputColor; ?>"></div>
                        <div class="hexMid"  style="background-color: #<?php echo $inputColor; ?>"></div>
                        <div class="hexRight"  style="border-left-color: #<?php echo $inputColor; ?>"></div>


                        <div class="hexLeft"  style="border-right-color: #<?php echo $colorObject->sha2; ?>"></div>
                        <div class="hexMid"  style="background-color: #<?php echo $colorObject->sha2; ?>"></div>
                        <div class="hexRight"  style="border-left-color: #<?php echo $colorObject->sha2; ?>"></div>

                    </div>
                </td> 
            <div class="schemePreview">
                <?php
                $previewCount = 0;
                $i = 0;
                while ($previewCount < 2) {
                    if ($userItems[$i]->scheme == "sha") {
                        formatSmallItem($userid, returnItem($userItems[$i]->itemid));
                        $previewCount++;
                    }
                    $i++;
                }
                ?>
            </div>
        </tr>
        <tr class="matchSchemeColumn">
            <td class="hovereffect" id="natural_scheme" onclick="changeScheme('ana')" onmouseover="showDescription('natural_scheme')" onmouseout="hideDescription()">
                <span class="schemeName">OSWALD (<?php echo $anaCount; ?>)</span><br/>  
                <div class="schemeContainer">
                    <div class="hexLeft"  style="border-right-color: #<?php echo $colorObject->ana1; ?>"></div>
                    <div class="hexMid"  style="background-color: #<?php echo $colorObject->ana1; ?>"></div>
                    <div class="hexRight"  style="border-left-color: #<?php echo $colorObject->ana1; ?>"></div>


                    <div class="hexLeft"  style="border-right-color: #<?php echo $inputColor; ?>"></div>
                    <div class="hexMid"  style="background-color: #<?php echo $inputColor; ?>"></div>
                    <div class="hexRight"  style="border-left-color: #<?php echo $inputColor; ?>"></div>


                    <div class="hexLeft"  style="border-right-color: #<?php echo $colorObject->ana2; ?>"></div>
                    <div class="hexMid"  style="background-color: #<?php echo $colorObject->ana2; ?>"></div>
                    <div class="hexRight"  style="border-left-color: #<?php echo $colorObject->ana2; ?>"></div>
                </div>
            </td>
        <div class="schemePreview">
            <?php
            $previewCount = 0;
            $i = 0;
            while ($previewCount < 2) {
                if ($userItems[$i]->scheme == "ana") {
                    formatSmallItem($userid, returnItem($userItems[$i]->itemid));
                    $previewCount++;
                }
                $i++;
            }
            ?>
        </div>
    </tr>
    <tr class="matchSchemeColumn">
        <td class="hovereffect" id="standout_scheme" onclick="changeScheme('tri')" onmouseover="showDescription('standout_scheme')" onmouseout="hideDescription()">
            <span class="schemeName">MUNSELL (<?php echo $triCount; ?>)</span><br/> 

            <div class="schemeContainer">

                <div class="hexLeft"  style="border-right-color: #<?php echo $colorObject->tri1; ?>"></div>
                <div class="hexMid"  style="background-color: #<?php echo $colorObject->tri1; ?>"></div>
                <div class="hexRight"  style="border-left-color: #<?php echo $colorObject->tri1; ?>"></div>


                <div class="hexLeft"  style="border-right-color: #<?php echo $inputColor; ?>"></div>
                <div class="hexMid"  style="background-color: #<?php echo $inputColor; ?>"></div>
                <div class="hexRight"  style="border-left-color: #<?php echo $inputColor; ?>"></div>


                <div class="hexLeft"  style="border-right-color: #<?php echo $colorObject->tri2; ?>"></div>
                <div class="hexMid"  style="background-color: #<?php echo $colorObject->tri2; ?>"></div>
                <div class="hexRight"  style="border-left-color: #<?php echo $colorObject->tri2; ?>"></div>

            </div>
        </td>
    <div class="schemePreview">
        <?php
        $previewCount = 0;
        $i = 0;
        while ($previewCount < 2) {
            if ($userItems[$i]->scheme == "tri") {
                formatSmallItem($userid, returnItem($userItems[$i]->itemid));
                $previewCount++;
            }
            $i++;
        }
        ?>
    </div>
</tr>
<tr class="matchSchemeColumn">
    <td class="hovereffect" id="complimentary_scheme" onclick="changeScheme('comp')" onmouseover="showDescription('complimentary_scheme')" onmouseout="hideDescription()">
        <span class="schemeName">VONGOE (<?php echo $compCount; ?>)</span><br/>          
        <div class="schemeContainer">
            <div class="hexLeft"  style="border-right-color: #<?php echo $colorObject->comp; ?>"></div>
            <div class="hexMid"  style="background-color: #<?php echo $colorObject->comp; ?>"></div>
            <div class="hexRight"  style="border-left-color: #<?php echo $colorObject->comp; ?>"></div>

            <div class="hexLeft"  style="border-right-color: #<?php echo $inputColor; ?>"></div>
            <div class="hexMid"  style="background-color: #<?php echo $inputColor; ?>"></div>
            <div class="hexRight"  style="border-left-color: #<?php echo $inputColor; ?>"></div>

            <div class="hexLeft"  style="border-right-color: #<?php echo $colorObject->comp; ?>"></div>
            <div class="hexMid"  style="background-color: #<?php echo $colorObject->comp; ?>"></div>
            <div class="hexRight"  style="border-left-color: #<?php echo $colorObject->comp; ?>"></div>
        </div>
    </td>
<div class="schemePreview">
    <?php
    $previewCount = 0;
    $i = 0;
    while ($previewCount < 2) {
        if ($userItems[$i]->scheme == "comp") {
            formatSmallItem($userid, returnItem($userItems[$i]->itemid));
            $previewCount++;
        }
        $i++;
    }
    ?>

</div>
</tr> 
</table>
</body>
</html>
