<html><head>
        <script src="http://code.jquery.com/jquery-latest.js"></script>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <?php
    /*     * *************FUNCTIONS******************************** */
    include('simple_html_dom.php');

    function colorPalette($imageFile, $granularity = 5) {
        $granularity = max(1, abs((int) $granularity));
        $colors = array();
        $size = @getimagesize($imageFile);
        if ($size === false) {
            user_error("Unable to get image size data");
            return false;
        }
//$img = @imagecreatefromjpeg($imageFile); 
        if ($size[2] == 1)
            $img = @imagecreatefromgif($imageFile);
        if ($size[2] == 2)
            $img = @imagecreatefromjpeg($imageFile);
        if ($size[2] == 3)
            $img = @imagecreatefrompng($imageFile);

        if (!$img) {
            user_error("Unable to open image file");
            return false;
        }
        for ($x = 0; $x < $size[0]; $x += $granularity) {
            for ($y = 0; $y < $size[1]; $y += $granularity) {
                $thisColor = imagecolorat($img, $x, $y);
                $rgb = imagecolorsforindex($img, $thisColor);
                $red = round(round(($rgb['red'] / 0x33)) * 0x33);
                $green = round(round(($rgb['green'] / 0x33)) * 0x33);
                $blue = round(round(($rgb['blue'] / 0x33)) * 0x33);
                $thisRGB = sprintf('%02X%02X%02X', $red, $green, $blue);
                if (array_key_exists($thisRGB, $colors)) {
                    $colors[$thisRGB]++;
                } else {
                    $colors[$thisRGB] = 1;
                }
            }
        }
        arsort($colors);
        return $colors;
    }

    function borderPalette($imageFile, $granularity = 5) {
        $granularity = max(1, abs((int) $granularity));
        $colors = array();
        $size = @getimagesize($imageFile);
        if ($size === false) {
            user_error("Unable to get image size data");
            return false;
        }
//$img = @imagecreatefromjpeg($imageFile); 

        if ($size[2] == 1)
            $img = @imagecreatefromgif($imageFile);
        if ($size[2] == 2)
            $img = @imagecreatefromjpeg($imageFile);
        if ($size[2] == 3)
            $img = @imagecreatefrompng($imageFile);

        if (!$img) {
            user_error("Unable to open image file");
            return false;
        }
        for ($x = 0; $x < $size[0]; $x += $granularity) {
            for ($y = 0; $y < $size[1]; $y += $granularity) {
                $thisColor = imagecolorat($img, $x, $y);
                $rgb = imagecolorsforindex($img, $thisColor);
                $red = round(round(($rgb['red'] / 0x33)) * 0x33);
                $green = round(round(($rgb['green'] / 0x33)) * 0x33);
                $blue = round(round(($rgb['blue'] / 0x33)) * 0x33);
                $thisRGB = sprintf('%02X%02X%02X', $red, $green, $blue);
                if ($x == 0 || $y == 0) {
                    if (array_key_exists($thisRGB, $colors)) {
                        $colors[$thisRGB]++;
                    } else {
                        $colors[$thisRGB] = 1;
                    }
                }
            }
            arsort($colors);
            return $colors;
        }
    }

    function getHtml2Rgb($str_color) {
        if ($str_color[0] == '#')
            $str_color = substr($str_color, 1);

        if (strlen($str_color) == 6)
            list($r, $g, $b) = array($str_color[0] . $str_color[1],
                $str_color[2] . $str_color[3],
                $str_color[4] . $str_color[5]);
        elseif (strlen($str_color) == 3)
            list($r, $g, $b) = array($str_color[0] . $str_color[0], $str_color[1] . $str_color[1], $str_color[2] . $str_color[2]);
        else
            return false;

        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);
        $arr_rgb = '(' . $r . ',' . $g . ',' . $b . ')';
// Return colors format liek R(255) G(255) B(255) 
        return $arr_rgb;
    }

    function totalpx($imageFile, $granularity) {
        $size = @getimagesize($imageFile);
        $totpx = $size[0] * $size[1];
        $pcrpx = round($totpx / $granularity);
        return $pcrpx;
    }

    /*     * ******************************************************* */
    $item_num = 1;
    $granularity = 5; // $_GET['g'];
    $query = $_GET['q'];
    echo "<form method='GET' action='/chameleon'>";
    /* <input type='text' name='g' placeholder='granularity' value=$granularity /> */
    echo"<input type='text' name='q' placeholder='keyword' value=$query />
        <input type='submit' />
        </form>";
//amazon api

    define('AWS_ACCESS_KEY_ID', 'AKIAIJ4GROGXAA6WK76A');
    define('AWS_SECRET_ACCESS_KEY', 'vIY5qeTPt+wOA5e13urJ+UenwMcgDznsuT+IULqJ');
    define('AMAZON_ASSOC_TAG', 'colorfits-20');

    function amazon_get_signed_url($searchTerm) {
        $base_url = "http://ecs.amazonaws.com/onca/xml";
        $params = array(
            'AWSAccessKeyId' => AWS_ACCESS_KEY_ID,
            'AssociateTag' => AMAZON_ASSOC_TAG,
            'Version' => "2011-08-01",
            'Operation' => "ItemSearch",
            'Service' => "AWSECommerceService",
            'ResponseGroup' => "ItemAttributes,Images",
            'Availability' => "Available",
            'Condition' => "All",
            'MaximumPrice' => "10000",
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

    $hexcode1_array = array();
    $hexcode2_array = array();
    $hexcode3_array = array();
    $price_array = array();
    $description_array = array();
    $purchaseurl_array = array();
    $imageurl_array = array();
    $gender_array = array();

    $show = amazon_get_signed_url($query);
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

    for ($i = 0; $i < 10; $i++) {
        $aDescription = $array[Items][Item][$i][ItemAttributes][Title];
        $aUrl = urldecode($array[Items][Item][$i][DetailPageURL]);
        $aImage = $array[Items][Item][$i][LargeImage][URL];
        $afeature1 = $array[Items][Item][$i][ItemAttributes][Feature][0];
        $afeature2 = $array[Items][Item][$i][ItemAttributes][Feature][1];
        $afeature3 = $array[Items][Item][$i][ItemAttributes][Feature][2];
        $aprice = $array[Items][Item][$i][ItemAttributes][ListPrice][FormattedPrice];
        if (!$aprice) {
            $price_html = file_get_html($aUrl);
            $value = $price_html->getElementById('price', 0);
            $value = strip_tags($value);
            preg_match('/\$([0-9]+[\.]*[0-9]*)/', $value, $match);
            $aprice = $match[1];
        }
        $aprice = str_replace("$", "", $aprice);

        if ($aUrl != "") {
            // sample usage: 
            $image = $aImage;
            $palette = colorPalette($image, $granularity);
            $border_palette = borderPalette($image, $granularity);
            if ($palette && $border_palette) {
                $total_pixel = totalpx($image, $granularity);
                $colors_to_show = 13;
                $shown_array = array();
                echo '<div id="box"><table  border="1" style="display:inline-block;height:355px"><tr><td>Color</td><td>Color Hex</td><td>Color RGB</td><td>Count</td><td>Percentage</td></tr> 
                <button class="btn" onclick="removeRow(' . $item_num . ')">x</button>';
                for ($h = 0; $h < $colors_to_show; $h++) {
                    $remove_this = 0;
                    $color = array_keys($palette);
                    if (array_key_exists($color[$h], $border_palette)) {
                        //$remove_this = 1; // this says that the current color is a border color
                    }
                    if ($remove_this != 1) { // only prints the colors that aren't border colors
                        $hex = $color[$h];
                        $color_pixel = $palette[$color[$h]];
                        $percentage = ($color_pixel / $total_pixel) * 100;
                        echo '<tr class="items" onclick="changeColor(this)" id="item-' . $item_num . "-" . $h . '"><td style="background-color:#' . $hex . ';width:2em;">&nbsp;</td><td class="hex">' . $hex . '</td><td>rgb' . getHtml2Rgb($hex) . '</td><td>' . $palette[$color[$h]] . '</td><td>' . number_format($percentage, 1) . ' %</td>';
                        $shown_array[] = $color[$h];
                    }
                }
                $iswomen = preg_match("/women/i", $aDescription) || preg_match("/women/i", $afeature1) || preg_match("/women/i", $afeature2) || preg_match("/women/i", $afeature3) ||
                        preg_match("/girl/i", $aDescription) || preg_match("/girl/i", $afeature1) || preg_match("/girl/i", $afeature2) || preg_match("/girl/i", $afeature3);
                $ismen = preg_match("/ men/i", $aDescription) || preg_match("/ men/i", $afeature1) || preg_match("/ men/i", $afeature2) || preg_match("/ men/i", $afeature3) || preg_match("/ guy/i", $aDescription) || preg_match("/ guy/i", $afeature1) || preg_match("/ guy/i", $afeature2) || preg_match("/ guy/i", $afeature3);
                if ($iswomen && $ismen) {
                    // unisex
                    $gender_array[$item_num] = "2";
                } else if ($iswomen && !$ismen) {
                    // women
                    $gender_array[$item_num] = "0";
                } else if ($ismen && !$iswomen) {
                    // men
                    $gender_array[$item_num] = "1";
                }
                $hexcode1_array[$item_num] = $shown_array[0];
                $hexcode2_array[$item_num] = $shown_array[1];
                $hexcode3_array[$item_num] = $shown_array[2];
                $price_array[$item_num] = $aprice;
                $description_array[$item_num] = $aDescription;
                $purchaseurl_array[$item_num] = $aUrl;
                $imageurl_array[$item_num] = $aImage;

                echo '</table>';
                echo "<div id=\".$web_rank.\" style='text-align: left; display: inline-block; float: left; width: 50%; height:350px; padding: 10px; border-bottom: SOLID 1px #C4C4C4;'>";
                echo "<input id='maleradio$item_num' type='checkbox' " . (($ismen) ? 'checked' : '') . "  name='sex' onclick='changeGender($item_num)' value='male'>Male</input>
                  <input id='femaleradio$item_num' type='checkbox' " . (($iswomen) ? 'checked' : 'no') . "  name='sex' onclick='changeGender($item_num)' value='female'>Female</input>";
                echo "<div id='leftcolumn' style='width: 0px; float: left; display:inline; padding: 5px;'>";
                echo "</div>";
                echo "<div id='rightcolumn' style='width: 600px; float: left; padding: 0px;'>";
                echo "<a href = \"$aUrl\" target='_blank' STYLE=\"TEXT-DECORATION: NONE;\">";
                echo "<img src='$aImage' height='300' style='float:left;'>";
                echo "<font size='3' color='#$lcolor' style='padding: 3px;'><b>$aDescription</b></font>
            <font size='3' color='#$lcolor' style='padding: 3px;'><b>$" . (($aprice) ? $aprice : 'price unavailable') . "</b></font></a><br>";
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
                echo "</div>
            </div><br/>";
                $item_num++;
            }
        }
    }
    ?>

    <script type="text/javascript">






        function uploadClick() {
            $("#csvHolder").val($("#csv_output").text());
            $("#thefile_button").trigger('click');
        }





        hexcode_array = new Array();
        var i;
        removed_array = new Array();
<?php
echo "var description_array = " . json_encode($description_array) . "
           var hexcode1_array = " . json_encode($hexcode1_array) . "
           var hexcode2_array = " . json_encode($hexcode2_array) . "
           var hexcode3_array = " . json_encode($hexcode3_array) . "
           var price_array = " . json_encode($price_array) . "
           var imageurl_array = " . json_encode($imageurl_array) . "
           var purchaseurl_array = " . json_encode($purchaseurl_array) . "
           var gender_array = " . json_encode($gender_array);
?>

        for (i = 1; i < 11; i++) {
            hexcode_array[i] = [3, 2, 1];
        }
        function dumpCSV() {
            $("#csv_output").html(""); // clears
            for (i = 1; i < 11; i++) {
                if ($.inArray(i, removed_array) == -1) {
                    $("#csv_output").append("<br/>" + imageurl_array[i] + ",");
                    $("#csv_output").append(description_array[i] + ",");
                    $("#csv_output").append(price_array[i] + ",");
                    $("#csv_output").append(gender_array[i] + ",");
                    //
                    // multi-array
                    // [col1, col2, col3]
                    $("#csv_output").append($("#item-" + i + "-" + hexcode_array[i][0]).find(".hex").html() + ",");
                    $("#item-" + i + "-" + hexcode_array[i][0]).addClass("selected");
                    $("#csv_output").append($("#item-" + i + "-" + hexcode_array[i][1]).find(".hex").html() + ",");
                    $("#item-" + i + "-" + hexcode_array[i][1]).addClass("selected");
                    $("#csv_output").append($("#item-" + i + "-" + hexcode_array[i][2]).find(".hex").html() + ",");
                    $("#item-" + i + "-" + hexcode_array[i][2]).addClass("selected");
                    $("#csv_output").append(purchaseurl_array[i] + ",");
                }
            }
        }

        function removeRow(id) {
            if ($.inArray(id, removed_array) == -1) {
                removed_array.push(id);
            }
            dumpCSV();
        }
        function changeColor(e) {
            var item_info = e.id.split("-");
            var item_number = item_info[1];
            var color_number = item_info[2];
            hexcode_array[item_number].unshift(color_number);
            var removed_color = hexcode_array[item_number].pop();
            $("#item-" + item_number + "-" + removed_color).removeClass("selected");
            dumpCSV();
        }

        function changeGender(id) {
            // 0 = female
            // 1 = male
            if ($("#maleradio" + id).prop('checked') && $("#femaleradio" + id).prop('checked')) {
                gender_array[id] = 2;
            }
            else if ($("#maleradio" + id).prop('checked') && !$("#femaleradio" + id).prop('checked'))
            {
                gender_array[id] = 1;
            }
            else if (!$("#maleradio" + id).prop('checked') && $("#femaleradio" + id).prop('checked'))
            {
                gender_array[id] = 0;
            }
            console.log(gender_array);
            dumpCSV();
        }






    </script>
    <style>
        .colors{
            width:50px;
            height:50px;
        }
        .selected{
            border:limegreen inset 5px;
            background-color:limegreen;
        }
        .items:hover{
            cursor: pointer;
        }
    </style>



    <body onload="dumpCSV()">
        <div id="csv_output">
        </div>
        <br/><br/><br/>

    <center><br/><br/>
        <form action="chameleon_processing.php" method="Post" enctype="multipart/form-data" id="form">         
            <input type="password" class="form" placeholder="password" name="password" /><br/>
            <input type="text" name="csv" id="csvHolder" placeholder="csv i.e 'nike, burger king, adidas'" style="margin:0 auto;left:0px;height:35px;width:291px"/><br/>
            <input type="submit" class="btn" id="submit" value="INSERT INTO HUECLUES" style="width:250px;"/>
        </form>
    </center>
</body>
</html>
