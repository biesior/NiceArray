# `\Biesior\Utility\NiceArray` class

[![Donate](https://img.shields.io/static/v1?label=Donate&message=paypal.me/biesior&color=brightgreen "Donate the contributor via PayPal.me, amount is up to you")](https://www.paypal.me/biesior/19.99EUR)
[![State](https://img.shields.io/static/v1?label=stable&message=1.0.0&color=blue 'Latest known version')](https://github.com/biesior/version-updater/tree/0.0.9-alpha)
![Updated](https://img.shields.io/static/v1?label=upated&message=2020-08-22+21:21:59&color=lightgray 'Latest known update date') 
[![Minimum PHP version](https://img.shields.io/static/v1?label=PHP&message=5.4.0+or+higher&color=blue "Minimum PHP version")](https://www.php.net/releases/5_4_0.php)

## What does it do?

Allows displaying PHP array as... PHP array code:

## Public methods

There are several public samples that can or must bu used always you need to `setData()` than you need to `render()` (literally `echo` it) or `return()` as value. All other public methods are optional.

| Method                 | Default       | What to do                                                                                                                                                                                                                                                                         |
|:-----------------------|:--------------|:-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `setData()`            | `empty array` | Just add an `array` data you want to display                                                                                                                                                                                                                                       |
| `setResolveObjects()`  | `false`       | Setter for `$resolveObjects` property.<br>If `false`  only object's name with `(not resolved)` suffix will be displayed.<br>If `true` some more data of the object with `print_r()` will be shown.<br><br>Check sample outpu of usage 2 for clarification.                         |
| `setResolveBooleans()` | `true`        | Setter for `$resolveBooleans` which is `true` by default and should stay as is.<br>Normally PHP when displaying bool it returns `1` for `true` and `null` for `false`, <br>We want to display `true` or `false` instead.<br><br>See `some_true` and `some_false` in sample output  |
| `render()`             | `n/a`         | Renders output directly to the browser/console                                                                                                                                                                                                                                     |
| `return()`             | `n/a`         | Returns formatted array as a variable, so you can modify it or send with email or anything else.                                                                                                                                                                                   |

<!-- !Thanks https://www.tablesgenerator.com/markdown_tables -->

## Some samples

### Input array:

```php
$sampleInputArray = [
    'some_string' => 'Some string',
    'some_int'    => 7,
    'some_float'  => 1.23,
    'some_true'   => 1 == 1,
    'some_false'  => 1 == 2,
    'some_array'  => ['foo' => 'bar', 'baz' => 'zee'],
    'some_obj'    => new DateTime(),
];
``` 

### Usage 1

```php
// import namespace so you don't need to use FQN each time later
use Biesior\Utility\NiceArray;

// require, include or autoload here, although autoloader is best option 
// for this sample let's go with old, good require_once ae:
require_once 'Classes/NiceArray.php';

// some lines later...

// Initialize $niceArray obj once
$niceArray = new NiceArray();

// and use it as meny times you want:

$niceArray
    ->setData($sampleInputArray)
    ->renderArray('outputArray');
```

#### Sample output:

```php
$outputArray = [
  'some_string' => 'Some string',
  'some_int' => 7,
  'some_float' => 1.23,
  'some_array' => [
    'foo' => 'bar',
    'baz' => 'zee',
  ],
  'dt_obj' => 'DateTime Object (not resolved)',
];
``` 

**Note** In this and next output sample(s) intends and formatting of the result __may__ (and probably __will__) be different. This is **not** purpose of this code to keep formatting of the input array. If you really need to use same formatting for input and output arrays it's probably task for your IDE to format them with your preferred settings. 

### Usage 2

As probably uou observed when it comes to deal with objects values within an array there's only its name with `(not resolved)` suffix.

If you want to get some more info about object you can turn on objects resolving with `setResolveObjects(true)` before `render()` method call like: 

```php
// skipping requires and imports from Usage 1, just rewrite them if needed

$niceArray = new NiceArray();

$niceArray
    ->setData($sampleInputArray)
    ->setResolveObjects(true)
    ->render('outputArray');
```

#### The ouput will be:

Literally if value is an object, `print_r()` is used for its displaying. 

It's just equivalent for `print_r($obj)` with some formatting.

```php
$outputArray = [
  'some_string' => 'Some string',
  'some_int' => 7,
  'some_float' => 1.23,
  'some_true' => true,
  'some_false' => false,
  'some_array' => [
    'foo' => 'bar',
    'baz' => 'zee',
  ],
  'some_obj' => 'DateTime Object
           (
               [date] => 2020-08-23 00:48:49.755825
               [timezone_type] => 3
               [timezone] => UTC
           )',
];
```

#### Be careful!

Although most objects could be resolved like this, some monsters like [mPDF](https://mpdf.github.io/) with thousands properties and/or binary code inside will fail on resolving. Although these classes does it work (kudos!) sometimes it's just impossible or at least not suggested debugging them.

### Why initialize? 

... instead of using static methods?

#### Answer short:

Because we're living in XXI centur and we're `@lazy` ;)

#### Longer answer

If we wanted stay with `static` only methods we could go with procedural style and skip all thi OOP. 

What's more, `setData()` method validates if incoming data is `array`.
