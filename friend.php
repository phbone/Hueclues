<?php
session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');
include('global_objects.php');

$userid = $_SESSION['userid'];

$loggedIn = isset($userid);
    

$itemid = $_GET['itemid'];
$itemObject = returnItem($itemid);
$inputColor = $itemObject->hexcode;
// tolerance is for how specific color matches are
$saturation_tolerance = 100;
$light_tolerance = 100;
$hue_tolerance = 8.33;  

$userid = $_SESSION['userid'];
$user = database_fetch("user", "userid", $userid);
$colorObject = colorsMatching($inputColor);
$emptyMessage = "<br/><br/>Want More Matches?<br/>Invite Friends";
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initiateTools() ?>
        <link rel="stylesheet" href="/fancybox/source/jquery.fancybox.css?" type="text/css" media="screen" />
        <script type="text/javascript" src="/fancybox/source/jquery.fancybox.pack.js?"></script>
        <link rel="stylesheet" type="text/css" href="/css/huev1.css" />
        <script type="text/javascript">
            //tells you whether the tabs are pressed or not
<?php initiateTypeahead(); ?>


            function toggleCheckboxes() {
                if ($("#closetBox").is(':checked')) {
                    $(".closet").fadeIn();
                }
                else {
                    $(".closet").hide();
                }
                if ($("#followingBox").is(':checked')) {
                    $(".following").fadeIn();
                }
                else {
                    $(".following").hide();
                }
                if ($("#storeBox").is(':checked')) {
                    $(".store").fadeIn();
                }
                else {
                    $(".store").hide();
                }
            }
            var userid = '<?php echo $userid ?>';

            $(document).ready(function(e) {
                bindActions();
                genderFilter(2);
                enableSelectBoxes();
                $('#filterInput').keyup(function() {
                    filterItems($('#filterInput').val())
                });
                $(".selected").html("Filter By:");
                $('#shaScheme').bind('mouseenter', function() {
                    showDescription('sha');
                });
                $('#shaScheme').bind('mouseleave', function() {
                    hideDescription();
                });
                $('#anaScheme').bind('mouseenter', function() {
                    showDescription('ana');
                });
                $('#anaScheme').bind('mouseleave', function() {
                    hideDescription();
                });
                $('#compScheme').bind('mouseenter', function() {
                    showDescription('comp');
                });
                $('#compScheme').bind('mouseleave', function() {
                    hideDescription();
                });
                $('#triScheme').bind('mouseenter', function() {
                    showDescription('tri');
                });
                $('#triScheme').bind('mouseleave', function() {
                    hideDescription();
                });
            });


            function genderFilter(gender) {
                // gender:
                // 0 = female
                // 1 = male
                // 2 = unisex
                if (gender == 0) {
                    $(".1").slideUp();
                    $(".0").slideDown();
                }
                else if (gender == 1) {
                    $(".0").slideUp();
                    $(".1").slideDown();
                }
                else if (gender == 2) {
                    $(".1").slideDown();
                    $(".0").slideDown();
                }
            }
            function changeScheme(scheme) {
                $(".hovereffect").removeClass("clicked");
                $("#" + scheme + "Scheme").addClass("clicked");
                $(".schemePreview").hide();
                $("#itemSort").fadeIn();
                toggleCheckboxes();
                $(".matched").hide();
                $("." + scheme).fadeIn();
            }

            function showDescription(id) {
                var txt = new Array();
                txt["ana"] = "Offers a blend of colors that would appear together in nature.";
                txt["comp"] = "Matches with maximum contrast. ";
                txt["tri"] = "Matches the selected color with two well balanced color matches.";
                txt["sha"] = "Offers a lighter and darker shade of the selected color. ";

                $("#schemeDescription").html(txt[id]);

                $("#schemeDescription").prependTo($("#" + id + "Scheme").find(".schemePreview"));
                $("#schemeDescription").slideDown();
            }

            function hideDescription(id) {
                $("#schemeDescription").hide();
            }


        </script>
        <style>
        </style>
    </head>
    <body>
        <?php initiateNotification() ?>
        <img src="/img/loading.gif" id="loading" />
        <?php commonHeader(); ?>
        

        <div id="matchContainer">
            
        </div>

    </body>
</html>
