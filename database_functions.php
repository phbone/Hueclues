<?php

// corresponds to mysql_query of the form SELECT * FROM
// creates and sends the query using mysql_query()
function database_query($database, $field1, $val1, $field2 = "", $val2 = "", $field3 = "", $val3 = "", $field4 = "", $val4 = "") {
// determine how many fields are used to call database
// if the 2nd field and value are blank, query does not need AND
    // for more specific statements, function auto adds more AND statements

    if ($field2 == "" && $val2 == "") {
        $query = "SELECT * FROM " . $database . " WHERE " . $field1 . "= '" . $val1 . "'";
    } else {

        $query = "SELECT * FROM " . $database . " WHERE " . $field1 . "= '" . $val1 . "' AND " . $field2 . "='" . $val2 . "'";
        if ($val3) {
            $query .= " AND " . $field3 . "='" . $val3 . "'";
        }
        if ($val4) {
            $query .= " AND " . $field4 . "='" . $val4 . "'";
        }
    }
    $result = mysql_query($query);
    return $result;
}

// corresponds to mysql_query of the form SELECT * FROM with OR statements
// creates and sends the query using mysql_query()
function database_or_query($database, $field1, $val1, $field2 = "", $val2 = "", $orfield1 = "", $orvar1 = "", $orfield2 = "", $orvar2 = "") {
// determine how many fields are used to call database
// if the 2nd field and value are blank, query does not need AND
    if ($field2 == "" && $val2 == "") {
        $query = "SELECT * FROM " . $database . " WHERE (" . $field1 . "= '" . $val1 . "'";
    } else {
        $query = "SELECT * FROM " . $database . " WHERE " . $field1 . "= '" . $val1 . "' AND (" . $field2 . "='" . $val2 . "'";
    }
    if (!$orfield1 == "" && !$orvar1 == "") {
        $query .= " OR " . $orfield1 . "= '" . $orvar1 . "'";
    }
    if (!$orfield2 == "" && !$orvar2 == "") {
        $query .= " OR " . $orfield2 . "= '" . $orvar2 . "'";
    }

    $query .= ")";
    $result = mysql_query($query);
    return $result;
}

// corresponds to mysql_querys of the form SELECT * FROM
// uses the function mysql_fetch_array()
function database_fetch($database, $field1, $val1, $field2 = "", $val2 = "") {
    $result = database_query($database, $field1, $val1, $field2, $val2);
    $database_var = mysql_fetch_array($result);
    return $database_var;
}

function database_like_results($database, $field, $like) {

// USED for search query: looks for words containing $like
    $query = "SELECT * FROM " . $database . " WHERE " . $field . " LIKE %" . $like . "%";
    return mysql_query($query);
}

function database_order_fetch($database, $field1, $val1, $field2 = "", $val2 = "", $orderby = "", $direction = "DESC") {

// determine how many fields are used to call database
// if the 2nd field and value are blank, query does not need AND
    if ($field2 == "" && $val2 == "") {
        $query = "SELECT * FROM " . $database . " WHERE " . $field1 . "= '" . $val1 . "'";
    } else {
        $query = "SELECT * FROM " . $database . " WHERE " . $field1 . "= '" . $val1 . "' AND " . $field2 . "='" . $val2 . "'";
    }
    if ($orderby) {
        $query = $query . " ORDER BY " . $orderby . " " . $direction;
    }
    $result = mysql_query($query);
    $database_var = mysql_fetch_array($result);
    return $database_var;
}

// corresponds to mysql_querys of the form UPDATE ... SET.... WHERE 
// $fieldmatch and $valuematch are variables after WHERE
function database_update($database, $fieldmatch, $valuematch, $fieldmatch2, $valuematch2, $f1, $v1, $f2 = "", $v2 = "", $f3 = "", $v3 = "", $f4 = "", $v4 = "", $f5 = "", $v5 = "", $f6 = "", $v6 = "", $f7 = "", $v7 = "") {
    $f = array($f1, $f2, $f3, $f4, $f5, $f6, $f7);
    $v = array($v1, $v2, $v3, $v4, $v5, $v6, $v7);

    for ($i = 1; $i < 7; $i++) {
        if ($f[$i] != "") {
            $f[$i] = ", " . $f[$i];
        }
        if ($v[$i] != "") {
            $v[$i] = "='" . $v[$i] . "'";
        }
    }
    if ($fieldmatch2 && $valuematch2) {
        $query = "UPDATE " . $database . " SET " . $f[0] . "='" . $v[0] . "' " . $f[1] . $v[1] . $f[2] . $v[2] . $f[3] . $v[3] . $f[4] . $v[4] . $f[5] . $v[5] . $f[6] . $v[6] . $f[7] . $v[7] . " WHERE " . $fieldmatch . " ='" . $valuematch . "' AND " . $fieldmatch2 . "='" . $valuematch2 . "'";
    } else {
        $query = "UPDATE " . $database . " SET " . $f[0] . "='" . $v[0] . "' " . $f[1] . $v[1] . $f[2] . $v[2] . $f[3] . $v[3] . $f[4] . $v[4] . $f[5] . $v[5] . $f[6] . $v[6] . $f[7] . $v[7] . " WHERE " . $fieldmatch . " ='" . $valuematch . "'";
    }
    mysql_query($query);
}

// corresponds to mysql_query of the form SELECT * FROM
// uses the function mysql_num_row()
// returns an integer - number of rows which satisfy given conditions
function database_count($database, $field1, $val1, $field2 = "", $val2 = "") {
    $result = database_query($database, $field1, $val1, $field2, $val2);
    $database_row = mysql_num_rows($result);
    return $database_row;
}

// corresponds to mysql_query of the form INSERT INTO 
// uses the fcuntion mysql_query()
function database_insert($database, $f1, $v1, $f2 = "", $v2 = "", $f3 = "", $v3 = "", $f4 = "", $v4 = "", $f5 = "", $v5 = "", $f6 = "", $v6 = "", $f7 = "", $v7 = "", $f8 = "", $v8 = "", $f9 = "", $v9 = "") {
    $f = array($f1, $f2, $f3, $f4, $f5, $f6, $f7, $f8, $f9);
    $v = array($v1, $v2, $v3, $v4, $v5, $v6, $v7, $v8, $v9);

    for ($i = 1; $i < 9; $i++) {
        if ($f[$i] != "") {
            $f[$i] = ", " . $f[$i];
        }
        if ($v[$i] != "") {
            $v[$i] = ", '" . $v[$i] . "'";
        }
    }
    $query = "INSERT INTO " . $database . " (" . $f[0] . $f[1] . $f[2] . $f[3] . $f[4] . $f[5] . $f[6] . $f[7] . $f[8] . ") VALUES(" . "'" . $v[0] . "'" . $v[1] . $v[2] . $v[3] . $v[4] . $v[5] . $v[6] . $v[7] . $v[8] . ")";
    mysql_query($query);
}

// corresponds to mysql_query of the form INSERT INTO 
// uses the fcuntion mysql_query()
function database_view_insert($database, $f1, $v1, $f2 = "", $v2 = "", $f3 = "", $v3 = "", $f4 = "", $v4 = "", $f5 = "", $v5 = "", $f6 = "", $v6 = "", $f7 = "", $v7 = "", $f8 = "", $v8 = "", $f9 = "", $v9 = "") {
    $f = array($f1, $f2, $f3, $f4, $f5, $f6, $f7, $f8, $f9);
    $v = array($v1, $v2, $v3, $v4, $v5, $v6, $v7, $v8, $v9);

    for ($i = 1; $i < 9; $i++) {
        if ($f[$i] != "") {
            $f[$i] = ", " . $f[$i];
        }
        if ($v[$i] != "") {
            $v[$i] = ", '" . $v[$i] . "'";
        }
    }
    $query = "INSERT INTO " . $database . " (" . $f[0] . $f[1] . $f[2] . $f[3] . $f[4] . $f[5] . $f[6] . $f[7] . $f[8] . ") VALUES(" . "'" . $v[0] . "'" . $v[1] . $v[2] . $v[3] . $v[4] . $v[5] . $v[6] . $v[7] . $v[8] . ")";
    echo $query;
}

// corresponds to mysql_query of the form DELETE FROM
// uses the function mysql_query()
function database_delete($database, $f1, $v1, $f2 = "", $v2 = "") {
    if ($f2 == "" || $v2 == "") {
// only one field used
        $query = "DELETE FROM " . $database . " WHERE " . $f1 . "= '" . $v1 . "'";
    } else {
        $query = "DELETE FROM " . $database . " WHERE " . $f1 . "= '" . $v1 . "' AND " . $f2 . "='" . $v2 . "'";
    }
    mysql_query($query);
}

// a function that increments a value within a database by
// a given increment value
function database_increment($database, $field, $value, $update, $increment) {
    $db = database_fetch($database, $field, $value);
    $new = $db[$update] + $increment;
    $query = "UPDATE " . $database . " SET " . $update . "= '" . $new . "' WHERE " . $field . " ='" . $value . "'";
    mysql_query($query);
}

// a function that decrements a value within a database by
// a given decrement value
function database_decrement($database, $field, $value, $update, $decrement) {
    $db = database_fetch($database, $field, $value);
    $new = $db[$update] - $decrement;
    $query = "UPDATE " . $database . " SET " . $update . "= '" . $new . "' WHERE " . $field . " ='" . $value . "'";
    mysql_query($query);
}

// a function that decrements a value within a database by
// a given decrement value 
function database_paginate($database, $offset, $number) {
    $query = "SELECT * FROM " . $database . " LIMIT " . $offset . ", '" . $number;
    mysql_query($query);
}

?>
