<?php

namespace Alltvex\ScoutOpenSearch\OpenSearch;

interface HitsIteratorAggregate extends \IteratorAggregate
{
    public function __construct(array $results, callable $callback = null);

    public function getIterator();
}
