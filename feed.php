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


$userfollowing_query = database_query("follow", "followerid", $userid);
while ($follow = mysql_fetch_array($userfollowing_query)) {
//// people the user is following
    $friend_array[] = $follow['userid'];
}



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
        <script src="/js/global_javascript.js" type="text/javascript" charset="utf-8" ></script>
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <link rel="stylesheet" type="text/css" href="/css/global.css" />

        <script>

<?php initiateTypeahead(); ?>

            var followingArray = <?php echo json_encode($friend_array) ?>;
            var userid = "<?php echo $userid ?>";
            var offset = 0;
            var limit = 5; //get 5 items at a time
            var database = "item";
            if (<?php echo $user['following']; ?> > 0) {
                var enablePagination = "1";
            }
            else {
                var enablePagination = "0";
            }


            $(document).ready(function(e) {
                bindActions();
                initiatePagination(database, followingArray);
                $('#filterInput').keyup(function() {
                    filterItems($('#filterInput').val())
                });
            });


            function flipRequest(id) {
                if (id == "followers") {
                    $("#followers").fadeIn();
                    $("#following").hide();
                    $("#top").hide();
                }
                else if (id == "following") {
                    $("#following").fadeIn();
                    $("#followers").hide();
                    $("#top").hide();
                }
                else if (id == "top") {
                    $("#top").fadeIn();
                    $("#following").hide();
                    $("#followers").hide();
                }
            }




        </script>
    </head>
    <body>
        <?php include_once("analyticstracking.php") ?>
        <img src="/img/loading.gif" id="loading" />
        <?php commonHeader(); ?>
        <div id="mainContainer">

            <div id="topLabel"><span id="topText" onclick="flipRequest('top')">TOP CLOSETS</span></div>

            <div id="topContainer" style="top:210px;">
                <div id="followers" class="previewContainer" style="display:none;">
                    <br/>
                    <div class="linedTitle">
                        <span class="linedText">
                            Followers
                        </span>
                    </div>
                    <br/>
                    <?php
                    $follower_query = database_query("follow", "userid", $userid);
                    while ($follower = mysql_fetch_array($follower_query)) {
                        formatUser($userid, $follower['followerid']);
                    }
                    ?>
                </div>
                <div id="following" class="previewContainer" style="display:none;">
                    <br/>
                    <div class="linedTitle">
                        <span class="linedText">
                            Following
                        </span>
                    </div>
                    <br/>
                    <?php
                    $following_query = database_or_query("follow ", "followerid", $userid);
                    while ($following = mysql_fetch_array($following_query)) {
                        // shows who your closet is connected with
                        formatUser($userid, $following['userid']);
                    }
                    ?>
                </div>
                <div id="top" class="previewContainer">
                    <br/>
                    <div class="linedTitle">
                        <span class="linedText">
                            Most Followed
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
            <div id="itemBackground">
                <div class="divider">
                    <hr class="left"/>
                    <span id="mainHeading">THE HIVE</span>
                    <hr class="right" />
                </div>
                
                <input type='text' id='filterInput' placeholder="(Sort by keyword) i.e pockets"></input>
                
                
                <button id="loadMore" class="greenButton"  onclick="itemPagination(database, followingArray);">Load More...</button>

            </div>
        </div>
    </body>
</html>