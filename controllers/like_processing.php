<?php

session_start();
include('../connection.php');
include('../global_tools.php');
include('../database_functions.php');

$userid = $_SESSION['userid'];
$itemid = $_GET['itemid'];

if (isset($userid)) {
    $item = database_fetch("item", "itemid", $itemid);
    if ($item['userid'] != $userid) { // you can't do anything with your own items
        $like = database_fetch("want", "userid", $userid, "itemid", $itemid);
        if ($like) {// like exists
            database_delete("want", "userid", $userid, "itemid", $itemid);
            database_decrement("item", "itemid", $itemid, "like_count", 1);
            $status = "unliked";
        } else { // like doesn't exist
            $time = time();
            // Like the item
            database_insert("want", "itemid", $itemid, "userid", $userid, "time", $time);
            database_increment("item", "itemid", $itemid, "like_count", 1);
            $status = "liked";
            // Notify the user by email
            $user = database_fetch("user", "userid", $userid);
            $item = database_fetch("item", "itemid", $itemid);
            $owner = database_fetch("user", "userid", $item['userid']);
            $to = $owner['email'];
            $subject = "You got a new like!";
            $message = emailTemplate($user['name'] . " (" . $user['username'] . ") has just liked your item '" . $item['description'] . "'");
            $header = "MIME-Version: 1.0" . "\r\n";
            $header .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
            $header .= "From: Hueclues <noreply@hueclues.com>" . "\r\n"
                    . 'Reply-To: noreply@hueclues.com' . "\r\n";
            mail($to, $subject, $message, $header);

            // Add a new notification to database
            database_insert("notification", "userid", $owner['userid'], "from_userid", $userid, "itemid", $itemid, "type", "0", "time", $time);
        }
    }
}

$item = database_fetch("item", "itemid", $itemid);
$like_count = $item['like_count'];
$error = mysql_error(); // check for any errors in mysql
echo json_encode(array('status' => $status, "count" => $like_count, 'error' => $error));
?>
