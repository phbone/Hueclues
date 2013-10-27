<?php
session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');
include('global_objects.php');
include('algorithms.php');

$extraction_image = $_GET['image'];
$itemid = $_GET['itemid'];
$itemObject = returnItem($itemid);
$inputColor = $itemObject->hexcode;
// tolerance is for how specific color matches are
$saturation_tolerance = 100;
$light_tolerance = 100;
$hue_tolerance = 8.33;

$shade_count = 10;
$userid = $_SESSION['userid'];

function cmp($a, $b) {
    // array low -> high
    // priority high -> low
    // reverse comparison string
    return strcmp($b->priority, $a->priority);
}

$compCount = 0;
$anaCount = 0;
$splitCount = 0;
$shadeCount = 0;

$compColors = array();
$anaColors = array();
$splitColors = array();
$shadeColors = array();
$shades = hsl_shades($inputColor, $shade_count);
$tints = hsl_tints($inputColor, $shade_count);


$compColors[0] = $inputColor;
$compColors[1] = hsl_complimentary($inputColor);
$compColors[2] = hsl_complimentary($inputColor);
$triadColors[0] = $inputColor;
$triadColors[1] = hsl_triadic1($inputColor);
$triadColors[2] = hsl_triadic2($inputColor);
$anaColors[0] = $inputColor;
$anaColors[1] = hsl_analogous1($inputColor);
$anaColors[2] = hsl_analogous2($inputColor);
$splitColors[0] = $inputColor;
$splitColors[1] = hsl_split1($inputColor);
$splitColors[2] = hsl_split2($inputColor);
$shadeColors[0] = $inputColor;
$shadeColors[1] = $tints[3];
$shadeColors[2] = $shades[3];
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

        <link rel="stylesheet" type="text/css" href="/css/newhue.css" />
        <script type="text/javascript">
            //tells you whether the tabs are pressed or not
<?php initiateTypeahead(); ?>


            function toggleTab(id) {
                if ($("#" + id).hasClass('active')) {
                    $("#" + id).removeClass('active');
                    $("." + id + 'page').fadeOut();
                }
                else {
                    $("#" + id).addClass('active');
                    $("." + id + 'page').fadeIn();
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
            function changeScheme(scheme) {
                $('.schemeMatches').fadeOut();
                $('#' + scheme + "Matches").fadeIn();
            }
        </script>
    </head>
    <body>
        <img src="/img/loading.gif" id="loading" />
        <?php commonHeader(); ?>


        <div id="matchContainer" >

            <div id="side_container">
                <div class="picture_box">
                    <?php
                    formatSmallItem($userid, $itemObject, 300);
                    ?> 
                    <ul class="matchButtons">
                        <li id='closettab' class="matchTab" onclick="toggleTab('closettab')">
                            MY CLOSET
                        </li><br/>
                        <li id='followingtab' class="matchTab" onclick="toggleTab('followingtab')">
                            FOLLOWED CLOSETS
                        </li><br/>
                        <li id="storetab" class="matchTab" onclick="toggleTab('storetab');">
                            STORE MATCHES
                        </li>
                    </ul>
                </div>
            </div>


            <div id="main_container" id="item_display">
                <div id="historycontainer">
                    <div class="matchPage">
                        <input type='text' id='filterInput' placeholder="(Sort by keyword) i.e pockets"></input>
                    </div>
                    <br/>

                    <div id="compMatches" class="schemeMatches">
                        <div class="closettabpage" class="matchPage">
                            <?php
                            if (!$userid) {
                                echo "<a href='/index.php'><span class = 'messageGreen'>Login to see matches from your Closet</span></a>";
                            } else {

                                $itemQuery = database_query("item", "userid", $userid);

                                while ($item = mysql_fetch_array($itemQuery)) {
                                    $itemColor = $item['code'];
                                    $closet_same_color1 = hsl_same_color($compColors[1], $itemColor, $hue_tolerance, $saturation_tolerance, $light_tolerance);
                                    $closet_same_color2 = hsl_same_color($compColors[2], $itemColor, $hue_tolerance, $saturation_tolerance, $light_tolerance);

                                    if ($closet_same_color1 || $closet_same_color2) {// && ($same_shade || $same_tint)) {
                                        $item_object = returnItem($item['itemid']);
                                        formatItem($userid, $item_object);
                                        $compCount++;
                                    }
                                }
                            }
                            ?>
                        </div>
                        <div class="followingtabpage" class="matchPage">
                            <?php
                            if (!$userid) {
                                echo "<a href='/index.php'><span class = 'messageGreen'>Login to see matches from those you are following</span></a>";
                            } else {
                                $followingItems = returnAllItemsFromFollowing($userid);
                                for ($i = 0; $i < sizeof($followingItems); $i++) {
                                    $itemColor = $followingItems[$i]['code'];
                                    $closet_same_color1 = hsl_same_color($compColors[1], $itemColor, $hue_tolerance, $saturation_tolerance, $light_tolerance);
                                    $closet_same_color2 = hsl_same_color($compColors[2], $itemColor, $hue_tolerance, $saturation_tolerance, $light_tolerance);

                                    if ($closet_same_color1 || $closet_same_color2) {// && ($same_shade || $same_tint)) {
                                        $item_object = returnItem($followingItems[$i]['itemid']);
                                        formatItem($userid, $item_object);
                                        $compCount++;
                                    }
                                }
                            }
                            ?>
                        </div>
                        <div class="storetabpage" class="matchPage">

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


                                if ($inputColor) {
                                    /// CHANGE 100 TO APPROPRIATE LEVEL BEFORE LAUNCH
                                    /// CASE: The user has given a color/scheme and views items depending on match priority
                                    //  Check if any of the 3 item colors corresponds to and of the 3 scheme colors
                                    //  Separate based on priority
                                    array_push($match_items_order, storeMatch($storeitem['itemid'], $compColors, $hue_tolerance, $saturation_tolerance, $light_tolerance));
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
                            if ($inputColor) {
                                // sort according to degree of match(priority) if there was a color entered
                                usort($match_items_order, "cmp");
                            }

                            for ($i = 0; $i < count($match_items_order); $i++) {
                                $match_object = $match_items_order[$i];
                                if ($match_object->url && $match_object->priority > 0) {
/////////////////////////////// STORE ITEM HAS A URL//////////////////////////////////
                                    formatStoreItem($match_object);
                                    $compCount++;
                                }
                            }
                            ?>

                        </div>
                    </div>




                    <div id="anaMatches" class="schemeMatches">

                        <div class="closettabpage" class="matchPage">
                            <?php
                            if (!$userid) {
                                echo "<a href='/index.php'><span class = 'messageGreen'>Login to see matches from your Closet</span></a>";
                            } else {

                                $itemQuery = database_query("item", "userid", $userid);

                                while ($item = mysql_fetch_array($itemQuery)) {
                                    $itemColor = $item['code'];
                                    $closet_same_color1 = hsl_same_color($anaColors[1], $itemColor, $hue_tolerance, $saturation_tolerance, $light_tolerance);
                                    $closet_same_color2 = hsl_same_color($anaColors[2], $itemColor, $hue_tolerance, $saturation_tolerance, $light_tolerance);

                                    if ($closet_same_color1 || $closet_same_color2) {// && ($same_shade || $same_tint)) {
                                        $item_object = returnItem($item['itemid']);
                                        formatItem($userid, $item_object);
                                        $anaCount++;
                                    }
                                }
                            }
                            ?>
                        </div>
                        <div class="followingtabpage" class="matchPage">
                            <?php
                            if (!$userid) {
                                echo "<a href='/index.php'><span class = 'messageGreen'>Login to see matches from those you are following</span></a>";
                            } else {
                                $followingItems = returnAllItemsFromFollowing($userid);
                                for ($i = 0; $i < sizeof($followingItems); $i++) {
                                    $itemColor = $followingItems[$i]['code'];
                                    $closet_same_color1 = hsl_same_color($anaColors[1], $itemColor, $hue_tolerance, $saturation_tolerance, $light_tolerance);
                                    $closet_same_color2 = hsl_same_color($anaColors[2], $itemColor, $hue_tolerance, $saturation_tolerance, $light_tolerance);

                                    if ($closet_same_color1 || $closet_same_color2) {// && ($same_shade || $same_tint)) {
                                        $item_object = returnItem($followingItems[$i]['itemid']);
                                        formatItem($userid, $item_object);
                                        $anaCount++;
                                    }
                                }
                            }
                            ?>
                        </div>
                        <div class="storetabpage" class="matchPage">

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


                                if ($inputColor) {
                                    /// CHANGE 100 TO APPROPRIATE LEVEL BEFORE LAUNCH
                                    /// CASE: The user has given a color/scheme and views items depending on match priority
                                    //  Check if any of the 3 item colors corresponds to and of the 3 scheme colors
                                    //  Separate based on priority
                                    array_push($match_items_order, storeMatch($storeitem['itemid'], $anaColors, $hue_tolerance, $saturation_tolerance, $light_tolerance));
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
                            if ($inputColor) {
                                // sort according to degree of match(priority) if there was a color entered
                                usort($match_items_order, "cmp");
                            }

                            for ($i = 0; $i < count($match_items_order); $i++) {
                                $match_object = $match_items_order[$i];
                                if ($match_object->url && $match_object->priority > 0) {
/////////////////////////////// STORE ITEM HAS A URL//////////////////////////////////
                                    formatStoreItem($match_object);
                                    $anaCount++;
                                }
                            }
                            ?>

                        </div>
                    </div>




                    <div id="triadMatches" class="schemeMatches">
                        <div class="closettabpage" class="matchPage">
                            <?php
                            if (!$userid) {
                                echo "<a href='/index.php'><span class = 'messageGreen'>Login to see matches from your Closet</span></a>";
                            } else {

                                $itemQuery = database_query("item", "userid", $userid);

                                while ($item = mysql_fetch_array($itemQuery)) {
                                    $itemColor = $item['code'];
                                    $closet_same_color1 = hsl_same_color($triadColors[1], $itemColor, $hue_tolerance, $saturation_tolerance, $light_tolerance);
                                    $closet_same_color2 = hsl_same_color($triadColors[2], $itemColor, $hue_tolerance, $saturation_tolerance, $light_tolerance);

                                    if ($closet_same_color1 || $closet_same_color2) {// && ($same_shade || $same_tint)) {
                                        $item_object = returnItem($item['itemid']);
                                        formatItem($userid, $item_object);
                                        $triadCount++;
                                    }
                                }
                            }
                            ?>
                        </div>
                        <div class="followingtabpage" class="matchPage">
                            <?php
                            if (!$userid) {
                                echo "<a href='/index.php'><span class = 'messageGreen'>Login to see matches from those you are following</span></a>";
                            } else {
                                $followingItems = returnAllItemsFromFollowing($userid);
                                for ($i = 0; $i < sizeof($followingItems); $i++) {
                                    $itemColor = $followingItems[$i]['code'];
                                    $closet_same_color1 = hsl_same_color($triadColors[1], $itemColor, $hue_tolerance, $saturation_tolerance, $light_tolerance);
                                    $closet_same_color2 = hsl_same_color($triadColors[2], $itemColor, $hue_tolerance, $saturation_tolerance, $light_tolerance);

                                    if ($closet_same_color1 || $closet_same_color2) {// && ($same_shade || $same_tint)) {
                                        $item_object = returnItem($followingItems[$i]['itemid']);
                                        formatItem($userid, $item_object);
                                        $triadCount++;
                                    }
                                }
                            }
                            ?>
                        </div>
                        <div class="storetabpage" class="matchPage">

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


                                if ($inputColor) {
                                    /// CHANGE 100 TO APPROPRIATE LEVEL BEFORE LAUNCH
                                    /// CASE: The user has given a color/scheme and views items depending on match priority
                                    //  Check if any of the 3 item colors corresponds to and of the 3 scheme colors
                                    //  Separate based on priority
                                    array_push($match_items_order, storeMatch($storeitem['itemid'], $triadColors, $hue_tolerance, $saturation_tolerance, $light_tolerance));
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
                            if ($inputColor) {
                                // sort according to degree of match(priority) if there was a color entered
                                usort($match_items_order, "cmp");
                            }

                            for ($i = 0; $i < count($match_items_order); $i++) {
                                $match_object = $match_items_order[$i];
                                if ($match_object->url && $match_object->priority > 0) {
/////////////////////////////// STORE ITEM HAS A URL//////////////////////////////////
                                    formatStoreItem($match_object);
                                    $triadCount++;
                                }
                            }
                            ?>

                        </div>
                    </div>












                    <div id="shadeMatches" class="schemeMatches">
                        <div class="closettabpage" class="matchPage">
                            <?php
                            if (!$userid) {
                                echo "<a href='/index.php'><span class = 'messageGreen'>Login to see matches from your Closet</span></a>";
                            } else {

                                $itemQuery = database_query("item", "userid", $userid);

                                while ($item = mysql_fetch_array($itemQuery)) {
                                    $itemColor = $item['code'];
                                    $closet_same_color1 = hsl_same_color($shadeColors[1], $itemColor, $hue_tolerance, $saturation_tolerance, $light_tolerance);
                                    $closet_same_color2 = hsl_same_color($shadeColors[2], $itemColor, $hue_tolerance, $saturation_tolerance, $light_tolerance);

                                    if ($closet_same_color1 || $closet_same_color2) {// && ($same_shade || $same_tint)) {
                                        $item_object = returnItem($item['itemid']);
                                        formatItem($userid, $item_object);
                                        $shadeCount++;
                                    }
                                }
                            }
                            ?>
                        </div>
                        <div class="followingtabpage" class="matchPage">
                            <?php
                            if (!$userid) {
                                echo "<a href='/index.php'><span class = 'messageGreen'>Login to see matches from those you are following</span></a>";
                            } else {
                                $followingItems = returnAllItemsFromFollowing($userid);
                                for ($i = 0; $i < sizeof($followingItems); $i++) {
                                    $itemColor = $followingItems[$i]['code'];
                                    $closet_same_color1 = hsl_same_color($shadeColors[1], $itemColor, $hue_tolerance, $saturation_tolerance, $light_tolerance);
                                    $closet_same_color2 = hsl_same_color($shadeColors[2], $itemColor, $hue_tolerance, $saturation_tolerance, $light_tolerance);

                                    if ($closet_same_color1 || $closet_same_color2) {// && ($same_shade || $same_tint)) {
                                        $item_object = returnItem($followingItems[$i]['itemid']);
                                        formatItem($userid, $item_object);
                                        $shadeCount++;
                                    }
                                }
                            }
                            ?>
                        </div>
                        <div class="storetabpage" class="matchPage">

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


                                if ($inputColor) {
                                    /// CHANGE 100 TO APPROPRIATE LEVEL BEFORE LAUNCH
                                    /// CASE: The user has given a color/scheme and views items depending on match priority
                                    //  Check if any of the 3 item colors corresponds to and of the 3 scheme colors
                                    //  Separate based on priority
                                    array_push($match_items_order, storeMatch($storeitem['itemid'], $shadeColors, $hue_tolerance, $saturation_tolerance, $light_tolerance));
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
                            if ($inputColor) {
                                // sort according to degree of match(priority) if there was a color entered
                                usort($match_items_order, "cmp");
                            }

                            for ($i = 0; $i < count($match_items_order); $i++) {
                                $match_object = $match_items_order[$i];
                                if ($match_object->url && $match_object->priority > 0) {
/////////////////////////////// STORE ITEM HAS A URL//////////////////////////////////
                                    formatStoreItem($match_object);
                                    $shadeCount++;
                                }
                            }
                            ?>

                        </div>
                    </div>









                    <table id="matchpanel">
                        <tr>
                            <td class="hovereffect" id="shadey_scheme" onclick="redirectTo('shade')" onmouseover="showDescription('shadey_scheme')" onmouseout="hideDescription()">
                                <span class="schemeName">BATTISTA (<?php echo $shadeCount; ?>)</span><br/>          
                                <div class="schemeContainer">

                                    <div class="hexLeft"  style="border-right-color: #<?php echo $tints[3]; ?>"></div>
                                    <div class="hexMid"  style="background-color: #<?php echo $tints[3]; ?>"></div>
                                    <div class="hexRight"  style="border-left-color: #<?php echo $tints[3]; ?>"></div>


                                    <div class="hexLeft"  style="border-right-color: #<?php echo $hexcode; ?>"></div>
                                    <div class="hexMid"  style="background-color: #<?php echo $hexcode; ?>"></div>
                                    <div class="hexRight"  style="border-left-color: #<?php echo $hexcode; ?>"></div>


                                    <div class="hexLeft"  style="border-right-color: #<?php echo $shades[3]; ?>"></div>
                                    <div class="hexMid"  style="background-color: #<?php echo $shades[3]; ?>"></div>
                                    <div class="hexRight"  style="border-left-color: #<?php echo $shades[3]; ?>"></div>

                                </div>
                            </td></tr><tr>
                            <td class="hovereffect" id="natural_scheme" onclick="redirectTo('analogous')" onmouseover="showDescription('natural_scheme')" onmouseout="hideDescription()">
                                <span class="schemeName">OSWALD (<?php echo $analCount; ?>)</span><br/>  
                                <div class="schemeContainer">
                                    <div class="hexLeft"  style="border-right-color: #<?php echo $anal1; ?>"></div>
                                    <div class="hexMid"  style="background-color: #<?php echo $anal1; ?>"></div>
                                    <div class="hexRight"  style="border-left-color: #<?php echo $anal1; ?>"></div>


                                    <div class="hexLeft"  style="border-right-color: #<?php echo $hexcode; ?>"></div>
                                    <div class="hexMid"  style="background-color: #<?php echo $hexcode; ?>"></div>
                                    <div class="hexRight"  style="border-left-color: #<?php echo $hexcode; ?>"></div>


                                    <div class="hexLeft"  style="border-right-color: #<?php echo $anal2; ?>"></div>
                                    <div class="hexMid"  style="background-color: #<?php echo $anal2; ?>"></div>
                                    <div class="hexRight"  style="border-left-color: #<?php echo $anal2; ?>"></div>
                                </div>
                            </td></tr><tr>


                            <td class="hovereffect" id="standout_scheme" onclick="redirectTo('triad')" onmouseover="showDescription('standout_scheme')" onmouseout="hideDescription()">
                                <span class="schemeName">MUNSELL (<?php echo $triadCount; ?>)</span><br/> 

                                <div class="schemeContainer">

                                    <div class="hexLeft"  style="border-right-color: #<?php echo $triad1; ?>"></div>
                                    <div class="hexMid"  style="background-color: #<?php echo $triad1; ?>"></div>
                                    <div class="hexRight"  style="border-left-color: #<?php echo $triad1; ?>"></div>


                                    <div class="hexLeft"  style="border-right-color: #<?php echo $hexcode; ?>"></div>
                                    <div class="hexMid"  style="background-color: #<?php echo $hexcode; ?>"></div>
                                    <div class="hexRight"  style="border-left-color: #<?php echo $hexcode; ?>"></div>


                                    <div class="hexLeft"  style="border-right-color: #<?php echo $triad2; ?>"></div>
                                    <div class="hexMid"  style="background-color: #<?php echo $triad2; ?>"></div>
                                    <div class="hexRight"  style="border-left-color: #<?php echo $triad2; ?>"></div>

                                </div>
                            </td></tr><tr>
                            <td class="hovereffect" id="complimentary_scheme" onclick="redirectTo('comp')" onmouseover="showDescription('complimentary_scheme')" onmouseout="hideDescription()">
                                <span class="schemeName">VONGOE (<?php echo $compCount; ?>)</span><br/>          
                                <div class="schemeContainer">
                                    <div class="hexLeft"  style="border-right-color: #<?php echo $comp; ?>"></div>
                                    <div class="hexMid"  style="background-color: #<?php echo $comp; ?>"></div>
                                    <div class="hexRight"  style="border-left-color: #<?php echo $comp; ?>"></div>

                                    <div class="hexLeft"  style="border-right-color: #<?php echo $hexcode; ?>"></div>
                                    <div class="hexMid"  style="background-color: #<?php echo $hexcode; ?>"></div>
                                    <div class="hexRight"  style="border-left-color: #<?php echo $hexcode; ?>"></div>

                                    <div class="hexLeft"  style="border-right-color: #<?php echo $comp; ?>"></div>
                                    <div class="hexMid"  style="background-color: #<?php echo $comp; ?>"></div>
                                    <div class="hexRight"  style="border-left-color: #<?php echo $comp; ?>"></div>


                                </div>
                            </td>
                        </tr> 
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
