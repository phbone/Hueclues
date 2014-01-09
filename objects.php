<?php

// contains the intialization of all objects

class item_object {

    public $owner_id = "";
    public $owner_name = "";
    public $owner_username = "";
    public $owner_picture = "";
    public $owner_followers = "";
    public $purchaselink = "";
    public $gender = ""; // m, f or u
    public $itemid = "";
    public $image_link = "";
    public $hexcode = "";
    public $tags = "";
    public $description = "";
    public $save_time = "";
    public $image_origin = "";
    public $search_string = "";
    public $item_tags_string = "";
    public $association = "";
    public $matchScheme = "";
    public $like_count = "";
    public $likedbyuser = "";
    public $text_color = "";
}

class outfit_object {

    public $owner_id = "";
    public $outfitid = "";
    public $name = "";
    public $time = "";
    public $itemcount = "";
    public $item1 = "";
    public $item2 = "";
    public $item3 = "";
    public $item4 = "";
    public $item5 = "";
    public $item6 = "";

}

class store_match_object {

// the itemid of the store item
// the priority, i.e degree of match
// 1 being lowest (1 color matches) 3 being highest (3 colors match)
    public $itemid = "";
    public $description = "";
    public $gender = "";
    public $purchaselink = "";
    public $colors = array();
    public $url = "";
    public $priority = "";
    public $price = "";
    public $scheme = ""; // ana, tri, comp, spl, sha

}

class colorObject {

// the itemid of the store item
// the priority, i.e degree of match
// 1 being lowest (1 color matches) 3 being highest (3 colors match)
    public $hex = "";
    public $comp = "";
    public $ana1 = "";
    public $ana2 = "";
    public $spl1 = "";
    public $spl2 = "";
    public $tri1 = "";
    public $tri2 = "";
    public $sha1 = "";
    public $sha2 = "";

}

class matchObject {

// match object returns itemid with associations:
//      whether the item is 
//      from following, closet, or store

    public $priority = ""; // how well item matches
    public $itemid = "";
    public $source = ""; // closet, following, or store
    public $scheme = ""; // analogous(ana), complimentary(comp)

// shades(sha), split(spl), triadic (tri)
}

?>
