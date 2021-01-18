<?php

use Biesior\Utility\NiceArray;

require_once 'Classes/NiceArray.php';

// initialize an object (why? find it out in https://github.com/biesior/NiceArray#readme
$niceArray = new NiceArray();

// prepare some data
$sampleInputArray = [
    'some_string' => 'Some string',
    'some_int'    => 7,
    'some_float'  => 1.23,
    'some_true'   => 1 == 1,
    'some_false'  => 1 == 2,
    'some_array'  => [
        'foo' => 'bar',
        'baz' => 'zee',
    ],
    'some_obj'    => new DateTime(),
];


$niceArray
    ->setData($sampleInputArray)
    ->setUseAnsiColors(true)
    ->setResolveObjects(true)
    ->renderArray("newArray");












//echo '<pre>';
//echo '</pre>';
