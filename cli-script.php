<?php

use Biesior\Utility\NiceArray;

require_once 'Classes/NiceArray.php';

$currentDate = new DateTime();

// Initialize obj once
$niceArray = new NiceArray();

// prepare some data
$sampleInputArray = array(
    'some_string' => 'Some string',
    'some_int'    => 7,
    'some_float'  => 1.23,
    'some_true'   => 1 == 1,
    'some_false'  => 1 == 2,
    'some_array'  => array('foo' => 'bar', 'baz' => 'zee'),
    'some_obj'    => new DateTime(),
);

$nullArray = null;

$emptyArray = array();

echo PHP_EOL;
echo PHP_EOL;
$niceArray
    ->setData($sampleInputArray)
    ->setUseAnsiColors(true)
    ->setResolveObjects(true)
    ->renderArray('colorArray');
echo PHP_EOL;
echo PHP_EOL;
echo PHP_EOL;

$monoArray = new NiceArray();
$monoArray
    ->setData($sampleInputArray)
    ->renderArray('monoArray');
echo PHP_EOL . PHP_EOL;


$colorArray = array('some_string' => 'Some string', 'some_array' => array('foo' => 'bar', 'baz' => 'zee',), 'some_obj' => 'DateTime Object(    [date] => 2020-08-23 11:39:14.857221    [timezone_type] => 3    [timezone] => UTC)',);


