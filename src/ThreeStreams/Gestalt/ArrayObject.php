<?php
declare(strict_types=1);

namespace ThreeStreams\Gestalt;

use Closure;

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
     * Executes the callback for each of the elements.  Stops iterating if the callback returns `false`.
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
