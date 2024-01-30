<?php

namespace Alltvex\ScoutOpenSearch\OpenSearch;

use Traversable;

interface HitsIteratorAggregate extends \Countable, \IteratorAggregate
{
    public function __construct(array $results, callable $callback = null);

    public function count(): int;

    public function getLastSort(): array;

    public function getHits(): array;

    public function getIterator(): Traversable;
}
