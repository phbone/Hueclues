<?php
session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');
include('global_objects.php');
include('facebook_connect.php');

$userid = $_SESSION['userid'];


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
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0;" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <link rel="apple-touch-icon" href="icon.png"/>
        <link rel="stylesheet" href="/css/mobile.css">
        <link rel="stylesheet" href="/css/global.css">
        <script src="/js/global_javascript.js" type="text/javascript" charset="utf-8" ></script>
        <link rel="stylesheet" href="/fancybox/source/jquery.fancybox.css?" type="text/css" media="screen" />
        <script type="text/javascript" src="/fancybox/source/jquery.fancybox.pack.js?"></script>


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
    var facebook_images_selected = new Array();
    var instagram_images_selected = new Array();
        
        
    $(document).ready(function(e){
<?php checkNotifications(); ?>
    });      
        
    $('#file').change(function(){
        var file_name = $(this).val();
    });
      
            
    function isLoading(status){
        if (status=="true"){
            $("#loading").show();
        }
        else if (status=="false"){
            $("#loading").hide();
        }
    }
            
            
    function fileName() {
        var filename= $('#file').val();
        var lastIndex = filename.lastIndexOf("\\");
        if (lastIndex >= 0) {
            filename = filename.substring(lastIndex + 1);
        }
        $("#fakeupload").val(filename);
    }
            
    function clearInputs(){
        $("#url").val('');
        $("#file").replaceWith("<input name='image' id='file' type='file' onchange='fileName()' style='display:none;' />");
        $("#fakeupload").val("Upload File");
    }
            
    function sendImage(){
        if ($("#url").val()){
            document.forms["urlform"].submit();
            //submit to urlprocessing
        }
        else if($("#file").val()){
            document.forms["fileform"].submit();
        }
        else{
            window.location = "/extraction";
        }
    }
    function OpenFiles(){
        clearInputs();
        document.fileform.image.click();
    }
            

 
    function changePicture(){
        document.fileForm.image.click();
    }
    
    function submitPicture() {
        document.fileForm.submit();
    }
        
        </script>

        <style>
            #uploadHeading{
                margin-top:25px;
                font-size:30px;
            }
            #uploadContainer{
                width:100%;
            }
            .uploadText{
                width:100%;
                text-align:center;
                font-family:"Quicksand";
            }
            .thumbnail_frame{
                width: 200px; 
                height: 200px;
                overflow: hidden;
                position:absolute;
                padding:0px;
                background-color:#fbfbfb;
                border:transparent 6px solid;
                border-radius:250px 250px 250px 250px;
            }
            .added {
                border-color: #5bb75b;
            }
            #fakeupload{
                width:100%;
                border:1px ridge #808285;
                color:white;
                background-color:#51BB75;
                border-radius:3px;
                padding-left:15px;
                padding-right:15px;
                padding-top:5px;
                padding-bottom:5px;
                font-family:"Quicksand";
                font-size:18px;
            }
            #fakeupload:hover{
                cursor:pointer; 
            }
            .hexIcon{
                width:80%;
                margin-left:10%;
                margin-right:10%;
                height:auto;
            }
        </style>
    </head>
    <body>
        <?php initiateNotification() ?>
        <?php commonHeader() ?>
        <img src="/img/loading.gif" id="loading"/>
        <br/><br/><br/>
        <div id="mobileContainer">
            <span id="uploadHeading">UPLOAD A PICTURE</span>
            <div id="uploadContainer">
                <img src="/img/uploadImage.png" class="hexIcon"></img><br/>
                <div class="uploadText">(jpg, png, gif)</div><br/>
                <div class="upload_form">
                    <form enctype="multipart/form-data" id="fileForm" class="upload_form" name="fileForm" action="/upload_processing.php?type=image" method="post" accept="image/gif,image/jpeg,image/png">
                        <input name="image" id="file" type="file" onchange="submitPicture()" style="z-index:-1;opacity:0;position:absolute;" />
                        <input type="button" id="fakeupload" onclick="changePicture()" value="Browse">
                    </form>
                    <br/><br/>
                </div>
            </div>
        </div>
    </body>
</html>