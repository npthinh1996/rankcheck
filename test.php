<?php

$file = file_get_contents('import.csv');
$file = mb_convert_encoding($file, "UTF-8", "UTF-16LE");

$data = str_getcsv($file, "\n");
unset($data[0]);
$str = '';

foreach($data as $ar){
    $ar = str_getcsv($ar, "\t");
    $i = 0;
    foreach($ar as $el){
        if($i == 1) $str = $str . $el . ",";
        $i++;
    }
}

$str = rtrim($str, ',');
echo $str;

?>
