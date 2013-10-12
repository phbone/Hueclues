<?php
session_start();
include('connection.php');
include('global_tools.php');
include('global_objects.php');
include('database_functions.php');
include('algorithms.php');
$userid = $_SESSION['userid'];
if (!$userid) {
    header("Location:/");
}
$tab = $_GET['tab'];
if (!$tab) {
    $tab = "all";
}
$i = 0; // div index
$user = database_fetch("user", "userid", $userid);
$totalPhotoCount = $user['urlcount'] + $user['filecount'] + $user['igcount'] + $user['fbcount'];
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initiateTools() ?>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script type="text/javascript" src="/js/global_javascript.js"></script>
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <link rel="stylesheet" href="/fancybox/source/jquery.fancybox.css?" type="text/css" media="screen" />
        <script type="text/javascript" src="/fancybox/source/jquery.fancybox.pack.js?"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <link rel="stylesheet" type="text/css" href="/css/global.css" />
        <link rel="stylesheet" type="text/css" href="/css/extraction.css" />




        <script type="text/javascript">
<?php initiateTypeahead(); ?>
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
            var lastPhoto = "";

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
                    $('.usedImages').fadeIn();
                }
                else if (id == "unusedtab") {
                    $('.historypage').fadeIn();
                    $('.usedImages').fadeOut();
                }
                else {
                    var idText = $('#' + id).text();
                    $('.selectBox .selected').text(idText);
                    $('.historypage').hide();
                    $('.usedImages').fadeIn();
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

                var textColor = fontColor(hexcode);
                $("#extractionHexcode").val(hexcode);
                $(".extractionForm").css("color", textColor);
                if (textColor == "#FFFFFF") {
                    $("#extractionDescription").addClass("white");
                    $("#extractionTags").addClass("white");
                }
                else if (textColor == "#000000") {
                    $("#extractionDescription").removeClass("white");
                    $("#extractionTags").removeClass("white");
                }
                $("#saveForm").css("background-color", "#" + hexcode);
                $('#previewpoint').css('left', pagex - 4);
                $('#previewpoint').css('top', pagey - 4);
                $('#previewpoint2').css('left', pagex - 2);
                $('#previewpoint2').css('top', pagey - 2);
            }


            function extractImage(photo_type, photo_link, url_origin) { // ajax request that gives a hexcode and gets the color theory matches
                // if photo_type is url you get a url link
                // if photo_type is file you get a imageid
                // url_origin = 0 -> native url
                // url_origin = 1 -> facebook
                // url_origin = 2 -> instagram
                $("#loading").show();
                if (lastPhoto == photo_link) {
                    window.scrollTo(0, 0);
                    $("#loading").hide();
                }
                else {
                    lastPhoto = photo_link;
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
                        console.log(html);
                        $("#loading").hide();
                        $("#div" + divid).fadeOut();
                    }
                });
            }

            function addMore() {

                $('html,body').animate({
                    scrollTop: $("#tabs_container").offset().top},
                'slow');
                $.fancybox.close();
            }

            function saveItem() {
                $("#loading").show();
                var send_data = $("#itemForm").serialize();
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
    </head>
    <body>
        <?php initiateNotification() ?>
        <img src="/img/loading.gif" id="loading"/>
        <?php commonHeader(); ?>
        <br/><br/>
        <div id="extraction_container" style="display:none">


            <span id="extractionHeading"> <div class="divider">
                    <hr class="left" style="width:32%" />
                    <span id="mainHeading">CLICK YOUR ITEM</span>
                    <hr class="right" style="width:32%" />
                </div></span>

            <div id="canvas_background">
                <canvas id="canvas" width="400" height="500" onclick="getColor(event)">
                    Your Browser Does not support HTML 5
                </canvas>   
                <div class="well form-vertical" id="saveForm">
                    <form method="POST" action="/saveitem_processing.php" id="itemForm" style="display:inline;">
                        <input type="hidden" name="url_origin" id="save_url_origin" value="" />
                        <input type="hidden" name="photo_type" id="save_photo_type" value="" />
                        <input type="hidden" name="photo_url" id="save_photo_url" value=""/>
                        <input type="hidden" name="photo_imageid" id="save_photo_imageid" value=""/>
                        <input type="hidden" name="code" id="extractionHexcode" value="" style="height:50px;width:145px;font-size:18px;" placeholder="  Hexcode"/>
                        <input type="text" value="" class="extractionForm" name="description" id="extractionDescription" placeholder="(Description) i.e Red Polo Shirt"/>
                        <input type="text" value="" class="extractionForm" name="tags" id="extractionTags" placeholder="(Style Tags) i.e #summer#polo#shirt#collared" style="top: 28px;"/>
                        <input type="text" value="" class="extractionForm" name="purchaseLink" id="extractionLink" placeholder="(Optional: Link to Item) i.e www.amazon/buy/shirt" />
                    </form>  
                    <button id="saveform_button" class="greenButton" style="height: 74px;margin-top: 8px;width: 106px;padding: 5px;position: absolute;left: 294px;top: -8px;font-size: 20px; text-shadow: 1px 1px 2px white;border-radius:0px;line-height: 156%;" onclick="saveItem()">SAVE TO CLOSET</button>
                </div> 
            </div>
            <div id="previewpoint" class="eyedropper" >
            </div>
            <div id="previewpoint2" class="eyedropper" >
            </div>
        </div>

        <div id="tabs_container">
            <span id="extractionTitle">
                <div class="divider">
                    <hr class="left" style="width:40%;" />
                    <span id="mainHeading">UPLOADS</span>
                    <hr class="right" style="width:40%;"/>
                </div>
            </span>
            <div id="historycontainer" style="position:relative">
                <a href="/upload"><button class="greenButton" style="right:0px;height:35px;width:275px;position:absolute;font-size:22px;font-family:'Quicksand'">UPLOAD NEW ITEM</button></a>


                <div class='selectBox' onchange="checkValue()">
                    <span class='selected'></span>
                    <span class='selectArrow'><i class="icon-chevron-down"></i></span>
                    <div class="selectOptions" >  
                        <span class="selectOption" id="unusedtab" value="unusedtab" onclick = "flipTab('unusedtab')">Unused(<?php echo $user['igcount'] + $user['urlcount'] + $user['filecount'] + $user['fbcount'] - $user['itemcount']; ?>)</span>
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
                        $urlQuery = "SELECT * FROM url WHERE userid='" . $userid . "' ORDER BY urlid DESC";
                        $urlResult = mysql_query($urlQuery);
                        while ($url = mysql_fetch_array($urlResult)) {
                            // picture formatting
                            $used = "";
                            if (database_fetch("item", "urlid", $url['urlid'])) {
                                $used = " usedImages";
                            }

                            echo "
                                <div id='div" . $i . "' class='imageContainer" . $used . "'>
<button class='itemAction' style='position:absolute;z-index:2' onclick=\"removeImage('0', '" . $url['urlid'] . "', '', '" . $i . "')\"><img class='itemActionImage' src='/img/trashcan.png'></img></button>
<input type='image' alt='   This link is broken' src='" . $url['url'] . "' onclick=\"extractImage('url', '" . $url['url'] . "', '0')\" class='thumbnaileffect'  /> 
                                    </div>";
                            $i++;
                        }
                        if ($totalPhotoCount == 0) {
                            echo "<a href='/upload' class='emptyPrompt'><span class='messageGreen'style='font-family: Century Gothic'; font-size: 35px;'> </br> </br> Click \"UPLOAD NEW ITEM\" to add photos to your Closet.</span></a>";
                        }
                        ?>
                    </div>
                </div>
                <div id="filetabpage" class="historypage">
                    <div class="historypanel">
                        <?php
                        $imgQuery = "SELECT * FROM image WHERE userid='" . $userid . "' ORDER BY imageid DESC";
                        $imgResult = mysql_query($imgQuery);
                        while ($image = mysql_fetch_array($imgResult)) {
                            // formatting for picture
                            $used = "";
                            if (database_fetch("item", "imageid", $image['imageid'])) {
                                $used = " usedImages";
                            }
                            echo
                            "
                            <div id='div" . $i . "' class='imageContainer" . $used . "'>
<button class='itemAction' style='position:absolute;z-index:2' onclick=\"removeImage('3', '', '" . $image['imageid'] . "', '" . $i . "')\"><img class='itemActionImage' src='/img/trashcan.png' /></i></button>
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
                        $urlQuery = "SELECT * FROM facebookurl WHERE userid='" . $userid . "' ORDER BY urlid DESC";
                        $urlResult = mysql_query($urlQuery);
                        while ($url = mysql_fetch_array($urlResult)) {
                            // picture formatting
                            $used = "";
                            if (database_fetch("item", "urlid", $url['urlid'])) {
                                $used = " usedImages";
                            }
                            echo "
                                <div id='div" . $i . "' class='imageContainer" . $used . "'>
<button class='itemAction' style='position:absolute;z-index:2' onclick=\"removeImage('1', '" . $url['urlid'] . "', '', '" . $i . "')\"><img class='itemActionImage' src='/img/trashcan.png'></img></button>
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
                        $urlQuery = "SELECT * FROM instagramurl WHERE userid='" . $userid . "' ORDER BY urlid DESC";
                        $urlResult = mysql_query($urlQuery);
                        while ($url = mysql_fetch_array($urlResult)) {
                            // picture formatting
                            $used = "";
                            if (database_fetch("item", "urlid", $url['urlid'])) {
                                $used = " usedImages";
                            }
                            echo "
                                <div id='div" . $i . "' class='imageContainer" . $used . "'>
<button class='itemAction' style='position:absolute;z-index:2' onclick=\"removeImage('2', '" . $url['urlid'] . "', '', '" . $i . "')\"><img class='itemActionImage' src='/img/trashcan.png'></img></button>
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
