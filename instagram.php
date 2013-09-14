<?php
session_start();
include('connection.php');
include('database_functions.php');


$client_id = "eb073737e50d41b9b0c8bd6c81125739";
$client_secret = "9c56891f658243179f2c9f7514a0b141";
$redirect_url = "http://hueclues.com/instagramlanding.php";
$auth_url = "https://api.instagram.com/oauth/authorize/?client_id=" . $client_id . "&amp;redirect_uri=" . $redirect_url . "&amp;response_type=token";

?>
<html>
    <body>

        <a href="<?php echo $auth_url ?>">Authenticate</a>

    </body>
</html>