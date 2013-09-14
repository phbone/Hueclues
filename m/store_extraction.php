<?php
session_start();
include('connection.php');
include('global_functions.php');
include('database_functions.php');
include('algorithms.php');
include('header.php');
$storeid = $_SESSION['storeid'];
$photo_file_type = $_GET['photo_type'];
$photo_url_link = $_GET['photo_url'];
$photo_file_imageid = $_GET['photo_imageid'];
$store_itemid = $_GET['itemid']; // if this is updating an existing store item, this is the itemid
// there only exists itemid if this is a previously saved item being updated

if ($photo_file_type == "url") {
    $urlname = "http://hueclues.com/extraction_url_processing.php?url=" . $photo_url_link . "&callback=json";
    $url_to_data = file_get_contents($urlname);
    $url_object = json_decode($url_to_data, true);
    $base64_image = $url_object['data'];
    $image_string = file_get_contents($photo_url_link);
    $url = database_fetch("storeurl", "url", $photo_url_link);
    $item = database_fetch("storeitem", "urlid", $url['urlid']);
} elseif ($photo_file_type == "file" && $storeid) {
    $image_database = database_fetch("storeimage", "imageid", $photo_file_imageid);
    $image_type = getImagetype($image_database['type']);
    $base64_image = "data:image/" . $image_type . ";base64," . $image_database['data'];
    $image_string = base64_decode($image_database['data']);
    $item = database_fetch("storeitem", "imageid", $photo_file_imageid);
}

$image = imagecreatefromstring($image_string);
$width = imagesx($image);
$height = imagesy($image);

$original_ratio = ($width / $height);
$maxwidth = "400";
$maxheight = "500";
$width_ratio = ($width / $maxwidth);
$height_ratio = ($height / $maxheight);


if ($width_ratio > 1) {
    $width = $width / $width_ratio;
    $height = ($width / $original_ratio);
} else if ($height_ratio > 1) {
    $height = $height / $height_ratio;
    $width = $height * $original_ratio;
}

$drawing_height = $maxheight / 2 - $height / 2;
$drawing_width = $maxwidth / 2 - $width / 2;
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
            var current_hex = "1";
            
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
            }
        );
        
        
        
           
        
        
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
                    context.drawImage(img, <?php echo $drawing_width ?>, <?php echo $drawing_height ?>, <?php echo $width ?>,<?php echo $height ?>);
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
                $("#inputcode"+current_hex).val(hexcode);
                $('#previewpointi'+current_hex).css('left',pagex-4);
                $('#previewpointi'+current_hex).css('top',pagey-4);
                $('#previewpointo'+current_hex).css('left',pagex-2);
                $('#previewpointo'+current_hex).css('top',pagey-2);
                $('#hexbox'+current_hex).css("background-color", "#"+hexcode);
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
            
           
            function changeHex(num){
                current_hex = num;
                $(".change").removeClass("active");
                $("#button"+num).addClass("active");
            }        
            
          
        </script>
        <style>

            .specific.hexcodebox{
                padding:4px;
                height:50px;
                width:140px;
                font-size:18px;
            }
        </style>
    </head>


    <body onload="initiateCanvas()">

        <div id="navigationbar">
            <?php commonHeader(); ?>
        </div>

        <br/><br/><br/>


        <span id="notification">
            <?php
            echoClear('store_save_notification');
            ?>
        </span>

        <div class="extraction_container">
            <div class="well form-vertical" id="saveform" style="height:400px">
                <br/>
                <form method="POST" action="/store_saveitem_processing.php" id="chosen_color_form" style="display:inline;">
                    <input type="hidden" name="photo_type" value="<?php echo $photo_file_type ?>" />
                    <input type="hidden" name="photo_url" value="<?php echo $photo_url_link ?>"/>
                    <input type="hidden" name="photo_imageid" value="<?php echo $photo_file_imageid ?>"/>

                    <ol style="list-style:none;width:165px;">
                        <li id="hexbox1" class="well hexbox" style="<?php if ($item) echo "background-color:#" . $item['code1']; ?>">
                            <button type="button" class="btn change active" id="button1" onclick="changeHex('1')" ><i class="icon-edit"></i></button>
                            <input type="text" readonly="readonly" value="<?php if ($item) echo $item['code1']; ?>" class="specific hexcodebox" name ="code1" id="inputcode1" placeholder=" 1st Color ">
                            </input>
                        </li>
                        <li id="hexbox2" class="well hexbox" style="<?php if ($item) echo "background-color:#" . $item['code2']; ?>">
                            <button type="button" class="btn change" id="button2" onclick="changeHex('2')" ><i class="icon-edit"></i></button> 
                            <input type="text" readonly="readonly" value="<?php if ($item) echo $item['code2']; ?>" class="specific hexcodebox" name ="code2" id="inputcode2" placeholder=" 2nd Color">
                            </input>
                        </li>
                        <li id="hexbox3" class="well hexbox" style="<?php if ($item) echo "background-color:#" . $item['code3']; ?>">
                            <button type="button" class="btn change" id="button3" onclick="changeHex('3')" ><i class="icon-edit"></i></button>
                            <input type="text" readonly="readonly" value="<?php if ($item) echo $item['code3']; ?>" class="specific hexcodebox" name ="code3" id="inputcode3" placeholder=" 3rd Color">
                            </input>
                        </li>
                    </ol>
                    <input type="text" name="description" id="itemdescription" style="margin-top:10px;width:150px;" value="<?php if ($item) echo stripslashes($item['description']); ?>" placeholder="  Item Description"/><br/>
                    <select name="gender" style="width:164px;margin-top:10px;margin-bottom:0px;padding:0px;">
                        <option value="" style="color:#fbfbfb;font-family:inherit;">Choose Gender</option>
                        <option value="0">Womens</option>
                        <option value="1">Mens</option>
                        <option value="2">Unisex</option>
                    </select>
                    <input type="hidden" value="<?php echo $photo_update?>" name="update" />
                    <input type="hidden" value="<?php echo $store_itemid?>" name="itemid" />
                    <input type="text" value="<?php if ($item) echo $item['purchaselink']; ?>" name="buyurl" id="itemdescription" style="margin-top:10px; width:150px;" placeholder=" Link to Purchase" />
                    <input type="submit" value="Save To Catalogue" class="btn" style="height:40px;margin-top:8px;width:160px;"/>
                </form>
            </div>


            <?php
            if (!$image_string) {
                echo "<span class='alert alert-error'>We couldn't find your image sorry, <a href='/store_history.php'>try again</a></span>";
            }
            ?>
            <div id="canvas_background"></div>
            <canvas id="canvas" width="<?php echo $maxwidth ?>" height="<?php echo $maxheight ?>" onclick="getColor(event)">
                Your Browser Does not support HTML 5
            </canvas>



            <div class="well hovereffect" id="description">
            </div>



        </div>
        <div id="previewpointi1" >
        </div>
        <div id="previewpointo1" >
        </div>

        <div id="previewpointi2" >
        </div>
        <div id="previewpointo2" >
        </div>

        <div id="previewpointi3" >
        </div>
        <div id="previewpointo3" >
        </div>
    </body>
</html>
