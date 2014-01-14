<?php
session_start();
require_once 'connection.php';
include('database_functions.php');
include('global_tools.php');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initiateTools() ?>
        <link rel="stylesheet" type="text/css" href="/css/global.css" />
        <link rel="stylesheet" type="text/css" href="/css/static.css" />

    </head>

    <body>
        <?php commonHeader(); ?>
        <div id='staticContainer'>
            <span class='staticText'>
                <span id='staticTitle'>FAQ</span><br/><br/>

                <span class='staticHeading'>What is hueclues?</span><br/><br/>hueclues is a social platform where users promote, manage and select clothing. Just upload a photo, select the color of the garment and add a description to add an item to your virtual closet and share it with your followers. After, use our match function to perfect your look, create and save outfits, and shop through our integrated online store.<br/><br/>

                <span class='staticHeading'>What are Outfits?</span><br/><br/>Outfits are a useful way to keep track of color matches and styles that spark your interest. By clicking the “+ to outfit” button on items you are able to name and save styles for future use. Outfits can be made out of all hueclues items including your items, and the items of those that you follow.<br/><br/>

                <span class='staticHeading'>Why did you call it hueclues?</span><br/><br/>hueclues offers users multiple color schemes to use when matching items in their closet based on color theory algorithms. The name hueclues stems from the two words: hue- representing color, and clues- representing the aid we provide in finding a match<br/><br/>
                
                <span class='staticHeading'>Who can see my pictures?</span><br/><br/>Anyone signed up for hueclues can view any other users items using the search function, but only the items of the users that you follow will appear in the hive. Also, the trending page will feature the most popular items selected from the entire hueclues community. Although we are looking into adding certain privacy features in the future, we believe that keeping hueclues as public as possible allows for a better user experience.<br/><br/>
                
                <span class='staticHeading'>How can I make money using hueclues?</span><br/><br/>Since hueclues allows users to add a URL to uploaded items, users can use affiliate programs, personal websites and blogs to sell through the hueclues community. <br/><br/>
                
                <span class='staticHeading'>What is an affiliate program?</span><br/><br/>An affiliate program is an agreement with an e-commerce company that allows you to earn commission on sales that are a result of a link that they provide you with to promote their products on other websites.<br/><br/>
                
                <span class='staticHeading'>What other services are you compatible with?</span><br/><br/>hueclues allows users to log into their Facebook and Instagram accounts to quickly and easily upload all of their photos from these services to help you fill your hueclues closet.<br/><br/>
                
                <span class='staticHeading'>When are you going to make a mobile app?</span><br/><br/>We look forward to creating a mobile app to compliment the website and add to the hueclues experience, but first we would like to make sure that the website experience is as satisfying as possible.<br/><br/>
                
                <span class='staticHeading'>Are you hiring?</span><br/><br/>Yes, we are always looking for talented designers and engineers. For inquiries contact Bryan Wan at bryanwan23@gmail.com or Danny Brown at headleydanielbrown@gmail.com. <br/><br/>
                
                <span class='staticHeading'>Who do I contact for technical support?</span><br/><br/>For technical support, email Bryan Wan at bryanwan23@gmail.com or Danny Brown at headleydanielbrown@gmail.com.<br/><br/>
            </span>
        </div>
    </body>
</html>