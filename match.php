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
    $scheme_colors[2] = "";
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
<?php initiateTypeahead(); ?>

            var userid = '<?php echo $userid ?>';
            $(document).ready(function(e) {
                bindActions();
                flipTab('closettab');
                genderFilter(2);
                enableSelectBoxes();
                $(".selected").html("Filter By:");
            });


            function flipTab(id) {
                $('#followingtab').removeClass('active');
                $('#closettab').removeClass('active');
                $('#storetab').removeClass('active');
                $('#' + id).addClass('active');
                $('.matchPage').hide();
                $('#' + id + 'page').fadeIn();
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
        </script>
    </head>
    <body>
        <img src="/img/loading.gif" id="loading" />
        <?php commonHeader(); ?>
        <span id="matchHeading">
            MATCH YOUR HUES
        </span>
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
                <div id="historycontainer">
                    <ul class="matchButtons">
                        <li id='closettab' class="matchTab" onclick="flipTab('closettab')">
                            MY CLOSET
                        </li>
                        <li id='followingtab' class="matchTab" onclick="flipTab('followingtab')">
                            FOLLOWED CLOSETS
                        </li>
                        <li id="storetab" class="matchTab active" onclick="flipTab('storetab');">
                            STORE MATCHES
                        </li>


                    </ul>
                    <br/>
                    <div id="closettabpage" class="matchPage">
                        <?php
                        if (!$userid) {
                            echo "<span class = 'alert alert-error'><a href='/index.php'>Login</a> to use this feature</span>";
                        } else {



                            $followingItemColorArray = returnAllItemsFromFollowing($userid, "code");
                            for ($i = 0; $i < sizeof($followingItemColorArray); $i++) {
                                $closet_same_color1 = hsl_same_color($scheme_colors[1], $followingItemColorArray[$i], $hue_tolerance, $saturation_tolerance, $light_tolerance);
                                $closet_same_color2 = hsl_same_color($scheme_colors[2], $followingItemColorArray[$i], $hue_tolerance, $saturation_tolerance, $light_tolerance);

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
                            $following_ids = array();
                            $follow_query = database_query("follow", "followerid", $userid);
                            while ($follow = mysql_fetch_array($follow_query)) {
                                array_push($following_ids, $follow['userid']);
                            }

                            // create the query to get the items all the people you are following
                            $item_query = "SELECT * from item WHERE userid IN (";
                            for ($i = 0; $i < (count($following_ids) - 1); $i++) {
                                $item_query = $item_query . "'" . $following_ids[$i] . "',";
                            }
                            $item_query = $item_query . "'" . $following_ids[$i] . "')";

                            $item_result = mysql_query($item_query);
                            while ($item = mysql_fetch_array($item_result)) {
                                $description = $item['description'];
                                $saved_color = $item['code'];
                                $closet_same_color1 = hsl_same_color($scheme_colors[1], $saved_color, $hue_tolerance, $saturation_tolerance, $light_tolerance);
                                $closet_same_color2 = hsl_same_color($scheme_colors[2], $saved_color, $hue_tolerance, $saturation_tolerance, $light_tolerance);
                                // tells you if the closet item being examined is a color that fulfills the color scheme
                                if ($closet_same_color1 || $closet_same_color2) {// && ($same_shade || $same_tint)) {
                                    $item_object = returnItem($item['itemid']);
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
                                       <span class='storeTitle'><span class='storePercent' title='Color Match Percentage'>" . 2 * $match_object->priority . "%</span>" . stripslashes($match_object->description) . "</span>              
<img alt='  This Image Is Broken' src='" . $match_object->url . "' class='fixedwidththumb thumbnaileffect' /><br/><br/>
                                                   <div class='storeLink'>
                                                   <a href='" . $match_object->purchaselink . "' target='_blank' class='storeUrl'>View Item In Store</a>
                                                       </div>
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
