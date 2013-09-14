<?php

//amazon api

define('AWS_ACCESS_KEY_ID', 'AKIAIJ4GROGXAA6WK76A');
define('AWS_SECRET_ACCESS_KEY', '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');
define('AMAZON_ASSOC_TAG', 'colorfits-20');

function amazon_get_signed_url($searchTerm) {
    $base_url = "http://ecs.amazonaws.com/onca/xml";
    $params = array(
        'AWSAccessKeyId' => AWS_ACCESS_KEY_ID,
        'AssociateTag' => AMAZON_ASSOC_TAG,
        'Version' => "2010-11-01",
        'Operation' => "ItemSearch",
        'Service' => "AWSECommerceService",
        'ResponseGroup' => "ItemAttributes,Images",
        'Availability' => "Available",
        'Condition' => "All",
        'Operation' => "ItemSearch",
        'SearchIndex' => 'Apparel', //Change search index if required, you can also accept it as a parameter for the current method like $searchTerm
        'Keywords' => $searchTerm);

//'ItemPage'=>"1",
//'ResponseGroup'=>"Images,ItemAttributes,EditorialReview",

    if (empty($params['AssociateTag'])) {
        unset($params['AssociateTag']);
    }

// Add the Timestamp
    $params['Timestamp'] = gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time());

// Sort the URL parameters
    $url_parts = array();
    foreach (array_keys($params) as $key)
        $url_parts[] = $key . "=" . str_replace('%7E', '~', rawurlencode($params[$key]));
    sort($url_parts);

// Construct the string to sign
    $url_string = implode("&", $url_parts);
    $string_to_sign = "GET\necs.amazonaws.com\n/onca/xml\n" . $url_string;

// Sign the request
    $signature = hash_hmac("sha256", $string_to_sign, AWS_SECRET_ACCESS_KEY, TRUE);

// Base64 encode the signature and make it URL safe
    $signature = urlencode(base64_encode($signature));

    $url = $base_url . '?' . $url_string . "&Signature=" . $signature;

    return ($url);
}

$getthis = $_GET['q'];
echo $getthis;
$show = amazon_get_signed_url($getthis);

$ch = curl_init($show);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$c = curl_exec($ch);

$xml = simplexml_load_string($c);
$json = json_encode($xml);
$array = json_decode($json, TRUE);

$checkamazon = $array[Items][Item][0][DetailPageURL];

if ($checkamazon != "") {
    echo "<br><br><font size='3' color='#$lcolor' style='padding: 3px;'><b>Products</b></font><br>";
}

for ($i = 0; $i < 6; $i++) {

    $aprice = $array[Items][Item][$i][ItemAttributes][ListPrice][FormattedPrice];
    $aDescription = $array[Items][Item][$i][ItemAttributes][Title];
    $aUrl = $array[Items][Item][$i][DetailPageURL];
    $aImage = $array[Items][Item][$i][LargeImage][URL];
    $afeature1 = $array[Items][Item][$i][ItemAttributes][Feature][0];
    $afeature2 = $array[Items][Item][$i][ItemAttributes][Feature][1];
    $afeature3 = $array[Items][Item][$i][ItemAttributes][Feature][2];

    if ($aUrl != "") {
        echo "<div id=\".$web_rank.\" style='text-align: left; display: block; float: left; width: 100%; padding: 10px; border-bottom: SOLID 1px #C4C4C4;'>";
        echo "<div id='leftcolumn' style='width: 0px; float: left; display:inline; padding: 5px;'>";

        echo "</div>";
        echo "<div id='rightcolumn' style='width: 600px; float: left; padding: 0px;'>";
        echo "<a href = \"$aUrl\" target='_blank' STYLE=\"TEXT-DECORATION: NONE;\">";
        echo "<img src='$aImage'>";
        echo "<font size='3' color='#$lcolor' style='padding: 3px;'><b>$aDescription</b></font><font size='3' color='#$lcolor' style='padding: 3px;'><b>" .(($aprice) ? $aprice : 'price unavailable')."</b></font></a><br>";
        echo "<font size='2' color='#$lcolor' style='padding: 3px;'>";
        if ($afeature1 != "") {
            echo " $afeature1";
        }
        if ($afeature2 != "") {
            echo ", $afeature2";
        }
        if ($afeature3 != "") {
            echo ", $afeature3";
        }
        echo "</font>";
        echo "</div>";
        echo "</div>";
    }
}
?>