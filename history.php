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
        <script type="text/javascript" src="/js/facebook.js"></script>
        <script src="//connect.facebook.net/en_US/all.js"></script> 
        <script src="http://malsup.github.com/jquery.form.js"></script>
        <script type="text/javascript" src="/js/uploadv1.js"></script>
        <link rel="stylesheet" href="/fancybox/source/jquery.fancybox.css?" type="text/css" media="screen" />
        <script type="text/javascript" src="/fancybox/source/jquery.fancybox.pack.js?"></script>
        <link rel="stylesheet" type="text/css" href="/css/uploadv1.css" />

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
            var userid = "<?php echo $userid ?>";

            $(document).ready(function(e) {
<?php checkNotifications(); ?>
                enableSelectBoxes();
                getInstagram();
                $("#fileForm").ajaxForm(options);
            });

        </script>
    </head>
    <body>
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
                                <button id=\"fbphoto_load\" class='importButton' onclick=\"getPictures()\">Load Facebook Photos</button>";
                            } else {
                                echo
                                "<a href=" . $loginUrl . " target='_blank'><button class='importButton' >Use Facebook Photos</button></a>";
                            }
                            ?>
                        </div>
                    </div>

                    <div class="upload_method">
                        <img src="/img/uploadInstagram.png" class="hexIcon"></img>
                        <span id="highlight" class="upload_text">Upload from Instagram</span>
                        <br/><br/>
                        <div class="upload_form">
                            <a href="<?php echo $auth_url ?>"><button class="importButton" >Use Instagram Photos</button></a>
                        </div>
                    </div>

                    <div class="upload_method">
                        <img src="/img/uploadImage.png" class="hexIcon"></i>
                        <span class="upload_text">Upload an image file<br/>(jpg, png, gif)</span><br/><br/>
                        <div class="upload_form">
                            <form enctype="multipart/form-data" multiple id="fileForm" class="upload_form" name="fileForm" action="/controllers/upload_processing.php?type=image" method="post" accept="image/gif,image/jpeg,image/png">
                                <input name="images[]" id="file" type="file" onchange="submitPicture()" style="opacity:0;position:absolute;z-index:-1;" multiple />
                                <input type="button" id="fakeupload" onclick="changePicture()" class="importButton" value="Choose Picture(s)" />
                            </form> <div id="progress">
                                <div id="bar"></div>
                                <div id="percent">0%</div >
                            </div>
                            <br/>
                            <div id="message"></div>
                        </div>
                    </div>

                    <div class="upload_method">
                        <img src="/img/uploadUrl.png" class="hexIcon"></img>
                        <span class="upload_text">Copy & paste image URL<br/>(jpg, png, gif)</span><br/><br/>
                        <div class="upload_form">
                            <form method="POST" id="urlForm" name="urlForm"  action="/controllers/upload_processing.php?type=url" >
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
                    <span class='selectArrow'><i class="fa fa-chevron-down"></i></span>
                    <div class="selectOptions" >  
                        <span class="selectOption" id="facebooktab" value="facebooktab" onclick = "flipTab('facebooktab')">facebook</span>
                        <span class="selectOption" id="instagramtab" value="instagramtab" onclick = "flipTab('instagramtab')">instagram</span>
                    </div>
                </div>


                <form action="/controllers/bulk_upload_processing.php" id="imported_urls_form" method="POST" >
                    <input type="hidden" id="facebook_urls" name="facebook_urls" value="" />
                    <input type="hidden" id="instagram_urls" name="instagram_urls" value="" />
                    <button class="greenButton" id="bulkButton" onclick="importImages()" >IMPORT IMAGES</button>

                </form>

                <br/>
                <div id="facebooktabpage" class="historypage">
                    <div class="historypanel" id="fbphoto_landing">
                        <span id="fbphoto_instruction"><span onclick="dropContainer('upload_highlight');">Use Facebook Photos</span></span>

                    </div>
                </div> 

                <div id="instagramtabpage" class="historypage">
                    <div class="historypanel" id="igphoto_landing">
                        <span id="igphoto_instruction"><span onclick="dropContainer('upload_highlight');">Use Instagram Photos</span>"</span>
                    </div>
                    <button id="paginationButton" class="greenButton" onclick="getInstagram()">Load More...</button>
                </div>
            </div>
        </div>
    </body>
</html>