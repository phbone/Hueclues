<?php
session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');
include('global_objects.php');

$itemid = $_GET['itemid'];
$itemObject = returnItem($itemid);
$inputColor = $itemObject->hexcode;
// tolerance is for how specific color matches are
$saturation_tolerance = 100;
$light_tolerance = 100;
$hue_tolerance = 8.33;

$userid = $_SESSION['userid'];
$user = database_fetch("user", "userid", $userid);
$colorObject = colorsMatching($inputColor);
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initiateTools() ?>
        <link rel="stylesheet" href="/fancybox/source/jquery.fancybox.css?" type="text/css" media="screen" />
        <script type="text/javascript" src="/fancybox/source/jquery.fancybox.pack.js?"></script>
        <link rel="stylesheet" type="text/css" href="/css/hue.css" />
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
                $('#shaScheme').bind('mouseenter', function() {
                    showDescription('sha');
                });
                $('#shaScheme').bind('mouseleave', function() {
                    hideDescription();
                });
                $('#anaScheme').bind('mouseenter', function() {
                    showDescription('ana');
                });
                $('#anaScheme').bind('mouseleave', function() {
                    hideDescription();
                });
                $('#compScheme').bind('mouseenter', function() {
                    showDescription('comp');
                });
                $('#compScheme').bind('mouseleave', function() {
                    hideDescription();
                });
                $('#triScheme').bind('mouseenter', function() {
                    showDescription('tri');
                });
                $('#triScheme').bind('mouseleave', function() {
                    hideDescription();
                });
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
                $(".hovereffect").removeClass("clicked");
                $("#" + scheme + "Scheme").addClass("clicked");
                $(".schemePreview").hide();
                $("#itemSort").fadeIn();
                toggleCheckboxes();
                $(".matched").hide();
                $("." + scheme).fadeIn();
            }

            function showDescription(id) {
                var txt = new Array();
                txt["ana"] = "Offers a blend of colors that would appear together in nature.";
                txt["comp"] = "Matches with maximum contrast. ";
                txt["tri"] = "Matches the selected color with two well balanced color matches.";
                txt["sha"] = "Offers a lighter and darker shade of the selected color. ";

                $("#schemeDescription").html(txt[id]);

                $("#schemeDescription").prependTo($("#" + id + "Scheme").find(".schemePreview"));
                $("#schemeDescription").slideDown();
            }

            function hideDescription(id) {
                $("#schemeDescription").hide();
            }


        </script>
        <style>
        </style>
    </head>
    <body>
        <?php include_once("analyticstracking.php") ?>
        <?php initiateNotification() ?>
        <img src="/img/loading.gif" id="loading" />
        <?php commonHeader(); ?>


        <div id="matchContainer">
            <div id="side_container"> 
                <div class="picture_box">
                    <?php
                    formatUserSearch($itemObject->owner_id);
                    formatItem($userid, $itemObject);
                    ?> 
                </div>
            </div>


            <div id="main_container" id="item_display">
                <div class="divider" style="margin-top:-155px;">
                    <hr class="left" style="width:13%;"/>
                    <span id="mainHeading">
                        MATCH WITH OTHER CLOSETS
                    </span>
                    <hr class="right" style="width:13%" />
                </div>

                <div id="itemSort">
                    <input type='text' style="margin-bottom:71px; top:65px;"id='filterInput' placeholder="(Sort using hashtags) i.e pockets"></input>
                    <br/>
                    <ul class="matchButtons">
                        <li class="sourceButton"><label><input type="checkbox" checked="checked" id="closetBox" class="matchCheckbox" onchange="toggleCheckboxes()">&nbsp MATCH W. MY CLOSET</label>
                        </li>
                        <li class="sourceButton"><label><input type="checkbox" checked="checked" id="followingBox" class="matchCheckbox" onchange="toggleCheckboxes()">&nbsp MATCH W. PEOPLE I FOLLOW</label>
                        </li>
                        <li class="sourceButton"><label><input type="checkbox" checked="checked" id="storeBox" class="matchCheckbox" onchange="toggleCheckboxes()">&nbsp MATCH W. HUECLUES</label>
                            <?php /*
                              <div class='selectBox' style="top:10px;margin-top:-15px;">
                              <span class='selected' style="width:75px;text-indent:10px;height:25px;">Filter By:</span>
                              <span class='selectArrow' style="height:25px;"><i class="icon-chevron-down" style="position:absolute;left:-33px;"></i></span>
                              <div class="selectOptions" style="width:106px;">
                              <span class="selectOption" id="noFilter" style="width:106px;" onclick = "genderFilter(2)">None</span>
                              <span class="selectOption" id="womenFilter" style="width:106px;" onclick = "genderFilter(0)">Women</span>
                              <span class="selectOption" id="menFilter" style="width:106px;" onclick = "genderFilter(1)">Men</span>
                              </div>
                              </div>
                             */ ?>
                        </li>
                    </ul>
                    <br/>
                    <?php
                    $colorSchemeMap = array('sha', 'sha', 'ana', 'ana', 'tri', 'tri', 'comp', 'comp');
                    $colorSchemePreviewItemids = array();
                    $previewCount = 0;
                    $matchingItems = returnAllMatchingItems($userid, $itemid);
                    $compCount = $matchingItems['compCount'];
                    $anaCount = $matchingItems['anaCount'];
                    $shaCount = $matchingItems['shaCount'];
                    $triCount = $matchingItems['triCount'];

                    $userItems = $matchingItems['userItems'];
                    $storeItems = $matchingItems['storeItems'];

                    for ($i = 0; $i < count($userItems); $i++) {
                        echo "<div class='" . $userItems[$i]->source . "'><div class='matched " . $userItems[$i]->scheme . "'>";
                        formatItem($userid, returnItem($userItems[$i]->itemid));
                        echo "</div></div>";
                        for ($k = 0; $k < 4; $k++) {
                            if (strpos($userItems[$i]->scheme, $colorSchemeMap[$k * 2]) !== false && $previewKey < 8) {
                                if (!$colorSchemePreviewItemids[$k * 2]) {
                                    $colorSchemePreviewItemids[$k * 2] = $userItems[$i]->itemid;
                                } else {
                                    $colorSchemePreviewItemids[($k * 2) + 1] = $userItems[$i]->itemid;
                                }
                                $previewCount++;
                            }
                        }
                    }

                    function cmp($a, $b) {
// array low -> high
// priority high -> low
// reverse comparison string
                        return strcmp($b->priority, $a->priority);
                    }

                    if ($inputColor) {
// sort according to degree of match(priority) if there was a color entered
                        usort($storeItems, "cmp");
                    }
                    for ($i = 0; $i < count($storeItems); $i++) {
                        echo "<div class='store'><div class='matched " . $storeItems[$i]->scheme . "'>";
                        formatStoreItem($storeItems[$i]);
                        echo "</div></div>";
                    }
                    ?>
                </div>
                <table id="matchpanel">
                    <div id="schemeDescription"></div>
                    <tr class="matchSchemeColumn">
                        <td class="hovereffect" id="shaScheme" onclick="changeScheme('sha')">
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

                            </div><br/>
                            <span class="finePrint">click colors to see more</span>
                            <div class="schemePreview">
                                <?php
                                formatSmallItem($userid, returnItem($colorSchemePreviewItemids[0]), 200, "off");
                                formatSmallItem($userid, returnItem($colorSchemePreviewItemids[1]), 200, "off");
                                ?>
                            </div>
                        </td> 

                    </tr>
                    <tr class="matchSchemeColumn">
                        <td class="hovereffect" id="anaScheme" onclick="changeScheme('ana')">
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
                            </div> <br/>
                            <span class="finePrint">click colors to see more</span>
                            <div class="schemePreview">
                                <?php
                                formatSmallItem($userid, returnItem($colorSchemePreviewItemids[2]), 200, "off");
                                formatSmallItem($userid, returnItem($colorSchemePreviewItemids[3]), 200, "off");
                                ?>
                            </div>
                        </td>

                    </tr>
                    <tr class="matchSchemeColumn">
                        <td class="hovereffect" id="triScheme" onclick="changeScheme('tri')">
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
                            <br/>
                            <span class="finePrint">click colors to see more</span>
                            <div class="schemePreview">
                                <?php
                                formatSmallItem($userid, returnItem($colorSchemePreviewItemids[4]), 200, "off");
                                formatSmallItem($userid, returnItem($colorSchemePreviewItemids[5]), 200, "off");
                                ?>
                            </div>
                        </td>

                    </tr>
                    <tr class="matchSchemeColumn">
                        <td class="hovereffect" id="compScheme" onclick="changeScheme('comp')">
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
                            <br/>
                            <span class="finePrint">click colors to see more</span>
                            <div class="schemePreview">
                                <?php
                                formatSmallItem($userid, returnItem($colorSchemePreviewItemids[6]), 200, "off");
                                formatSmallItem($userid, returnItem($colorSchemePreviewItemids[7]), 200, "off");
                                ?>
                            </div>
                        </td>

                    </tr> 
                </table>
            </div>
        </div>

    </body>
</html>
