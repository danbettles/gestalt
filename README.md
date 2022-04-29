# Gestalt

[![Stand With Ukraine](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/badges/StandWithUkraine.svg)](https://stand-with-ukraine.pp.ua)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/danbettles/gestalt/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/danbettles/gestalt/?branch=main) [![Code Coverage](https://scrutinizer-ci.com/g/danbettles/gestalt/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/danbettles/gestalt/?branch=main) [![Build Status](https://scrutinizer-ci.com/g/danbettles/gestalt/badges/build.png?b=main)](https://scrutinizer-ci.com/g/danbettles/gestalt/build-status/main)

Provides classes that implement basic patterns involving collections.  Key components are a class implementing a simplified filter-chain pattern, `SimpleFilterChain`, and a simple array class, `ArrayObject`.

## SimpleFilterChain

A simple unidirectional filter chain.

### ->execute(mixed &$request, [mixed $valueToBreak = false])

Invokes each filter in turn; the specified 'request' will be passed to each filter.

Iteration will stop if a filter returns the value of `$valueToBreak`.  If iteration is forcibly stopped then the method will return the value of `$valueToBreak`.  If, however, iteration is allowed to continue until completion then the method will return `null`.

```php
use DanBettles\Gestalt\SimpleFilterChain;

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

\assert($request === [1, 2]);
\assert($returnValue === false);
```

## ArrayObject

A simple array class.  Instances are mutable (i.e. methods change the state of the object).

### ->sortByKey([array $order = array()])

When no arguments are passed, behaves the same as [ksort()](https://www.php.net/manual/en/function.ksort.php).

```php
use DanBettles\Gestalt\ArrayObject;

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
```

Otherwise, the elements can be put in the order specified in `$order`; this applies to arrays with numeric or non-numeric keys.

```php
use DanBettles\Gestalt\ArrayObject;

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
```

### ->each(\Closure $callback)

Executes the callback for each of the elements.  The callback is passed the key and the value of the current element, in that order.

```php
use DanBettles\Gestalt\ArrayObject;

$elements = [
    'foo' => 'bar',
    'baz' => 'qux',
    'quux' => 'quuz',
];

$output = [];

$arrayObject = (new ArrayObject($elements))->each(function ($key, $value) use (&$output) {
    $output[$key] = $value;
});

\assert($output === $elements);
```

`each()` will stop iterating if the callback returns exactly `false`.

```php
use DanBettles\Gestalt\ArrayObject;

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
```

### ->reindexByColumn(string $columnKey)

Useful when working with collections of records (arrays/objects) of the same type.  Could be used to reindex an array of records selected from a database, by the values from a particular column, for example.

```php
use DanBettles\Gestalt\ArrayObject;

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
```
