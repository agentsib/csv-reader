# CSV Reader

Simple way read CSV file

[![Latest Stable Version](https://poser.pugx.org/agentsib/csv-reader/version?format=flat-square)](https://packagist.org/packages/agentsib/csv-reader)
[![Total Downloads](https://poser.pugx.org/agentsib/csv-reader/downloads?format=flat-square)](https://packagist.org/packages/agentsib/csv-reader)
[![Latest Unstable Version](https://poser.pugx.org/agentsib/csv-reader/v/unstable?format=flat-square)](//packagist.org/packages/agentsib/csv-reader)
[![License](https://poser.pugx.org/agentsib/csv-reader/license?format=flat-square)](https://packagist.org/packages/agentsib/csv-reader)


## Installation

Install via [composer](https://getcomposer.org/):

```sh
composer require agentsib/csv-reader
```

## Read CSV file with headers

Example file `test.csv`:

```csv
id;name;value
1;test1;value1
2;test2;value2
```

Read file:

```php
<?

$resource = fopen('test.csv','r');
$csv = new AgentSIB\CsvReader\CsvReader($resource, true, ';');

// Get headers array
$csv->getHeaders(); // ["id", "name", "value"]
// Check header 
$csv->hasHeader('id'); // true

foreach ($csv as $row) {
    // Get value by column number
    echo $row[1]; // test1,test2
    // Get value by column name
    echo $row['value']; // value1,value2
    // Get value by not exists column
    echo $row['not_exists']; // null
    
    // Check value for exists
    isset($row[1]); // true
    isset($row[20]); // false
    isset($row['value']); //true
    isset($row['not_exists']); //false 
}

$csv->rewind();
$firstRow = $csv->current();
foreach ($firstRow as $key => $value) {
    echo $key.' => '.$value.PHP_EOL;
}
// id => 1
// name => test1
// value => value1

// Replace headers example
$csv->replaceHeaders(['prop_id', 'prop_name', 'prop_value']);

$csv->getHeaders(); // ['prop_id', 'prop_name', 'prop_value']

$csv->rewind();
$firstRow = $csv->current();
foreach ($firstRow as $key => $value) {
    echo $key.' => '.$value.PHP_EOL;
}
// prop_id => 1
// prop_name => test1
// prop_value => value1

// Replace headers example 2
$csv->replaceHeaders(['prop_id', 'prop_name']);

$csv->getHeaders(); // ['prop_id', 'prop_name']

$csv->rewind();
$firstRow = $csv->current();
foreach ($firstRow as $key => $value) {
    echo $key.' => '.$value.PHP_EOL;
}
// prop_id => 1
// prop_name => test1

```

## Read CSV file without headers

Example file `test.csv`:

```csv
1;test1;value1
2;test2;value2
```

Read file:

```php
<?

$resource = fopen('test.csv','r');
$csv = new AgentSIB\CsvReader\CsvReader($resource, false, ';');

// Get headers array
$csv->getHeaders(); // []

foreach ($csv as $row) {
    // Get value by column number
    echo $row[1]; // test1,test2
    echo $row[20]; // null,null

    // Check value for exists
    isset($row[1]); // true
    isset($row[20]); // false
}

$csv->rewind();
$firstRow = $csv->current();
foreach ($firstRow as $key => $value) {
    echo $key.' => '.$value.PHP_EOL;
}
// 0 => 1
// 1 => test1
// 2 => value1

// Replace headers example
$csv->replaceHeaders(['prop_id', 'prop_name', 'prop_value']);

$csv->getHeaders(); // ['prop_id', 'prop_name', 'prop_value']

$csv->rewind();
$firstRow = $csv->current();
foreach ($firstRow as $key => $value) {
    echo $key.' => '.$value.PHP_EOL;
}
// prop_id => 1
// prop_name => test1
// prop_value => value1

// Replace headers example 2
$csv->replaceHeaders(['prop_id', 'prop_name']);

$csv->getHeaders(); // ['prop_id', 'prop_name']

$csv->rewind();
$firstRow = $csv->current();
foreach ($firstRow as $key => $value) {
    echo $key.' => '.$value.PHP_EOL;
}
// prop_id => 1
// prop_name => test1

```

You can set headers on initialize:

```php
<?php

$resource = fopen('test.csv','r');
$csv = new AgentSIB\CsvReader\CsvReader($resource, ['id', 'name', 'value'], ';');

// Get headers array
$csv->getHeaders(); // ['id', 'name', 'value']

$csv->rewind();
$firstRow = $csv->current();
foreach ($firstRow as $key => $value) {
    echo $key.' => '.$value.PHP_EOL;
}
// id => 1
// name => test1
// value => value1

```

## Known issue

If you use stream `php://input`, you need save content to another stream, because `rewind` function not work in this case.
For example:

```php
<?php

$resource = fopen('php://input', 'rb');

$r = fopen('php://memory', 'r+');
fwrite($r, stream_get_contents($resource));
rewind($r);

$csv = new AgentSIB\CsvReader\CsvReader($r);

```