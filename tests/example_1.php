<?php

namespace Tests;

use ThreeStreams\Gestalt\ArrayObject;

require_once __DIR__ . '/../vendor/autoload.php';

$elements = [
    'foo' => 'bar',
    'baz' => 'qux',
    'quux' => 'quuz',
    'corge' => 'grault',
];

$result = (new ArrayObject($elements))->sortByKey();

assert($result === [
    'baz' => 'qux',
    'corge' => 'grault',
    'foo' => 'bar',
    'quux' => 'quuz',
]);
