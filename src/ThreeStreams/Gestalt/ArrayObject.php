<?php
declare(strict_types=1);

namespace ThreeStreams\Gestalt;

use Closure;

/**
 * A simple array class.  Instances are mutable (i.e. methods change the state of the object).
 */
class ArrayObject
{
    /** @var array */
    private $elements;

    public function __construct(array $elements)
    {
        $this->setElements($elements);
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
     * When no arguments are passed, behaves the same as [\ksort()](https://www.php.net/manual/en/function.ksort.php).
     * Otherwise, the elements can be put in the order specified in `$order`; this applies to arrays with numeric or
     * non-numeric keys.
     */
    public function sortByKey(array $order = []): self
    {
        if (empty($order)) {
            $sorted = $this->getElements();
            \ksort($sorted);

            return $this->setElements($sorted);
        }

        $elements = $this->getElements();
        $base = \array_intersect_key(\array_flip($order), $elements);
        $sorted = \array_replace($base, $elements);

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
}
