<?php
session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');
include('global_objects.php');
include('facebook_connect.php');

$userid = $_SESSION['userid'];
if (!$userid) {
    header("Location:/");
}

$client_id = "eb073737e50d41b9b0c8bd6c81125739";
$client_secret = "9c56891f658243179f2c9f7514a0b141";
$redirect_url = "http://hueclues.com/upload";
$auth_url = "https://api.instagram.com/oauth/authorize/?client_id=" . $client_id . "&amp;redirect_uri=" . $redirect_url . "&amp;response_type=token";


$facebook_user = $_SESSION['facebook_user'];
$params = array(
    'scope' => 'user_photos',
    'redirect_uri' => 'http://hueclues.com/upload'
);

$loginUrl = $facebook->getLoginUrl($params);
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initiateTools() ?>
        <script type="text/javascript" src="/js/global_javascript.js"></script>
        <script type="text/javascript" src="/js/facebook.js"></script>
        <script src="//connect.facebook.net/en_US/all.js"></script>
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <link rel="stylesheet" href="/fancybox/source/jquery.fancybox.css?" type="text/css" media="screen" />
        <script type="text/javascript" src="/fancybox/source/jquery.fancybox.pack.js?"></script>
        <link rel="stylesheet" type="text/css" href="/css/global.css" />
        <link rel="stylesheet" type="text/css" href="/css/history.css" />

        <script type="text/javascript">
<?php initiateTypeahead(); ?>
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
            var uploaddropped = 0;
            var historydropped = 0;
            var nextMaxId = "";
            var nextMaxUrl = "";
            var facebook_images_selected = new Array();
            var instagram_images_selected = new Array();


            $(document).ready(function(e) {
<?php checkNotifications(); ?>
                enableSelectBoxes();
                getInstagram();
            });

            function dropContainer(name) // name can either be upload or history
            {
                if (name == "upload" || name == "upload_highlight") {
                    if (uploaddropped == 0) {
                        $("#historycontainer").hide();
                        historydropped = 0;
                        $("#uploadcontainer").fadeIn();
                        uploaddropped = 1;
                        if (name == "upload_highlight") {
                            $("#highlight").css("font-weight", "900");
                        }
                    }
                }
                else if (name == "history") {
                    $("#uploadcontainer").hide();
                    uploaddropped = 0;
                    $("#historycontainer").fadeIn();
                    historydropped = 1;
                    $("#highlight").css("font-weight", "normal");
                    $("#highlight").css("text-decoration", "none");

                }
            }

            function flipTab(id) {
                var idText = $('#' + id).text();
                $('.selectBox .selected').text(idText);
                $('.historypage').hide();
                $('#' + id + 'page').fadeIn();
            }

            function flipUpload(id) {
                $('#uploadurltab').removeClass('active');
                $('#uploadfiletab').removeClass('active');
                $('#' + id).addClass('active');
                $('.uploadpage').hide();
                $('#' + id + 'page').fadeIn();
            }

            function getPictures() { // ajax request that gives a hexcode and gets the color theory matches
                isLoading("true");
                data = 'photo_count=' + $("#fbphoto_count").val();
                $.ajax({
                    type: "POST",
                    url: "/getfacebookphotos_processing.php",
                    data: data,
                    success: function(html) {
                        dropContainer('history');
                        flipTab('facebooktab');
                        ajaxObject = jQuery.parseJSON(html);
                        $("#fbphoto_load").hide();
                        $("#fbphoto_count").hide();
                        $("#fbphoto_instruction").hide();
                        $("#fbphoto_landing").html(ajaxObject.response);
                        isLoading("false");

                    }
                });
            }

            function isLoading(status) {
                if (status == "true") {
                    $("#loading").show();
                }
                else if (status == "false") {
                    $("#loading").hide();
                }
            }

            function defaultTabs() {
                flipUpload('uploadurltab');
                flipTab('facebooktab');
                dropContainer('upload');
            }

            function fileName() {
                var filename = $('#file').val();
                var lastIndex = filename.lastIndexOf("\\");
                if (lastIndex >= 0) {
                    filename = filename.substring(lastIndex + 1);
                }
                $("#fakeupload").val(filename);
            }

            function submitUrl() {
                if ($("#url").val()) {
                    document.forms["urlForm"].submit();
                } else {
                    window.location = "/extraction";
                }
            }


            function importImages() {
                facebook_images_selected.join(" ");
                $("#facebook_urls").val(facebook_images_selected);
                instagram_images_selected.join(" ");
                $("#instagram_urls").val(instagram_images_selected);
                document.getElementById('imported_urls_form').submit();
            }
            function addFacebookImage(num) {
                var selected = $("#fb_frame" + num).hasClass("added");
                if (selected) {
                    // image is being unselected
                    var remove_index = facebook_images_selected.indexOf($("#fb_url" + num).val());
                    facebook_images_selected.splice(remove_index, 1);
                    $("#fb_frame" + num).removeClass("added");
                } else {
                    // image is being selected
                    facebook_images_selected.push($("#fb_url" + num).val());
                    $("#fb_frame" + num).addClass("added");
                }
            }
            function addInstagramImage(num) {
                var selected = $("#ig_frame" + num).hasClass("added");
                if (selected) {
                    // image is being unselected
                    var remove_index = instagram_images_selected.indexOf($("#ig_url" + num).val());
                    instagram_images_selected.splice(remove_index, 1);
                    $("#ig_frame" + num).removeClass("added");
                } else {
                    // image is being selected
                    instagram_images_selected.push($("#ig_url" + num).val());
                    $("#ig_frame" + num).addClass("added");
                }
            }
            function changePicture() {
                document.getElementById("file").click();
            }

            function submitPicture() {
                $("#loading").show();
                document.fileForm.submit();
            }

            function getInstagram() {
                var token_string = window.location.hash;
                token = token_string.replace("#access_token=", "");
                if (nextMaxUrl) {
                    url = nextMaxUrl;
                }
                else {
                    url = "https://api.instagram.com/v1/users/self/media/recent/?&access_token=" + token + "&count=-1&callback=?";
                }
                var instagramData;
                if (window.location.hash) {
                    $("#loading").show();
                    $.ajax({
                        type: "GET",
                        url: url,
                        dataType: "jsonp",
                        cache: false,
                        success: function(instagramResponse) {
                            var i;
                            var instagramImage;
                            dropContainer('history');
                            flipTab('instagramtab');
                            $("#igphoto_instruction").hide();
                            instagramData = instagramResponse.data;
                            instagramPagination = instagramResponse.pagination;
                            if (instagramPagination) {
                                nextMaxUrl = instagramResponse.pagination.next_url;
                            }
                            else {
                                $("#paginationButton").hide();
                            }
                            for (i = 0; i < instagramData.length; i++) {
                                instagramImage = instagramData[i];
                                $('#igphoto_landing').append("<div class='thumbnail_frame' id='ig_frame" + i + "' ><input type='hidden' value='" + instagramImage.images.standard_resolution.url + "' id='ig_url" + i + "' name='photo_url' /><input type='image' alt='This link is broken' src='" + instagramImage.images.standard_resolution.url + "' class='thumbnaileffect' id='ig_image" + i + "'onclick='addInstagramImage(" + i + ")' /></div>");
                            }
                            $("#loading").hide();
                        }
                    });
                }
            }

        </script>
    </head>
    <body>
        <?php include_once("analyticstracking.php") ?>
        <?php initiateNotification() ?>
        <img src="/img/loading.gif" id="loading" />
        <?php commonHeader(); ?>
        <br/><br/>
        <div id="tabs_container">


            <div class="divider">
                <hr class="left" style="width:32%;"/>
                <span id="mainHeading" onclick="dropContainer('upload')">
                    UPLOAD PHOTOS
                </span>
                <hr class="right" style="width:32%" />
            </div>



            <div id="uploadcontainer">
                <div id="uploadurltabpage" class="uploadpage">
                    <div class="upload_method">
                        <img src="/img/uploadFacebook.png" class="hexIcon"></img>
                        <span id="highlight" class="upload_text">Upload from Facebook</span>
                        <br/><br/>
                        <div class="upload_form">
                            <?php
                            if ($facebook_user) {
                                echo "<input type=\"hidden\" class='upload_form' id=\"fbphoto_count\" name=\"photo_count\" placeholder=\"How many pictures?\" />
                                <button id=\"fbphoto_load\" class='importButton' onclick=\"getPictures()\">Load Facebook Pictures</button>";
                            } else {
                                echo
                                "<a href=" . $loginUrl . " target='_blank'><button class='importButton' >Connect with Facebook</button></a>";
                            }
                            ?>
                        </div>
                    </div>

                    <div class="upload_method">
                        <img src="/img/uploadInstagram.png" class="hexIcon"></img>
                        <span id="highlight" class="upload_text">Upload from Instagram</span>
                        <br/><br/>
                        <div class="upload_form">
                            <a href="<?php echo $auth_url ?>"><button class="importButton" >Connect to Instagram</button></a>
                        </div>
                    </div>

                    <div class="upload_method">
                        <img src="/img/uploadImage.png" class="hexIcon"></i>
                        <span class="upload_text">Upload an image file<br/>(jpg, png, gif)</span><br/><br/>
                        <div class="upload_form">
                            <form enctype="multipart/form-data" id="fileForm" class="upload_form" name="fileForm" action="/upload_processing.php?type=image" method="post" accept="image/gif,image/jpeg,image/png">
                                <input name="image" id="file" type="file" onchange="submitPicture()" style="opacity:0;position:absolute;z-index:-1;" />
                                <input type="button" id="fakeupload" onclick="changePicture()" class="importButton" value="Browse">
                            </form><br/><br/>
                        </div>
                    </div>

                    <div class="upload_method">
                        <img src="/img/uploadUrl.png" class="hexIcon"></img>
                        <span class="upload_text">Copy & paste image URL<br/>(jpg, png, gif)</span><br/><br/>
                        <div class="upload_form">
                            <form method="POST" id="urlForm" name="urlForm"  action="/upload_processing.php?type=url" >
                                <input type="text" class="urlInput" name="url" id="url" placeholder="Paste Link Here" />
                                <button onclick="submitUrl()" id="uploadUrl"><img height="20" src="/img/uploadArrow.png"></img></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            <div id="historycontainer" style="display:none;">
                <div class='selectBox' onchange="checkValue()">
                    <span class='selected'></span>
                    <span class='selectArrow'><i class="icon-chevron-down"></i></span>
                    <div class="selectOptions" >  
                        <span class="selectOption" id="facebooktab" value="facebooktab" onclick = "flipTab('facebooktab')">facebook</span>
                        <span class="selectOption" id="instagramtab" value="instagramtab" onclick = "flipTab('instagramtab')">instagram</span>
                    </div>
                </div>


                <form action="/bulk_upload_processing.php" id="imported_urls_form" method="POST" >
                    <input type="hidden" id="facebook_urls" name="facebook_urls" value="" />
                    <input type="hidden" id="instagram_urls" name="instagram_urls" value="" />
                    <button class="greenButton" id="bulkButton" onclick="importImages()" >IMPORT IMAGES</button>

                </form>

                <br/>
                <div id="facebooktabpage" class="historypage" style="display:block">
                    <div class="historypanel" id="fbphoto_landing" style="background-color:transparent; height:auto; border:1px solid white;">
                        <span id="fbphoto_instruction">You Must First "<span style="cursor:pointer;text-decoration:underline;" onclick="dropContainer('upload_highlight');">Connect With Facebook</span>"</span>

                    </div>
                </div> 

                <div id="instagramtabpage" class="historypage" style="display:block">
                    <div class="historypanel" id="igphoto_landing" style="background-color:transparent; height:auto; border:1px solid white;">
                        <span id="igphoto_instruction">You Must First "<span style="cursor:pointer;text-decoration:underline;" onclick="dropContainer('upload_highlight');">Connect With Instagram</span>"</span>
                    </div>
                    <button id="paginationButton" class="greenButton" style="margin:auto;width:150px;display:block;position:relative;" onclick="getInstagram()">Load More...</button>
                </div>
            </div>
        </div>
    </body>
</html>