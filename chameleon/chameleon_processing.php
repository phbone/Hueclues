<?php

include('/connection.php');
include('/database_functions.php');

$password = $_POST['password'];
$csv = $_POST['csv'];
$csv = explode(",", $csv);
$i = 0;
$count = count($csv);

echo $password;
print_r($csv);
if ($password == "wellshieeet") {
    
    while ($i < $count) {
        //url, description, price, gender, code1, code2, code3, purchaselink
        if (database_count("storeitem", "url", $csv[$i], "description", $csv[$i + 1])<1) {
            database_insert("storeitem", "url", $csv[$i], "description", $csv[$i + 1], "price", $csv[$i + 2], "gender", $csv[$i + 3], "code1", $csv[$i + 4], "code2", $csv[$i + 5], "code3", $csv[$i + 6], "purchaselink", $csv[$i + 7]);
            $i+=8;
        }
    }
    echo "inserted " . ($i / 8) . "items";
} else {
    echo "incorrect password";
}
?>
