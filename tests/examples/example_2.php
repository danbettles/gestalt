<?php

namespace Tests;

use ThreeStreams\Gestalt\ArrayObject;

require_once __DIR__ . '/../../vendor/autoload.php';

$elements = [
    'foo' => 'bar',
    'baz' => 'qux',
    'quux' => 'quuz',
    'corge' => 'grault',
];

$order = [
    'corge',
    'foo',
    'quux',
];

$result = (new ArrayObject($elements))->sortByKey($order);

assert($result === [
    'corge' => 'grault',
    'foo' => 'bar',
    'quux' => 'quuz',
    'baz' => 'qux',
]);
