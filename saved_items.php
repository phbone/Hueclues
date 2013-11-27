<?php
session_start();
include('connection.php');
include('database_functions.php');

$userid = $_SESSION['userid'];
// your userid
$owner_username = $_GET['username'];
$owner = database_fetch("user", "username", $owner_username);
$closet_owner = $owner['userid'];
//// userid of the person whose closet your trying to see
if (!$userid && !$owner_username) {
    header("Location:http://hueclues.com");
}
if ($userid && !$owner_username) {
// sends you to your own closet
    $owner = database_fetch("user", "userid", $userid);
    header("Location:/closet/" . $owner['username']);
}
$owns_closet = ($userid == $closet_owner);
$item_count = $owner['itemcount'];
$useridArray[] = $owner['userid'];
$view = $_GET['view'];
include('global_tools.php');
include('global_objects.php');
$size = getimagesize($owner['picture']);
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initiateTools() ?>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <script src="/js/global_javascript.js" type="text/javascript" charset="utf-8" ></script>
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <script src="http://code.jquery.com/jquery-latest.js"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <link rel="stylesheet" type="text/css" href="/css/global.css" />
        <link rel="stylesheet" type="text/css" href="/css/closet.css" />
        <script type="text/javascript" >

<?php initiateTypeahead(); ?>

            var userid = "<?php echo $userid ?>";
            var useridArray = <?php echo json_encode($useridArray) ?>;
            console.log('<?php echo $view ?>');
            var offset = 0;
            var limit = 5;
            var database = 'item';
            if (<?php echo $item_count; ?> > 0) {
                var enablePagination = "1";
            }
            else {
                var enablePagination = "0";
            }

            $(document).ready(function(e) {
                bindActions();
                initiatePagination(database, useridArray);
                $('#filterInput').keyup(function() {
                    filterItems($('#filterInput').val())
                });
                flipView('<?php echo $view ?>');
            });

            function submitForm(formid) {
                document.getElementById(formid).submit();
            }


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
            function flipView(id) {
                // switches to item or outfits
                if (id == "closet") {
                    $("#itemBackground").fadeIn();
                    $("#outfitBackground").fadeOut();
                    enablePagination = "1";
                }
                else if (id == "outfit") {
                    enablePagination = "0";
                    $("#itemBackground").fadeOut();
                    $("#outfitBackground").fadeIn();
                }

            }
            function gotoCloset() {
                window.location.href = "/closet/" + $("#user_search").val();
            }
        </script>
    </head>
    <body>
        <?php commonHeader() ?>
        <img src="/img/loading.gif" id="loading"/>
        <div id="mainContainer">
            <?php
            $share_text = $owner['name'] . "%27s%20closet%20on%20hueclues";
            if ($owns_closet) {
                $share_text = "My%20closet%20on%20hueclues";
            }
            ?>
            <div class="selfContainer">
                <img class='selfPicture' src="<?php echo $owner['picture']; ?>" <?php
                if ($owns_closet) {
                    echo "onclick='Redirect(\"/account\")'";
                }
                ?> ></img>
                <span class="selfName">
                    <?php echo $owner['name'] . " (" . $owner['username'] . ")"; ?>
                </span>
                <span class="selfBio" onclick="Redirect('/account')">
                    <?php echo $owner['bio']; ?>
                </span>
                <br/>
                <div id="follow_nav">
                    <div class="selfDetail">
                        <span class="selfCount" id="following_btn" onclick="flipView('closet')"><?php echo $owner['itemcount']; ?>
                        </span>
                        <br/>items 
                    </div>
                    <div class="selfDetail">
                        <span class="selfCount" id="following_btn" onclick="flipRequest('following')"><?php echo $owner['following']; ?>
                        </span>
                        <br/>following 
                    </div>
                    <div class="selfDetail">
                        <span class="selfCount" id="follower_btn" onclick="flipRequest('followers')"><?php echo $owner['followers']; ?>
                        </span>
                        <br/>followers
                    </div>
                    <div class="selfDetail" title="to be released when we have 1000 users!">
                        <span class="selfCount" id="follower_btn" onclick="flipView('outfit')"><?php echo $owner['outfitcount']; ?>
                        </span>
                        <br/>outfits
                    </div>
                </div><br/>
                <?php
                if ($owns_closet) {
                    echo "<a href='/extraction'><button id='uploadItem' class='greenButton'>UPLOAD AN ITEM &nbsp<img class='buttonImage' src='/img/camera.png'></img></button></a>";
                } else {
                    echo "<button id='followaction" . $owner['userid'] . "' class='closetFollow greenFollowButton " . ((database_fetch("follow ", "userid", $owner['userid'], "followerid", $userid)) ? 'clicked' : '') . "' 
                    onclick='followButton(" . $owner['userid'] . ")'>" . ((database_fetch("follow ", "userid", $owner['userid'], "followerid", $userid)) ? "following" : "follow") . "</button>";
                }
                ?>
            </div>



            <div id="topContainer">
                <div id="followers" class="previewContainer">
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
                <div id="following" class="previewContainer" style="display:none;">
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
            </div> 
            <div id="itemBackground"> 
                <div class="divider">
                    <hr class="left"/>
                    <span id="mainHeading"><?php
                        // $other user refers to the person who you are trying to view
                        $other_user = database_fetch("user ", "userid", $closet_owner);
                        if ($other_user) {
                            echo "CLOSET";
                        } else {
                            echo "INVALID";
                        }
                        ?></span>
                    <hr class="right" />
                </div>
                <div id="shareContainer">Share:
                    <a onclick="window.open('http://www.facebook.com/sharer.php?u=http://hueclues.com/closet/<?php echo $owner_username; ?>', 'newwindow', 'width=550, height=400')" href="#">                    
                        <img class="shareIcon" src="/img/shareFacebook.png" ></img></a>
                    <a onclick="window.open('http://twitter.com/share?text=<?php echo $share_text . "&url=http://hueclues.com/closet/" . $owner_username; ?>', 'newwindow', 'width=550, height=400')" href="#">
                        <img class="shareIcon" src="/img/shareTwitter.png" ></img></a>
                </div>
                <input type='text' id='filterInput' placeholder="(Sort using hashtags) i.e pockets"></input>
                <br/><br/>
                <?php
                if ($owns_closet && $item_count == 0) {
                    echo "<a href='/upload' style='text-decoration:none;'><span class='messageGreen'>You dont have any items yet, add some now</span></a>";
                }
                ?>          

                <button id="loadMore" class="greenButton"  onclick="itemPagination(database, useridArray);">Load More...</button>

            </div>

            <div id="outfitBackground" style='display:none;'> 

                <?php
                if ($owns_closet) {
                    echo "<button id = 'createOutfitButton' class = 'greenButton bigButton' onclick = 'createOutfit()'>Create New Outfit</button><br/>";
                }
                $outfitQuery = database_query("outfit", "userid", $closet_owner);
                while ($outfit = mysql_fetch_array($outfitQuery)) {
                    formatOutfit($userid, $outfit['outfitid']);
                }
                ?>
            </div>

        </div>
    </body>
</html>