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

function formatUserSearch($userid) {
    // returns a profile stamp of the input userid

    $owner = database_fetch("user", "userid", $userid);
    echo "<div class='userSearchContainer' onclick='Redirect(\"/closet/".$owner['username']."\")'>
                <img class='userSearchPicture' src='" . $owner['picture'] . "'></img>
                <span class='userSearchName'>" . $owner['name'] . "(" . $owner['username'] . ")</span>
                <span class='userSearchBio'>" . $owner['bio'] . "</span><br/>
                <div id='follow_nav'>
                    <div class='userSearchDetail'>
                        <span class='userSearchCount' id='following_btn'>" . $owner['itemcount'] . "</span>
                        <br/>items 
                    </div>
                    <div class='userSearchDetail'>
                        <span class='userSearchCount' id='following_btn'>" . $owner['following'] . "</span>
                        <br/>following 
                    </div>
                    <div class='userSearchDetail'>
                        <span class='userSearchCount' id='follower_btn'>" . $owner['followers'] . "</span>
                        <br/>followers
                    </div>
                    <div class='userSearchDetail'>
                        <span class='userSearchCount' id='follower_btn'>" . $owner['outfitcount'] . "</span>
                        <br/>outfits˙
                    </div>
                </div>
             </div>";
}

?>
