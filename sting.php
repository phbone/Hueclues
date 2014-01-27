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
            padding-top:10px;
            padding:5px;
            width:500px;
            margin: auto;
            background-color:#<?php echo $query ?>;
            color:#<?php echo fontColor($query); ?>
        }
    </style>
    <body>
        <img src = "/img/loading.gif" id = "loading" />
        <?php commonHeader();
        ?>
        <div id="tabs_container">
            <div class="divider">
                <hr class="left" style="width: 33%;">
                <span id="mainHeading">SEARCH COLORS</span>
                <hr class="right" style="width: 33%;">
            </div>
            <span class="queryTitle">RESULTS FOR #"<?php echo $query; ?>"</span><br/><br/>
            <?php
            stingColor($query);
            ?>
        </div>
    </body>
</html>