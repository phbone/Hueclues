<?php
session_start();
include('connection.php');
include('global_objects.php');
include('global_tools.php');
include('database_functions.php');


$userid = $_SESSION['userid'];
$query = $_GET['q'];
$searchQuery = "SELECT * FROM user WHERE username LIKE '%" . $query . "%'";
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initiateTools() ?>
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <link rel="stylesheet" type="text/css" href="/css/global.css" />
        <script type="text/javascript">
            var userid = '<?php echo $userid ?>';
<?php initiateTypeahead(); ?>




        </script>
    </head>
    <style>
        #tag_container{
            margin:auto;
            width:1130px;
            margin-top:150px;
            opacity:0.8;
            padding:15px;
            position:relative;
            background-color:white;
            height:auto;
            padding-top:25px;
            min-height:440px;
        }
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
        #userSearchResults{
            width:600px;
            margin:auto;
            position:relative;
        }
        
    </style>
    <body>
        <img src="/img/loading.gif" id="loading" />
        <?php commonHeader(); ?>
        <div id="tabs_container">
            <div class="divider">
                <hr class="left" style="width: 33%;">
                <span id="mainHeading">SEARCH RESULTS</span>
                <hr class="right" style="width: 33%;">
            </div>
            <span class="queryTitle"><?php echo $query ?></span><br/><br/>

            <div id="userSearchResults">
                <?php
                $searchResults = mysql_query($searchQuery);
                while ($user = mysql_fetch_array($searchResults)) {
                    formatUserSearch($user['userid']);
                }
                ?>

            </div>
            
        </div>
    </body>
</html>