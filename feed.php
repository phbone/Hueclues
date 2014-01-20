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
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <link rel="stylesheet" type="text/css" href="/css/global.css" />
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
                    trend.find("#trendingBackground").fadeOut();
                    trend.find("#topContainer").slideUp();
                    trendLabel.animate({top: '545px', opacity: 0.7});
                    trendLabel.promise().done(function() {
                        feedLabel.animate({opacity: 1});
                        feed.find("#feedBackground").fadeIn();
                        feed.find("#topContainer").slideDown();
                    });
                }
                else if (id == 'trending') {
                    feed.find("#feedBackground").fadeOut();
                    feed.find("#topContainer").slideUp();
                    trendLabel.animate({top: "210px", opacity: 1});
                    feedLabel.animate({opacity: 0.7});
                    trendLabel.promise().done(function() {
                        trend.find("#trendingBackground").fadeIn();
                        trend.find("#topContainer").slideDown();
                    });
                }
            }


            function viewItemsTaggedWith(tag) {
                $(".taggedItems").hide();
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
    $("#feedItemButton").css("background-color", "#51BB75");
    $("#feedOutfitButton").css("background-color", "#58595B");
}
function showOutfitToggle() {
    $("#outfitBackground").hide();
    $("#itemBackground").show();
    $("#feedOutfitButton").css("background-color", "#51BB75");
    $("#feedItemButton").css("background-color", "#58595B");
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
        </style>
    </head>
    <body>
        <img src="/img/loading.gif" id="loading" />
        <?php commonHeader(); ?>
        <div class="mainContainer" id="feed">

            <div  id="feedLabel" class="topLabel" style='opacity:0.7;'><span id="topText" onclick="feedTrendToggle('feed')" >FRIENDS' CLOSETS</span></div>
            <div id="trendingLabel" class="topLabel" style="top:210px;" onclick="feedTrendToggle('trending')"><span id="topText">WHAT'S BUZZING</span></div>



            <div id="topContainer" style="top:210px; display:none;">
                <div id="top" class="previewContainer">
                    <button id="feedItemButton"class="greenButton" style="width:167px; margin:0px; font-size:15px;" onclick="showOutfitToggle();">Items</button>
                    <button id="feedOutfitButton"class="greenButton" style="width:167px; margin:-1px; font-size:15px;" onclick="showItemToggle();">Outfits</button>
                    <br/>
                    <br/>
                    <div class="linedTitle">
                        <span class="linedText">
                            Popular
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
                    <input type='text' id='filterInput' placeholder="(Sort by keyword) i.e pockets"></input>
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
                            Tags
                        </span>
                    </div>
                    <br/>
                    <?php
                    $trendingItems = array();
                    $tagNames = array();
                    $numberOfTags = 10;
                    $tagsQuery = "SELECT * FROM tag ORDER BY count DESC LIMIT " . $numberOfTags;
                    $tagsResult = mysql_query($tagsQuery);
                    while ($tag = mysql_fetch_array($tagsResult)) {
                        echo "<span class='tagLinks' onclick=\"viewItemsTaggedWith('" . $tag['name'] . "')\">#" . $tag['name'] . "</span><br/>";
                        $tagNames[] = $tag['name'];
                        $trendingItems[] = $tag['tagid']; // get the tagid of the 10 most popular tags
                    }
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
                    <?php
                    $existingItems = array();
                    for ($i = 0; $i < $numberOfTags; $i++) {
                        // select 10 tags with the most 
                        $tagmapQuery = "SELECT * FROM tagmap WHERE tagid = '" . $trendingItems[$i] . "' ORDER BY tagmapid DESC LIMIT 10";
                        $tagmapResult = mysql_query($tagmapQuery);

                        while ($tagmap = mysql_fetch_array($tagmapResult)) {
                            $item = database_fetch("item", "itemid", $tagmap['itemid']);

                            // prevents an item appearing multiple times from having 2 trending tags
                            // prevents any items from friends 
                            if (!in_array($tagmap['itemid'], $existingItems) && !in_array($item['userid'], $friend_array)) {
                                $item_object = returnItem($tagmap['itemid']);
                                $tags = str_replace("#", " ", $item_object->tags);
                                echo "<div class='taggedItems" . $tags . "'>";
                                formatItem($userid, $item_object);
                                echo "</div>";
                                $existingItems[] = $tagmap['itemid'];
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>