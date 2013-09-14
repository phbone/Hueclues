<?php
session_start();
include('connection.php');
include('database_functions.php');
include('algorithms.php');
include('global_tools.php');
$userid = $_SESSION['userid'];

$owner_username = $_GET['username'];
$owner = database_fetch("user", "username", $owner_username);
$closet_owner = $owner['userid'];
//// userid of the person whose closet your trying to see
if ($userid && !$owner_username) {
// sends you to your own closet
    $owner = database_fetch("user", "userid", $userid);
    $ownerUsername = $owner['username'];
    redirectTo("/closet/$ownerUsername");
}
if (!$userid && !$owner_username) {
    redirectTo("/");
}
$owns_closet = ($userid == $closet_owner);
$item_count = $owner['itemcount'];
$useridArray[] = $owner['userid'];
include('global_objects.php');
$size = getimagesize($owner['picture']);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0;" />
        <link rel="apple-touch-icon" href="/apple-touch-icon.png"/>
        <link rel="apple-touch-icon-precomposed" href="/apple-touch-icon-precomposed.png"/>

        <link rel="stylesheet" href="/css/mobile.css">
        <link rel="stylesheet" href="/css/global.css">
        <script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
        <script src="/js/global_javascript.js" type="text/javascript" charset="utf-8" ></script>

        <script type="text/javascript" >
    
            var userid="<?php echo $userid ?>";
            var useridArray = <?php echo json_encode($useridArray) ?>;
  
            var offset = 0;
            var limit = 5;
            var database = 'item';
            if(<?php echo $item_count; ?>>0){
                var enablePagination = "1";
            }
            else {
                var enablePagination = "0";
            }
            
            $(document).ready(function(e){
                bindActions();
                initiatePagination(database, useridArray);
            });
        
            function submitForm(formid){
                document.getElementById(formid).submit();
            }
            
            function flipRequest(id){
                $(".closetContainer").hide();
                $(".selfCount").removeClass('text_active');
                $("#"+id+"Page").fadeIn();
                $("#"+id+"Button").addClass('text_active');
            }
            
    
            function gotoCloset(){
                window.location.href = "/closet/"+$("#user_search").val();
            }
            function changePicture(){
                document.pictureForm.image.click();
            }
    
            function submitPicture() {
                document.pictureForm.submit();
            }
        </script>
        <style>
            div.closetContainer{
                opacity:0.8;
                margin-right:0px;
                width:100%;
                position:absolute;
                background-color:white;
                height:auto;
                margin-top:175px;
                padding-top:25px;
            }
            #share_container{
                font-size:15px;
                color:grey;
                display:inline-block;
            }

        </style>
    </head>
    <body>  
        <img src="/img/loading.gif" id="loading"/>
        <?php commonHeader() ?>

        <div id="mobileContainer">
            <div class="selfContainer">
                <a href="/account" style="text-decoration:none;float:right;"><img src='/img/gear.png' style="height:25px;"></img></a>
                <div class="selfCover">
                    <img class='selfPicture' src="<?php echo $owner['picture']; ?>" <?php
        if ($owns_closet) {
            echo "onclick='changePicture()'";
        }
        ?> ></img>
                </div>   
                <span class="selfName">
                    <?php echo $owner['name']; ?>
                </span><br/><br/>
                <div id="follow_nav">
                    <div class="selfDetail">
                        <span class="selfCount" id="itemButton" onclick="flipRequest('item')"><?php echo $owner['itemcount']; ?></span><br/>
                        items
                    </div>
                    <div class="selfDetail">
                        <span class="selfCount" id="followingButton" onclick="flipRequest('following')"><?php echo $owner['following']; ?>
                        </span><br/>following 
                    </div>
                    <div class="selfDetail">
                        <span class="selfCount" id="followerButton" onclick="flipRequest('followers')"><?php echo $owner['followers']; ?>
                        </span><br/>followers
                    </div>
                </div><br/>
            </div>
            <?php
            if ($owns_closet) {
                echo "<a href='/extraction'><button id='uploadItem' class='greenButton'>UPLOAD AN ITEM <img class='buttonImage' src='/img/camera.png'></img></button></a>";
            } else {
                echo "<button id='followaction" . $owner['userid'] . "' style='position:absolute;top:135px;padding-left:2.5%;padding-right:2.5%;width:100%;' class='greenButton " . ((database_fetch("follow ", "userid", $owner['userid'], "followerid", $userid)) ? 'clicked' : '') . "' 
                    onclick='followButton(" . $owner['userid'] . ")'>" . ((database_fetch("follow ", "userid", $owner['userid'], "followerid", $userid)) ? "unfollow" : "follow") . "</button>";
            }
            ?>


            <div id="followersPage" class="closetContainer previewContainer" style="display:none;">
                <br/>
                <div class="linedTitle">
                    <span class="linedText">
                        Followers
                    </span>
                </div>
                <br/>
                <?php
                $follower_query = database_query("follow", "userid", $closet_owner);
                while ($follower = mysql_fetch_array($follower_query)) {
                    formatUser($userid, $follower['followerid']);
                }
                ?>
            </div>
            <div id="followingPage" class="closetContainer previewContainer" style="display:none;">
                <br/>
                <div class="linedTitle">
                    <span class="linedText">
                        Following
                    </span>
                </div>
                <br/>
                <?php
                $following_query = database_or_query("follow ", "followerid", $closet_owner);
                while ($following = mysql_fetch_array($following_query)) {
                    // shows who your closet is connected with
                    formatUser($userid, $following['userid']);
                }
                ?>
            </div>


            <div id="itemPage"class="closetContainer">
                <span id="heading">
                    <?php
                    // $other user refers to the person who you are trying to view
                    if ($owner) {
                        echo $owner['username'] . "'s Closet</span><br/><br/>";
                    } else {
                        echo "This Closet Doesn't exist!</span><br/><br/>";
                    }
                    if ($owns_closet && $item_count == 0) {
                        echo "<span class='alert-info'><a href='/upload'>You dont have any items yet, add some now</a> </span>";
                    }
                    ?>  

                    <button id="loadMore" class="greenButton"  onclick="itemPagination();" style="position:relative;margin:auto;width:250px;height:30px;display:block;">Load More...</button>
            </div>
            <br/><br/>
        </div>
    </body>
</html>