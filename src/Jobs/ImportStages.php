<?php

namespace Alltvex\ScoutOpenSearch\Jobs;

use Alltvex\ScoutOpenSearch\Jobs\Stages\CleanUp;
use Alltvex\ScoutOpenSearch\Jobs\Stages\CreateWriteIndex;
use Alltvex\ScoutOpenSearch\Jobs\Stages\PullFromSource;
use Alltvex\ScoutOpenSearch\Jobs\Stages\RefreshIndex;
use Alltvex\ScoutOpenSearch\Jobs\Stages\SwitchToNewAndRemoveOldIndex;
use Alltvex\ScoutOpenSearch\OpenSearch\Index;
use Alltvex\ScoutOpenSearch\Searchable\ImportSource;
use Illuminate\Support\Collection;

class ImportStages extends Collection
{
    /**
     * @param  ImportSource  $source
     * @return Collection
     */
    public static function fromSource(ImportSource $source)
    {
        $index = Index::fromSource($source);

        return (new self([
            new CleanUp($source),
            new CreateWriteIndex($source, $index),
            PullFromSource::chunked($source),
            new RefreshIndex($index),
            new SwitchToNewAndRemoveOldIndex($source, $index),
        ]))->flatten()->filter();
    }
}
