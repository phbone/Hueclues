<?php
session_start();
include('connection.php');
include('database_functions.php');
include('global_tools.php');

$notification = $_SESSION['upload_notification'];

$userid = $_SESSION['userid'];


$user = database_fetch("user", "userid", $userid);
?>
<!DOCTYPE html>
<html>
    <head>  
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0;" />
        <script type="text/javascript" src="/js/global_javascript.js"></script>
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <link rel="stylesheet" type="text/css" href="/css/global.css" />
        <link rel="stylesheet" type="text/css" href="/css/mobile.css" />
        <script>
    
            $(document).ready(function(e){
<?php checkNotifications(); ?>
    });
    function changePicture(){
        document.pictureForm.image.click();
    }
    
    function submitPicture() {
        document.pictureForm.submit();
    }
        </script>

        <style>
            .greeting{
                font-family:inherit;
                font-color:inherit;
                left:70px;
                position:absolute;
            }
            .selfPicture{
                height:40px;
                position:absolute;
                border:#58595B ridge 1px;
            }
            .selfPicture:hover{
                cursor:pointer;
                box-shadow: 1px 1px 5px #51BB75;
            }
            #pictureForm{
                width:100%;
            }
            .accountInput{
                width:95%;
                padding:1%;
                margin:1%;
            }
            .logoutButton{
                width:100px;
                padding:5px 15px;
                background-color:#808285;
                color:white;
                font-family:"Quicksand";
                border:1px ridge transparent;
                border-radius:3px;
            }
            .logoutButton:hover{
                cursor:pointer;
                backgroud-color:#58595B;
            }
        </style>
    </head>
    <body>
        <div id="mobileContainer">
            
            <form enctype="multipart/form-data" id="pictureForm" accept="image/jpeg" name="pictureForm" method="POST" action="/profilepicture_processing.php">
                <input name="image" type="file" style="z-index:-1;opacity:0;position:absolute;" onchange="submitPicture()" name="pictureSrc" />
            </form>
            <div class="selfContainer" style="height:400px;">
                <span class="accountHeading">Update Your Account Settings</span>
                <br/><br/> 
                <img class='selfPicture' style="height:70px;position:relative;width:70px;margin:auto;display:block;" src="/viewprofile.php?id=<?php echo $userid; ?>" onclick='changePicture()'></img>
                <span style='font-size:10px;display:block;margin:auto;margin-top:10px;width:76px'>click to change</span>
                <br/><br/>
                <form action="/account_processing.php" method="POST">
                    <input type="text" autocomplete="off" class="accountInput" name="name" value ="<?php echo $user['name'] ?>" placeholder="Name" /><br/>
                    <input type="text" autocomplete="off" class="accountInput" name="password" value ="<?php echo $user['password'] ?>" placeholder="Password" /><br/>
                    <input type="text" autocomplete="off" class="accountInput" name="email" value ="<?php echo $user['email'] ?>" placeholder="Email" /><br/><br/>
                    <button type="submit" style="display:block;margin:auto;" class="greenButton"/>UPDATE INFO</button>
                </form>
                <br/>
                <hr/>
                <a href="/logout.php" style="width:100px;margin:auto;display:block;"><button class="logoutButton" ><i class="icon-off" ></i>Logout</button></a>
            </div>
        </div>
    </div>      <?php initiateNotification() ?>
    <?php commonHeader() ?>
</body>
</html>
