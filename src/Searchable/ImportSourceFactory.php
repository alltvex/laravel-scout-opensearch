<?php

namespace Alltvex\ScoutOpenSearch\Searchable;

interface ImportSourceFactory
{
    public static function from(string $className): ImportSource;
}
