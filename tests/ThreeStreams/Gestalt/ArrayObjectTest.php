<?php

declare(strict_types=1);

namespace ThreeStreams\Gestalt\Tests;

use PHPUnit\Framework\TestCase;
use ThreeStreams\Gestalt\ArrayObject;
use InvalidArgumentException;

class ArrayObjectTest extends TestCase
{
    public function testIsInstantiable()
    {
        $emptyArrayObject = new ArrayObject();

        $this->assertSame([], $emptyArrayObject->getElements());

        $elements = ['foo' => 'bar', 'baz' => 'qux'];
        $arrayObject = new ArrayObject($elements);

        $this->assertSame($elements, $arrayObject->getElements());
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

    public function testEachExecutesTheCallbackForEachOfTheElements()
    {
        $elements = [
            'foo' => 'bar',
            'baz' => 'qux',
            'quux' => 'quuz',
        ];

        $output = [];

        (new ArrayObject($elements))->each(function ($key, $value) use (&$output) {
            $output[$key] = $value;
        });

        $this->assertSame($elements, $output);
    }

    public function testEachStopsIteratingIfTheCallbackReturnsFalse()
    {
        $output = [];

        (new ArrayObject([
            'foo' => 'bar',
            'baz' => 'qux',
            'quux' => 'quuz',
        ]))->each(function ($key, $value) use (&$output) {
            $output[$key] = $value;
            return false;
        });

        $this->assertSame([
            'foo' => 'bar',
        ], $output);
    }

    public function testAppendAddsAnElementAtTheEndOfTheArray()
    {
        $arrayObject = (new ArrayObject())
            ->append('foo')
            ->append('bar')
            ->append('baz')
        ;

        $this->assertSame(['foo', 'bar', 'baz'], $arrayObject->getElements());
    }

    public function testSetAddsOrUpdatesAnElement()
    {
        $arrayObject = (new ArrayObject())
            ->set('foo', 'bar')
            ->set('baz', 'qux')
            ->set('quux', 'quuz')
        ;

        $this->assertSame([
            'foo' => 'bar',
            'baz' => 'qux',
            'quux' => 'quuz',
        ], $arrayObject->getElements());
    }

    public function providesReindexedArrays(): array
    {
        $fooArray = [
            'id' => 123,
            'name' => 'Foo',
        ];

        $barArray = [
            'id' => 456,
            'name' => 'Bar',
        ];

        $bazArray = [
            'id' => 789,
            'name' => 'Baz',
        ];

        $fooObject = (object) $fooArray;
        $barObject = (object) $barArray;
        $bazObject = (object) $bazArray;

        return [[
            //Only array records.
            [
                'Bar' => $barArray,
                'Foo' => $fooArray,
                'Baz' => $bazArray,
            ],
            [
                $barArray,
                $fooArray,
                $bazArray,
            ],
            'name',
        ], [
            //Only object records.
            [
                'Bar' => $barObject,
                'Foo' => $fooObject,
                'Baz' => $bazObject,
            ],
            [
                $barObject,
                $fooObject,
                $bazObject,
            ],
            'name',
        ], [
            //A mix of array and object records.
            [
                'Bar' => $barObject,
                'Foo' => $fooArray,
                'Baz' => $bazObject,
            ],
            [
                $barObject,
                $fooArray,
                $bazObject,
            ],
            'name',
        ]];
    }

    /**
     * @dataProvider providesReindexedArrays
     */
    public function testReindexbycolumnvalueReindexesTheElementsByTheSpecifiedColumn(
        array $expected,
        array $input,
        string $columnKey
    ) {
        $arrayObject = new ArrayObject($input);

        $something = $arrayObject->reindexByColumn($columnKey);

        $this->assertSame($arrayObject, $something);
        $this->assertSame($expected, $something->getElements());
    }

    public function providesElementsThatAreNotAllRecords(): array
    {
        return [[
            [
                [
                    'id' => 123,
                    'name' => 'Foo',
                ],
                'Bar',  //Not a record ;-)
            ],
            'name',
            1,
        ]];
    }

    /**
     * @dataProvider providesElementsThatAreNotAllRecords
     */
    public function testReindexbycolumnvalueThrowsAnExceptionIfAnElementIsNotARecord(
        array $input,
        string $columnKey,
        $faultyElementKey
    ) {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("The element at index `{$faultyElementKey}` is not a record.");

        (new ArrayObject($input))->reindexByColumn($columnKey);
    }

    public function providesRecordsMissingTheRequiredColumn(): array
    {
        return [[
            [
                [
                    'id' => 123,
                    'name' => 'Foo',
                ],
                [
                    'id' => 456,
                    'name' => 'Bar',
                ],
                [
                    'id' => 789,
                    'title' => 'Baz',
                    //`name` element does not exist.
                ],
            ],
            'name',
            2,
        ]];
    }

    /**
     * @dataProvider providesRecordsMissingTheRequiredColumn
     */
    public function testReindexbycolumnvalueThrowsAnExceptionIfAnElementDoesNotContainTheRequiredColumn(
        array $input,
        string $columnKey,
        $faultyElementKey
    ) {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("The record at index `{$faultyElementKey}` does not contain the field `{$columnKey}`.");

        (new ArrayObject($input))->reindexByColumn($columnKey);
    }
}
