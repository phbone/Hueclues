<?php
session_start();
require_once 'connection.php';
include('database_functions.php');
include('global_tools.php');
?>


<html>
    <head>
        <?php initiateTools() ?>
        <link rel="stylesheet" type="text/css" href="/css/global.css" />
        <link rel="stylesheet" type="text/css" href="/css/static.css" />
        <script type="text/javascript" src="/js/global_javascript.js"></script>
        <script src="http://code.jquery.com/jquery-latest.js"></script>

    </head>

    <body>
        <?php commonHeader(); ?>
        <div id='staticContainer'>
            <span class='jobText'>
                <span id='jobTitle'>Job Description</span><br/>

                <b>Position</b>: Content Manager<br/>

                <b>Purpose</b>: To aid in the process of maintaining awareness of current fashion trends and styles relevant to the target audience to improve the user experience.<br/>

                <b>Duties:</b><br/><br/>
                <ul>
                    <li>Consistently add relevant content to your hueclues account closet.</li>
                    <li>Stay up to date on the latest fashion trends.</li>
                    <li>Create a hueclues account for a familiar brand and regularly add brand specific items to the closet.</li>
                    <li>Promote hueclues through social media as advised.</li>
                </ul>

                <b>Qualifications:</b><br/><br/>
                <ul>
                    <li>Demonstrable interest in fashion </li>
                    <li>General understanding of social media</li>
                    <li>Frequent use of various social media platforms (facebook, instagram, twitter)</li>
                    <li>Reliability and dedication</li>

                </ul>
                <b>Benefits: </b><br/><br/>
                <ul>
                    <li>All content managers will be able to promote their own products to users through the URL feature on closet items (associate programs, promo codes, personal designs,blogs, etc.)</li>
                    <li>Content managers will be featured in the “Top Closets” and “Trending Page”, increasing their followers and reach.</li>
                    <li>Content managers will have all premium features added to hueclues available to their account for free.</li> 
                    <li>Content managers that demonstrate their dedication to the company and their ability to handle the position at an above satisfactory level, will be offered more involved and lucrative positions within the company based on their individual skill set.</li>
                </ul>
            </span>
        </div>
    </body>
</html>