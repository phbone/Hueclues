(function(a,b,c){
    if(c in b&&b[c]){
        var d,e=a.location,f=/^(a|html)$/i;
        a.addEventListener("click",function(a){
            d=a.target;
            while(!f.test(d.nodeName))d=d.parentNode;
            "href"in d&&(d.href.indexOf("http")||~d.href.indexOf(e.host))&&(a.preventDefault(),e.href=d.href)
        },!1)
    }
})(document,window.navigator,"standalone")

if (navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPod/i)
    || navigator.userAgent.match(/iPad/i)) 
{
    document.title = "hueclues";
}
            
function Redirect(link)
{
    window.location=link;
}

function checkValue(){
    var tab = $('#selectBox .selected').find(":selected").text();
    if(tab.indexOf("url") >= 0){
        flipTab('urltab');
    }else if(tab.indexOf("image") >= 0){
        flipTab('filetab');   
    }else if(tab.indexOf("facebook") >= 0){
        flipTab('facebooktab');
    }else if(tab.indexOf("instagram") >= 0){
        flipTab('instagramtab');
    }
}



function initiatePagination(database, array){
    itemPagination(database, array);
    $(window).scroll(function() {
        if($(window).scrollTop() + $(window).height() == $(document).height()) {
            if($(window).scrollTop() + $(window).height() == $(document).height()) {
                itemPagination(database, array);
            }
        }
    });
}

function formatItem(userid, itemObject){
    var addString = "";
    var lockString = "readonly='true'";
    if(userid == itemObject.owner_id){
        addString = "<a class = 'itemAction' onclick = 'removeItem(" + itemObject.itemid + ")'style = 'margin-left:0px'><img class='itemActionImage' style='height:20px' src='/img/trashcan.png'></i></a>";
        lockString= "";    
    }
    $("<div class='itemContainer' id='item"+ itemObject.itemid +"'><div id='itemPreview' class='previewContainer'>\n\
<div id='user"+itemObject.owner_id+"' class='itemUserContainer'><a href = '/closet/"+itemObject.owner_username+"' class='userPreview'>\n\
<img class='userPicture' src='/viewprofile.php?id="+itemObject.owner_id+"'></img><div class='userText'>"+itemObject.owner_username+"\
<br/><span class='followerCount'>"+itemObject.owner_followers+" followers</span></div></a></div></div>\n\
<span class = 'itemDescription' style='background-color:#"+itemObject.hexcode+"'>" + stripslashes(itemObject.description)+"</span>\n\
<br/>"+addString+"<a class = 'itemAction' id = 'tag_search' href = '/tag?q=" + itemObject.search_string+"' style = 'margin-left:39px'><img class='itemActionImage' src='/img/tag.png'></img></a>\n\
<a class = 'itemAction' id = 'color_search' href = '/hue/"+itemObject.itemid+"' style = 'margin-left:78px;'><img class='itemActionImage' style='height:18px' src='/img/bee.png'></img></a>\n\
<img alt = '  This Image Is Broken' src = '"+itemObject.image_link+ "' class = 'fixedwidththumb thumbnaileffect' /><br/>\n\
<div class='itemTagBox' style='background-color:#"+itemObject.hexcode+"'>\n\
<input type = 'text' class='itemTag'  name = 'tags'"+lockString+"onchange = 'updateTags(this, "+itemObject.itemid+")' value = '"+itemObject.tags+"' placeholder = 'define this style with #hashtags' /></div><br/></div>").insertBefore('#loadMore').fadeIn();
    
}
function itemPagination(database, array){
    if(enablePagination == "1"){
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
            success: function(html){
                updateObject = jQuery.parseJSON(html);
                console.log(updateObject);
                if(updateObject.updates == null){
                    enablePagination = "0";
                    $("#loadMore").hide();
                }
                else{
                    var i =0;
                    for(i=0;i<limit;i++){
                        if(updateObject.updates[i]){
                            formatItem(userid, updateObject.updates[i]);
                            offset++;
                        }
                    }  
                    enablePagination = "1";
                }
                offset++;
                bindActions();
                $("#loading").hide();
                
            } 
        });
    }
}
function stripslashes (str) {
    return (str + '').replace(/\\(.?)/g, function (s, n1) {
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

    


function displayNotification(notification){
    $("#notification").html(notification);
    $("a#fancyNotification").fancybox({ 
        'href' : '#notification',
        autoSize: false,
        beforeLoad : function() {         
            this.width  = 500;
            this.height = 200;
        }  
    });
    if(notification){
        $("#fancyNotification").trigger('click');
    }
}


function enableSelectBoxes(){
    $('div.selectBox').each(function(){
        $(this).children('span.selected').html($(this).children('div.selectOptions').children('span.selectOption:first').html());
        $(this).attr('value',$(this).children('div.selectOptions').children('span.selectOption:first').attr('value'));
					
        $(this).children('span.selected,span.selectArrow').click(function(){
            if($(this).parent().children('div.selectOptions').css('display') == 'none'){
                $(this).parent().children('div.selectOptions').css('display','block');
            }
            else
            {
                $(this).parent().children('div.selectOptions').css('display','none');
            }
        });
					
        $(this).find('span.selectOption').click(function(){
            $(this).parent().css('display','none');
            $(this).closest('div.selectBox').attr('value',$(this).attr('value'));
            $(this).parent().siblings('span.selected').html($(this).html());
        });
    });				
}


function updateTags(e, itemid){
    $("#loading").show();
    var search_string;
    var send_data = {
        'tags': e.value, 
        'itemid': itemid
    }
    $.ajax({
        type: "POST",
        url: "/tag_processing.php",
        data: send_data,
        success: function(html){
            tagObject = jQuery.parseJSON(html);
            tagObject.join(" #");
            this.value = "#" + tagObject;
            search_string = this.value;
            search_string = search_string.replace(/,/g, "#");
            search_string = search_string.replace(/#/g, "%23");
            $("#item"+itemid).children("#tag_search").attr("href", "/tag.php?q="+search_string);
            $("#loading").hide();
        }
    });
}
            
function removeItem(itemid){
    enablePagination = "0";
    $.ajax({
        type: "GET",
        url: "/delete_saveditem_processing.php",
        data: {
            'itemid': itemid
        },
        success: function(html){
            $("#item"+itemid).slideUp();
            enablePagination = "1";
        }
    })
}

function bindActions(){
    $('.itemContainer').bind('mouseenter', function() {
        showActions(this.id);
    });
    $('.itemContainer').bind('mouseleave', function(){
        hideActions(this.id);
    }); 
    $('.imageContainer').bind('mouseenter', function() {
        showActions(this.id);
    });
    $('.imageContainer').bind('mouseleave', function(){
        hideActions(this.id);
    }); 
}
    
function showActions(itemid){
    
    $("#"+itemid).children(".itemTagBox").animate({
        'padding-top': 45
    }, 100);
    $("#"+itemid).children(".itemAction").show();
    $("#"+itemid).children(".itemDescription").slideDown(75);
}
function hideActions(itemid){
    $("#"+itemid).children(".itemAction").hide();
    $("#"+itemid).children(".itemTagBox").animate({
        'padding-top': 10
    }, 100);
    $("#"+itemid).children(".itemDescription").slideUp(75);
}


function followButton(follow_userid){
    $("#loading").show();
    // REQUIRES JAVASCRIPT USERID IF NOT WON'T WORK'
    $.ajax({
        type: "POST",
        url: "/follow_processing.php",
        data: {
            'follow_userid': follow_userid, 
            'userid': userid
        },
        success: function(html){
            followObject = jQuery.parseJSON(html);
            if (followObject.status == "unfollowed"){
                $("#followaction"+follow_userid).removeClass("clicked");
                $("#followaction"+follow_userid).html("follow");
                            
            }else if(followObject.status == "followed"){
                $("#followaction"+follow_userid).html("unfollow");
                $("#followaction"+follow_userid).addClass("clicked");
            }
            $("#loading").hide();
        }
    })
}