<?php

namespace LaravelCms\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class Filter
{
    /**
     * The request instance.
     *
     * @var Request
     */
    public $request;

    /**
     * The array of matched parameters.
     *
     * @var null|array
     */
    public $parameters;

    /**
     * Current app language.
     *
     * @var string
     */
    public $lang;

    /**
     * The value delimiter.
     *
     * @var string
     */
    protected static $delimiter = ',';

    /**
     * Filter constructor.
     */
    public function __construct()
    {
        $this->request = request();
        $this->lang = app()->getLocale();
    }

    /**
     * Apply filter if the request parameters were satisfied.
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function filter(Builder $builder): Builder
    {
        $when = empty($this->parameters()) || $this->request->hasAny($this->parameters());

        return $builder->when($when, function (Builder $builder) {
            return $this->run($builder);
        });
    }

    /**
     * The array of matched parameters.
     *
     * @return array|null
     */
    public function parameters(): ?array
    {
        return $this->parameters;
    }

    /**
     * Apply to a given Eloquent query builder.
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    abstract public function run(Builder $builder): Builder;

    /**
     * The displayable name of the filter.
     *
     * @return string
     */
    public function name(): string
    {
        return class_basename(static::class);
    }

    /**
     * Whether there are suitable parameters in the query to apply the filter.
     *
     * @return bool
     */
    public function isApply(): bool
    {
        return count($this->request->only($this->parameters(), [])) > 0;
    }

    /**
     * Link without filters applied
     *
     * @return string
     */
    public function resetLink(): string
    {
        $params = http_build_query($this->request->except($this->parameters()));

        return url($this->request->url().'?'.$params);
    }
}
