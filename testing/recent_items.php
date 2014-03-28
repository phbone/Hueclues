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
                        $itemObject = returnItem($item['itemid']);
                        formatItem($userid, $itemObject);
                        
                        $hex = $item['code'];
                        // Convert the hex color into a 6 bit string
                        $r = strval(round(hexdec(substr($hex, 0, 2))*(7/255)));
                        $g = strval(round(hexdec(substr($hex, 2, 2))*(7/255)));
                        $b = strval(round(hexdec(substr($hex, 4, 2))*(7/255)));
                        $color6bit = $r . $g . $b;
                        
                        $R = ($color6bit[0] == '0')? '00' : dechex(intval($color6bit[0])*(255/7));
                        $G = ($color6bit[1] == '0')? '00' : dechex(intval($color6bit[1])*(255/7));
                        $B = ($color6bit[2] == '0')? '00' : dechex(intval($color6bit[2])*(255/7));
                        $hex = $R . $G . $B;
                        
                        echo "<span style='display:block; background-color:#".$hex."'>".$hex."</span>";
                    }
                ?>

            </div>

        </div>
    </body>
</html>