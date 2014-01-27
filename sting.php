<?php
session_start();
include('connection.php');
include('global_tools.php');
include('global_objects.php');
include('database_functions.php');

$userid = $_SESSION['userid'];
$query = $_GET['q'];
?>

<!DOCTYPE html>
<html>
    <head>
        <?php initiateTools() ?>
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <script type="text/javascript">
            var userid = '<?php echo $userid ?>';
<?php initiateTypeahead(); ?>

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
        <img src="/img/loading.gif" id="loading" />
        <?php commonHeader(); ?>
        <div id="tabs_container">
            <div class="divider">
                <hr class="left" style="width: 33%;">
                <span id="mainHeading">SEARCH TAGS</span>
                <hr class="right" style="width: 33%;">
            </div>
            <span class="queryTitle">RESULTS FOR "<?php echo $query; ?>"</span><br/><br/>

            <?php
            stingColor($query);
            ?>
        </div>
    </body>
</html>