<?php

namespace ThreeStreams\Gestalt\Tests;

use ThreeStreams\Gestalt\ArrayObject;

require_once __DIR__ . '/../../vendor/autoload.php';

$output = [];

(new ArrayObject([
    'foo' => 'bar',
    'baz' => 'qux',
    'quux' => 'quuz',
]))->each(function ($key, $value) use (&$output) {
    $output[$key] = $value;
    return false;
});

\assert($output === [
    'foo' => 'bar',
]);
