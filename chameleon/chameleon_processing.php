<?php

include('/connection.php');
include('/database_functions.php');

$password = $_POST['password'];
$csv = $_POST['csv'];
$csv = explode(",", $csv);
$i = 0;
$count = count($csv);


if ($password == "wellshieeet") {
    
    echo $count;
    while ($i < $count) {
        //url, description, price, gender, code1, code2, code3, purchaselink
        echo " inside while loop";
        if (database_count("storeitem", "url", $csv[$i], "description", $csv[$i + 1])<1) {// check repeats
            database_insert("storeitem", "url", $csv[$i], "description", $csv[$i + 1], "price", $csv[$i + 2], "gender", $csv[$i + 3], "code1", $csv[$i + 4], "code2", $csv[$i + 5], "code3", $csv[$i + 6], "purchaselink", $csv[$i + 7]);
        }
        else{
            echo "repeat caught<br/>";
        }
        $i+=8;
    }
    echo "inserted " . ($i / 8) . "items";
} else {
    echo "incorrect password";
}
?>
