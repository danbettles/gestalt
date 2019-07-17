<?php
declare(strict_types=1);

namespace ThreeStreams\Gestalt\Tests;

use PHPUnit\Framework\TestCase;
use ThreeStreams\Gestalt\SimpleFilterChain;
use TypeError;
use stdClass;

class SimpleFilterChainTest extends TestCase
{
    /**
     * Creates an array containing all the types of `callable` -- see
     * https://www.php.net/manual/en/language.types.callable.php
     *
     * @return callable[]
     */
    private function createAllTypesOfCallable(): array
    {
        return [
            'gettype',  //(Chosen because it'll accept any type of input.)
            [new TestCallables(), 'instanceMethod'],
            [TestCallables::class, 'staticMethod'],
            TestCallables::class . '::staticMethod',
            function () {
            },
            new TestCallables(),  //`TestCallables` implements `__invoke()`.
        ];
    }

    /**
     * For convenience, creates some kind of `callable`.
     */
    private function createCallable(): callable
    {
        $callables = $this->createAllTypesOfCallable();
        $callable = reset($callables);

        return $callable;
    }

    public function providesValidFilterArrays(): array
    {
        return [[
            [],  //None...
        ], [
            $this->createAllTypesOfCallable(),  //...Or some.
        ]];
    }

    /**
     * @dataProvider providesValidFilterArrays
     */
    public function testCanBeInstantiatedWithAnArrayOfFilters($filters)
    {
        $this->assertSame(
            $filters,
            (new SimpleFilterChain($filters))->getFilters()
        );
    }

    public function testConstructorAcceptsAllTypesOfCallable()
    {
        $filters = $this->createAllTypesOfCallable();

        $this->assertSame(
            $filters,
            (new SimpleFilterChain($filters))->getFilters()
        );
    }

    public function testCanBeInstantiatedWithNoArguments()
    {
        $this->assertInstanceOf(SimpleFilterChain::class, new SimpleFilterChain());
    }

    public function providesInvalidFilters(): array
    {
        return [[
            null,
        ], [
            'foo',
        ], [
            123,
        ], [
            1.23,
        ], [
            [],
        ], [
            new stdClass,
        ]];
    }

    /**
     * @dataProvider providesInvalidFilters
     */
    public function testConstructorThrowsAnExceptionIfAFilterIsNotACallable($invalidFilter)
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessageRegExp('~ must be callable,~');

        new SimpleFilterChain([$invalidFilter]);
    }

    public function testAppendfilterAddsAFilterAtTheEndOfTheArray()
    {
        $callable1 = $this->createCallable();
        $callable2 = $this->createCallable();
        $callable3 = $this->createCallable();

        $chain = (new SimpleFilterChain())
            ->appendFilter($callable1)
            ->appendFilter($callable2)
            ->appendFilter($callable3)
        ;

        $this->assertSame([
            $callable1,
            $callable2,
            $callable3,
        ], $chain->getFilters());
    }

    public function providesValidFilters(): array
    {
        return array_map(function (callable $callable) {
            return [$callable];
        }, $this->createAllTypesOfCallable());
    }

    /**
     * @dataProvider providesValidFilters
     */
    public function testAppendfilterAcceptsAllTypesOfCallable($filter)
    {
        $chain = (new SimpleFilterChain())
            ->appendFilter($filter)
        ;

        $this->assertSame([
            $filter,
        ], $chain->getFilters());
    }

    /**
     * @dataProvider providesInvalidFilters
     */
    public function testAppendfilterThrowsAnExceptionIfTheFilterIsNotACallable($invalidFilter)
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessageRegExp('~ must be callable,~');

        (new SimpleFilterChain())->appendFilter($invalidFilter);
    }

    public function testExecuteInvokesEachFilterInTurn()
    {
        $chain = (new SimpleFilterChain([
            function (&$request) {
                $request[] = 1;
            },
            function (&$request) {
                $request[] = 2;
            },
            function (&$request) {
                $request[] = 3;
            },
        ]));

        $request = [];

        $result = $chain->execute($request);

        $this->assertSame([1, 2, 3], $request);
        $this->assertNull($result);
    }

    public function testExecuteInvokesEachFilterInTurnUntilAFilterReturnsFalse()
    {
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

        $result = $chain->execute($request);

        $this->assertSame([1, 2], $request);
        $this->assertFalse($result);
    }

    public function testExecuteInvokesEachFilterInTurnUntilAFilterReturnsTheSpecifiedValue()
    {
        $chain = (new SimpleFilterChain([
            function (&$request) {
                $request[] = 1;
            },
            function (&$request) {
                $request[] = 2;
                return true;
            },
            function (&$request) {
                $request[] = 3;
            },
        ]));

        $request = [];

        $result = $chain->execute($request, true);

        $this->assertSame([1, 2], $request);
        $this->assertTrue($result);
    }

    public function testExecuteCanInvokeAnyTypeOfCallable()
    {
        $request = 'irrelevant';

        $returnValue = (new SimpleFilterChain($this->createAllTypesOfCallable()))
            ->execute($request)
        ;

        $this->assertNull($returnValue);
    }
}
