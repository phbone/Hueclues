<?php

include('../facebook.php');

// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
            'appId' => '146921452113038',
            'secret' => 'a588a8b1fd399759e51c3553f44d0c35',
        ));

// Get User ID
$facebook_user = $facebook->getUser();
$_SESSION['facebook_user'] = $facebook_user;


if ($facebook_user) {

    $logoutUrl = $facebook->getLogoutUrl();
    try {
        // Proceed knowing you have a logged in user who's authenticated.
        $htmlreturn_string = "facebook_user:".$facebook_user;
        $user_profile = $facebook->api('/' . $facebook_user);
        $htmlreturn_string = "User Profile Successful";
    } catch (FacebookApiException $e) {
        // If the user is logged out, you can have a 
        // user ID even though the access token is invalid.
        // In this case, we'll get an exception, so we'll
        // just ask the user to login again here.
        error_log($e->getType());
        error_log($e->getMessage());
    }
} else {
    // No user, print a link for the user to login
}
?>
