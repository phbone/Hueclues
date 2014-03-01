<?php
session_start();
include('connection.php');
include('global_tools.php');
include('global_objects.php');
include('database_functions.php');
// $friend_array contains a list of userids composed of users attached to this account
// $update_array is a list of objects containing the relevant update information
//instagram API goes in here


$userid = $_SESSION['userid'];
if (!$userid) {
    header("Location:/");
}
$user = database_fetch("user", "userid", $userid);
database_update("user", "userid", $userid, "", "", "last_login_time", time());

$userfollowing_query = database_query("follow", "followerid", $userid);
while ($follow = mysql_fetch_array($userfollowing_query)) {
//// people the user is following
    $friend_array[] = $follow['userid'];
}
$friend_array[] = $userid;


//PAGINATION TEST
// pass query to pagination for 25 items
// receive 25 items in php item_objects in variables as a json response
// javascript picks up the slack and spits out 25 formatted items
// will need to create formatItem in javascript
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initiateTools() ?>
        <script>

<?php initiateTypeahead(); ?>

            var followingArray = <?php echo json_encode($friend_array) ?>;
            var userid = "<?php echo $userid ?>";
            var itemOffset = 0;
            var outfitOffset = 0;
            var limit = 5; //get 5 items at a time
            var database = "item";
            if (<?php echo $user['following']; ?> > 0) {
                var enablePagination = "1";
            }
            else {
                var enablePagination = "0";
            }


            function feedTrendToggle(id) {
                // id = feed or trend, which to open
                var feed = $("#feed");
                var feedLabel = $("#feedLabel");
                var trend = $("#trending");
                var trendLabel = $("#trendingLabel");

                if (id == 'feed') {
                    // view feed page
                    trend.find("#trendingBackground").fadeOut();
                    trend.find("#topContainer").slideUp();
                    // collapse triangle
                    trendLabel.find(".fa").removeClass("fa-caret-down").addClass("fa-caret-right");
                    trendLabel.animate({top: '545px', opacity: 0.7});
                    trendLabel.promise().done(function() {
                        feedLabel.animate({opacity: 1});
                        feed.find("#feedBackground").fadeIn();
                        feed.find("#topContainer").slideDown();
                        outfitPagination('outfit', followingArray);
                        feed.find("#feedButtons").fadeIn();
                        // uncollapse triangle
                        feedLabel.find(".fa").removeClass("fa-caret-right").addClass("fa-caret-down");

                    });
                }
                else if (id == 'trending') {
                    // view trending page
                    feed.find("#feedBackground").fadeOut();
                    feed.find("#feedButtons").fadeOut();
                    feed.find("#topContainer").slideUp();
                    // collapse triangle
                    feedLabel.find(".fa").removeClass("fa-caret-down").addClass("fa-caret-right");

                    trendLabel.animate({top: "210px", opacity: 1});
                    feedLabel.animate({opacity: 0.7});
                    trendLabel.promise().done(function() {
                        trend.find("#trendingBackground").fadeIn();
                        trend.find("#topContainer").slideDown();
                        // uncollapse triangle
                        trendLabel.find(".fa").removeClass("fa-caret-right").addClass("fa-caret-down");

                    });
                }
            }


            function viewItemsTaggedWith(tag) {
                // check if tag is hexcode or normal tag
                var isHex  = /(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(tag);
                if(isHex){
                $("#activeTagText").css("color", fontColor(tag));
                $("#trending").find("#itemBackground").css("background", "#" + tag);
                }

                $(".taggedItems").hide();
                $("#activeTagText").text("#" + tag);
                $("." + tag).fadeIn();
                bindActions();
            }


            $(document).ready(function(e) {
                bindActions();
                initiatePagination(database, followingArray);
                $('#filterInput').keyup(function() {
                    filterItems($('#filterInput').val())
                });
            });

            function showItemToggle() {
                $("#itemBackground").hide();
                $("#outfitBackground").show();
                $("#feedItemButton").removeClass("active");
                $("#feedOutfitButton").addClass("active");
            }

            function showOutfitToggle() {
                $("#outfitBackground").hide();
                $("#itemBackground").show();
                $("#feedOutfitButton").removeClass("active");
                $("#feedItemButton").addClass("active");
                outfitPagination('outfit', followingArray);
            }






        </script>
        <style>
            .tagLinks:hover{
                cursor: pointer;
                color: #51BB75;
            }
            .topLabel{
                cursor:pointer;
            }
            .feedTab{
                width:185px;
                margin:0px;
                font-size:15px;
                position:fixed;
                background-color:#DDD;
                margin-bottom:10px;
                color:#51BB75;
                opacity:1;
                top:200px;
                border:0px;
                cursor:pointer;
                padding-top:10px;
                padding-bottom:10px;
                margin-top:5px;
            }
            .feedTab.active{
                background:url('/img/bg.png');
            }
            #top{
                padding-top:10px;
            }
            #activeTagText{
                text-align:center;
                margin-bottom:25px;
                font-size:20px;
            }
            .topLabel i.fa{
                position:absolute;
                right:20px;
            }
            .colorTags{
                padding:5px;
                display:block;
                position:relative;
                margin:-7px 3px;
                cursor:pointer;
                text-align:center;
            }
            .colorTags:hover{
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <img src="/img/loading.gif" id="loading" />
        <?php commonHeader(); ?>
        <div class="mainContainer" id="feed">

            <div id="feedLabel" class="topLabel" style='opacity:0.7;' onclick="feedTrendToggle('feed')"><span id="topText">CLOSETS I FOLLOW</span><i class='fa fa-caret-right'></i></div>
            <div id="trendingLabel" class="topLabel" style="top:210px;" onclick="feedTrendToggle('trending')"><span id="topText">WHAT'S BUZZING</span><i class='fa fa-caret-down'></i></div>

            <div id="feedButtons" style="display:none;">
                <button id="feedItemButton" class="feedTab active" onclick="showOutfitToggle();" style="margin-left:185px;">Items</button>
                <button id="feedOutfitButton" class="feedTab" onclick="showItemToggle();">Outfits</button>
            </div>

            <div id="topContainer" style="top:250px; display:none;height:270px;">
                <div id="top" class="previewContainer">
                    <div class="linedTitle">
                        <span class="linedText">
                            Suggested Closets
                        </span>
                    </div>
                    <br/>
                    <?php
                    $most_followed_query = "SELECT * FROM user ORDER by followers desc LIMIT 100";
                    $most_followed_result = mysql_query($most_followed_query);
                    while ($most_followed = mysql_fetch_array($most_followed_result)) {
                        // id of person that has a lot of following and should appear in top closets
                        // check if user is already following them
                        $follow = database_fetch("follow", "userid", $most_followed['userid'], "followerid", $userid);
                        if (!$follow) {
                            formatUser($userid, $most_followed['userid']);
                        }
                    }
                    ?>
                </div>
            </div>
            <div id="feedBackground" style='display:none;'>
                <div id="itemBackground">
                    <div class="divider">
                        <hr class="left"/>
                        <span id="mainHeading">NEW ITEMS</span>
                        <hr class="right" />
                    </div>
                    <input type='text' id='filterInput' placeholder="filter items by #tags"></input>
                    <button id="loadMore" class="greenButton"  onclick="itemPagination('item', followingArray);">Load More...</button>
                </div>  

                <div id="outfitBackground" style='display:none;'>
                    <div class="divider">
                        <hr class="left"/>
                        <span id="mainHeading">NEW OUTFITS</span>
                        <hr class="right" />
                    </div>
                    <button id="loadMore" class="greenButton"  onclick="outfitPagination('outfit', followingArray);">Load More...</button>
                </div>
            </div>
        </div>



        <div class="mainContainer" id="trending">
            <div id="topContainer" style="top:260px;">
                <div id="followers" class="previewContainer">
                    <br/>
                    <div class="linedTitle">
                        <span class="linedText">
                            Colors
                        </span>
                    </div>
                    <br/><br/>
                    <?php
                    $trending = trendingHex();
                    $trendingTags = trendingTags();
                    ?>
                </div>
            </div>
            <div id="trendingBackground">
                <div id="itemBackground">
                    <div class="divider">
                        <hr class="left" style="width:35%;"/>
                        <span id="mainHeading">TRENDING</span>
                        <hr class="right" style="width:35%;" />
                    </div>
                    <div id="activeTagText"></div>
                    <?php
                    trendingItemsColor($trending);
                    trendingItems($trendingTags);
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>
