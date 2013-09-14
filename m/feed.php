<?php
session_start();
include('connection.php');
include('global_tools.php');
include('database_functions.php');
include('algorithms.php');
// $friend_array contains a list of userids composed of users attached to this account
// $update_array is a list of objects containing the relevant update information
//instagram API goes in here

if (!$_SESSION['userid']) {
    redirectTo("/");
}
$userid = $_SESSION['userid'];

$userfollowing_query = database_query("follow", "followerid", $userid);
while ($follow = mysql_fetch_array($userfollowing_query)) {
//// people the user is following
    $friend_array[] = $follow['userid'];
}

$user = database_fetch("user", "userid", $userid);

include('global_objects.php');
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="apple-touch-icon" href="/apple-touch-icon.png"/>
        <link rel="apple-touch-icon-precomposed" href="/apple-touch-icon-precomposed.png"/>

        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0;" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black">

        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <link rel="stylesheet" href="/css/mobile.css">
        <link rel="stylesheet" href="/css/global.css">
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <script src="/js/global_javascript.js" type="text/javascript" charset="utf-8" ></script>
        <script>
<?php initiateTypeahead(); ?>
    var followingArray = <?php echo json_encode($friend_array) ?>;
    var userid = "<?php echo $userid ?>";
    var offset = 0;
    var limit = 5; //get 5 items at a time
    if(<?php echo $user['following']; ?>>0){
        var enablePagination = "1";
    }
    else {
        var enablePagination = "0";
    }
            
    $(document).ready(function(e){
        if(window.navigator.standalone){
            localStorage.username = '<?php echo $user['username']; ?>';
            localStorage.password = '<?php echo $user['password']; ?>';
        }
        bindActions();
        initiatePagination('item', followingArray);
    });
        
    function flipRequest(id){
        if(id=="followers"){
            $("#followers").fadeIn();
            $("#following").hide();
            $("#top").hide();
        }
        else if(id=="following"){
            $("#following").fadeIn();
            $("#followers").hide();
            $("#top").hide();
        }
        else if(id=="top"){
            $("#top").fadeIn();
            $("#following").hide();
            $("#followers").hide();
        }
    }
            
    (function(a,b,c){if(c in b&&b[c]){var d,e=a.location,f=/^(a|html)$/i;a.addEventListener("click",function(a){d=a.target;while(!f.test(d.nodeName))d=d.parentNode;"href"in d&&(d.href.indexOf("http")||~d.href.indexOf(e.host))&&(a.preventDefault(),e.href=d.href)},!1)}})(document,window.navigator,"standalone")
        </script>
    </head>
    <style>
        #followContainer{
            overflow-y:scroll;
            display:inline;
            padding-top:20px;
            height:160px;
            background-color:white;
            opacity:0.8;
            position:absolute;
            width:95;
            padding:10px;
        }
        #topContainer{
            height:auto;
            background-color:white;
            opacity:0.8;
            position:relative;
            width:95%;
            margin-top:25px;
            padding-left:2.5%;
            padding-right:2.5%;
            padding-top:20px;
        }
        #updateContainer{
            opacity:0.8;
            margin-right:0px;
            padding-left:2.5%;
            padding-right:2.5%;
            width:95%;
            position:relative;
            background-color:white;
            height:auto;
            margin-top:25px;
            padding-top:37px;
        }   
        #topLabel{
            text-align:center;
            position:absolute;
            font-size:25px;
            color: #51BB75;
            top:350px;
            font-family:"Quicksand";
            background: url('/img/bg.png');
            padding:10px;
            height:25px;
            width:290px;
        }

        .buttonImage{
            height:12px;
        }
        #topText{
            font-family:"Century Gothic";
        }
        #topText:hover{
            cursor:pointer;
        }
        #title:hover{
            color: #333;
            cursor:pointer;
        }
        a{text-decoration:none;}
    </style>
    <body>
        <?php commonHeader() ?>
        <img src="/img/loading.gif" id="loading"/>
        <div id="mobileContainer">
            <form action='/search_processing.php' id='searchForm' method ='GET' style='display:inline-block'>   
                <div class='input-append' style='display:inline;'>
                    <input id='searchInput' autocomplete='off' type='text' value='' name='q' placeholder='  search user or #tag' />
                    <button type='submit' id='searchButton'></button>
                </div>
            </form> 
            <div id="topContainer">
                <div id="top" class="previewContainer">
                    <div class="linedTitle">
                        <span class="linedText">
                            Top Closets
                        </span>
                    </div>
                    <br/>
                    <?php
                    $most_followed_query = "SELECT * FROM user ORDER by followers desc LIMIT 5";
                    $most_followed_result = mysql_query($most_followed_query);
                    while ($most_followed = mysql_fetch_array($most_followed_result)) {
                        formatUser($userid, $most_followed['userid']);
                    }
                    ?>
                </div>
            </div>
            <div id="updateContainer">

                <button id="loadMore" class="greenButton"  onclick="itemPagination();" style="position:relative;margin:auto;width:250px;height:30px;display:block;">Load More...</button>

            </div>
        </div>
    </body>
</html>