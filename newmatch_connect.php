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

/*
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

 * 
 * 
 * 
 */
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
                $("#itemSort").fadeIn();
                $('.schemeMatches').fadeOut();
                $('#' + scheme + "Matches").fadeIn();
            }
        </script>
        <style>
            .schemeMatches{
                display:none;
            }
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
                <div id="itemSort">
                    <input type='text' id='filterInput' placeholder="(Sort by keyword) i.e pockets"></input>
                    <br/>
                    <div id="compMatches" class="schemeMatches">
                        <div class="closettabpage">
                         <?php 
                         $matchingItems = returnAllMatchingItems($userid, $itemid);
                         print_r($matchingItems);
                         for($i=0;$i<count($matchingItems);$i++){
                             
                         }
                         ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <table id="matchpanel">
            <tr class="matchSchemeColumn">
                <td class="hovereffect" id="shadey_scheme" onclick="changeScheme('shade')" onmouseover="showDescription('shadey_scheme')" onmouseout="hideDescription()">
                    <span class="schemeName">BATTISTA (<?php echo $shadeCount; ?>)</span><br/>          
                    <div class="schemeContainer">

                        <div class="hexLeft"  style="border-right-color: #<?php echo $shadeColors[1]; ?>"></div>
                        <div class="hexMid"  style="background-color: #<?php echo $shadeColors[1]; ?>"></div>
                        <div class="hexRight"  style="border-left-color: #<?php echo $shadeColors[1]; ?>"></div>


                        <div class="hexLeft"  style="border-right-color: #<?php echo $inputColor; ?>"></div>
                        <div class="hexMid"  style="background-color: #<?php echo $inputColor; ?>"></div>
                        <div class="hexRight"  style="border-left-color: #<?php echo $inputColor; ?>"></div>


                        <div class="hexLeft"  style="border-right-color: #<?php echo $shadeColors[2]; ?>"></div>
                        <div class="hexMid"  style="background-color: #<?php echo $shadeColors[2]; ?>"></div>
                        <div class="hexRight"  style="border-left-color: #<?php echo $shadeColors[2]; ?>"></div>

                    </div>
                </td> <div class="schemePreview">

            </div>
        </tr>
        <tr class="matchSchemeColumn">
            <td class="hovereffect" id="natural_scheme" onclick="changeScheme('ana')" onmouseover="showDescription('natural_scheme')" onmouseout="hideDescription()">
                <span class="schemeName">OSWALD (<?php echo $anaCount; ?>)</span><br/>  
                <div class="schemeContainer">
                    <div class="hexLeft"  style="border-right-color: #<?php echo $anaColors[1]; ?>"></div>
                    <div class="hexMid"  style="background-color: #<?php echo $anaColors[1]; ?>"></div>
                    <div class="hexRight"  style="border-left-color: #<?php echo $anaColors[1]; ?>"></div>


                    <div class="hexLeft"  style="border-right-color: #<?php echo $inputColor; ?>"></div>
                    <div class="hexMid"  style="background-color: #<?php echo $inputColor; ?>"></div>
                    <div class="hexRight"  style="border-left-color: #<?php echo $inputColor; ?>"></div>


                    <div class="hexLeft"  style="border-right-color: #<?php echo $anaColors[2]; ?>"></div>
                    <div class="hexMid"  style="background-color: #<?php echo $anaColors[2]; ?>"></div>
                    <div class="hexRight"  style="border-left-color: #<?php echo $anaColors[2]; ?>"></div>
                </div>
            </td>
        <div class="schemePreview">

        </div>
    </tr>
    <tr class="matchSchemeColumn">
        <td class="hovereffect" id="standout_scheme" onclick="changeScheme('triad')" onmouseover="showDescription('standout_scheme')" onmouseout="hideDescription()">
            <span class="schemeName">MUNSELL (<?php echo $triadCount; ?>)</span><br/> 

            <div class="schemeContainer">

                <div class="hexLeft"  style="border-right-color: #<?php echo $triadColors[1]; ?>"></div>
                <div class="hexMid"  style="background-color: #<?php echo $triadColors[1]; ?>"></div>
                <div class="hexRight"  style="border-left-color: #<?php echo $triadColors[1]; ?>"></div>


                <div class="hexLeft"  style="border-right-color: #<?php echo $inputColor; ?>"></div>
                <div class="hexMid"  style="background-color: #<?php echo $inputColor; ?>"></div>
                <div class="hexRight"  style="border-left-color: #<?php echo $inputColor; ?>"></div>


                <div class="hexLeft"  style="border-right-color: #<?php echo $triadColors[2]; ?>"></div>
                <div class="hexMid"  style="background-color: #<?php echo $triadColors[2]; ?>"></div>
                <div class="hexRight"  style="border-left-color: #<?php echo $triadColors[2]; ?>"></div>

            </div>
        </td>
    <div class="schemePreview">
    </div>
</tr>
<tr class="matchSchemeColumn">
    <td class="hovereffect" id="complimentary_scheme" onclick="changeScheme('comp')" onmouseover="showDescription('complimentary_scheme')" onmouseout="hideDescription()">
        <span class="schemeName">VONGOE (<?php echo $compCount; ?>)</span><br/>          
        <div class="schemeContainer">
            <div class="hexLeft"  style="border-right-color: #<?php echo $compColors[1]; ?>"></div>
            <div class="hexMid"  style="background-color: #<?php echo $compColors[1]; ?>"></div>
            <div class="hexRight"  style="border-left-color: #<?php echo $compColors[1]; ?>"></div>

            <div class="hexLeft"  style="border-right-color: #<?php echo $inputColor; ?>"></div>
            <div class="hexMid"  style="background-color: #<?php echo $inputColor; ?>"></div>
            <div class="hexRight"  style="border-left-color: #<?php echo $inputColor; ?>"></div>

            <div class="hexLeft"  style="border-right-color: #<?php echo $compColors[1]; ?>"></div>
            <div class="hexMid"  style="background-color: #<?php echo $compColors[1]; ?>"></div>
            <div class="hexRight"  style="border-left-color: #<?php echo $compColors[1]; ?>"></div>
        </div>
    </td>
<div class="schemePreview">

</div>
</tr> 
</table>
</body>
</html>
