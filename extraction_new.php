<?php
session_start();
include('connection.php');
include('global_functions.php');
include('database_functions.php');
include('algorithms.php');
include('header.php');
$userid = $_SESSION['userid'];
$photo_file_type = "url";
$photo_url_link = "http://images2.alphacoders.com/673/6730.jpg"; 
$photo_file_imageid = "16";




/*
 * FOR LATER
 * $hexcode_input = $_GET['hexcode'];
  if ($hexcode_input) {

  } */



if ($photo_file_type == "url") {
    $urlname = "http://hueclues.com/extraction_url_processing.php?url=" . $photo_url_link . "&callback=json";
    $url_to_data = file_get_contents($urlname);
    $url_object = json_decode($url_to_data, true);
    $base64_image = $url_object['data'];
    $image_string = file_get_contents($photo_url_link);
    // $image = imagecreatefromstring($image_string);
} elseif ($photo_file_type == "file" && $userid) {
    $image_database = database_fetch("image", "imageid", $photo_file_imageid);
    $image_type = getImagetype($image_database['type']);
    $base64_image = "data:image/" . $image_type . ";base64," . $image_database['data'];
    $image_string = base64_decode($image_database['data']);

    /* if ($image_type == "jpg") {
      $image = imagecreatefromstring($image_string);
      } else if ($image_type == "png") {
      $image_string = 'iVBORw0KGgoAAAANSUhEUgAAABwAAAASCAMAAAB/2U7WAAAABl'
      . 'BMVEUAAAD///+l2Z/dAAAASUlEQVR4XqWQUQoAIAxC2/0vXZDr'
      . 'EX4IJTRkb7lobNUStXsB0jIXIAMSsQnWlsV+wULF4Avk9fLq2r'
      . '8a5HSE35Q3eO2XP1A1wQkZSgETvDtKdQAAAABJRU5ErkJggg==';
      $image = imagepng($image_string, NULL, 9);
      } */
}
$image = imagecreatefromstring($image_string);
$width = imagesx($image);
$height = imagesy($image);
$original_ratio = ($width / $height);
$maxwidth = "400";
$maxheight= "500";
$width_ratio = ($width/$maxwidth);
$height_ratio = ($height/$maxheight);


if($width_ratio>1){
    $width = $width/$width_ratio;
    $height = ($width / $original_ratio);
}
else if ($height_ratio>1){
    $height = $height/$height_ratio;
    $width = $height * $original_ratio;
}

$drawing_height = $maxheight/2 - $height/2;
$drawing_width = $maxwidth/2 - $width/2;
?>





<!DOCTYPE html>
<html>
    <head>
        <title></title>

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <?php initiateTools() ?>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <script type="text/javascript" src="/js/bootstrap.min.js"></script>
        <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="/css/bootstrap-responsive.min.css" />
        <link rel="stylesheet" type="text/css" href="/css/global.css" />
        <script src="http://code.jquery.com/jquery-latest.js" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript">
            
            var img_src = '<?php echo $base64_image; ?>';
            var img = new Image();
            var context = "";
            var xcor ="";
            var ycor ="";
            var hexcode ="";
            var preview ="";
            var xoffset = "";
            var yoffset = "";
            var pagex = "";
            var pagey = "";
            var colorObject;
            var matchhide = "false";
            
            
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


            $(document).ready(function(e){
                $("#canvas").mousemove(function(e){
                    xcor = e.pageX-xoffset;
                    ycor = e.pageY-yoffset;
                    pagex = e.pageX;
                    pagey = e.pageY;
                    previewColor();
                });
                $("#canvas").click(function(){
                    requestMatches($('#inputcode').val());
                    
                });
            }
        );
        
        
        
            function requestMatches(){ // ajax request that gives a hexcode and gets the color theory matches
                data = 'input_color=' + hexcode;
                $.ajax({
                    type: "POST",
                    url: "/extraction_color_processing.php",
                    data: data,
                    success: function(html){
                        colorObject = jQuery.parseJSON(html);
                        $(".consistent").css("background-color", "#"+hexcode);
                        $("#complimentary").css("background-color", "#"+colorObject.complimentary);
                        $("#analogous1").css("background-color", "#"+colorObject.analogous1);
                        $("#analogous2").css("background-color", "#"+colorObject.analogous2);
                        $("#triadic1").css("background-color", "#"+colorObject.triadic1);
                        $("#triadic2").css("background-color", "#"+colorObject.triadic2);
                        $("#split1").css("background-color", "#"+colorObject.split1);
                        $("#split2").css("background-color", "#"+colorObject.split2);
                        $("#shade1").css("background-color", "#"+colorObject.shades[3]);
                        //   $("#shade2").css("background-color", "#"+colorObject.shades[6]);
                        //   $("#tint1").css("background-color", "#"+colorObject.tints[3]);
                        $("#tint2").css("background-color", "#"+colorObject.tints[6]);
                    }
                });
            }
        
            function redirectTo(destination){
                window.location="/match.php?color="+hexcode+"&scheme="+destination;
            }
        
            function RGBtoHex(R,G,B) {return toHex(R)+toHex(G)+toHex(B)}
            
            function toHex(N) {
                if (N==null) return "00";
                N=parseInt(N); if (N==0 || isNaN(N)) return "00";
                N=Math.max(0,N); N=Math.min(N,255); N=Math.round(N);
                return "0123456789ABCDEF".charAt((N-N%16)/16)
                    + "0123456789ABCDEF".charAt(N%16);
            }
            
            function initiateCanvas(){ // initially loads the images and procures the canvas 
                img.src=img_src;
                img.onload = function(){
                    context = document.getElementById('canvas').getContext('2d');
                    context.drawImage(img, <?php echo $drawing_width?>, <?php echo $drawing_height?>, <?php echo $width?>,<?php echo $height?>);
                    getoffsets();
                };
            }
            
            function previewColor(){ // changes the border of the picture to match the pixel the mouse is hovering over
                data = context.getImageData(xcor, ycor, 1, 1).data;
                preview = RGBtoHex(data[0], data[1], data[2]);
                $("#canvas").css("border-color", "#"+preview);
                
            }
            
            function getColor(e){ // used to grab the color of the pixel at the x,y coordinate then plots the previews
                data = context.getImageData(xcor, ycor, 1, 1).data;
                hexcode = RGBtoHex(data[0], data[1], data[2]);
                $("#inputcode").val(hexcode);
                $("#saveform").css("background-color","#"+hexcode);
                $("#saveform_button").css("color","#"+hexcode);
                $('#previewpoint').css('left',pagex-4);
                $('#previewpoint').css('top',pagey-4);
                $('#previewpoint2').css('left',pagex-2);
                $('#previewpoint2').css('top',pagey-2);
            }
            
            
            function getoffsets(){ // determines how far the picture is from the top left corner
                
                if (isChrome){
                    border = parseInt($('#canvas').css("border-width"));
                }
                else{
                    border = 20;
                }
                xoffset = $('#canvas').offset().left + border;
                yoffset = $('#canvas').offset().top + border;
            }
            
            function matchSuppress(){
                if (matchhide == "false"){
                    $('.colorsphere').css("height", "5px");
                    document.getElementById('arrow').src="/img/arrow_up.png";
                    matchhide="true";
                }
                else if(matchhide=="true"){
                    $('.colorsphere').css("height", "80px");
                    document.getElementById('arrow').src="/img/arrow_down.png";
                    matchhide="false";
                }
                        
            }

            function showDescription(id){
                
                
                var prompt=", \nclick to see matches";
                var txt = new Array();
                
                txt["consistent"]="Matches with the same Color" +prompt;
                txt["complimentary"]="Matches the complimentary color, creates maximum contrast, drawing attention" +prompt;
                txt["triadic1"]="This scheme matches your color with 2 nicely balanced colors" +prompt;
                txt["analogous1"]="This family of color schemes often appears in Nature, and is calming to the eye"+prompt ;
                txt["split1"]="This family provides a stable vibrance in these colors"+prompt;
                txt["shade1"]="With varying in amount of black, this produces a dark smooth and unified color scheme"+prompt;
                txt["tint1"]="With varying in amount of white, this produces a bright smooth and unified color scheme"+prompt;

                var x = $("#"+id).position().left;
                $("#description").show();
                $("#description").text(txt[id]);
                $("#description").css('left', x+50);
            }

            function hideDescription(id){
                $("#description").hide();
            }
            
            
          
        </script>
        <style>
            
#canvas{
    border-width:20px;
    border-style: solid;
    display:inline-block;
    margin-left:38px;
    position:absolute;
    left:500px;
    top:200px;
}
#extraction_container{
    top:100px;
    height:820px;
}

#matchpanel{
    position:fixed;
    bottom:-1px;
    width:90%;
    left:5%;
    right:5%;
    z-index:2;
}
#extraction_notification{
    width:162px;
    font-size:15px;
    padding:0px;
    margin:0px;
    position:absolute;

}
#pic{
    position:absolute;
    top:100px;
    left:0px;
    border-width:30px;
    border-style:solid;
}
#previewpoint{
    width:8.5px;
    height:8.5px;
    position:absolute;
    border-radius: 50px 50px 50px;
    border:2px solid white;
    z-index:1;
    bottom:0px;
    left:500px;
}
#previewpoint2{
    width:4.5px;
    height:4.5px;
    position:absolute;
    border-radius: 50px 50px 50px;
    border:2px solid black;
    z-index:1;
    bottom:0px;
    left:500px;
}

#previewform{
    position:relative;
    left:0px;
    top:10px;
}

#description{
    position: fixed;
    bottom: 160px;
    width: 130px;
    display: none;
}

#saveform{
    width:175px;
    height:250px;
    display:inline-block;
    position:absolute;
    left:300px;
    top:200px;
    padding-top:0px;
    padding-right:0px;
    border:none;
    
}
#canvas_background{
    background-color:white;
    opacity:0.7;
    position:absolute;
    height:<?php echo intval($maxheight) + 40?>px;
    width:<?php echo intval($maxwidth) + 40?>px;
    margin-left:38px;
    left:500px;
    top:200px;
    z-index:0;
}

        </style>
    </head>


    <body onload="initiateCanvas()">
        <div id="navigationbar">
            <?php commonHeader(); ?>
        </div>
        <br/><br/><br/>
                    <span class="notification">
                        <?php
                        echoClear('save_notification');
                        ?>
                    </span>
        <div id="extraction_container">
                    <div class="well form-vertical" id="saveform">
                        <form method="POST" action="/saveitem_processing.php" id="chosen_color_form" style="display:inline;">
                            <input type="hidden" name="from_facebook" value="<?php echo $_GET['from_facebook']; ?>" />
                            <input type="hidden" name="photo_type" value="<?php echo $photo_file_type ?>" />
                            <input type="hidden" name="photo_url" value="<?php echo $photo_url_link ?>"/>
                            <input type="hidden" name="photo_imageid" value="<?php echo $photo_file_imageid ?>"/>
                            <?php // hide the hexbox as suggested by Danny?>
                            <input type="hidden" value="<?php echo $input_color ?>" style="height:50px;width:145px;font-size:18px;" name ="code" id="inputcode" placeholder="  Hexcode"/>
                            <br/>
                            <input type="text" value="" name="description" id="itemdescription"  style="margin-top:10px;width:145px;" placeholder="Describe This Item"/>

                            <input type="submit" id="saveform_button" value="Save To My Trunk" class="btn" style="height:40px;margin-top:8px;width:160px;padding:5px;"/>
                

                        </form>
                    </div>


                    <?php
                    if (!$image_string) {
                        echo "<span class='alert alert-error'>We couldn't find your image sorry, <a href='/history.php'>try again</a></span>";
                    }
                    ?>
            <div id="canvas_background"></div>
                    <canvas id="canvas" width="<?php echo $maxwidth?>" height="<?php echo $maxheight?>" onclick="getColor(event)">
                        Your Browser Does not support HTML 5
                    </canvas>


                    <div class="well theme_description" id="description">
                    </div>

            
                    <table id="matchpanel">
                        <tr>
                        <td class="well hovereffect" onclick="redirectTo('analogous')" onmouseover="showDescription('analogous1')" onmouseout="hideDescription()">
                            <span style="color:#004600;">NATURAL</span><br/>                            <div class="colorsphere front_sphere" id="analogous1"></div>
                            <div class="colorsphere consistent middle_sphere" ></div>
                            <div class="colorsphere back_sphere" id="analogous2"></div>
                        </td>

                        <td class="well hovereffect" onclick="redirectTo('comp')" onmouseover="showDescription('complimentary')" onmouseout="hideDescription()">
                            <span style="color:#004600;">COMPLIMENTARY</span><br/>                  <div class="colorsphere consistent front_sphere" ></div>
                            <div class="colorsphere back_sphere" id="complimentary" ></div>
                        </td>

                        <td class="well hovereffect" onclick="redirectTo('triad')" onmouseover="showDescription('triadic1')" onmouseout="hideDescription()">
                            <span style="color:#004600;">STANDOUT</span><br/>            <div class="colorsphere front_sphere" id="triadic1"> </div>
                            <div class="colorsphere consistent middle_sphere" ></div>
                            <div class="colorsphere back_sphere" id="triadic2"></div>
                        </td>


                        <td class="well hovereffect" onclick="redirectTo('shade')" onmouseover="showDescription('shade1')" onmouseout="hideDescription()">
                            <span style="color:#004600;">SHADE-Y</span><br/>          <div class="colorsphere front_sphere" id="tint2"></div>
                            <div class="colorsphere consistent middle_sphere" ></div>
                            <div class="colorsphere back_sphere" id="shade1" ></div>
                        </td>


                        <td class="well hovereffect" onclick="matchSuppress();" style="width:30px; padding-left:25px;">
                            <img id="arrow" src="/img/arrow_down.png" width="20" height="20" style="z-index:3;">
                        </td><br/><br/><br/>
                        </tr> 
                    </table>
            
        </div>
        <div id="previewpoint" >
        </div>
        <div id="previewpoint2" >
        </div>
    </body>
</html>
