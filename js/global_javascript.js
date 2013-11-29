
function Redirect(link)
{
    if (link) {
        window.location = link;
    }
}

function checkValue() {
    var tab = $('#selectBox .selected').find(":selected").text();
    if (tab.indexOf("url") >= 0) {
        flipTab('urltab');
    } else if (tab.indexOf("image") >= 0) {
        flipTab('filetab');
    } else if (tab.indexOf("facebook") >= 0) {
        flipTab('facebooktab');
    } else if (tab.indexOf("instagram") >= 0) {
        flipTab('instagramtab');
    }
}

function showPurchaseLink(itemid) {
    $("#item" + itemid).find(".purchaseLink").show(75);
}

function togglePurchaseLink(itemid) {
    $("#item" + itemid).find(".purchaseLink").toggle();
}
function hidePurchaseLink(itemid) {
    $("#item" + itemid).find(".purchaseLink").hide(75);
}

function updatePurchaseLink(e, itemid) {
    $("#loading").show();
    var send_data = {
        'purchaseLink': e.value,
        'itemid': itemid
    }
    $.ajax({
        type: "POST",
        url: "/purchaselink_processing.php",
        data: send_data,
        success: function(html) {
            var response;
            var purchaseLink;
            response = jQuery.parseJSON(html);
            purchaseLink = response.purchaseLink;
            $("#item" + itemid).children(".purchaseLink").text(purchaseLink);
            $("#loading").hide();
        }
    });
}

function headerMenu(toggle) {
    if (toggle == "on") {
        $("#collapsedMenu").css("display", "block");
    }
    else if (toggle == "off") {
        $("#collapsedMenu").css("display", "none");
    } else {
        if ($("#collapsedMenu").css("display") == "none") {
            $("#collapsedMenu").css("display", "block");
        }
        else if ($("#collapsedMenu").css("display") == "block") {
            $("#collapsedMenu").css("display", "none");
        }
    }
}
function initiatePagination(database, array) {
    itemPagination(database, array);
    $(window).scroll(function() {
        if ($(window).scrollTop() + $(window).height() == $(document).height()) {
            itemPagination(database, array);
        }
    });
}

function formatOutfitItems(userid, itemObject) {
    // formats items that appear under outfits in the header

    if (itemObject.itemid) {
        $('#outfitBar').append("<div class='outfitItemContainer' id='item" + itemObject.itemid + "' style='color:#" + itemObject.text_color + ";height:125px;'>\n\
<a class = 'deleteItemFromOutfitButton' onclick = 'removeFromOutfit(" + itemObject.itemid + ")' style='display:block;'><i class='itemActionImage icon-remove-sign'></i></a>\n\
<span class='outfitItemDescription'>" + itemObject.description + "</span>\n\
<img alt = '  This Image Is Broken' class='outfitImage' src = '" + itemObject.image_link + "' onclick='Redirect(\"/hue/" + itemObject.itemid + "\")'/>\n\
<div class='outfitItemTagBox' style='background-color:#" + itemObject.hexcode + "'>\n\
<input type = 'text' class='purchaseLink'  name = 'purchaseLink' onblur='hidePurchaseLink(" + itemObject.itemid + ")' onchange = 'updatePurchaseLink(this, " + itemObject.itemid + ")' value = '" + itemObject.purchaselink + "' placeholder = 'Link to Where You Bought It' />\n\
</div><br/></div>");
    } else {
        $('#outfitBar').append("<div class='outfitItemContainer' style='width:150px;'></div>");
    }
}

function formatItem(userid, itemObject) {

    var addString = "";
    var canEdit = "";
    var purchaseString = "";

    var tags = itemObject.tags;
    var tags = tags.split("#");
    var tagString = "";
    for (var i = 1; i < tags.length; i++) {
        tagString += " " + formatHashtag(tags[i]);
    }
    if (!tagString) {
        tagString = "#no hashtags yet";
    }

    if (userid == itemObject.owner_id) { // owns item
        addString = "<a class = 'itemAction trashIcon' onclick = 'removeItem(" + itemObject.itemid + ")'><i class='itemActionImage icon-remove-sign'></i></a>";
        canEdit = "<i class='icon-edit editIcon' onclick='toggleEditTags(this," + itemObject.itemid + ")'></i>";
        purchaseString = "onclick='togglePurchaseLink(" + itemObject.itemid + ")'"; // if owns item toggle edit
    }
    else {
        if (itemObject.purchaselink) {
            var purchaseDisabled = "";
            purchaseString = "href='" + itemObject.purchaselink + "' target='_blank'"; // if doens't own item send to link
        }
        else {
            var purchaseDisabled = " style='color:#808285;font-color:#808285;'";
            purchaseString = "href='javascript:void(0)'"; // if doens't own item send to link
        }
    }

    if (itemObject.likedbyuser == "liked" || userid == itemObject.owner_id) {
        var likeString = " liked' ></i><span class='likeText'>" + itemObject.like_count + "</span>";
    } else if (itemObject.likedbyuser == "unliked") {
        var likeString = "' ></i><span class='likeText'>like</span> ";
    }



    $("<div class='itemContainer' id='item" + itemObject.itemid + "' style='color:#" + itemObject.text_color + "'><div id='itemPreview' class='previewContainer'>\n\
<div id='user" + itemObject.owner_id + "' class='itemUserContainer'><a href = '/closet/" + itemObject.owner_username + "' class='userPreview'>\n\
<img class='userPicture' src='" + itemObject.owner_picture + "'></img><div class='userText'>" + itemObject.owner_username + "\
<br/><span class='followerCount'>" + itemObject.owner_followers + " followers</span></div></a></div></div>\n\
<span class = 'itemDescription' style='background-color:#" + itemObject.hexcode + "'>" + stripslashes(itemObject.description) + "</span>" + addString + "\
<a class = 'itemAction outfitIcon' id = 'add_to_outfit' onclick='addToOutfit(" + itemObject.itemid + ")' ><i class='itemActionImage icon-plus' title='add to current outfit'></i> to outfit</a>\n\
<a class = 'itemAction beeIcon' id = 'color_search' href = '/hue/" + itemObject.itemid + "'><img class='itemActionImage' title='match by color' src='/img/bee.png'></img> match</a>\n\
<a class = 'itemAction purchaseIcon' " + purchaseDisabled + " id = 'color_search' " + purchaseString + " ><i class='itemActionImage icon-search' title='get this link' style='font-size:20px;'></i> explore</a>\n\
<a class = 'itemAction likeIcon' onclick='likeButton(" + itemObject.itemid + ")'><i  title='like this' style='font-size:20px;'class=' itemActionImage icon-heart" + likeString + "</a>\n\
<img alt = '  This Image Is Broken' src = '" + itemObject.image_link + "' onclick='Redirect(\"/hue/" + itemObject.itemid + "\")' class = 'fixedwidththumb thumbnaileffect' />\n\
<div class='itemTagBox' style='background-color:#" + itemObject.hexcode + "'>\n\
<div class='hashtagContainer' placeholder = 'define this style with #hashtags'>" + tagString + canEdit + "<hr style='width:80%'/></div>\n\
<input type = 'text' class='purchaseLink'  name = 'purchaseLink' onblur='hidePurchaseLink(" + itemObject.itemid + ")' onchange = 'updatePurchaseLink(this, " + itemObject.itemid + ")' value = '" + itemObject.purchaselink + "' placeholder = 'Link to Where You Bought It' />\n\
</div><br/></div>").insertBefore('#loadMore').fadeIn();
}

function itemPagination(database, array) {
    if (enablePagination == "1") {
        enablePagination = "0";
        $("#loading").show();
        var send_data = {
            'offset': offset,
            'database': database,
            'limit': limit,
            'useridArray[]': array
        }
        $.ajax({
            type: "GET",
            url: "/pagination_processing.php",
            data: send_data,
            success: function(html) {
                updateObject = jQuery.parseJSON(html);
                if (updateObject.updates) {
                    var i = 0;
                    for (i = 0; i < limit; i++) {
                        if (updateObject.updates[i]) {
                            formatItem(userid, updateObject.updates[i]);
                            offset++;
                        }
                    }
                    filterItems($('#filterInput').val())
                    enablePagination = "1";
                }
                else {
                    enablePagination = "0";
                    $("#loadMore").hide();
                }
                bindActions();
                $("#loading").hide();
            }
        });
    }
}
function enableSelectBoxes() {
    $('div.selectBox').each(function() {
        $(this).children('span.selected').html($(this).children('div.selectOptions').children('span.selectOption:first').html());
        $(this).attr('value', $(this).children('div.selectOptions').children('span.selectOption:first').attr('value'));
        $(this).children('span.selected,span.selectArrow').click(function() {
            if ($(this).parent().children('div.selectOptions').css('display') == 'none') {
                $(this).parent().children('div.selectOptions').css('display', 'block');
            }
            else
            {
                $(this).parent().children('div.selectOptions').css('display', 'none');
            }
        });
        $(this).find('span.selectOption').click(function() {
            $(this).parent().css('display', 'none');
            $(this).closest('div.selectBox').attr('value', $(this).attr('value'));
            $(this).parent().siblings('span.selected').html($(this).html());
        });
    });
}
function stripslashes(str) {
    return (str + '').replace(/\\(.?)/g, function(s, n1) {
        switch (n1) {
            case '\\':
                return '\\';
            case '0':
                return '\u0000';
            case '':
                return '';
            default:
                return n1;
        }
    });
}





function displayNotification(notification) {
    $("#notification").html(notification);
    $("a#fancyNotification").fancybox({
        'href': '#notification',
        autoSize: false,
        beforeLoad: function() {
            this.width = 500;
            this.height = 200;
        }
    });
    if (notification) {
        $("#fancyNotification").trigger('click');
    }
}

function formatHashtag(hashtag) {
    // INPUT: the hashtag as a word
    // OUTPUT: returns the html formatted hashtag
    return "<a class='hashtag' href='/tag?q=%23" + hashtag + "'>#" + hashtag + "</a>";

}
function updateTags(e, itemid) {
    $("#loading").show();
    var search_string;
    console.log(e.innerText);
    var tags = $("#item" + itemid).find(".hashtagContainer").text();
    console.log(tags);
    var send_data = {
        'tags': tags,
        'itemid': itemid
    }
    $.ajax({
        type: "POST",
        url: "/tag_processing.php",
        data: send_data,
        success: function(html) {
            tagObject = jQuery.parseJSON(html);
            tagObject.join(" #");
            //NEEDS TO BE CHANGED
            //
            //
            //
            this.value = "#" + tagObject;
            search_string = this.value;
            search_string = search_string.replace(/,/g, "#");
            search_string = search_string.replace(/#/g, "%23");
            $("#loading").hide();
        }
    });
}

function toggleEditTags(e, itemid) {

    var tagBox = $("#item" + itemid).find(".hashtagContainer");

    if (tagBox.hasClass("editing")) {
        tagBox.removeClass("editing");
        updateTags(e, itemid);
        tagBox.attr("contenteditable", "false");
    }
    else {
        tagBox.addClass("editing");
        tagBox.attr("contenteditable", "true");
    }
}

function removeItem(itemid) {
    $.ajax({
        type: "GET",
        url: "/delete_saveditem_processing.php",
        data: {'itemid': itemid},
        success: function(html) {
            $("#item" + itemid).slideUp();
        }
    })
}

function bindActions() {
    $('.itemContainer').bind('mouseenter', function() {
        showActions(this.id);
    });
    $('.itemContainer').bind('mouseleave', function() {
        hideActions(this.id);
    });
    $('.imageContainer').bind('mouseenter', function() {
        showActions(this.id);
    });
    $('.imageContainer').bind('mouseleave', function() {
        hideActions(this.id);
    });
}

function showActions(itemid) {
    $("#" + itemid).children(".itemTagBox").show();
}
function hideActions(itemid) {
    $("#" + itemid).children(".itemTagBox").hide();
}

function hex2rgb(hex) {
// looks at the bg color and selects an appropriate font color that will stand out
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}
function rgb2hsl(r, g, b) {
    r /= 255, g /= 255, b /= 255;
    var max = Math.max(r, g, b), min = Math.min(r, g, b);
    var h, s, l = (max + min) / 2;
    if (max == min) {
        h = s = 0; // achromatic
    } else {
        var d = max - min;
        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
        switch (max) {
            case r:
                h = (g - b) / d + (g < b ? 6 : 0);
                break;
            case g:
                h = (b - r) / d + 2;
                break;
            case b:
                h = (r - g) / d + 4;
                break;
        }
        h /= 6;
    }
    return [h, s, l];
}

function fontColor(hex) {
    var rgbColors = hex2rgb(hex);
    var hslColors = rgb2hsl(rgbColors.r, rgbColors.g, rgbColors.b);
    if (Math.round(hslColors[2]) === 1) {
        return "#000000"; // use a black text color for brighter bgs
    }
    else if (Math.round(hslColors[2]) === 0) {
        return "#FFFFFF"; // uses a white text color for darker bgs
    }
}

function followButton(follow_userid) {
    $("#loading").show();
    // REQUIRES JAVASCRIPT USERID IF NOT WON'T WORK'
    $.ajax({
        type: "POST",
        url: "/follow_processing.php",
        data: {
            'follow_userid': follow_userid,
            'userid': userid
        },
        success: function(html) {
            followObject = jQuery.parseJSON(html);
            if (followObject.status == "unfollowed") {
                $("button#followaction" + follow_userid).html("follow");
                $("button#followaction" + follow_userid).removeClass("clicked");
            } else if (followObject.status == "followed") {
                $("#user" + follow_userid).slideUp();
                $("button#followaction" + follow_userid).html("following");
                $("button#followaction" + follow_userid).addClass("clicked");
            }
            $("#loading").hide();
        }
    });
}

function likeButton(itemid) {
    $.ajax({
        type: "GET",
        url: "/like_processing.php",
        data: {
            'itemid': itemid
        },
        success: function(html) {
            likeObject = jQuery.parseJSON(html);
            console.log(likeObject.error);
            if (likeObject.status == "liked") {
// do things with css when an item is liked
                $("#item" + itemid).find(".likeText").html(likeObject.count);
                $("#item" + itemid).find(".icon-heart").addClass("liked");
            }
            else if (likeObject.status == "unliked") {
                $("#item" + itemid).find(".likeText").html("like");
                $("#item" + itemid).find(".icon-heart").removeClass("liked");
            }
            $("#loading").hide();
        }
    });
}




function loadOutfit() {// reloads outfit
    $("#loading").show();
    $.ajax({
        type: "POST",
        url: "/outfits_processing.php",
        data: {
            'action': "load"
        },
        success: function(html) {
            loadObject = jQuery.parseJSON(html);
            var notEmpty = 0;
            var emptyPrompt = "";
            var outfitName = "";
            var username = loadObject.username;
            if (loadObject.objects) {
                $("#outfitBar").html("");
                for (var i = 0; i < 6; i++) {
                    formatOutfitItems(userid, loadObject.objects[i]);
                    if (loadObject.objects[i].owner_id) {
                        notEmpty += 1;
                    }
                }
                if (notEmpty === 0) {
                    var emptyPrompt = "<span id='emptyOutfitPrompt'>Use + to outfit to add items</span>";
                }
                if (loadObject.name) {
                    outfitName = loadObject.name;
                }
                console.log(loadObject.objects);

                $("#outfitBar").append(emptyPrompt + "<div id='outfitActions'>\n\
<input type='text' id='outfitName' maxlength='50' placeholder=' name your outfit' value='" + outfitName + "'/>\n\
<button class = 'greenButton' id = 'deleteOutfitButton' title='delete this outfit' onclick = 'deleteOutfit()'>X</button>\n\
<button class='greenButton' id='saveOutfitButton' onclick='saveOutfit()'>Save</button>\n\
<button class='greenButton' id='createOutfitNavButton' onclick='createOutfit()'>New Outfit</button>\n\
<button class='greenButton' id='viewAllOutfitButton' onclick='Redirect(\"/closet/" + username + "/outfit\")'>View All</button>\n\
</div>");
            }
            $("#loading").hide();
        }
    });
}


function toggleOutfit(status) {
    var div = $("#outfitBar");
    var outBut = $("#outfitNavigation");
    if (div.css("display") == "none" || status == "show") {
        loadOutfit();
        div.slideDown();
        outBut.addClass("clicked");
    }
    else {
        div.slideUp();
        outBut.removeClass("clicked");
    }

}

function addToOutfit(itemid) {
    $("#loading").show();
    $.ajax({
        type: "POST",
        url: "/outfits_processing.php",
        data: {
            'itemid': itemid,
            'action': "add"
        },
        success: function(html) {
            addObject = jQuery.parseJSON(html);
            if (addObject.notification == "success") {
                console.log("success message reached, problem with notification setup");
                toggleOutfit("show");
//var notification = "This item was added to your current outfit!<br/><a href='/outfits'>Go To Outfits</a>";
                //$("#notification").html(notification);
                //displayNotification(notification);
            }
            $("#loading").hide();
        }
    });
}
function removeFromOutfit(itemid) {
    $("#loading").show();
    $.ajax({
        type: "POST",
        url: "/outfits_processing.php",
        data: {
            'itemid': itemid,
            'action': "remove"
        },
        success: function(html) {
            removeObject = jQuery.parseJSON(html);
            if (removeObject.notification == "success") {
                $("#item" + itemid).fadeOut();
            }
            $("#loading").hide();
        }
    });
}
function createOutfit() {
    $("#loading").show();
    $.ajax({
        type: "POST",
        url: "/outfits_processing.php",
        data: {
            'action': "create"
        },
        success: function(html) {
            createObject = jQuery.parseJSON(html);
            if (createObject.notification == "success") {
                toggleOutfit("show");
                flipView("closet"); // flips to items if in closet

            }
            $("#loading").hide();
        }
    });
}

function saveOutfit() { // only saves the name
    var name = $("#outfitName").val();
    console.log(name);
    $("#loading").show();
    $.ajax({
        type: "POST",
        url: "/outfits_processing.php",
        data: {
            'name': name,
            'action': "save"
        },
        success: function(html) {
            saveObject = jQuery.parseJSON(html);
            if (saveObject.notification == "success") {
                toggleOutfit("show");
                var url = window.location.href;
                if (url.indexOf("hueclues.com/closet") != -1) {// in closet
                    location.reload();
                }
                console.log("saved successful");
            }
            $("#loading").hide();
        }
    });
}
function editOutfit(outfitid) {
    $("#loading").show();
    $.ajax({
        type: "POST",
        url: "/outfits_processing.php",
        data: {
            'outfitid': outfitid,
            'action': "edit"
        },
        success: function(html) {
            editObject = jQuery.parseJSON(html);
            if (editObject.notification == "success") {
                toggleOutfit("show");
                flipView("closet");
                var url = window.location.href;
                if (url.indexOf("hueclues.com/closet") != -1) {// in closet
                    $(".currentOutfit").removeClass("currentOutfit");
                    $("#outfit" + outfitid).addClass("currentOutfit");
                }
            }
            $("#loading").hide();
        }
    });
}
function deleteOutfit() {
    $("#loading").show();
    $.ajax({
        type: "POST",
        url: "/outfits_processing.php",
        data: {
            'action': "delete"
        },
        success: function(html) {
            editObject = jQuery.parseJSON(html);
            if (editObject.notification == "success") {
                loadOutfit();
                console.log("deleted");
            }
            $("#loading").hide();
        }
    });
}

function filterItems(query) {
    query = query.split(/#| /);
    $('.itemContainer').each(function(i, obj) {
// looping through every item on page
        var tags = $(this).find(".hashtagContainer").text();
        var desc = $(this).find(".itemDescription").text();
        for (var i = 0; i < query.length; i++) {
// item contains every query word separated by  or #
            if (tags.indexOf(query[i]) != -1 || desc.indexOf(query[i]) != -1) {
                $(this).show();
            }
            else {
                $(this).hide();
            }
        }
    });
}
