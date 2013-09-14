<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
        <title>Company Info Parser</title>
        <script type="text/javascript" src="/js/bootstrap.min.js"></script>
        <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="/css/bootstrap-responsive.min.css" />
        <script src="http://code.jquery.com/jquery-latest.js"></script>
        <script language="Javascript" type="text/javascript">
            var string_name = "";
            $(document).ready(function() {
                $('#thefile_button').change(function(e) {
                    if (e.target.files != undefined) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            string_name +=  e.target.result;
                            $("#csv").val(string_name);
                        };
                        reader.readAsText(e.target.files.item(0));
                    }
                    return false;
                });
            });
            
            function uploadClick(){
                $("#thefile_button").trigger('click');
            }
        </script>
    </head>
    
    <body>
        <center><br/><br/>
        <form action="chameleon_processing.php" method="Post" enctype="multipart/form-data" id="form">         
            <input type="password" class="form" placeholder="password" name="password" /><br/>
            <input type="text" name="csv" id="csv" placeholder="csv i.e 'nike, burger king, adidas'" style="margin:0 auto;left:0px;height:35px;width:291px"/><br/>
            <img src="http://icons.iconarchive.com/icons/deleket/rounded-square/256/Button-Upload-icon.png" id="upfile1" width="50" onclick="uploadClick()" style="cursor:pointer" />
            <input type="file" class="form" accept=".csv" name="thefile" id="thefile_button" style="display:none" />

            <input type="submit" class="btn" id="submit" value="Send" style="width:250px;"/>
        </form>
        </center>
    </body>
</html>