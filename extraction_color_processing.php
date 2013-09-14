<?php
session_start();
include('connection.php');
include('database_functions.php');
include('algorithms.php');



$input_color = $_POST['input_color']; // should be a hexcode for color
$shade_count = 10;

$comp = hsl_complimentary($input_color);
$shades = hsl_shades($input_color, $shade_count);
$tints = hsl_tints($input_color, $shade_count);
$triad1 = hsl_triadic1($input_color);
$triad2 = hsl_triadic2($input_color);
$anal1 = hsl_analogous1($input_color);
$anal2 = hsl_analogous2($input_color);
$split1 = hsl_split1($input_color);
$split2 = hsl_split2($input_color);

$color_matches = array(
    'complimentary' => $comp,
    'triadic1' => $triad1,
    'triadic2' => $triad2,
    'analogous1' => $anal1,
    'analogous2' => $anal2,
    'split1' => $split1,
    'split2' => $split2,
    'tints' => $tints,
    'shades' => $shades
);

echo json_encode($color_matches);
?>
