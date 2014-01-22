<?php
session_start();
include('connection.php');
include('global_tools.php');
include('global_objects.php');
include('database_functions.php');
$userid = $_SESSION['userid'];
if (!$userid) {
    header("Location:/");
}
$tab = $_GET['tab'];
if (!$tab) {
    $tab = "unused";
}
$i = 0; // div index
$user = database_fetch("user", "userid", $userid);
$totalPhotoCount = $user['urlcount'] + $user['filecount'] + $user['igcount'] + $user['fbcount'];
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initiateTools() ?>
        <script type="text/javascript" src="/js/extraction.js"></script>
        <link rel="stylesheet" href="/fancybox/source/jquery.fancybox.css?" type="text/css" media="screen" />
        <script type="text/javascript" src="/fancybox/source/jquery.fancybox.pack.js?"></script>
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
            var showInputs = 0;
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
            var userid = "<?php echo $userid ?>";
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



        </script>
    </head>
    <body>
        <?php include_once("analyticstracking.php") ?>
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
                    <form id="itemForm">
                        <input type="hidden" name="url_origin" id="save_url_origin" value="" />
                        <input type="hidden" name="photo_type" id="save_photo_type" value="" />
                        <input type="hidden" name="photo_url" id="save_photo_url" value=""/>
                        <input type="hidden" name="photo_imageid" id="save_photo_imageid" value=""/>
                        <input type="hidden" name="code" id="extractionHexcode" value="" placeholder="  Hexcode"/>
                        <input type="text" value="" class="extractionForm" name="description" id="extractionDescription" maxlength="25" placeholder="i.e Red Polo Shirt"/>
                        <input type="text" value="" class="extractionForm" name="tags" id="extractionTags" placeholder="i.e #sun#polo#tops#pocket" style="top: 28px;"/>
                        <input type="text" value="" class="extractionForm" name="purchaseLink" id="extractionLink" placeholder="(Optional: Link to Item) i.e www.amazon/buy/shirt" />
                    </form>  
                    <button id="saveFormButton" class="greenButton" onclick="saveItem()"><span id="saveFormButtonTxt">POST TO</span><span id="saveFormButtonTxt">CLOSET</span></button>

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
                <a href="/upload"><button class="greenButton" id="uploadNewItem">UPLOAD NEW ITEM</button></a>


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
                <div id = "urltabpage" class = "extractionPage" style = "display:block;">

                    <?php
                    $urlQuery = "SELECT * FROM url WHERE userid='" . $userid . "' ORDER BY urlid DESC";
                    $urlResult = mysql_query($urlQuery);
                    while ($url = mysql_fetch_array($urlResult)) {
                        // picture formatting
                        $used = "";
                        if (database_fetch("item", "urlid", $url['urlid'], "image_origin", "0")) {
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
                <div id="filetabpage" class="extractionPage">
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
                <div id="facebooktabpage" class="extractionPage">
                    <?php
                    $urlQuery = "SELECT * FROM facebookurl WHERE userid='" . $userid . "' ORDER BY urlid DESC";
                    $urlResult = mysql_query($urlQuery);
                    while ($url = mysql_fetch_array($urlResult)) {
                        // picture formatting
                        $used = "";
                        if (database_fetch("item", "urlid", $url['urlid'], "image_origin", "1")) {
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
                <div id="instagramtabpage" class="extractionPage">
                    <?php
                    $urlQuery = "SELECT * FROM instagramurl WHERE userid='" . $userid . "' ORDER BY urlid DESC";
                    $urlResult = mysql_query($urlQuery);
                    while ($url = mysql_fetch_array($urlResult)) {
                        // picture formatting
                        $used = "";
                        if (database_fetch("item", "urlid", $url['urlid'], "image_origin", "2")) {
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
    </body>
</html>
