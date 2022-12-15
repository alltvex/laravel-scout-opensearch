<?php

namespace Alltvex\ScoutOpenSearch\OpenSearch;

interface Alias
{
    public function name(): string;

    public function config(): array;
}
