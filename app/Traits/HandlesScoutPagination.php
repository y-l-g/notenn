<?php

namespace App\Traits;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

trait HandlesScoutPagination
{
    /**
     * Pagine manuellement les résultats de Laravel Scout.
     *
     * @param  mixed  $scoutResults  (raw() ou get())
     * @param  array  $options  (pour LengthAwarePaginator)
     */
    public function paginateScoutResults($scoutResults, int $perPage = 15, array $options = []): LengthAwarePaginator
    {
        $currentPage = request()->input('page', 1);
        $items = $scoutResults instanceof Collection
            ? $scoutResults
            : collect($scoutResults['hits'] ?? []);

        return new LengthAwarePaginator(
            items: $items->forPage($currentPage, $perPage),
            total: $items->count(),
            perPage: $perPage,
            currentPage: $currentPage,
            options: array_merge(['path' => request()->url()], $options)
        );
    }
}
