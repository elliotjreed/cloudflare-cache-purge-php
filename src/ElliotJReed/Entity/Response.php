<?php

declare(strict_types=1);

namespace ElliotJReed\Entity;

class Response
{
    /**
     * @var \ElliotJReed\Entity\Result[]
     */
    private array $results = [];

    /**
     * @return Result[]
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @param \ElliotJReed\Entity\Result ...$results
     *
     * @return $this
     */
    public function addResults(Result ...$results): self
    {
        $this->results = [...$this->results, ...$results];

        return $this;
    }

    /**
     * @return bool
     */
    public function hasResults(): bool
    {
        return \count($this->results) > 0;
    }
}
