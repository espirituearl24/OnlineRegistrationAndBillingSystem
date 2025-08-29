<?php

spl_autoload_register ('ClassContainer');

function ClassContainer($classes){
    $first = "classes/";
    $third = ".class.php";
    $whole = $first . $classes . $third ;  
    include_once $whole;
}


?> 