<?php
session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');

$notification = $_SESSION['upload_notification'];
$userid = $_SESSION['userid'];
$user = database_fetch("user", "userid", $userid);
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initiateTools() ?>
        <script type="text/javascript" src="/js/global_javascript.js"></script>
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <link rel="stylesheet" href="/fancybox/source/jquery.fancybox.css?" type="text/css" media="screen" />
        <script type="text/javascript" src="/fancybox/source/jquery.fancybox.pack.js?"></script>

        <link rel="stylesheet" type="text/css" href="/css/global.css" />
        <link rel="stylesheet" type="text/css" href="/css/account.css" />
        <script>
<?php initiateTypeahead(); ?>

            $(document).ready(function(e) {
<?php checkNotifications(); ?>
            });


            function changePicture() {
                document.pictureForm.image.click();
            }

            function submitPicture() {
                document.pictureForm.submit();
            }
        </script>
    </head>
    <body>
        <?php initiateNotification() ?>
        <?php commonHeader(); ?>
        <div id="account_container">
            <form enctype="multipart/form-data" id="pictureForm" accept="image/jpeg" name="pictureForm" method="POST" action="/profilepicture_processing.php">
                <input name="image" type="file" style="position:absolute;opacity:0;z-index:-1;" onchange="submitPicture()" name="pictureSrc" />
            </form>

            <div class="selfContainer" style="height:425px;">
                <span class="accountHeading">Update Your Account Settings</span>
                <br/><br/> 
                <img class='selfPicture' style="height:100px;position:relative;width:100px;margin:auto;display:block;" src="<?php echo $user['picture']; ?>" onclick='changePicture()'></img>
                <span style='font-size:10px;display:block;margin:auto;margin-top:10px;width:76px;text-align: center;'>click to change</span>
                <br/>
                <form action="/account_processing.php" method="POST">
                    <input type="text" autocomplete="off" class="fat_form" name="name" value ="<?php echo $user['name'] ?>" placeholder="Name" /><br/>
                    <input type="text" autocomplete="off" class="fat_form" name="bio" value ="<?php echo $user['bio'] ?>" placeholder="Who are you?" maxlength="40"/><br/>
                    <input type="password" autocomplete="off" class="fat_form" name="password" value ="<?php echo $user['password'] ?>" placeholder="Password" /><br/>

                    <input type="text" autocomplete="off" class="fat_form" name="email" value ="<?php echo $user['email'] ?>" placeholder="Email" /><br/><br/>
                    <button type="submit" style="display:block;margin:auto;" class="greenButton"/>UPDATE INFO</button>
                </form>
                <br/>
                <hr/>
                <a href="/logout.php" style="width:100px;margin:auto;display:block;"><button class="logoutButton" ><i class="icon-off" ></i>Logout</button></a>
            </div>
        </div>
    </body>
</html>
