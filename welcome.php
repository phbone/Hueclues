<?php
session_start();
include('connection.php');
include('global_objects.php');
include('global_tools.php');
include('database_functions.php');

$userid = $_SESSION['userid'];
?>
<!doctype html>
<html>
    <head> 
        <?php initiateTools() ?>
        <script src="http://code.jquery.com/jquery-latest.js"></script>
        <script src="/js/global_javascript.js" type="text/javascript" charset="utf-8" ></script>
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <link rel="stylesheet" type="text/css" href="/css/global.css" />
        <script>
            var userid = '<?php echo $userid ?>';
            var num = 1;
            var followCount = 5;
            $(document).ready(function(e){
                $("#loading").show();
                if(!userid){
                    Redirect('/');
                }
                $('<img/>').attr('src', '/img/Orientation_1.jpg').load(function() {
                    showTutorial();
                    $("#welcomeTutorial").fadeIn();
                });
                $("#loading").hide();
            });
    
            function showTutorial(){
                if(num == 5){
                    $("#welcomeStart").show();
                    $("#welcomeTutorial").hide();
                }
                else if(num == 6){
                    $("#welcomeUpload").show();
                    $("#welcomeFollow").hide();
                }
                $("#img"+num).show();
                num++;
              
            }
            
            
            
            function followButton(follow_userid){
                $("#loading").show();
                // REQUIRES JAVASCRIPT USERID IF NOT WON'T WORK'
                $.ajax({
                    type: "POST",
                    url: "/follow_processing.php",
                    data: {
                        'follow_userid': follow_userid, 
                        'userid': userid
                    },
                    success: function(html){
                        followObject = jQuery.parseJSON(html);
                        if(followObject.status == "followed"){
                            $("#user"+follow_userid).fadeOut().slideUp();
                            followCount=followCount-1;
                            if(followCount==0){
                                $("#welcomeFollow").hide();
                                $("#welcomeUpload").fadeIn();
                            }
                            else if(followCount == 1){
                                $("#followCountText").text(followCount+ " closet");   
                            }
                            else{
                                $("#followCountText").text(followCount+ " closets");
                            }
                        }
                        else  if (followObject.status == "unfollowed"){
                            $("button#followaction"+follow_userid).html("follow");
                            $("button#followaction"+follow_userid).removeClass("clicked");
                        }
                        $("#loading").hide();
                    }
                });
               
            }
        </script>
        <style>
            #welcomeHeading{
                font-size:40px;
                font-family:"Century Gothic";
                color:#58595B;
            }
            .tutorialImage{
                display:none;
                left:0px;
                top:55px;
                height:595px;
                width:1130px;
                position:absolute;
                margin:auto;
            }
            #welcomeTutorial{
                width:1130px;
                height:auto;
                margin:auto;
                position:relative;
            }
            #welcomeContainer{
                width:100%;
                height:100%;
                position:relative;
                margin:auto;
            }
            .divider hr {
                width:31%;
            }

            #welcomeStart{
                top:175px;
                left:0px;
                width:100%;
                position:absolute;
            }
            #welcomeFollow{
                top:-250px;
                left:525px;
                position:absolute;
                opacity:0.8;
            }
            #topText{
                font-family:"Century Gothic";
            }
            .hexLeft{
                border-right: 65px solid #51BB75;
            }

            .hexMid{
                opacity:0.85;
                float: left;
                width: 112px;
                height: 200px;
                background-color:#51BB75;
            }

            .hexRight{
                border-left: 65px solid #51BB75;
            }
            .hexRight, .hexLeft{  
                float: left;
                border-top: 100px solid transparent;
                border-bottom: 100px solid transparent;
                opacity:0.85;
            }
            #hexTest1{
                top:290px;
                left:-105px;
                font-size:20px;
                position:relative;
                z-index:3;
                text-align:center;
                color:white;
            }
            #hexTest2{
                top:135px;
                position:relative;
                z-index:3;
                text-align:center;
                color:white;
                cursor:pointer;
                font-size:20px;
            }
            #hexagon1{
                left:-150px;
                top:215px;
                position:absolute;
                width:400px;
                z-index:-1;
            }
            #hexagon2{
                display:block;
                position:relative;
                margin:auto;
                height:200px;
                width:250px;
            }
            #hexagon2:hover, #hexText2:hover{
                cursor:pointer;
            }
            #nextButton{
                left:1130px;
                top:55px;
                width:100px;
                height:592px;
                font-size:25px;
                text-align:center;
                position:absolute;
            }
        </style>
    </head>
    <body>      
        <img src="/img/loading.gif" id="loading" />
        <?php commonHeader(); ?>
        <div id="welcomeContainer">


            <div id="welcomeTutorial" style="display:none;">
                <img src="/img/Orientation_1.jpg" alt="img1" class="tutorialImage" id="img1"  style="z-index:1">
                <img src="/img/Orientation_2.jpg" alt="img2" class="tutorialImage" id="img2"  style="z-index:2">
                <img src="/img/Orientation_3.jpg" alt="img3" class="tutorialImage" id="img3"  style="z-index:3">
                <img src="/img/Orientation_4.jpg" alt="img4" class="tutorialImage" id="img4"  style="z-index:4">
                <button id="nextButton" class="greenButton" onclick="showTutorial()">></button>
            </div>

            <div id="welcomeStart" style="z-index:4;display:none;">
                <div class="divider">
                    <hr class="left"/>
                    <span id="welcomeHeading">
                        LET'S GET STARTED
                    </span>
                    <hr class="right" />
                </div>



                <div id="welcomeFollow">
                    <div id="hexTest1">Follow <span id="followCountText">5 closets</span><br/>to continue.</div>

                    <div id="hexagon1" >
                        <div class="hexLeft"></div>
                        <div class="hexMid"></div>
                        <div class="hexRight"></div>
                    </div>
                    <div id="topLabel">
                        <span id="topText" onclick="flipRequest('top')">TOP CLOSETS</span></div>
                    <div id="topContainer" style="height:235px;">
                        <div id="top" class="previewContainer">
                            <?php
                            $most_followed_query = "SELECT * FROM user ORDER by followers desc LIMIT 25";
                            $most_followed_result = mysql_query($most_followed_query);
                            while ($most_followed = mysql_fetch_array($most_followed_result)) {
                                formatUser($userid, $most_followed['userid']);
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <div id="welcomeUpload" style="display:none;">
                    <div id="hexTest2" onclick="Redirect('/upload')">Click here to<br/> upload your first<br/> item!</div>
                    <div id="hexagon2" onclick="Redirect('/upload')">
                        <div class="hexLeft"></div>
                        <div class="hexMid"></div>
                        <div class="hexRight"></div>
                    </div>
                </div>
            </div>
    </body>
</html>