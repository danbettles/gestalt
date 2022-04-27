<?php

declare(strict_types=1);

namespace DanBettles\Gestalt;

use const false;
use const null;

/**
 * A simple unidirectional filter chain.
 */
class SimpleFilterChain
{
    private $filters;

    public function __construct(array $filters = [])
    {
        $this->setFilters($filters);
    }

    public function appendFilter(callable $filter): self
    {
        $this->filters[] = $filter;
        return $this;
    }

    private function setFilters(array $filters): self
    {
        $this->filters = [];

        foreach ($filters as $filter) {
            $this->appendFilter($filter);
        }

        return $this;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Invokes each filter in turn; the specified 'request' will be passed to each filter.
     *
     * Iteration will stop if a filter returns the value of `$valueToBreak`.  If iteration is forcibly stopped then the
     * method will return the value of `$valueToBreak`.  If, however, iteration is allowed to continue until completion
     * then the method will return `null`.
     *
     * @param mixed $request
     * @param mixed $valueToBreak
     * @return mixed
     */
    public function execute(&$request, $valueToBreak = false)
    {
        foreach ($this->getFilters() as $filter) {
            $returnValue = $filter($request);

            if ($valueToBreak === $returnValue) {
                return $returnValue;
            }
        }

        return null;
    }
}
