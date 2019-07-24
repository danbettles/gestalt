# Gestalt

[![Build Status](https://travis-ci.com/threestreams/gestalt.svg?branch=master)](https://travis-ci.com/threestreams/gestalt)

Provides classes that implement basic patterns involving collections.  Key components are a class implementing a simplified filter-chain pattern, `SimpleFilterChain`, and a simple array class, `ArrayObject`.

## SimpleFilterChain

A simple unidirectional filter chain.

### ->execute(mixed &$request, [mixed $valueToBreak = false])

Invokes each filter in turn; the specified 'request' will be passed to each filter.

Iteration will stop if a filter returns the value of `$valueToBreak`.  If iteration is forcibly stopped then the method will return the value of `$valueToBreak`.  If, however, iteration is allowed to continue until completion then the method will return `null`.

```php
use ThreeStreams\Gestalt\SimpleFilterChain;

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
```

## ArrayObject

A simple array class.  Instances are mutable (i.e. methods change the state of the object).

### ->sortByKey([array $order = array()])

When no arguments are passed, behaves the same as [ksort()](https://www.php.net/manual/en/function.ksort.php).

```php
use ThreeStreams\Gestalt\ArrayObject;

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
```

Otherwise, the elements can be put in the order specified in `$order`; this applies to arrays with numeric or non-numeric keys.

```php
use ThreeStreams\Gestalt\ArrayObject;

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
```

### ->each(\Closure $callback)

Executes the callback for each of the elements.  The callback is passed the key and the value of the current element, in that order.

```php
use ThreeStreams\Gestalt\ArrayObject;

$elements = [
    'foo' => 'bar',
    'baz' => 'qux',
    'quux' => 'quuz',
];

$output = [];

$arrayObject = (new ArrayObject($elements))->each(function ($key, $value) use (&$output) {
    $output[$key] = $value;
});

assert($output === $elements);
```

`each()` will stop iterating if the callback returns exactly `false`.

```php
use ThreeStreams\Gestalt\ArrayObject;

$output = [];

(new ArrayObject([
    'foo' => 'bar',
    'baz' => 'qux',
    'quux' => 'quuz',
]))->each(function ($key, $value) use (&$output) {
    $output[$key] = $value;
    return false;
});

assert($output === [
    'foo' => 'bar',
]);
```
