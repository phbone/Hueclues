
var options = {
    beforeSend: function()
    {
        $("#progress").show();
        //clear everything
        $("#bar").width('0%');
        $("#message").html("");
        $("#percent").html("0%");
    },
    uploadProgress: function(event, position, total, percentComplete)
    {
        $("#bar").width(percentComplete + '%');
        $("#percent").html(percentComplete + '%');
    },
    success: function()
    {
        $("#bar").width('100%');
        $("#percent").html('100%');
    },
    complete: function(response)
    {
        $("#message").html("<font color='green'>" + response.responseText + "</font>");
    },
    error: function()
    {
        $("#message").html("<font color='red'> ERROR: unable to upload files</font>");
    }
}
function dropContainer(name) // name can either be upload or history
{
    if (name == "upload" || name == "upload_highlight") {
        if (uploaddropped == 0) {
            $("#historycontainer").hide();
            historydropped = 0;
            $("#uploadcontainer").fadeIn();
            uploaddropped = 1;
            if (name == "upload_highlight") {
                $("#highlight").css("font-weight", "900");
            }
        }
    }
    else if (name == "history") {
        $("#uploadcontainer").hide();
        uploaddropped = 0;
        $("#historycontainer").fadeIn();
        historydropped = 1;
        $("#highlight").css("font-weight", "normal");
        $("#highlight").css("text-decoration", "none");
    }
}

function flipTab(id) {
    var idText = $('#' + id).text();
    $('.selectBox .selected').text(idText);
    $('.historypage').hide();
    $('#' + id + 'page').fadeIn();
}

function flipUpload(id) {
    $('#uploadurltab').removeClass('active');
    $('#uploadfiletab').removeClass('active');
    $('#' + id).addClass('active');
    $('.uploadpage').hide();
    $('#' + id + 'page').fadeIn();
}

function getPictures() { // ajax request that gives a hexcode and gets the color theory matches
    isLoading("true");
    data = 'photo_count=' + $("#fbphoto_count").val();
    $.ajax({
        type: "POST",
        url: "/controllers/getfacebookphotos_processing.php",
        data: data,
        success: function(html) {
            dropContainer('history');
            flipTab('facebooktab');
            ajaxObject = jQuery.parseJSON(html);
            $("#fbphoto_load").hide();
            $("#fbphoto_count").hide();
            $("#fbphoto_instruction").hide();
            $("#fbphoto_landing").html(ajaxObject.response);
            isLoading("false");
        }
    });
}

function isLoading(status) {
    if (status == "true") {
        $("#loading").show();
    }
    else if (status == "false") {
        $("#loading").hide();
    }
}

function defaultTabs() {
    flipUpload('uploadurltab');
    flipTab('facebooktab');
    dropContainer('upload');
}

function fileName() {
    var filename = $('#file').val();
    var lastIndex = filename.lastIndexOf("\\");
    if (lastIndex >= 0) {
        filename = filename.substring(lastIndex + 1);
    }
    $("#fakeupload").val(filename);
}

function submitUrl() {
    if ($("#url").val()) {
        document.forms["urlForm"].submit();
    } else {
        window.location = "/extraction";
    }
}


function importImages() {
    facebook_images_selected.join(" ");
    $("#facebook_urls").val(facebook_images_selected);
    instagram_images_selected.join(" ");
    $("#instagram_urls").val(instagram_images_selected);
    document.getElementById('imported_urls_form').submit();
}
function addFacebookImage(num) {
    var selected = $("#fb_frame" + num).hasClass("added");
    if (selected) {
// image is being unselected
        var remove_index = facebook_images_selected.indexOf($("#fb_url" + num).val());
        facebook_images_selected.splice(remove_index, 1);
        $("#fb_frame" + num).removeClass("added");
    } else {
// image is being selected
        facebook_images_selected.push($("#fb_url" + num).val());
        $("#fb_frame" + num).addClass("added");
    }
}
function addInstagramImage(num) {
    var selected = $("#ig_frame" + num).hasClass("added");
    if (selected) {
// image is being unselected
        var remove_index = instagram_images_selected.indexOf($("#ig_url" + num).val());
        instagram_images_selected.splice(remove_index, 1);
        $("#ig_frame" + num).removeClass("added");
    } else {
// image is being selected
        instagram_images_selected.push($("#ig_url" + num).val());
        $("#ig_frame" + num).addClass("added");
    }
}
function changePicture() {
    document.getElementById("file").click();
}

function submitPicture() {
    $("#fileForm").submit();
}

function getInstagram() {
    var token_string = window.location.hash;
    token = token_string.replace("#access_token=", "");
    if (nextMaxUrl) {
        url = nextMaxUrl;
    }
    else {
        url = "https://api.instagram.com/v1/users/self/media/recent/?&access_token=" + token + "&count=-1&callback=?";
    }
    var instagramData;
    if (window.location.hash) {
        $("#loading").show();
        $.ajax({
            type: "GET",
            url: url,
            dataType: "jsonp",
            cache: false,
            success: function(instagramResponse) {
                var i;
                var instagramImage;
                dropContainer('history');
                flipTab('instagramtab');
                $("#igphoto_instruction").hide();
                instagramData = instagramResponse.data;
                instagramPagination = instagramResponse.pagination;
                if (instagramPagination) {
                    nextMaxUrl = instagramResponse.pagination.next_url;
                }
                else {
                    $("#paginationButton").hide();
                }
                for (i = 0; i < instagramData.length; i++) {
                    instagramImage = instagramData[i];
                    $('#igphoto_landing').append("<div class='thumbnail_frame' id='ig_frame" + i + "' ><input type='hidden' value='" + instagramImage.images.standard_resolution.url + "' id='ig_url" + i + "' name='photo_url' /><input type='image' alt='This link is broken' src='" + instagramImage.images.standard_resolution.url + "' class='thumbnaileffect' id='ig_image" + i + "'onclick='addInstagramImage(" + i + ")' /></div>");
                }
                $("#loading").hide();
            }
        });
    }
}