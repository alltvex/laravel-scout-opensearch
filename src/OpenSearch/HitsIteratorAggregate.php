<?php

namespace Alltvex\ScoutOpenSearch\OpenSearch;

interface HitsIteratorAggregate extends \Countable, \IteratorAggregate
{
    public function __construct(array $results, callable $callback = null);

    public function count();

    public function getLastSort();

    public function getHits();

    public function getIterator();
}
