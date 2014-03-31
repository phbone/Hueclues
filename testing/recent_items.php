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

function convert_24bit_to_9bit($hex){
    $redValue = hexdec(substr($hex, 0, 2))*(7/255);
    $greenValue = hexdec(substr($hex, 2, 2))*(7/255);
    $blueValue = hexdec(substr($hex, 4, 2))*(7/255);

    $r = round($redValue, 0, PHP_ROUND_HALF_DOWN);
    $g = $r + round(round(($greenValue - $redValue)*10)/10);
    $b = $g + round(round(($blueValue - $greenValu)*10)/10);
    $color9bit = strval($r) . strval($g) . strval($b);
    
    return $color9bit;
}

function convert_9bit_to_24bit($color9bit){
    
    $R = ($color9bit[0] == '0')? '00' : dechex(intval($color9bit[0])*(255/7));
    $G = ($color9bit[1] == '0')? '00' : dechex(intval($color9bit[1])*(255/7));
    $B = ($color9bit[2] == '0')? '00' : dechex(intval($color9bit[2])*(255/7));
    $hex = $R . $G . $B;
    
    return $hex;
}

function deviation_magnitude($color9bit, $hex){
    
    $r = intval($color9bit[0]);
    $g = intval($color9bit[1]);
    $b = intval($color9bit[2]);
    
    $R = hexdec(substr($hex, 0, 2));
    $G = hexdec(substr($hex, 2, 2));
    $B = hexdec(substr($hex, 4, 2));
    
    $deviation = sqrt(pow($r - $R, 2) + pow($g - $G, 2) + pow($b - $B, 2));
    
    return $deviation;
    
}
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
                        
                        $color = $item['code'];
                        
                        $color9bit = convert_24bit_to_9bit($color);
                        $hex = convert_9bit_to_24bit($color9bit);
                        
                        echo "<span style='display:block; background-color:#".$hex."'>".$hex."</span>";
                    }
                ?>

            </div>

        </div>
    </body>
</html>