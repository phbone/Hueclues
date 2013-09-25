<?php
session_start();
include('connection.php');
include('global_tools.php');
include('global_objects.php');
include('database_functions.php');
include('algorithms.php');
include('header.php');
$userid = $_SESSION['userid'];
$itemid = $_GET['itemid'];


$saturation_tolerance = 100;
$light_tolerance = 100;
$hue_tolerance = 8.33;
$shade_count = 10;

$item_object = returnItem($itemid);
$hexcode = $item_object->hexcode;
$comp = hsl_complimentary($hexcode);
$shades = hsl_shades($hexcode, $shade_count);
$tints = hsl_tints($hexcode, $shade_count);
$triad1 = hsl_triadic1($hexcode);
$triad2 = hsl_triadic2($hexcode);
$anal1 = hsl_analogous1($hexcode);
$anal2 = hsl_analogous2($hexcode);
$split1 = hsl_split1($hexcode);
$split2 = hsl_split2($hexcode);


$followingItemColorArray = returnAllItemsFromFollowing($userid, "code");
$item = database_fetch("item", "itemid", $itemid);
$inputColor = $item['code'];
$compCount = 0;
$analCount = 0;
$splitCount = 0;
$triadCount = 0;
$shadeCount = 0;

for ($i = 0; $i < sizeof($followingItemColorArray); $i++) {
    if (hsl_is_analogous($inputColor, $followingItemColorArray[$i], $hue_tolerance, $saturation_tolerance, $light_tolerance)) {
        $analCount++;
    }
    if (hsl_is_complimentary($inputColor, $followingItemColorArray[$i], $hue_tolerance, $saturation_tolerance, $light_tolerance)) {
        $compCount++;
    }
    if (hsl_is_split($inputColor, $followingItemColorArray[$i], $hue_tolerance, $saturation_tolerance, $light_tolerance)) {
        $splitCount++;
    }
    if (hsl_is_triadic($inputColor, $followingItemColorArray[$i], $hue_tolerance, $saturation_tolerance, $light_tolerance)) {
        $triadCount++;
    }
    // for shade
    if (hsl_same_hue($inputColor, $followingItemColorArray[$i], $hue_tolerance, $saturation_tolerance, $light_tolerance)) {
        $shadeCount++;
    }
}

$storeQuery = database_query("storeitem");
$storeCodes1 = Array();
$storeCodes2 = Array();
$storeCodes3 = Array();
while ($storeitem = mysql_fetch_array($storeQuery)) {
    $storeCodes1[] = $storeitem['code1'];
    $storeCodes2[] = $storeitem['code2'];
    $storeCodes3[] = $storeitem['code3'];
}
// now $storeArray will have all the names.
// now $storeArray will have all the names.
?>

<!DOCTYPE html>
<html>
    <head>
        <?php initiateTools() ?>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <script type="text/javascript" src="/js/global_javascript.js" ></script>
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <link rel="stylesheet" type="text/css" href="/css/global.css" />

        <link rel="stylesheet" type="text/css" href="/css/hue.css" />
        <script type="text/javascript">

<?php initiateTypeahead(); ?>
            var img_url = '<?php echo $item_object->image_link; ?>';
            var img = new Image();
            var hexcode = "<?php echo $hexcode ?>";
            var preview = "";
            var colorObject;
            var matchhide = "false";
            var defaultText = "";

            $(document).ready(function(e) {

                bindActions();
            });


            ////////////////////////////////////////GETS BROWSER TYPE//////////////////////////////////////////
            var isOpera = !!(window.opera && window.opera.version);  // Opera 8.0+
            var isFirefox = testCSS('MozBoxSizing');                 // FF 0.8+
            var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
            // At least Safari 3+: "[object HTMLElementConstructor]"
            var isChrome = !isSafari && testCSS('WebkitTransform');  // Chrome 1+
            var isIE = /*@cc_on!@*/false || testCSS('msTransform');  // At least IE6

            function testCSS(prop) {
                return prop in document.documentElement.style;
            }
            ////////////////////////////////////////GETS BROWSER TYPE//////////////////////////////////////////



            function redirectTo(destination) {
                window.location = "/match?color=" + hexcode + "&scheme=" + destination + "&image=" + img_url;
            }
            function showDescription(id) {
                var prompt = ", \nclick to see matches";
                var txt = new Array();
                var color = new Array();
                txt["natural_scheme"] = "these colors appear together in nature, creating a harmonized looked that will absolutely charm others!";
                txt["complimentary_scheme"] = "these colors produce the most contrast, which attracts attention on even the first glance!";
                txt["standout_scheme"] = "the well balanced colors of this scheme keeps peoples' eyes glued to you";
                txt["shadey_scheme"] = "combining shades of one color unify your look as a whole, pretty cool right?";
                color["natural_scheme0"] = "#<?php echo $anal1 ?>";
                color["natural_scheme1"] = "#<?php echo $hexcode ?>";
                color["natural_scheme2"] = "#<?php echo $anal2 ?>";
                color["complimentary_scheme0"] = "#<?php echo $comp ?>";
                color["complimentary_scheme1"] = "#";
                color["complimentary_scheme2"] = "#";
                color["standout_scheme0"] = "#<?php echo $triad1 ?>";
                color["standout_scheme1"] = "#<?php echo $hexcode ?>";
                color["standout_scheme2"] = "#<?php echo $triad2; ?>";
                color["shadey_scheme0"] = "#<?php echo $tints[3]; ?>";
                color["shadey_scheme1"] = "#<?php echo $hexcode; ?>";
                color["shadey_scheme2"] = "#<?php echo $shades[3]; ?>";

                var bar_height = $('.itemContainer').height();
                $('.colorBar').css('height', bar_height);
                $("html, body").animate({scrollTop: $(document).height()}, 1000);

                $("#description").text(txt[id]);
                $("#desc_color1").css("background-color", color[id + 0]);
                $("#desc_color2").css("background-color", color[id + 1]);
                $("#desc_color3").css("background-color", color[id + 2]);


            }

            function hideDescription(id) {
                $("#description").text(defaultText);
                $("#desc_color1").css("background-color", "");
                $("#desc_color2").css("background-color", "");
                $("#desc_color3").css("background-color", "");
            }



        </script>
    </head>

    <body>
        <img src="/img/loading.gif" id="loading"/>
        <?php commonHeader(); ?>
        <div id="mainContainer">
            <div id="whiteBackground"> 
                <span id="hueHeading">
                    CHOOSE YOUR HUES
                </span>
                <div id="desc_color_holder">
                    <div class="colorBar" id="desc_color1"></div>
                    <div class="colorBar" id="desc_color3"></div>
                </div>
                <div style='right:-200px;position:relative;'>           
                    <?php
                    if ($itemid) {
                        formatItem($userid, $item_object);
                    } else {
                        echo "<a href='/closet' style='color::#6BB159;font-size:35px;font-weight:400px;background-color:white;padding:12px;'><i class='icon-eye-open'></i>Select an Item</a>";
                    }
                    ?>  
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
    </body>
</html>
