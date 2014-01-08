<?php

/*
  Contains functions dealing with items, small items, store items, NOT outfititems
 * 
 *  
 */

function formatItem($userid, $itemObject, $height = "") {
    $owns_item = ($userid == $itemObject->owner_id);
    $item_tags = array();
    $tagmap_query = database_query("tagmap", "itemid", $itemObject->itemid);
    $like = database_fetch("like", "userid", $userid, "itemid", $itemObject->itemid);
    $canEdit = "";
    $purchaseDisabled = "";


    while ($tagmap = mysql_fetch_array($tagmap_query)) {
        $tag = database_fetch("tag", "tagid", $tagmap['tagid']);
        $tagString .= formatHashtag($tag['name']);
    }


    if ($owns_item) {
        $purchaseString = "onclick=\"togglePurchaseLink(" . $itemObject->itemid . ")\"";
        $canEdit = "<i class='icon-edit editIcon' onclick='toggleEditTags(this," . $itemObject->itemid . ")'></i>";
    } else {
        if ($itemObject->purchaselink) {
            $purchaseString = "href='" . $itemObject->purchaselink . "' target='_blank'";
        } else {
            $purchaseDisabled = " style='color:#808285;font-color:#808285;'";
            $purchaseString = "href='javascript:void(0)'";
        }
    }
    // format likes
    if ($itemObject->likedbyuser == "liked" || $owns_item) {
        $likeString = " liked' ></i><span class='likeText'>" . $itemObject->like_count . "</span>";
    } else if ($itemObject->likedbyuser == "unliked") {
        $likeString = "' ></i><span class='likeText'>like</span> ";
    }

    echo "<div class='itemContainer' id='item" . $itemObject->itemid . "'style='color:#" . $itemObject->text_color . "' > 
    <div id='itemPreview' class='previewContainer'><div id='user" . $itemObject->owner_id . "' class='itemUserContainer'>
            <a href = '/closet/" . $itemObject->owner_username . "' class='userPreview'>
                <img class='userPicture' src='" . $itemObject->owner_picture . "'></img>
                <div class='userText'>" . $itemObject->owner_username . "
                    <br/><span class='followerCount'>" . $itemObject->owner_followers . " followers</span></div>
            </a></div></div>  
    <span class = 'itemDescription' style='background-color:#" . $itemObject->hexcode . "'>" . stripslashes($itemObject->description) . "</span>
        " . (($owns_item) ? "<a class = 'itemAction trashIcon' onclick = 'removeItem(" . $itemObject->itemid . ")'><i class='itemActionImage icon-remove-sign'></i></a>" : "") . "
    <a class = 'itemAction outfitIcon' id = 'tag_search' onclick='addToOutfit(" . $itemObject->itemid . ")'><i class='itemActionImage icon-plus' title='match by tags'></i> to outfit</a>
    <a class = 'itemAction beeIcon' id = 'color_search' href = '/hue/" . $itemObject->itemid . "' ><img class='itemActionImage' title='match by color'  src='/img/bee.png'></img> match</a>
    <a class = 'itemAction purchaseIcon' " . $purchaseDisabled . $purchaseString . " ><i class='itemActionImage icon-search' title='this user can give a source link'  style='font-size:20px'></i> explore</a>
    <a class = 'itemAction likeIcon' id = 'like' onclick='likeButton(" . $itemObject->itemid . ")'><i title='like this'  style='font-size:20px' class='itemActionImage icon-heart" . $likeString . "</a>    
    <img alt = '  This Image Is Broken' src = '" . $itemObject->image_link . "' onclick=\"Redirect('/hue/" . $itemObject->itemid . "')\" class = 'fixedwidththumb thumbnaileffect' style='height:" . (($height) ? $height . "px;width:auto" : "") . "' />

    <div class='itemTagBox' style='background-color:#" . $itemObject->hexcode . "'>
      <div class='hashtagContainer' placeholder = 'define this style with #hashtags'>" . $tagString . $canEdit . "<hr class='hashtagLine'/></div>
          <input type = 'text' class='purchaseLink'  name = 'purchaseLink' onblur='hidePurchaseLink(" . $itemObject->itemid . ")' onchange = 'updatePurchaseLink(this, " . $itemObject->itemid . ")' value = '" . $itemObject->purchaselink . "' placeholder = 'link to buy/find item' />     
    </div>
    <br/>
</div>";
}


function formatStoreItem($match_object) {
    echo "<div id='storeItem$match_object->itemid' class='storeMatch " . $match_object->gender . "'>
<div class='storeBar1' style='background-color:#" . $match_object->colors[0] . "'></div>
<div class='storeBar2' style='background-color:#" . $match_object->colors[1] . "'></div>
<div class='storeBar3' style='background-color:#" . $match_object->colors[2] . "'></div>
<span class='storeTitle'><span class='storePrice' title='Color Match Percentage'>$" . $match_object->price . "</span>  " . stripslashes($match_object->description) . "</span>              
<img alt='  This Image Is Broken' src='" . $match_object->url . "' class='fixedwidththumb thumbnaileffect' /><br/><br/>                                        
<a class='storeLink' href='" . $match_object->purchaselink . "' target='_blank' class='storeUrl'>View Item In Store</a>
</div>";
}


function formatSmallItem($userid, $itemObject, $width = "", $itemLink = "") {
    // this item has no user preview
    if ($itemObject->owner_id) {
        $owns_item = ($userid == $itemObject->owner_id);
        $item_tags = array();
        $tagmap_query = database_query("tagmap", "itemid", $itemObject->itemid);
        while ($tagmap = mysql_fetch_array($tagmap_query)) {
            $tag = database_fetch("tag", "tagid", $tagmap['tagid']);
            array_push($item_tags, $tag['name']);
        }
        $item_tags_string = implode(" #", $item_tags);
        if ($item_tags_string) {
            $item_tags_string = "#" . $item_tags_string;
        }
        if ($owns_item) {
            $purchaseString = "onclick=\"togglePurchaseLink(" . $itemObject->itemid . ")\"";
        } else {
            if ($itemObject->purchaselink) {
                $purchaseDisabled = "";
                $purchaseString = "href='" . $itemObject->purchaselink . "' target='_blank'";
            } else {
                $purchaseDisabled = " style='color:#808285;font-color:#808285;'";
                $purchaseString = "href='javascript:void(0)'";
            }
        }
        $search_string = str_replace("#", "%23", $item_tags_string);

        if ($itemLink == "off") {
            $itemLink = "";
        } else if (!$itemLink) { //
            $itemLink = "/hue/" . $itemObject->itemid;
        }
        echo "<div class='smallItemContainer' id='item" . $itemObject->itemid . "'style='color:#" . $itemObject->text_color . ";width:" . (($width) ? $width . "px;" : "") . "' >
    <span class = 'smallItemDescription' style='background-color:#" . $itemObject->hexcode . ";width:" . (($width) ? $width . "px;height:auto" : "") . "'>" . stripslashes($itemObject->description) . "</span>
        <a class = 'smallItemAction tagIcon' id = 'tag_search' href = '/tag?q=" . $search_string . "' ><img class='itemActionImage' title='match by tags' src='/img/tag.png'></img> search</a>
    <a class = 'smallItemAction beeIcon' id = 'color_search' href = '/hue/" . $itemObject->itemid . "' ><img class='itemActionImage' title='match by color'  src='/img/bee.png'></img> match</a>
    <a class = 'smallItemAction purchaseIcon' " . $purchaseDisabled . " id = 'color_search' " . $purchaseString . " >
    <i class='smallItemActionImage icon-search' title='this user can give a source link'></i> explore</a>
    <img alt = '  This Image Is Broken' src = '" . $itemObject->image_link . "' onclick=\"Redirect('$itemLink')\" class = 'fixedwidththumb thumbnaileffect' style='width:" . (($width) ? $width . "px;height:auto" : "") . "' />
    <br/>
    <div class='itemTagBox' style='background-color:#" . $itemObject->hexcode . ";width:" . (($width) ? $width . "px;height:auto" : "") . "'>
        <input type = 'text' class='itemTag'  name = 'tags'" . ((!$owns_item) ? "readonly = 'true'" : "") . " onchange = 'updateTags(this, " . $itemObject->itemid . ")' value = '" . $item_tags_string . "' placeholder = 'define this style with #hashtags' />
        <input type = 'text' class='purchaseLink'  name = 'purchaseLink' onblur='hidePurchaseLink(" . $itemObject->itemid . ")' onchange = 'updatePurchaseLink(this, " . $itemObject->itemid . ")' value = '" . $itemObject->purchaselink . "' placeholder = 'link to buy/find item' />     
    </div>
    <br/>
</div>";
    }
}

?>
