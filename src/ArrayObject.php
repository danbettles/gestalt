<?php

declare(strict_types=1);

namespace DanBettles\Gestalt;

use Closure;
use InvalidArgumentException;

use function array_flip;
use function array_intersect_key;
use function array_key_exists;
use function array_replace;
use function is_array;
use function is_object;
use function ksort;

use const false;

/**
 * A simple array class.  Instances are mutable (i.e. methods change the state of the object).
 */
class ArrayObject
{
    /** @var array */
    private $elements;

    public function __construct(array $elements = [])
    {
        $this->setElements($elements);
    }

    /**
     * Appends an element with the specified value.
     *
     * @param mixed $value
     */
    public function append($value): self
    {
        $this->elements[] = $value;
        return $this;
    }

    /**
     * Adds or updates an element.
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function set($key, $value): self
    {
        $this->elements[$key] = $value;
        return $this;
    }

    private function setElements(array $elements): self
    {
        $this->elements = $elements;
        return $this;
    }

    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * When no arguments are passed, behaves the same as [ksort()](https://www.php.net/manual/en/function.ksort.php).
     * Otherwise, the elements can be put in the order specified in `$order`; this applies to arrays with numeric or
     * non-numeric keys.
     */
    public function sortByKey(array $order = []): self
    {
        if (empty($order)) {
            $sorted = $this->getElements();
            ksort($sorted);

            return $this->setElements($sorted);
        }

        $elements = $this->getElements();
        $base = array_intersect_key(array_flip($order), $elements);
        $sorted = array_replace($base, $elements);

        return $this->setElements($sorted);
    }

    /**
     * Executes the callback for each of the elements.  The callback is passed the key and the value of the current
     * element, in that order.  `each()` will stop iterating if the callback returns exactly `false`.
     */
    public function each(Closure $callback): void
    {
        foreach ($this->getElements() as $key => $value) {
            if (false === $callback($key, $value)) {
                return;
            }
        }
    }

    /**
     * Reindexes the elements, which must be array/object records, using the values in the column with the specified key.
     *
     * @throws InvalidArgumentException If an element is not a record.
     * @throws InvalidArgumentException If a record does not contain a field with the specified name.
     */
    public function reindexByColumn(string $columnKey): self
    {
        $reindexed = [];

        foreach ($this->getElements() as $key => $element) {
            if (!is_array($element) && !is_object($element)) {
                throw new InvalidArgumentException("The element at index `{$key}` is not a record.");
            }

            $normalizedRecord = (array) $element;

            if (!array_key_exists($columnKey, $normalizedRecord)) {
                throw new InvalidArgumentException("The record at index `{$key}` does not contain the field `{$columnKey}`.");
            }

            $newKey = $normalizedRecord[$columnKey];
            $reindexed[$newKey] = $element;
        }

        return $this->setElements($reindexed);
    }
}
