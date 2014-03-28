<?php
/************************************************************************************************
 * This file prints the most recent 100 items ordered by color to visually inspect color trends *
 ************************************************************************************************/

session_start();
include('../connection.php');
include('../database_functions.php');
include('../global_tools.php');
include('../global_objects.php');

$userid = $_SESSION['userid'];

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

                <?php
                    // Query the 100 most recent items then order by color
                    $query = "SELECT * FROM (SELECT * FROM item ORDER BY itemid DESC LIMIT 0,100) AS recent ORDER BY code";
                    $result = mysql_query($query);
                    if(!$result) echo "QUERY FAILED: ".$query;
                    
                    while($item = mysql_fetch_array($result)){
                        formatItem($userid, $item);
                    }
                ?>

            </div>

        </div>
    </body>
</html>