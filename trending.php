<?php
session_start();
include('connection.php');
include('global_tools.php');
include('global_objects.php');
include('database_functions.php');
include('algorithms.php');
$userid = $_SESSION['userid'];
?>
<html>
    <head>
        <?php initiateTools() ?>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script type="text/javascript" src="/js/global_javascript.js"></script>
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <link rel="stylesheet" href="/fancybox/source/jquery.fancybox.css?" type="text/css" media="screen" />
        <script type="text/javascript" src="/fancybox/source/jquery.fancybox.pack.js?"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <link rel="stylesheet" type="text/css" href="/css/global.css" />
        <script type="text/javascript">
<?php initiateTypeahead(); ?>

            var userid = '<?php echo $userid ?>';
            $(document).ready(function(e) {
                bindActions();
            });

            function viewItemsTaggedWith(tag) {
                $(".taggedItems").hide();
                $("." + tag).fadeIn();
                bindActions();
            }
        </script>
        <style>
            .tagLinks:hover{
                cursor: pointer;
                color: #51BB75;
            }
        </style>
    </head>
    <body>
        <img src="/img/loading.gif" id="loading"/>
        <?php commonHeader(); ?>
        <div id="mainContainer">

            <div id="topLabel"><span id="topText">Trending Tags</span></div>

            <div id="topContainer" style="top:210px;">
                <div id="followers" class="previewContainer">
                    <br/>
                    <div class="linedTitle">
                        <span class="linedText">
                            Tags
                        </span>
                    </div>
                    <br/>
                    <?php
                    $trendingItems = array();
                    $tagNames = array();
                    $numberOfTags = 10;
                    $tagsQuery = "SELECT * FROM tag ORDER BY count DESC LIMIT" . $numberofTags;
                    $tagsResult = mysql_query($tagsQuery);
                    while ($tag = mysql_fetch_array($tagsResult)) {
                        echo "<span class='tagLinks' onclick=\"viewItemsTaggedWith('" . $tag['name'] . "')\">#" . $tag['name'] . "</span><br/>";
                        $tagNames[] = $tag['name'];
                        $trendingItems[] = $tag['tagid']; // get the tagid of the 10 most popular tags
                    }
                    ?>
                </div>
            </div>
            <div id="itemBackground">
                <div class="divider">
                    <hr class="left" style="width:30%;"/>
                    <span id="mainHeading">Trending Styles</span>
                    <hr class="right" style="width:30%;" />
                </div>
                <?php
                $existingItems = array();
                for ($i = 0; $i < $numberofTags; $i++) {
                    $tagmapQuery = "SELECT * FROM tagmap WHERE tagid = '" . $trendingItems[$i] . "' ORDER BY tagmapid DESC LIMIT 10";
                    $tagmapResult = mysql_query($tagmapQuery);

                    while ($tagmap = mysql_fetch_array($tagmapResult)) {
                        if (!in_array($tagmap['itemid'], $existingItems)) {
                            $item_object = returnItem($tagmap['itemid']);
                            $tags = str_replace("#", " ", $item_object->tags);
                            echo "<div class='taggedItems" . $tags . "'>";
                            formatItem($userid, $item_object);
                            echo "</div>";
                            $existingItems[] = $tagmap['itemid'];
                        }
                    }
                }
                ?>
            </div>
        </div>
    </body>
</html>
