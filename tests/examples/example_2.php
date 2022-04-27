<?php

namespace DanBettles\Gestalt\Tests;

use DanBettles\Gestalt\ArrayObject;

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

$result = (new ArrayObject($elements))
    ->sortByKey($order)
    ->getElements()
;

\assert($result === [
    'corge' => 'grault',
    'foo' => 'bar',
    'quux' => 'quuz',
    'baz' => 'qux',
]);
