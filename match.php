<?php
session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');
include('global_objects.php');
include('algorithms.php');

$extraction_image = $_GET['image'];
$input_color = $_GET['color'];
$scheme = $_GET['scheme'];
$filter = $_GET['filter'];

// tolerance is for how specific color matches are
$saturation_tolerance = 100;
$light_tolerance = 100;
$hue_tolerance = 8.33;


$shade_count = 10;
$userid = $_SESSION['userid'];
$scheme_colors = array();

function cmp($a, $b) {
    // array low -> high
    // priority high -> low
    // reverse comparison string
    return strcmp($b->priority, $a->priority);
}

if ($scheme == "comp") {
    $scheme_colors[0] = $input_color;
    $scheme_colors[1] = hsl_complimentary($input_color);
    $scheme_colors[2] = hsl_complimentary($input_color);
} else if ($scheme == "same") {
    $scheme_colors[0] = $input_color;
    $scheme_colors[1] = $input_color;
} else if ($scheme == "triad") {
    $scheme_colors[0] = $input_color;
    $scheme_colors[1] = hsl_triadic1($input_color);
    $scheme_colors[2] = hsl_triadic2($input_color);
} else if ($scheme == "analogous") {
    $scheme_colors[0] = $input_color;
    $scheme_colors[1] = hsl_analogous1($input_color);
    $scheme_colors[2] = hsl_analogous2($input_color);
} else if ($scheme == "split") {
    $scheme_colors[0] = $input_color;
    $scheme_colors[1] = hsl_split1($input_color);
    $scheme_colors[2] = hsl_split2($input_color);
} else if ($scheme == "shade") {
    $scheme_colors = hsl_shades($input_color, $shade_count);
    $scheme_colors[0] = $input_color;
} else if ($scheme == "tint") {
    $scheme_colors = hsl_tints($input_color, $shade_count);
    $scheme_colors[0] = $input_color;
}
if ($userid) { // user is logged in
} else {
    $_SESSION['match_notification'] = "";
}
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
        <link rel="stylesheet" type="text/css" href="/css/match.css" />
        <script type="text/javascript">
            //tells you whether the tabs are pressed or not
<?php initiateTypeahead(); ?>


            function toggleTab(id) {
                if ($("#" + id).hasClass('active')) {
                    $("#" + id).removeClass('active');
                    $("#" + id + 'page').fadeOut();
                }
                else {
                    $("#" + id).addClass('active');
                    $("#" + id + 'page').fadeIn();
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

                toggleTab('closettab');
                toggleTab('followingtab');
                toggleTab('storetab');
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
        </script>
    </head>
    <body>
        <img src="/img/loading.gif" id="loading" />
        <?php commonHeader(); ?>



        <div id="matchContainer" >

            <div id="side_container">
                <div class="picture_box">
                    <img id="extraction_picture" src="<?php echo $extraction_image; ?>" />
                </div>
                <div class="scheme_box">
                    <?php
                    if ($scheme_colors[0]) {
                        echo "<div class = 'hexLeft' style = 'border-right-color:#" . $scheme_colors[1] . "'></div>
                        <div class = 'hexMid' style = 'background-color:#" . $scheme_colors[1] . "'></div>
                        <div class = 'hexRight' style = 'border-left-color:#" . $scheme_colors[1] . "'></div>";
                    }
                    echo "<div class = 'hexLeft' style = 'border-right-color:#" . $scheme_colors[0] . "'></div>
                        <div class = 'hexMid' style = 'background-color:#" . $scheme_colors[0] . "'></div>
                        <div class = 'hexRight' style = 'border-left-color:#" . $scheme_colors[0] . "'></div>";
                    if ($scheme_colors[2]) {
                        echo "<div class = 'hexLeft' style = 'border-right-color:#" . $scheme_colors[2] . "'></div>
                        <div class = 'hexMid' style = 'background-color:#" . $scheme_colors[2] . "'></div>
                        <div class = 'hexRight' style = 'border-left-color:#" . $scheme_colors[2] . "'></div>";
                    }
                    ?>
                </div>
            </div>
            <div id="main_container" id="item_display">



                <div class="divider">
                    <hr class="left" style="width:22%;"/>
                    <span id="mainHeading" onclick="dropContainer('upload')">
                        FIND YOUR MATCH
                    </span>
                    <hr class="right" style="width:22%" />
                </div>



                <div id="historycontainer">
                    <ul class="matchButtons">
                        <li id='closettab' class="matchTab" onclick="toggleTab('closettab')">
                            MY CLOSET
                        </li>
                        <li id='followingtab' class="matchTab" onclick="toggleTab('followingtab')">
                            FOLLOWED CLOSETS
                        </li>
                        <li id="storetab" class="matchTab" onclick="toggleTab('storetab');">
                            STORE MATCHES
                        </li>


                    </ul>
                    <br/>
                    <div class="matchPage">
                        <input type='text' id='filterInput' placeholder="Sort by keyword"></input>
                    </div>
                    <div id="closettabpage" class="matchPage">
                        <?php
                        if (!$userid) {
                            echo "<span class = 'alert alert-error'><a href='/index.php'>Login</a> to use this feature</span>";
                        } else {

                            $itemQuery = database_query("item", "userid", $userid);

                            while ($item = mysql_fetch_array($itemQuery)) {
                                $itemColor = $item['code'];
                                $closet_same_color1 = hsl_same_color($scheme_colors[1], $itemColor, $hue_tolerance, $saturation_tolerance, $light_tolerance);
                                $closet_same_color2 = hsl_same_color($scheme_colors[2], $itemColor, $hue_tolerance, $saturation_tolerance, $light_tolerance);

                                if ($closet_same_color1 || $closet_same_color2) {// && ($same_shade || $same_tint)) {
                                    $item_object = returnItem($item['itemid']);
                                    formatItem($userid, $item_object);
                                }
                            }
                        }
                        ?>
                        <a href='/extraction' style='text-decoration:none'><div class='messageGreen'>Find more matches by adding items to your Closet</div></a><br/><br/>
                    </div>
                    <div id="followingtabpage" class="matchPage">
                        <?php
                        if (!$userid) {
                            echo "<span class = \"alert alert-error\"><a href=\"/index.php\">Login</a> to use this feature</span>";
                        } else {
                            $followingItems = returnAllItemsFromFollowing($userid);
                            for ($i = 0; $i < sizeof($followingItems); $i++) {
                                $itemColor = $followingItems[$i]['code'];
                                $closet_same_color1 = hsl_same_color($scheme_colors[1], $itemColor, $hue_tolerance, $saturation_tolerance, $light_tolerance);
                                $closet_same_color2 = hsl_same_color($scheme_colors[2], $itemColor, $hue_tolerance, $saturation_tolerance, $light_tolerance);

                                if ($closet_same_color1 || $closet_same_color2) {// && ($same_shade || $same_tint)) {
                                    $item_object = returnItem($followingItems[$i]['itemid']);
                                    formatItem($userid, $item_object);
                                }
                            }
                            echo "<br/><br/><a href='/hive' style='text-decoration:none'><div class='messageGreen'>Follow more closets to see more matches</div></a><br/><br/>";
                        }
                        ?>
                    </div>
                    <div id="storetabpage" class="matchPage">

                        <div class='selectBox' style="position:absolute;right:0px;top:1px;">
                            <span class='selected' style="width:100px;text-indent:10px;">Filter By:</span>
                            <span class='selectArrow'><i class="icon-chevron-down"></i></span>
                            <div class="selectOptions" style="width:100px;">
                                <span class="selectOption" id="noFilter" onclick = "genderFilter(2)">None</span>
                                <span class="selectOption" id="womenFilter" onclick = "genderFilter(0)">Women</span>
                                <span class="selectOption" id="menFilter" onclick = "genderFilter(1)">Men</span>
                            </div>
                        </div>


                        <?php
                        $storeitem_query = "SELECT * FROM storeitem WHERE itemid > 0";
                        $storeitem_query = mysql_query($storeitem_query);
                        $match_items_order = array();
                        while ($storeitem = mysql_fetch_array($storeitem_query)) {

                            $description = $storeitem['description'];
                            $saved_color1 = $storeitem['code1'];
                            $saved_color2 = $storeitem['code2'];
                            $saved_color3 = $storeitem['code3'];


                            if ($input_color) {
                                /// CHANGE 100 TO APPROPRIATE LEVEL BEFORE LAUNCH
                                /// CASE: The user has given a color/scheme and views items depending on match priority
                                //  Check if any of the 3 item colors corresponds to and of the 3 scheme colors
                                //  Separate based on priority
                                array_push($match_items_order, storeMatch($storeitem['itemid'], $scheme_colors, $hue_tolerance, $saturation_tolerance, $light_tolerance));
                            } else {
                                // CASE: no color has been chose, so show all items;
                                $store_object = new store_match_object();
                                $store_object->itemid = $storeitem['itemid'];
                                $store_object->colors = array($saved_color1, $saved_color2, $saved_color3);
                                $store_object->description = $description;
                                $store_object->priority = 1;
                                $store_object->gender = $storeitem['gender'];
                                $store_object->purchaselink = $storeitem['purchaselink'];
                                $store_object->url = $storeitem['url'];
                                array_push($match_items_order, $store_object);
                                count($match_items_order);
                            }
                        }
                        if ($input_color) {
                            // sort according to degree of match(priority) if there was a color entered
                            usort($match_items_order, "cmp");
                        }

                        for ($i = 0; $i < count($match_items_order); $i++) {
                            $match_object = $match_items_order[$i];
                            if ($match_object->url && $match_object->priority > 0) {
/////////////////////////////// STORE ITEM HAS A URL//////////////////////////////////
                                // picture formatting
                                echo "<div id='storeItem$match_object->itemid' class='storeMatch " . $match_object->gender . "'>
                                        <div class='storeBar1' style='background-color:#" . $match_object->colors[0] . "'>
                                        </div>
                                         <div class='storeBar2' style='background-color:#" . $match_object->colors[1] . "'>
                                        </div>
                                         <div class='storeBar3' style='background-color:#" . $match_object->colors[2] . "'>
                                        </div>
                                       <span class='storeTitle'><span class='storePrice' title='Color Match Percentage'>$" . $match_object->price . "</span>  " . stripslashes($match_object->description) . "</span>              
<img alt='  This Image Is Broken' src='" . $match_object->url . "' class='fixedwidththumb thumbnaileffect' /><br/><br/>                                        
                                                   <a class='storeLink' href='" . $match_object->purchaselink . "' target='_blank' class='storeUrl'>View Item In Store</a>
                                                       </div>";
                            }
                        }
                        ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
