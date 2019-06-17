<?php
declare(strict_types=1);

namespace Tests\ThreeStreams\Gestalt;

use PHPUnit\Framework\TestCase;
use ThreeStreams\Gestalt\ArrayObject;

class ArrayObjectTest extends TestCase
{
    public function testIsInstantiable()
    {
        $elements = ['foo' => 'bar', 'baz' => 'qux'];
        $arrayObject = new ArrayObject($elements);

        $this->assertEquals($elements, $arrayObject->getElements());
    }

    public function providesArraysSortedByKey()
    {
        return [[
            [
                'baz' => 'qux',
                'foo' => 'bar',
            ],
            [
                'foo' => 'bar',
                'baz' => 'qux',
            ],
            [],
        ], [
            [
                'foo',
                'bar',
                'baz',
            ],
            [
                'foo',
                'bar',
                'baz',
            ],
            [],
        ], [
            [
                1 => 'baz',
                2 => 'bar',
                3 => 'foo',
            ],
            [
                3 => 'foo',
                2 => 'bar',
                1 => 'baz',
            ],
            [],
        ], [  //Order array contains the same number of keys.
            [
                'corge' => 'grault',
                'foo' => 'bar',
                'quux' => 'quuz',
                'baz' => 'qux',
            ],
            [
                'foo' => 'bar',
                'baz' => 'qux',
                'quux' => 'quuz',
                'corge' => 'grault',
            ],
            [
                'corge',
                'foo',
                'quux',
                'baz',
            ],
        ], [  //Order array contains fewer keys.
            [
                'corge' => 'grault',
                'foo' => 'bar',
                'baz' => 'qux',
                'quux' => 'quuz',
            ],
            [
                'foo' => 'bar',
                'baz' => 'qux',
                'quux' => 'quuz',
                'corge' => 'grault',
            ],
            [
                'corge',
                'foo',
            ],
        ], [  //Order array contains extra keys.
            [
                'corge' => 'grault',
                'foo' => 'bar',
                'quux' => 'quuz',
                'baz' => 'qux',
            ],
            [
                'foo' => 'bar',
                'baz' => 'qux',
                'quux' => 'quuz',
                'corge' => 'grault',
            ],
            [
                'corge',
                'foo',
                'quux',
                'baz',
                'garply',
                'waldo',
            ],
        ], [  //Numeric keys and explicit order.
            [
                56 => 'bar',
                2 => 'baz',
                23 => 'foo',
                17 => 'qux',
            ],
            [
                23 => 'foo',
                56 => 'bar',
                2 => 'baz',
                17 => 'qux',
            ],
            [
                56,
                2,
            ],
        ]];
    }

    /**
     * @dataProvider providesArraysSortedByKey
     */
    public function testSortbykeySortsTheElementsByKey(array $expectedElements, array $elements, array $order)
    {
        $actual = (new ArrayObject($elements))
            ->sortByKey($order)
            ->getElements()
        ;

        $this->assertSame($expectedElements, $actual);
    }
}
