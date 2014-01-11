function flipTab(id) {
    if (id == "alltab") {
        $('.extractionPage').fadeIn();
        $('.usedImages').fadeIn();
    }
    else if (id == "unusedtab") {
        $('.extractionPage').fadeIn();
        $('.usedImages').fadeOut();
    }
    else {
        var idText = $('#' + id).text();
        $('.selectBox .selected').text(idText);
        $('.extractionPage').hide();
        $('.usedImages').fadeIn();
        $('#' + id + 'page').fadeIn();
    }
}

function RGBtoHex(R, G, B) {
    return toHex(R) + toHex(G) + toHex(B)
}

function toHex(N) {
    if (N == null)
        return "00";
    N = parseInt(N);
    if (N == 0 || isNaN(N))
        return "00";
    N = Math.max(0, N);
    N = Math.min(N, 255);
    N = Math.round(N);
    return "0123456789ABCDEF".charAt((N - N % 16) / 16)
            + "0123456789ABCDEF".charAt(N % 16);
}

function initiateCanvas() { // initially loads the images and procures the canvas 
    var canvas = document.getElementById("canvas");
    canvas.width = canvas.width; // clears the canvas
    img.src = img_src;
    img.onload = function() {
        context = document.getElementById('canvas').getContext('2d');
        context.drawImage(img, drawing_width, drawing_height, width, height);
        getoffsets();
    };
}

function previewColor() { // changes the border of the picture to match the pixel the mouse is hovering over
    data = context.getImageData(xcor, ycor, 1, 1).data;
    preview = RGBtoHex(data[0], data[1], data[2]);
    $("#canvas").css("border-color", "#" + preview);
}

function getColor(e) { // used to grab the color of the pixel at the x,y coordinate then plots the previews
    data = context.getImageData(xcor, ycor, 1, 1).data;
    hexcode = RGBtoHex(data[0], data[1], data[2]);

    if (showInputs == 0) {
        showInputs++;
        $("#saveFormButton").fadeIn();
        $(".extractionForm").fadeIn();

    }
    var textColor = fontColor(hexcode);
    $("#extractionHexcode").val(hexcode);
    if (textColor == "#FFFFFF") {
        $("#saveFormButtonTxt").addClass("white");
    }
    else if (textColor == "#000000") {
        $("#saveFormButtonTxt").removeClass("white");
    }
    $("#saveFormButtonTxt").css("color", textColor);
    $("#saveFormButton").css("background-color", "#" + hexcode);
    $('#previewpoint').css('left', pagex - 4);
    $('#previewpoint').css('top', pagey - 4);
    $('#previewpoint2').css('left', pagex - 2);
    $('#previewpoint2').css('top', pagey - 2);
}


function extractImage(photo_type, photo_link, url_origin) { // ajax request that gives a hexcode and gets the color theory matches
    // if photo_type is url you get a url link
    // if photo_type is file you get a imageid
    // url_origin = 0 -> native url
    // url_origin = 1 -> facebook
    // url_origin = 2 -> instagram
    $("#loading").show();
    if (lastPhoto == photo_link) {
        window.scrollTo(0, 0);
        $("#loading").hide();
    }
    else {
        lastPhoto = photo_link;
        if (photo_type == "url") {
            var send_data = {'photo_type': 'url', 'photo_url': photo_link, 'url_origin': url_origin}
        }
        else if (photo_type == "file") {
            var send_data = {'photo_type': 'file', 'photo_imageid': photo_link}
        }
        $.ajax({
            type: "GET",
            url: "/controllers/extraction_processing.php",
            data: send_data,
            success: function(html) {
                canvasObject = jQuery.parseJSON(html);
                img_url = canvasObject.image_url;
                img_src = canvasObject.image_string;
                drawing_height = canvasObject.drawing_height;
                drawing_width = canvasObject.drawing_width;
                width = canvasObject.width;
                height = parseInt(canvasObject.height);
                $("#save_photo_type").val(canvasObject.image_type);
                $("#save_photo_url").val(img_url);
                $("#save_photo_imageid").val(canvasObject.imageid);
                $("#save_url_origin").val(url_origin);
                initiateCanvas();
                $("#extraction_container").animate().slideDown('very slow').animate();
                $("#extractionDescription").val("");
                $("#extractionTags").val("");
                $("#extractionHexcode").val("");
                $("#saveForm").css("background-color", "#ffffff");
                window.scrollTo(0, 0);
                $("#loading").hide();
                $(".eyedropper").css("display", "block");
            }
        });
    }
}
function getoffsets() { // determines how far the picture is from the top left corner

    if (isChrome) {
        border = parseInt($('#canvas').css("border-width"));
    }
    else {
        border = 20;
    }
    xoffset = $('#canvas').offset().left + border;
    yoffset = $('#canvas').offset().top + border;
}

function removeImage(origin, urlid, imageid, divid) {
    $("#loading").show();
    var send_data = {"origin": origin, "urlid": urlid, "imageid": imageid};
    $.ajax({
        type: "GET",
        url: "/controllers/delete_image_processing.php",
        data: send_data,
        success: function(html) {
            console.log(html);
            $("#loading").hide();
            $("#div" + divid).fadeOut();
        }
    });
}

function addMore() {
    $('html,body').animate({
        scrollTop: $("#tabs_container").offset().top}, 'slow');
    $.fancybox.close();
}

function saveItem() {
    $("#loading").show();
    var send_data = $("#itemForm").serialize();
    $.ajax({
        type: "POST",
        url: "/controllers/saveitem_processing.php",
        data: send_data,
        success: function(html) {
            $(window).scrollTop(0);
            saveObject = jQuery.parseJSON(html);
            var notification = saveObject.status;
            $("#notification").html(notification);
            displayNotification(notification);
            $("#loading").hide();
        }
    });
}



