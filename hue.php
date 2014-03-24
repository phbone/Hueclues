<?php
session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');
include('global_objects.php');

$userid = $_SESSION['userid'];
$loggedIn = isset($userid);


$time = time();
$itemid = $_GET['itemid'];
$itemObject = returnItem($itemid);

// this needs to be global for suggestions to work
$inputColor = $itemObject->hexcode;

// tolerance is for how specific color matches are
$saturation_tolerance = 100;
$light_tolerance = 100;
$hue_tolerance = 8.33;

$colorObject = colorsMatching($inputColor);
if ($loggedIn) {
    $user = database_fetch("user", "userid", $userid);
    $emptyMessage = "";
    $item = database_fetch("item", "itemid", $itemid);
    $ownerid = $item['userid'];
    if ($item && ($userid != $ownerid)) {
        // check item exists and this is not your own item
        database_insert("notification", "userid", $ownerid, "from_userid", $userid, "itemid", $itemid, "type", "2", "time", $time);
    }
}

function cmp($a, $b) {
// array low -> high
// priority high -> low
// reverse comparison string
    return strcmp($b->priority, $a->priority);
}

$matchingItems = countMatchingItems($itemid);
$compCount = $matchingItems['compCount'];
$anaCount = $matchingItems['anaCount'];
$shaCount = $matchingItems['shaCount'];
$triCount = $matchingItems['triCount'];
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initiateTools() ?>
        <link rel="stylesheet" type="text/css" href="/css/huev1.css" />
        <script type="text/javascript">
            //tells you whether the tabs are pressed or not
<?php initiateTypeahead(); ?>



            var userid = "<?php echo $userid ?>";
            var itemOffset = 0;
            var outfitOffset = 0;
            var stingOffset = 0;
            var limit = 5;
            var paginateOutfit = "1";
            var paginateItem = "1";
            var paginateSting = "1";

            $(document).ready(function(e) {
                bindActions();
                stingPagination();
                $('#filterInput').keyup(function() {
                    filterItems($('#filterInput').val())
                });


                enableSelectBoxes();


                $('#filterInput').keyup(function() {
                    filterItems($('#filterInput').val())
                });


            });



            function stingPagination() {
                if (paginateSting == "1") {
                    paginateSting = "0";
                    $("#loading").show();

                    var send_data = {
                        'offset': stingOffset,
                        'color': '<?php echo $inputColor ?>'
                    }
                    $.ajax({
                        type: "GET",
                        url: "/controllers/sting_pagination_processing.php",
                        data: send_data,
                        success: function(html) {
                            stingObj = jQuery.parseJSON(html);


                            console.log(stingObj.schemeMap);

                            if (stingObj.results) {
                                var i = 0;
                                for (i = 0; i < stingObj.results.length; i++) {
                                    if (stingObj.results[i]) {
                                        formatAppSmallItem(userid, stingObj.results[i], "", stingObj.schemeMap[i]);
                                        stingOffset++;
                                    }
                                    else {
                                        $("#itemBackground #loadMore").hide();
                                    }
                                }
                                filterItems($('#filterInput').val());
                                paginateSting = "1";
                            }
                            bindActions();
                            $("#loading").hide();
                        }
                    });
                }
            }


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
                $("#itemSort").fadeIn();
                $(".appSmallItemContainer").hide();
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
        <?php initiateNotification() ?>
        <img src="/img/loading.gif" id="loading" />
        <?php commonHeader(); ?>


        <div id="matchContainer">
            <div id="side_container"> 
                <div class="picture_box">
                    <?php
                    formatAppItem($userid, $itemObject);
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



                <table id="matchpanel">
                    <div id="schemeDescription"></div>
                    <tr class="matchSchemeColumn">
                        <td class="hovereffect" id="shaScheme" onclick="changeScheme('comp')">
                            <span class="schemeName">BATTISTA (<?php echo $shaCount + $compCount; ?>)</span><br/>          
                            <div class="schemeContainer">

                                <div class="hexLeft"  style="border-right-color: #<?php echo $colorObject->comp; ?>"></div>
                                <div class="hexMid"  style="background-color: #<?php echo $colorObject->comp; ?>"></div>
                                <div class="hexRight"  style="border-left-color: #<?php echo $colorObject->comp; ?>"></div>


                                <div class="hexLeft"  style="border-right-color: #<?php echo $inputColor; ?>"></div>
                                <div class="hexMid"  style="background-color: #<?php echo $inputColor; ?>"></div>
                                <div class="hexRight"  style="border-left-color: #<?php echo $inputColor; ?>"></div>


                                <div class="hexLeft"  style="border-right-color: #<?php echo $colorObject->sha2; ?>"></div>
                                <div class="hexMid"  style="background-color: #<?php echo $colorObject->sha2; ?>"></div>
                                <div class="hexRight"  style="border-left-color: #<?php echo $colorObject->sha2; ?>"></div>

                            </div><br/>
                            <div class="schemePreview">
                                <?php
                                /* if ($shaCount == 0) {
                                  echo $emptyMessage;
                                  }
                                  formatAppSmallItem($userid, returnItem($colorSchemePreviewItemids[0]), "", 215, $inputColor);
                                  echo "<br/>";
                                  formatAppSmallItem($userid, returnItem($colorSchemePreviewItemids[1]), "", 215, $inputColor);

                                 *  ?>
                                 */
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
                            <div class="schemePreview">

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
                            <div class="schemePreview">

                            </div>
                        </td>

                    </tr>

                </table>

                <?php
                if (!$loggedIn) {
                    echo "<div id='signupMessage' onclick='openSignup()'>Sign In to see matches</div>";
                }
                ?>
            </div>


            <div id="itemBackground">
                <div id="itemSort">
                    <input type='text' style="margin-bottom:71px; top:65px;"id='filterInput' placeholder="search items: #tags"></input>
                    <br/>
                    <?php /*
                      <ul class="matchButtons">
                      <li class="sourceButton"><label><input type="checkbox" checked="checked" id="closetBox" class="matchCheckbox" onchange="toggleCheckboxes()">&nbsp MATCH MY CLOSET</label>
                      </li>
                      <li class="sourceButton"><label><input type="checkbox" checked="checked" id="followingBox" class="matchCheckbox" onchange="toggleCheckboxes()">&nbsp MATCH PEOPLE I FOLLOW</label>
                      </li>
                      <li class="sourceButton"><label><input type="checkbox" checked="checked" id="storeBox" class="matchCheckbox" onchange="toggleCheckboxes()">&nbsp MATCH HUECLUES</label>
                      </li>
                      </ul>
                     * 
                     */
                    ?>

                    <br/>
                </div>

                <button id="loadMore" class="greenButton"  onclick="stingPagination();">Load More...</button>

            </div>
        </div>
    </body>
</html>
