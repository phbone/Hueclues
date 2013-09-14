<?php
session_start();
include('connection.php');
include('global_tools.php');
include('global_objects.php');
include('database_functions.php');
include('algorithms.php');
$userid = $_SESSION['userid'];
$itemid = $_GET['itemid'];

$shade_count = 5;
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
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0;" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <link rel="apple-touch-icon" href="icon.png"/>
        <link rel="stylesheet" href="/css/mobile.css">
        <link rel="stylesheet" href="/css/global.css">
        <script src="/js/global_javascript.js" type="text/javascript" charset="utf-8" ></script>
        <script type="text/javascript">
            
            var img_url = '<?php echo $item_object->image_link; ?>';
            var img = new Image();
            var hexcode ="<?php echo $hexcode ?>";
            var preview ="";
            var colorObject;
            var matchhide = "false";
            var defaultText = "choose the way you want to look!";
            
            $(document).ready(function(e){
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

           

            function redirectTo(destination){
                window.location="/match?color="+hexcode+"&scheme="+destination+"&image="+img_url;
            }
            function showDescription(id){
                var prompt=", \nclick to see matches";
                var txt = new Array();
                var color = new Array();
                txt["natural_scheme"]="these colors appear together in nature, creating a harmonized looked that will absolutely charm others!" ;
                txt["complimentary_scheme"]="these colors produce the most contrast, which attracts attention on even the first glance!";
                txt["standout_scheme"]="the well balanced colors of this scheme keeps peoples' eyes glued to you";
                txt["shadey_scheme"]="combining shades of one color unify your look as a whole, pretty cool right?";
                color["natural_scheme0"] = "#<?php echo $anal1 ?>";
                color["natural_scheme1"] = "#<?php echo $hexcode ?>";
                color["natural_scheme2"] = "#<?php echo $anal2 ?>";
                color["complimentary_scheme0"] = "#<?php echo $comp ?>";
                color["complimentary_scheme1"] = "#";
                color["complimentary_scheme2"] = "#<?php echo $hexcode ?>";
                color["standout_scheme0"] = "#<?php echo $triad1 ?>";
                color["standout_scheme1"] = "#<?php echo $hexcode ?>";
                color["standout_scheme2"] = "#<?php echo $triad2; ?>";
                color["shadey_scheme0"] = "#<?php echo $tints[3]; ?>";
                color["shadey_scheme1"] = "#<?php echo $hexcode; ?>";
                color["shadey_scheme2"] = "#<?php echo $shades[3]; ?>";
                
                $("#description").text(txt[id]);
                $("#desc_color1").css("background-color", color[id+0]);
                $("#desc_color2").css("background-color", color[id+1]);
                $("#desc_color3").css("background-color", color[id+2]);
            }

            function hideDescription(id){
                $("#description").text(defaultText);
                $("#desc_color1").css("background-color", "");
                $("#desc_color2").css("background-color", "");
                $("#desc_color3").css("background-color", "");
            }
            
            
          
        </script>
        <style>
            #item_container{
                background-color: #<?php echo $hexcode; ?>;
                opacity: 0.7;
                width:95%;
                position:absolute;
                max-height:201px;
                vertical-align:top;
            }
            #descriptionContainer{
                background-color: white;
                opacity:0.7;
                width:95%;
                top:350px;
                position:absolute;
                height:150px;
                display:inline-block;
            }
            #description{
                color:black;
                position:absolute;
                font-size:15px;
                padding:20px;
            }
            #hueContainer{
                width:95%;
                position:relative;
                top:50px;
                margin:5px;
                margin-bottom:50px;
            }#desc_color_holder{
                margin:auto;
                position:absolute;
                top:20px;
                width:230px;
                right:223px;
            }




            .hexLeft, .hexRight{
                border-top: 15px solid transparent;
                border-bottom: 15px solid transparent;
                float: left;
                opacity:0.85;
            }
            .hexLeft{
                border-right: 7.5px solid;
                margin-left:-5px;
            }
            .hexRight{
                border-left: 7.5px solid;
                margin-right:-5px;
            }
            .hexMid{
                opacity:0.85;
                float: left;
                width: 15px;
                height: 30px;
                background-color:black;
            }




            .schemeContainer{
                left:5px;
                position:relative;
            }
            .colorBar{
                height:300px;
                width:60px;
                padding:1px;
                display:inline-block;
            }
            .schemeName{
                text-indent: 5px;
                color:#51BB75;
                font-size:14px;
                font-family:"Century Gothic";
            }
            .hovereffect{
                background-color:white;
                opacity:0.7;
                border: none;
                padding:0px 5px;
                text-align: center;
                border-top:3px white solid;
            }
            .hovereffect:hover{
                padding:0px 5px;
                background-color:white;
                cursor: pointer;
                opacity:1;
            }
        </style>
    </head>

    <body>

        <?php commonHeader() ?>
        <div id="mobileContainer">
                <div id="desc_color_holder">
                    <div class="colorBar" id="desc_color1"></div>
                    <div class="colorBar" id="desc_color3"></div>
                </div>
                <?php
                if ($itemid) {
                    formatItem($userid, $item_object);
                } else {
                    echo "<a href='/closet' style='color::#6BB159;font-size:35px;font-weight:400px;background-color:white;padding:12px;'><i class='icon-eye-open'></i>Select an Item</a>";
                }
                ?>

            <table style="width:100%">
                <tr>
                    <td class="hovereffect" id="shadey_scheme" onclick="redirectTo('shade')" onmouseover="showDescription('shadey_scheme')" onmouseout="hideDescription()">
                        <span class="schemeName">BATTISTA</span><br/>          
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
                    </td>
                    <td class="hovereffect" id="natural_scheme" onclick="redirectTo('analogous')" onmouseover="showDescription('natural_scheme')" onmouseout="hideDescription()">
                        <span class="schemeName">OSWALD</span><br/>  
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
                    </td>



                    <td class="hovereffect" id="standout_scheme" onclick="redirectTo('triad')" onmouseover="showDescription('standout_scheme')" onmouseout="hideDescription()">
                        <span class="schemeName">MUNSELL</span><br/> 
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
                    </td>

                    <td class="hovereffect" id="complimentary_scheme" onclick="redirectTo('comp')" onmouseover="showDescription('complimentary_scheme')" onmouseout="hideDescription()">
                        <span class="schemeName">VONGOE</span><br/>                  

                        <div class="schemeContainer" style="left:10px;">
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
    </body>
</html>
