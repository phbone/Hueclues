<?php

function formatUser($userid, $otherUserid) {
// Input: passes in the userid of the logged in user and userid of the other users
// Ouput: User preview, with follow/unfollow options
    if ($userid != $otherUserid) {
        $user = database_fetch("user", "userid", $otherUserid);
        echo "<div id='user" . $otherUserid . "' class='userContainer'>
    <a href = '/closet/" . $user['username'] . "' class='userPreview'>
       <img class='followUserPicture' src='" . $user['picture'] . "'></img>
        <div class='followUserText'>" . $user['username'] . "
            <br/><span class='followerCount'>" . $user['followers'] . " followers</span></div></a>        
    <button id='followaction" . $user['userid'] . "' class='greenFollowButton " . ((database_fetch("follow ", "userid", $user['userid'], "followerid", $userid)) ? 'clicked' : '') . "'
            onclick='followButton(" . $user['userid'] . ")'>" . ((database_fetch("follow ", "userid", $user['userid'], "followerid", $userid)) ? "following" : "follow") . "</button><br/>
</div>";
    }
}

function formatUserSearch($ownerid, $link = "true", $followButton = "true") {
    // returns a profile stamp of the input ownerid

    $userid = $_SESSION['userid'];
    $owner = database_fetch("user", "userid", $ownerid);
    
    // check if logged in user is already following ownerid
    $alreadyFollowing = database_fetch("follow", "userid", $ownerid, "followerid", $userid);

    if ($link == "true") {
        // links to the closet
        $closetLink = "href='/closet/" . $owner['username'] . "'";
    }
    if ($followButton == "true" && $userid!= $ownerid && !$alreadyFollowing) {
        $followHtml = "<button id='followaction$ownerid' class='greenButton cornerFollowButton' onclick='followButton($ownerid)'>follow</button> ";
    }

    echo "<a $closetLink class='userSearchLink'>
        <div class='userSearchContainer'>" . $followHtml . "
                <img class='userSearchPicture' src='" . $owner['picture'] . "'></img>
                <span class='userSearchName'>" . $owner['username'] . "</span>
                <span class='userSearchBio'>" . $owner['bio'] . "</span><br/>
                <div id='follow_nav'>
                    <div class='userSearchDetail'>
                        <span class='userSearchCount' id='following_btn'>" . $owner['itemcount'] . "</span>
                        <br/>items 
                    </div> 
                    <div class='userSearchDetail'>
                        <span class='userSearchCount' id='follower_btn'>" . $owner['outfitcount'] . "</span>
                        <br/>outfits˙
                    </div>
                    <div class='userSearchDetail'>
                        <span class='userSearchCount' id='following_btn'>" . $owner['following'] . "</span>
                        <br/>following 
                    </div>
                    <div class='userSearchDetail'>
                        <span class='userSearchCount' id='follower_btn'>" . $owner['followers'] . "</span>
                        <br/>followers
                    </div>
                </div>
             </div>
             </a>";
}

function formatFollowButton() {
    
}



function getFollowing($userid){
    // gets all the userids of the people user is following
    
    $followArray = array();
    // query rows where $userid is follower
    $followQuery = database_query("follow", "followerid", $userid);
    while($follow = mysql_fetch_array($followQuery)){
        $followArray[] = $follow['userid'];
    }
    return $followArray;
}
?>
