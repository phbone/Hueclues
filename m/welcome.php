<?php
session_start();
include('connection.php');
include('global_objects.php');
include('global_tools.php');
include('database_functions.php');
include('algorithms.php');
include('header.php');
?>



<!doctype html>
<html>
    <head> 
        <?php initiateTools() ?>
        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width">
        <link rel="stylesheet" href="/css/example.css">
        <link rel="stylesheet" href="/css/font-awesome.min.css">
        <script type="text/javascript" src="/js/bootstrap.min.js"></script>
        <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="/css/bootstrap-responsive.min.css" />
        <link rel="stylesheet" type="text/css" href="/css/global.css" />
        <script type="text/javascript" src="/js/global_javascript.js"></script>
        <script src="http://code.jquery.com/jquery-latest.js"></script>
        <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
        <script src="/js/jquery.slides.min.js"></script>
        <script>
            $(function() {
                $('#slides').slidesjs({
                    width: 940,
                    height: 425,
                    navigation: false
                });
            });
        </script>
        <!-- End SlidesJS Required -->
        <!-- SlidesJS Optional: If you'd like to use this design -->
        <style>
            body {
                -webkit-font-smoothing: antialiased;
                font: normal 15px/1.5 "Helvetica Neue", Helvetica, Arial, sans-serif;
                color: #232525;
                padding-top:70px;
            }

            #slides {
                display: none
            }

            #slides .slidesjs-navigation {
                margin-top:3px;
            }

            #slides .slidesjs-previous {
                margin-right: 5px;
                float: left;
            }

            #slides .slidesjs-next {
                margin-right: 5px;
                float: left;
            }

            .slidesjs-pagination {
                margin: 6px 0 0;
                float: right;
                list-style: none;
            }

            .slidesjs-pagination li {
                float: left;
                margin: 0 1px;
            }

            .slidesjs-pagination li a {
                display: block;
                width: 13px;
                height: 0;
                padding-top: 13px;
                background-image: url(img/pagination.png);
                background-position: 0 0;
                float: left;
                overflow: hidden;
            }

            .slidesjs-pagination li a.active,
            .slidesjs-pagination li a:hover.active {
                background-position: 0 -13px
            }

            .slidesjs-pagination li a:hover {
                background-position: 0 -26px
            }

            #slides a:link,
            #slides a:visited {
                color: #333
            }

            #slides a:hover,
            #slides a:active {
                color: #9e2020
            }

            .navbar {
                overflow: hidden
            }
        </style>
        <!-- End SlidesJS Optional-->

        <!-- SlidesJS Required: These styles are required if you'd like a responsive slideshow -->
        <style>
            #slides {
                display: none
            }

            .container {
                margin: 0 auto
            }

            /* For tablets & smart phones */
            @media (max-width: 767px) {
                body {
                    padding-left: 20px;
                    padding-right: 20px;
                }
                .container {
                    width: auto
                }
            }

            /* For smartphones */
            @media (max-width: 480px) {
                .container {
                    width: auto
                }
            }

            /* For smaller displays like laptops */
            @media (min-width: 768px) and (max-width: 979px) {
                .container {
                    width: 724px
                }
            }

            /* For larger displays */
            @media (min-width: 1200px) {
                .container {
                    width: 1170px
                }
            }
        </style>
        <!-- SlidesJS Required: -->
    </head>
    <body>
        <div id="navigationbar">
            <?php commonHeader(); ?>
        </div>
        <!-- SlidesJS Required: Start Slides -->
        <!-- The container is used to define the width of the slideshow -->
        <div class="container">
            <div id="slides">
                <img src="http://www.flickr.com/photos/listenmissy/5087404401/" alt="Photo by: Missy S Link: http://www.flickr.com/photos/listenmissy/5087404401/">
                <img src="http://www.flickr.com/photos/parksdh/5227623068/" alt="Photo by: Daniel Parks Link: http://www.flickr.com/photos/parksdh/5227623068/">
                <img src="http://www.flickr.com/photos/27874907@N04/4833059991/" alt="Photo by: Mike Ranweiler Link: http://www.flickr.com/photos/27874907@N04/4833059991/">
                <img src="http://www.flickr.com/photos/stuseeger/97577796/" alt="Photo by: Stuart SeegerLink: http://www.flickr.com/photos/stuseeger/97577796/">
                <a href="#" class="slidesjs-previous slidesjs-navigation"><i class="icon-chevron-left icon-large"></i></a>
                <a href="#" class="slidesjs-next slidesjs-navigation"><i class="icon-chevron-right icon-large"></i></a>
            </div>
        </div>
        <!-- End SlidesJS Required: Start Slides -->
    </body>
</html>