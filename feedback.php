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
        <link rel="stylesheet" href="/fancybox/source/jquery.fancybox.css?" type="text/css" media="screen" />
        <script type="text/javascript" src="/fancybox/source/jquery.fancybox.pack.js?"></script>
        <link rel="stylesheet" type="text/css" href="/css/account.css" />
        <script>
<?php initiateTypeahead(); ?>
            var userid = "<?php echo $userid ?>";
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
    </body>
</html>
