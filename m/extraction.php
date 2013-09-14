<?php
session_start();
include('connection.php');
include('global_tools.php');
include('global_objects.php');
include('database_functions.php');
include('algorithms.php');
$userid = $_SESSION['userid'];
$tab = $_GET['tab'];
if (!$tab) {
    $tab = "all";
}
$i = 0; // div index
$user = database_fetch("user", "userid", $userid);
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
        <link rel="stylesheet" href="/fancybox/source/jquery.fancybox.css?" type="text/css" media="screen" />
        <script type="text/javascript" src="/fancybox/source/jquery.fancybox.pack.js?"></script>
        <script src="/js/global_javascript.js" type="text/javascript" charset="utf-8" ></script>
        <script type="text/javascript">
            var img_url = "";
            var img_src = "";
            var img = new Image();
            var context = "";
            var xcor = "";
            var ycor = "";
            var hexcode = "";
            var preview = "";
            var xoffset = "";
            var yoffset = "";
            var pagex = "";
            var pagey = "";
            var canvasObject;
            var matchhide = "false";
            var drawing_width = 0;
            var drawing_height = 0;
            var width = 0;
            var height = 0;

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

            $(document).ready(function(e) {
                enableSelectBoxes();
                flipTab('<?php echo $tab ?>tab');
                $("#canvas").mousemove(function(e) {
                    xcor = e.pageX - xoffset;
                    ycor = e.pageY - yoffset;
                    pagex = e.pageX;
                    pagey = e.pageY;
                    previewColor();
                });
                bindActions();
            }
            );


            function flipTab(id) {
                if (id == "alltab") {
                    $('.historypage').fadeIn();
                }
                else {
                    var idText = $('#' + id).text();
                    $('.selectBox .selected').text(idText);
                    $('.historypage').hide();
                    $('#' + id + 'page').fadeIn();
                }
            }

            function RGBtoHex(R, G, B) {
                return toHex(R) + toHex(G) + toHex(B)
            }

            function toHex(N) {
                if (N == null)
                    return "00";
                N = parseInt(N);
                if (N == 0 || isNaN(N))
                    return "00";
                N = Math.max(0, N);
                N = Math.min(N, 255);
                N = Math.round(N);
                return "0123456789ABCDEF".charAt((N - N % 16) / 16)
                        + "0123456789ABCDEF".charAt(N % 16);
            }

            function initiateCanvas() { // initially loads the images and procures the canvas 
                var canvas = document.getElementById("canvas");
                canvas.width = canvas.width; // clears the canvas
                img.src = img_src;
                img.onload = function() {
                    context = document.getElementById('canvas').getContext('2d');
                    context.drawImage(img, drawing_width, drawing_height, width, height);
                    getoffsets();
                };
            }

            function previewColor() { // changes the border of the picture to match the pixel the mouse is hovering over
                data = context.getImageData(xcor, ycor, 1, 1).data;
                preview = RGBtoHex(data[0], data[1], data[2]);
                $("#canvas").css("border-color", "#" + preview);
            }

            function getColor(e) { // used to grab the color of the pixel at the x,y coordinate then plots the previews
                data = context.getImageData(xcor, ycor, 1, 1).data;
                hexcode = RGBtoHex(data[0], data[1], data[2]);
                $("#extractionHexcode").val(hexcode);
                $("#saveForm").css("background-color", "#" + hexcode);
                $('#previewpoint').css('left', pagex - 4);
                $('#previewpoint').css('top', pagey - 4);
                $('#previewpoint2').css('left', pagex - 2);
                $('#previewpoint2').css('top', pagey - 2);
            }


            function validateTags() {
                var text = $("#extractionTags").val();
            }
            function extractImage(photo_type, photo_link, url_origin) { // ajax request that gives a hexcode and gets the color theory matches
                // if photo_type is url you get a url link
                // if photo_type is file you get a imageid
                // url_origin = 0 -> native url
                // url_origin = 1 -> facebook
                // url_origin = 2 -> instagram
                $("#loading").show();
                if (photo_type == "url") {
                    var send_data = {'photo_type': 'url', 'photo_url': photo_link, 'url_origin': url_origin}
                }
                else if (photo_type == "file") {
                    var send_data = {'photo_type': 'file', 'photo_imageid': photo_link}
                }
                $.ajax({
                    type: "GET",
                    url: "/extraction_processing.php",
                    data: send_data,
                    success: function(html) {
                        canvasObject = jQuery.parseJSON(html);
                        img_url = canvasObject.image_url;
                        img_src = canvasObject.image_string;
                        drawing_height = canvasObject.drawing_height;
                        drawing_width = canvasObject.drawing_width;
                        width = canvasObject.width;
                        height = parseInt(canvasObject.height);
                        $("#save_photo_type").val(canvasObject.image_type);
                        $("#save_photo_url").val(img_url);
                        $("#save_photo_imageid").val(canvasObject.imageid);
                        $("#save_url_origin").val(url_origin);
                        initiateCanvas();
                        $("#extraction_container").animate().slideDown('very slow').animate();
                        $("#extractionDescription").val("");
                        $("#extractionTags").val("");
                        $("#extractionHexcode").val("");
                        $("#saveForm").css("background-color", "#ffffff");
                        window.scrollTo(0, 0);
                        $("#loading").hide();
                        $(".eyedropper").css("display", "block");
                    }
                });
            }
            function getoffsets() { // determines how far the picture is from the top left corner

                if (isChrome) {
                    border = parseInt($('#canvas').css("border-width"));
                }
                else {
                    border = 20;
                }
                xoffset = $('#canvas').offset().left + border;
                yoffset = $('#canvas').offset().top + border;
            }

            function removeImage(origin, urlid, imageid, divid) {
                $("#loading").show();
                var send_data = {"origin": origin, "urlid": urlid, "imageid": imageid};

                $.ajax({
                    type: "GET",
                    url: "/delete_image_processing.php",
                    data: send_data,
                    success: function(html) {
                        $("#loading").hide();
                        $("#div" + divid).fadeOut();
                    }
                });

            }

            function saveItem() {
                $("#loading").show();
                var send_data = $("#chosen_color_form").serialize();
                $.ajax({
                    type: "POST",
                    url: "/saveitem_processing.php",
                    data: send_data,
                    success: function(html) {
                        $(window).scrollTop(0);
                        saveObject = jQuery.parseJSON(html);
                        var notification = saveObject.status;
                        $("#notification").html(notification);
                        displayNotification(notification);
                        $("#loading").hide();
                    }

                });
            }

        </script>
        <style>

            #canvas{
                border-width:20px;
                border-style: solid;
                display:inline-block;
                position:relative;
                top:200px;
                left:0px;
            }
            #extraction_container{
                top:100px;
                height:810px;
            }

            #matchpanel{
                position:fixed;
                bottom:-1px;
                width:100%;
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
                left:0px;
            }
            #previewpoint2{
                width:4.5px;
                height:4.5px;
                position:absolute;
                border-radius: 50px 50px 50px;
                border:2px solid black;
                z-index:1;
                bottom:0px;
                left:0px;
            }

            #previewform{
                position:relative;
                left:0px;
                top:10px;
            }


            #saveForm{
                background-color:white;
                width:90%;
                height:135px;
                text-align:center;
                display:inline-block;
                position:absolute;
                left:0px;
                top:50px;
                padding:15px;
                border-radius:5px;
                border:none;
                opacity:0.85;

            }

            .upload_tab{
                background-color:white;
                color:rgb(35,31,32);
                border: none;
                opacity:0.5;
                border-radius: 30px 30px 0px 0px;
            }
            .clicked{
                background-color:white;
                color:rgb(35,31,32);
                opacity:1;
            }
            .image_input{
                font-family:"Quicksand Bold";
                background-color:white;
                color:rgb(65,173,73);
            }
            #filename{
                border:none;
                display: run-in;
            }
            .imageContainer{
                width: 200px; 
                height: 200px; 
                overflow: hidden;
                position:relative;
                display:inline-block;
                padding:5px;
                background-color:#fbfbfb;
            }
            .itemAction{
                display:none;
            }
            .itemAction:hover{
                cursor:pointer;
            }
            .eyedropper{
                display:none;
            }
            li.active{
                background-color:#808285;
            }
            #extractionHeading{
                top:10px;
                position:absolute;
                color:#58595B;
                font-family: "Quicksand";
                font-size:30px;
                width:320px;
            }
            #uploadsHeading{
                top:-40px;
                left:0px;
                position:absolute;
                color:#58595B;
                font-family: "Quicksand";
                font-size:40px;
            }
            img.itemActionImage{
                height:20px;
                box-shadow:0px 0px 3px 3px #51BB75;
                border-radius:100px;
                background-color: #51BB75;
            }
            img.itemActionImage:hover{
                box-shadow:0px 0px 3px 3px #51BB75;
                border-radius:100px;
                background-color: #51BB75;
            }
            .extractionForm{
                padding:5px;
                border-radius:3px;
                border:1px ridge;
            }



            div.selectBox
            {
                width:60%;
                z-index:3;
                position:relative;
                display:inline-block;
                cursor:default;
                text-align:left;
                line-height:30px;
                clear:both;
                color:#888;
            }
            span.selected
            {
                width:167px;
                text-indent:20px;
                border:1px solid #ccc;
                border-right:none;
                border-top-left-radius:5px;
                border-bottom-left-radius:5px;
                background:#f6f6f6;
                overflow:hidden;
            }
            span.selectArrow
            {
                width:30px;
                border:1px solid transparent;
                border-top-right-radius:5px;
                border-bottom-right-radius:5px;
                text-align:center;
                font-size:20px;
                -webkit-user-select: none;
                -khtml-user-select: none;
                -moz-user-select: none;
                -o-user-select: none;
                user-select: none;
                background:#51BB75;
                color:white;
            }

            span.selectArrow,span.selected
            {
                position:relative;
                float:left;
                height:30px;
                width:83%;
                z-index:1;
            }

            div.selectOptions
            {
                position:absolute;
                top:28px;
                left:0;
                width:83%;
                border:1px solid #ccc;
                border-bottom-right-radius:5px;
                border-bottom-left-radius:5px;
                overflow:hidden;
                background:#f6f6f6;
                padding-top:2px;
                display:none;
            }

            span.selectOption
            {
                display:block;
                width:80%;
                line-height:20px;
                padding:5px 10%;
            }

            span.selectOption:hover
            {
                color:#f6f6f6;
                background:#4096ee;	
            }	

            .historypage{
                margin:auto;
                width:215px;
            }
            #historycontainer{
                width:100%;
                background-color:#fbfbfb;
                opacity:0.8;
                height:auto;
            }
        </style>
    </head>


    <body>
        <?php initiateNotification() ?>
        <?php commonHeader() ?>
        <img src="/img/loading.gif" id="loading"/>
        <br/><br/><br/>
        <div id="extraction_container" style="display:none">
            <span id="extractionHeading">CLICK YOUR ITEM</span>
            <div id="saveForm">
                <form method="POST" action="/saveitem_processing.php" id="chosen_color_form" style="display:inline-block;">
                    <input type="hidden" name="url_origin" id="save_url_origin" value="" />
                    <input type="hidden" name="photo_type" id="save_photo_type" value="" />
                    <input type="hidden" name="photo_url" id="save_photo_url" value=""/>
                    <input type="hidden" name="photo_imageid" id="save_photo_imageid" value=""/>
                    <input type="hidden" name="code" id="extractionHexcode" value="" style="height:50px;width:145px;font-size:18px;" placeholder="  Hexcode"/><br/>
                    <input type="text" value="" class="extractionForm" name="description" id="extractionDescription"  style="width:280px;" placeholder="Describe This Item"/><br/>
                    <input type="text" value="" class="extractionForm" name="tags" id="extractionTags"  style="width:280px;" placeholder="#tag  #me" onChange="validateTags()" />
                </form><br/>
                <button id="saveform_button" class="greenButton" style="height:40px;margin-top:8px;width:160px;padding:5px;" onclick="saveItem()">SAVE TO CLOSET</button>
            </div>
            <canvas id="canvas" width="275" height="375" onclick="getColor(event)">
                Your Browser Does not support HTML 5
            </canvas>   
            <div id="previewpoint" class="eyedropper" >
            </div>
            <div id="previewpoint2" class="eyedropper" >
            </div>
        </div>
        <div id="mobileContainer">
            <span id="uploadsHeading">UPLOADS</span>
            <div id="historycontainer">
                <a href="/upload"><button class="greenButton" style="right:0px;height:33px;width:39%;position:absolute;font-size:22px;font-family:'Quicksand'">UPLOAD</button></a>

                <div class='selectBox' onchange="checkValue()">
                    <span class='selected'></span>
                    <span class='selectArrow' style="width:15%;">&#9660</span>
                    <div class="selectOptions" >  
                        <span class="selectOption" id="alltab" value="alltab" onclick = "flipTab('alltab')">All(<?php echo $user['igcount'] + $user['urlcount'] + $user['filecount'] + $user['fbcount']; ?>)</span>
                        <span class="selectOption" id="urltab" value="urltab" onclick="flipTab('urltab');">url(<?php echo $user['urlcount']; ?>)</span>
                        <span class="selectOption" id="filetab" value="filetab" onclick = "flipTab('filetab')">images(<?php echo $user['filecount']; ?>)</span>
                        <span class="selectOption" id="facebooktab" value="facebooktab" onclick = "flipTab('facebooktab')">facebook(<?php echo $user['fbcount']; ?>)</span>
                        <span class="selectOption" id="instagramtab" value="instagramtab" onclick = "flipTab('instagramtab')">instagram(<?php echo $user['igcount']; ?>)</span>
                    </div>
                </div>


                <br/>
                <div id = "urltabpage" class = "historypage" style = "display:block;">
                    <div class = "historypanel">
                        <?php
                        $urlquery = database_query("url", "userid", $userid);
                        while ($url = mysql_fetch_array($urlquery)) {
                            // picture formatting
                            echo "
                                <div id='div" . $i . "' class='imageContainer'>
<button class='itemAction' style='position:absolute;z-index:2;bottom:10px' onclick=\"removeImage('0', '" . $url['urlid'] . "', '', '" . $i . "')\"><img class='itemActionImage' src='/img/trashcan.png'></img></button>
                                <input type='image' alt='   This link is broken' src='" . $url['url'] . "' onclick=\"extractImage('url', '" . $url['url'] . "', '0')\" class='thumbnaileffect'  /> 
                                    </div>";
                            $i++;
                        }
                        ?>
                    </div>
                </div>
                <div id="filetabpage" class="historypage">
                    <div class="historypanel">
                        <?php
                        $imagequery = database_query("image", "userid", $userid);
                        while ($image = mysql_fetch_array($imagequery)) {
                            // formatting for picture
                            echo
                            "
                            <div id='div" . $i . "' class='imageContainer'>
<button class='itemAction' style='position:absolute;z-index:2;bottom:10px' onclick=\"removeImage('3', '', '" . $image['imageid'] . "', '" . $i . "')\"><img class='itemActionImage' src='/img/trashcan.png' /></i></button>
                                <input type='image' alt='   This link is broken' src='" . $image['url'] . "' onclick = \"extractImage('file', '" . $image['imageid'] . "')\" class='thumbnaileffect'  /> 
                                    </div>";
                            $i++;
                        }
                        ?>
                    </div>
                </div>
                <div id="facebooktabpage" class="historypage">
                    <div class="historypanel">
                        <?php
                        $urlquery = database_query("facebookurl", "userid", $userid);

                        while ($url = mysql_fetch_array($urlquery)) {
                            // picture formatting
                            echo "
                                <div id='div" . $i . "' class='imageContainer'>
<button class='itemAction' style='position:absolute;z-index:2;bottom:10px' onclick=\"removeImage('1', '" . $url['urlid'] . "', '', '" . $i . "')\"><img class='itemActionImage' src='/img/trashcan.png'></img></button>
<input type='image' alt='   This link is broken' src='" . $url['url'] . "' onclick=\"extractImage('url', '" . $url['url'] . "', '1')\" class='thumbnaileffect'  /> 
                                    </div>";
                            $i++;
                        }
                        ?>
                    </div>
                </div>
                <div id="instagramtabpage" class="historypage">
                    <div class="historypanel">
                        <?php
                        $urlquery = database_query("instagramurl", "userid", $userid);
                        while ($url = mysql_fetch_array($urlquery)) {
                            // picture formatting
                            echo "
                                <div id='div" . $i . "' class='imageContainer'>
<button class='itemAction' style='position:absolute;z-index:2;bottom:10px' onclick=\"removeImage('2', '" . $url['urlid'] . "', '', '" . $i . "')\"><img class='itemActionImage' src='/img/trashcan.png'></img></button>
                                <input type='image' alt='   This link is broken' src='" . $url['url'] . "' onclick=\"extractImage('url', '" . $url['url'] . "', '2')\" class='thumbnaileffect'  /> 
                                    </div>";
                            $i++;
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
