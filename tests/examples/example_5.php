<?php

namespace ThreeStreams\Gestalt\Tests;

use ThreeStreams\Gestalt\SimpleFilterChain;

require_once __DIR__ . '/../../vendor/autoload.php';

$chain = (new SimpleFilterChain([
    function (&$request) {
        $request[] = 1;
    },
    function (&$request) {
        $request[] = 2;
        return false;
    },
    function (&$request) {
        $request[] = 3;
    },
]));

$request = [];

$returnValue = $chain->execute($request);

assert($request === [1, 2]);
assert($returnValue === false);
