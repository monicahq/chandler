<?php

namespace App\Helpers;

class ScoutHelper
{
    /**
     * When updating a model, this method determines if we should update the search index.
     *
     * @return bool
     */
    public static function searchIndexShouldBeUpdated()
    {
        return (config('scout.driver') === 'algolia' && config('scout.algolia.id') !== '') || (config('scout.driver') === 'meilisearch' && config('scout.meilisearch.host') !== '');
    }
}
