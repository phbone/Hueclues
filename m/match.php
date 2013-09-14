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
$saturation_tolerance = 50;
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
    $color_count = 1;
    $scheme_colors[0] = $input_color;
    $scheme_colors[1] = hsl_complimentary($input_color);
} else if ($scheme == "same") {
    $color_count = 1;
    $scheme_colors[0] = $input_color;
    $scheme_colors[1] = $input_color;
} else if ($scheme == "triad") {

    $color_count = 2;
    $scheme_colors[0] = $input_color;
    $scheme_colors[1] = hsl_triadic1($input_color);
    $scheme_colors[2] = hsl_triadic2($input_color);
} else if ($scheme == "analogous") {

    $color_count = 2;
    $scheme_colors[0] = $input_color;
    $scheme_colors[1] = hsl_analogous1($input_color);
    $scheme_colors[2] = hsl_analogous2($input_color);
} else if ($scheme == "split") {

    $color_count = 2;
    $scheme_colors[0] = $input_color;
    $scheme_colors[1] = hsl_split1($input_color);
    $scheme_colors[2] = hsl_split2($input_color);
} else if ($scheme == "shade") {

    $color_count = $shade_count;
    $scheme_colors = hsl_shades($input_color, $shade_count);
    $scheme_colors[0] = $input_color;
} else if ($scheme == "tint") {

    $color_count = $shade_count;
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
        <meta name="apple-mobile-web-app-status-bar-style" content="black" />
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0;" />
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <link rel="apple-touch-icon" href="icon.png"/>
        <link rel="stylesheet" href="/css/mobile.css">
        <link rel="stylesheet" href="/css/global.css">
        <script src="/js/global_javascript.js" type="text/javascript" charset="utf-8" ></script>
        <script type="text/javascript">   
            var userid='<?php echo $userid ?>';
            $(document).ready(function(e){
                bindActions();
                flipTab('closettab');
                genderFilter(2);
                enableSelectBoxes();
                $(".selected").html("Filter By:");
            });
   
            function flipTab(id){
                $('#followingtab').removeClass('active');
                $('#closettab').removeClass('active');
                $('#storetab').removeClass('active');
                $('#'+id).addClass('active');
                $('.matchPage').hide();
                $('#'+id+'page').fadeIn();
            }
    
        
            function genderFilter(gender){
                // gender:
                // 0 = female
                // 1 = male
                // 2 = unisex
                if(gender==0){
                    $(".1").slideUp();
                    $(".0").slideDown();
                }
                else if (gender==1){
                    $(".0").slideUp();
                    $(".1").slideDown();
                }
                else if(gender==2){
                    $(".1").slideDown();
                    $(".0").slideDown();
                }
            }
        </script>
        <style>

            /*///////////////////////////////////////////////////////////////////////////////////////
            /////////////////////////////////////////////////////////////////////////////////////////
                                MATCH PAGE CSS - match.php
            /////////////////////////////////////////////////////////////////////////////////////////
            ///////////////////////////////////////////////////////////////////////////////////////*/


            .schemeBox{
                background-color:#fbfbfb;
                opacity:0.75;
                bottom:0px;
                top:0px;
                padding:0px;
                height:20px;;
                min-width:50px;
                left:0px;
                z-index:2;
                position:fixed;
            }
            #itemPicture{
                max-width:50px;
                left:0px;
                top:25px;
                position:fixed;
                z-index:2;
            }
            #item_display{
                width:60%;
                position:relative;
                margin:auto;
                left:120px;
            }
            .matchButtons{
                width:230px;
                margin:auto;
                margin-top:0px;
            }

            #matchHeading{
                color:#808285;
                font-size:40px;
                font-family:"Century Gothic";
                top:75px;
                left:141px;
                position:absolute;
            }

            .matchTab{
                list-style: none;
                background-color:white;
                display:inline-block;
                border-radius:5px;
                width:230px;
                text-decoration:none;
                text-align:center;
                font-family:"Century Gothic";
                border:1px ridge transparent;
                padding:5px;
            }
            .matchTab:hover{
                cursor:pointer;
            }
            .active{
                background-color:#51BB75;
                color:white;
            }
            .matchPage{
                background:url('/img/bg.png');
                opacity:0.9;
                min-height:435px;
                height:100%;
                margin-top:50px;
                padding: 15px 20px;
                border-radius:3px;
            }

            .storeMatch{
                opacity:0.85;
                border:none;
                display:block;
                width:260px;
                margin:70px auto;
                margin-bottom:110px;
                position:relative;
            }
            .storeBar1, .storeBar2, .storeBar3{
                border-radius:2px;
                margin:0px;
                position:absolute;
                display:inline-block;
                height:93%;
                width:30px;
            }
            .storeBar1{
                left:160px;
            }
            .storeBar2{
                left:195px;
            }
            .storeBar3{
                left:230px;
            }
            .storeTitle{
                left:0px;
                font-size:12px;
                top:-40px;
                line-height:1.5;
                width:100%;
                position:absolute;
            }
            .storeUrl{
                text-decoration:none;
                color:white;
            }
            .storeLink{
                width:240px;
                bottom:-16px;
                position:absolute;
                padding:10px;
                text-align:center;
                background-color:#51BB75;
            }
            .storeLink:hover{
                cursor:pointer;
                background-color:#58595B
            }
            .storePercent{
                background-color:#51BB75;
                color:white;
                padding:5px;
            }
            .messageGreen{
                font-family:"Quicksand";
                text-decoration:none;
                font-size:20px;
                color: #51BB75;
                border-radius:3px;
                text-align:center;
                padding:10px;
            }
            .messageGreen:hover{
                cursor:pointer;
                text-decoration:underline;
            }
            .hexLeft, .hexRight{
                border-top: 10px solid transparent;
                border-bottom: 10px solid transparent;
                float: left;
                opacity:0.85;
            }
            .hexLeft{
                border-right: 5px solid;
                margin-left:-2.5px;
            }
            .hexRight{
                border-left: 5px solid;
                margin-right:-2.5px;
            }
            .hexMid{
                opacity:0.85;
                float: left;
                width: 10px;
                height: 20px;
                background-color:black;
            }
        </style>
    </head>
    <body>
        <?php commonHeader() ?>
        <div id="mobileContainer"  id="item_display">
            <div class="schemeBox">
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
            <img id="itemPicture" src="<?php echo $extraction_image; ?>" />




            <ul class="matchButtons">
                <li id='closettab' class="matchTab" onclick="flipTab('closettab')">
                    MY CLOSET
                </li><br/><br/>
                <li id='followingtab' class="matchTab" onclick="flipTab('followingtab')">
                    FOLLOWED CLOSETS
                </li><br/><br/>
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
                    $item_query = database_query("item", "userid", $userid);
                    while ($item = mysql_fetch_array($item_query)) {
                        $description = $item['description'];
                        $saved_color = $item['code'];
                        $closet_same_color1 = hsl_same_color($scheme_colors[1], $saved_color, $hue_tolerance, $saturation_tolerance, $light_tolerance);
                        $closet_same_color2 = hsl_same_color($scheme_colors[2], $saved_color, $hue_tolerance, $saturation_tolerance, $light_tolerance);

                        if ($closet_same_color1 || $closet_same_color2) {// && ($same_shade || $same_tint)) {
                            $item_object = returnItem($item['itemid']);
                            formatItem($userid, $item_object);
                        }
                    }
                    echo "<a href='/extraction' style='text-decoration:none'><div class='messageGreen'>Find more matches by adding items to your Closet</div></a><br/><br/>";
                }
                ?>
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
                    echo "<a href='/hive' style='text-decoration:none'><div class='messageGreen'>Follow more closets to see more matches</div></a><br/><br/>";
                }
                ?>
            </div>
            <div id="storetabpage" class="matchPage">
                <div class='selectBox' style="width:135px;top:-70px;margin:auto;display:block;">
                    <span class='selected' style="width:100px;text-indent:10px;">Filter By:</span>
                    <span class='selectArrow'><i class="icon-chevron-down"></i></span>
                    <div class="selectOptions" style="width:100px;">
                        <span class="selectOption" id="noFilter" onclick = "genderFilter(2)">None</span>
                        <span class="selectOption" id="womenFilter" onclick = "genderFilter(0)">Women</span>
                        <span class="selectOption" id="menFilter" onclick = "genderFilter(1)">Men</span>
                    </div>
                </div>

                <div class="historypanel">
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
<img alt='  This Image Is Broken' src='" . $match_object->url . "' class='fixedwidththumb thumbnaileffect' style='width:155px' /><br/><br/>
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
    </body>
</html>
