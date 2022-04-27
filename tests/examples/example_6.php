<?php

namespace DanBettles\Gestalt\Tests;

use DanBettles\Gestalt\ArrayObject;

require_once __DIR__ . '/../../vendor/autoload.php';

$output = (new ArrayObject([
    [
        'id' => 876,
        'name' => 'Foo',
    ], [
        'id' => 12,
        'name' => 'Bar',
    ], [
        'id' => 1093,
        'name' => 'Baz',
    ],
]))
    ->reindexByColumn('id')
    ->getElements()
;

\assert([
    876 => [
        'id' => 876,
        'name' => 'Foo',
    ],
    12 => [
        'id' => 12,
        'name' => 'Bar',
    ],
    1093 => [
        'id' => 1093,
        'name' => 'Baz',
    ],
] === $output);
