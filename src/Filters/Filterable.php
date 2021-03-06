<?php

namespace LaravelCms\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use LaravelCms\Filters\HttpFilter;

trait Filterable
{
    /**
     * Apply the filter to the given query.
     *
     * @param Builder  $query
     * @param iterable $filters
     *
     * @return Builder
     */
    public function scopeFiltersApply(Builder $query, iterable $filters = []): Builder
    {
        return collect($filters)
            ->map(function ($filter) {
                return is_object($filter) ? $filter : resolve($filter);
            })
            ->reduce(function (Builder $query, Filter $filter) {
                return $filter->filter($query);
            }, $query);
    }

    /**
     * @param Builder         $builder
     * @param HttpFilter|null $httpFilter
     *
     * @return Builder
     */
    public function scopeFilters(Builder $builder, HttpFilter $httpFilter = null): Builder
    {
        $filter = $httpFilter ?? new HttpFilter();
        $filter->build($builder);

        return $builder;
    }

    /**
     * @param Builder $builder
     * @param string  $column
     * @param string  $direction
     *
     * @return Builder
     */
    public function scopeDefaultSort(Builder $builder, string $column, string $direction = 'asc')
    {
        if (empty($builder->getQuery()->orders)) {
            $builder->orderBy($column, $direction);
        }

        return $builder;
    }

    /**
     * @return Collection
     */
    public function getOptionsFilter(): Collection
    {
        return collect([
            'allowedFilters' => collect($this->allowedFilters ?? []),
            'allowedSorts' => collect($this->allowedSorts ?? []),
            'allowedRelationsFilters' => collect($this->allowedRelationsFilters ?? []),
        ]);
    }
}
