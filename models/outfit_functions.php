<?php

/* Functions dealing with the formatting of outfits, including outfit items,
 * uses functions dealing with the outfit class/global_objects
 * 
 */

function formatOutfit($userid, $outfitid) {
    // takes in the outfit id and returns outfit Object
    $outfitObject = returnOutfit($outfitid);
    if (!$outfitObject->name) {
        $outfitObject->name = "Untitled Outfit";
    }
    echo "<div class='outfitContainer' id='outfit" . $outfitObject->outfitid . "'>";
    echo "<div class='outfitRow' align='left'>";

    echo "<div class='outfitItemPreview'>";
    formatOutfitItem($userid, $outfitObject->item1, 175);
    echo "</div>";
    echo "<div class='outfitItemPreview'>";
    formatOutfitItem($userid, $outfitObject->item2, 175);
    echo "</div>";
    echo "<div class='outfitItemPreview'>";
    formatOutfitItem($userid, $outfitObject->item3, 175);
    echo "</div></div>";
    echo "<div class='outfitRow' align='left'>";
    echo "<div class='outfitItemPreview'>";
    formatOutfitItem($userid, $outfitObject->item4, 175);
    echo "</div>";
    echo "<div class='outfitItemPreview'>";
    formatOutfitItem($userid, $outfitObject->item5, 175);
    echo "</div>";
    echo "<div class='outfitItemPreview'>";
    formatOutfitItem($userid, $outfitObject->item6, 175);
    echo "</div></div>";
    echo "<span class='outfitName'>" . $outfitObject->name . "<hr class='outfitLine'/>";
    if ($userid == $outfitObject->owner_id) {
        // allows you to edit outfit if you created it
        echo"</br><i class='icon-edit cursor editOutfitButton' onclick='editOutfit(" . $outfitObject->outfitid . ")'></i>";
    }
    echo "</span>";
    echo "</div>";
}

function formatOutfitItem($userid, $itemObject, $height = "", $itemLink = "") { // by default clicking directs to item 
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
        $itemLink = "/hue/" . $itemObject->itemid;
        echo "<div class='outfitItemContainer' id='item" . $itemObject->itemid . "'style='color:#" . $itemObject->text_color . ";height:" . (($height) ? $height . "px;width:auto" : "") . "' >
        <img alt = '  This Image Is Broken' src = '" . $itemObject->image_link . "' onclick=\"Redirect('$itemLink')\" class = 'outfitImage' />
    <div class='outfitItemTagBox' style='background-color:#" . $itemObject->hexcode . ";'>
        <span class = 'outfitItemDescription' style='background-color:#" . $itemObject->hexcode . ";height:inherit'>" . stripslashes($itemObject->description) . "</span>
<input type = 'text' class='purchaseLink'  name = 'purchaseLink' onblur='hidePurchaseLink(" . $itemObject->itemid . ")' onchange = 'updatePurchaseLink(this, " . $itemObject->itemid . ")' value = '" . $itemObject->purchaselink . "' placeholder = 'link to buy/find item' />     
    </div>
    <br/>
</div>";
    }
}

?>
