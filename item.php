<?php
session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');
include('global_objects.php');

$userid = $_SESSION['userid'];
$user = database_fetch("user", "userid", $userid);

$itemid = $_GET['itemid'];
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initiateTools() ?>
        <link rel="stylesheet" type="text/css" href="/css/global.css" />
        <script type="text/javascript" >
            var userid = '<?php echo $userid ?>';
<?php initiateTypeahead(); ?>
<?php checkNotifications(); ?>

            $(document).ready(function(e) {
                bindActions();
            });
        </script>
    </head>
    <body>
        <?php initiateNotification() ?>
        <?php commonHeader() ?>
        <img src="/img/loading.gif" id="loading"/>
        <div class="mainContainer">

            <div id="itemBackground" style="margin:auto"> 

                <?php formatItem($userid, returnItem($itemid)); ?>

            </div>

        </div>
    </body>
</html>