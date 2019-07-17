<?php

namespace Tests;

use ThreeStreams\Gestalt\ArrayObject;

require_once __DIR__ . '/../../vendor/autoload.php';

$elements = [
    'foo' => 'bar',
    'baz' => 'qux',
    'quux' => 'quuz',
];

$output = [];

$arrayObject = (new ArrayObject($elements))->each(function ($key, $value) use (&$output) {
    $output[$key] = $value;
});

assert($elements === $output);
