<?php
session_start();
include('connection.php');
include('global_objects.php');
include('global_tools.php');
include('database_functions.php');


$userid = $_SESSION['userid'];
$tags_string = $_GET['q'];
$search_string = str_replace(" ", "", $tags_string);
$tags_array = explode("#", $search_string);
array_shift($tags_array);
$tags_count = count($tags_array);
$matching_itemid_array = array();
$tags_id = array();



for ($tag_index = 0; $tag_index < $tags_count; $tag_index++) {
    $tag = database_fetch("tag", "name", $tags_array[$tag_index]);
// leave priority optimization out for now
// note priority is based on how many items have that tag
// naturally you begin with the one that has the lowest number of items tagged
    $tags_id[$tag_index] = $tag['tagid'];
}

// algorithm for this search
// begin with the first tag, get all items with that tag
// look at the second tag, if there are any items in the second tag
// that is in the array, keep them, remove the rest of the items
// keep doing this

$i = 0;
// first tag
$tagmap_query = database_query("tagmap", "tagid", $tags_id[$i]);
while ($tagmap = mysql_fetch_array($tagmap_query)) {
    $matching_itemid_array[$i] = $tagmap['itemid'];
    $i++;
}

if ($tags_count > 1) { // multiple tags
    for ($index = 0; $index < count($matching_itemid_array); $index++) {
// loops through items in the first tag
        $itemid = $matching_itemid_array[$index];
// for each item, see if it is linked with the other tags
// k starts at 1, since the first tag has already been looked at
        for ($k = 0; $k < $tags_count; $k++) {
// loops through all of the other tags, the item must be
//tagged with all of the other tags
            $existance = database_count("tagmap", "itemid", $itemid, "tagid", $tags_id[$k]);
            if (!$existance || $existance == 0) {
// if there is even one tag that it doesn't have, remove it from the list
                $matching_itemid_array[$index] = "";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initiateTools() ?>
        <script type="text/javascript">
            var userid = '<?php echo $userid ?>';
<?php initiateTypeahead(); ?>

<?php checkNotifications(); ?>

            $(document).ready(function(e) {
                bindActions();
            });



        </script>
    </head>
    <style>
        #tagHeading{
            font-size:40px;
            font-family:"Century Gothic";
            left:100px;
            top:100px;
            position:absolute;
            color:#58595B;
        }
        .queryTitle{
            font-size:30px;
            display:block;
            text-align:center;
            position:relative;
            width:auto;
        }
    </style>
    <body>
        <?php initiateNotification() ?>
        <?php commonHeader(); ?>
        <img src="/img/loading.gif" id="loading" />
        <div id="tabs_container">
            <div class="divider">
                <hr class="left" style="width: 33%;">
                <span id="mainHeading">SEARCH TAGS</span>
                <hr class="right" style="width: 33%;">
            </div>
            <span class="queryTitle">RESULTS FOR "<?php echo $tags_string ?>"</span><br/><br/>

            <?php
            $result_count = count($matching_itemid_array);
            for ($i = 0; $i < $result_count; $i++) {
                if ($matching_itemid_array[$i]) {
                    $item_object = new item_object;
                    $item_object = returnItem($matching_itemid_array[$i]);
                    formatItem($userid, $item_object);
                }
            }
            ?>
        </div>
    </body>
</html>