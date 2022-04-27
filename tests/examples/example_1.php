<?php

namespace DanBettles\Gestalt\Tests;

use DanBettles\Gestalt\ArrayObject;

require_once __DIR__ . '/../../vendor/autoload.php';

$result = (new ArrayObject([
    'foo' => 'bar',
    'baz' => 'qux',
    'quux' => 'quuz',
    'corge' => 'grault',
]))
    ->sortByKey()
    ->getElements()
;

\assert($result === [
    'baz' => 'qux',
    'corge' => 'grault',
    'foo' => 'bar',
    'quux' => 'quuz',
]);
