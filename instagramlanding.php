<?php
session_start();
include('connection.php');
include('database_functions.php');
?>

<html>
    <head>
        <script src="http://code.jquery.com/jquery-latest.js" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript">
            var token;
            var url;
            var test_url;
            
            function getToken(){
                var token_string = window.location.hash;
                token = token_string.replace("#access_token=", "");
                url = "https://api.instagram.com/v1/users/self/media/recent/?&access_token="+token+"&callback=?";
                test_url = "https://api.instagram.com/v1/users/3/media/recent/?access_token=344738753.f59def8.ac453c06f1cd45999b0d52aa312871b3";
                /*   
                $.getJSON( url, function(data){
                    $.each(data.data, function(key,item){
                        $.each(item, function(subKey, subitem){
                            var value= ( typeof subitem=='string')? subitem : subitem.length;
                            $('body').append('<div>'+subKey+' : '+value +'</div>');
                                 
                        })
                    });
                })
                 */
                var instagramData;
                $.ajax({
                    type: "GET",
                    url: url,
                    dataType: "jsonp",
                    cache: false,
                    success: function(instagramResponse){
                        $.each(instagramResponse.data, function(index) {
                            instagramData = instagramResponse.data[index];
                            $('body').append('<img src="'+instagramData.images.standard_resolution.url+'"></img>');
                        });
                    }
                });
            }
            
            
        </script>
    </head>
    <body onload="getToken()">


    </body>
</html>