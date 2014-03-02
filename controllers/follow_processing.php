<?php

session_start();
include('../connection.php');
include('../database_functions.php');
include('../global_tools.php');

$userid = $_SESSION['userid'];
$leaderid = $_POST['follow_userid']; // the person the user wants to follow
// the query is actually backwards, where the userid would be the followerid

if ($userid != $leaderid) {// you can't follow yourself
    $follow = database_fetch("follow", "userid", $leaderid, "followerid", $userid);
    $time = time();
    if ($follow) {
        database_delete("follow", "userid", $leaderid, "followerid", $userid);
        database_decrement("user", "userid", $leaderid, "followers", "1");
        database_decrement("user", "userid", $userid, "following", "1");
        $follow_status = "unfollowed";
        
        // Delete the notification if it's still unseen
        database_delete("notification", "userid", $leaderid, "from_userid", $userid, "type", "1", "seen", "0");
        
    } elseif (!$follow && $userid) {
        database_insert("follow", "userid", $leaderid, "followerid", $userid, "time", $time);
        database_increment("user", "userid", $leaderid, "followers", "1");
        database_increment("user", "userid", $userid, "following", "1");
        $follow_status = "followed";
        $followUser = database_fetch("user", "userid", $userid);
        $leaderUser = database_fetch("user", "userid", $leaderid);
        $message = emailTemplate($followUser['username'] . " is now following you on hueclues!");
        $header = "MIME-Version: 1.0" . "\r\n";
        $header .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
        $header .= "From: Hueclues <noreply@hueclues.com>" . "\r\n"
                . 'Reply-To: noreply@hueclues.com' . "\r\n";
        mail($leaderUser['email'], "New follower on hueclues", $message, $header);

        // Add a new notification to database
        database_insert("notification", "userid", $leaderid, "from_userid", $userid, "itemid", "NULL", "time", $time, "type", "1");
    }
}
echo json_encode(array('status' => $follow_status));
?>
