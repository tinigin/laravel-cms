<?php

namespace LaravelCms\Table;

use Illuminate\Support\Str;
use LaravelCms\Exceptions\BadDataException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use LaravelCms\Models\Cms\Section;
use Illuminate\Support\Arr;

class Grid {
    protected $options = [
        'limit' => 20,
        'sortable' => false,
        'add' => true,
        'delete' => true,
        'multiple-delete' => false,
    ];

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $className;

    /**
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $query;

    /**
     * Array of columns
     * @var array
     */
    protected $columns;

    /**
     * Items
     * @var array
     */
    protected $items = [];

    /**
     * Current section
     * @var Section
     */
    protected $section;

    /**
     * Filter HTML form elements
     * @var array
     */
    protected $filter = [];

    /**
     * Show all items
     * @var boolean
     */
    protected $all = false;

    /**
     * Grid constructor.
     * @param string $storageKeyName
     * @param array $options
     * @throws BadDataException
     */
    public function __construct(array $options, Section $section)
    {
        $this->section = $section;
        $this->initOptions($options);

        $this->process();
    }

    /**
     * @param array $options
     * @throws BadDataException
     */
    protected function initOptions(array $options)
    {
        if (!isset($options['class'])) {
            throw new BadDataException;

        } else {
            $this->className = $options['class'];
        }

        foreach (['sortable', 'add', 'delete', 'multiple-delete', 'limit'] as $key) {
            if (isset($options[$key])) {
                $this->options[$key] = $options[$key];
            }
        }

        if (!isset($options['columns']) || !is_array($options['columns']))
            throw new BadDataException;

        $this->columns = $options['columns'];
    }

    /**
     * Set option
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    protected function setOption(string $key, mixed $value): void
    {
        $this->options[$key] = $value;
    }

    /**
     * Get option value
     * @param string $key
     * @param null $default
     * @return string
     */
    protected function getOption(string $key, $default = null): string
    {
        return isset($this->options[$key]) && $this->options[$key] ? $this->options[$key] : $default;
    }

    protected function process()
    {
        $this->setOption('all', request()->has('all'));

        $this->query = $this->className::filters();

        $url = request()->url();
        $query = request()->query();

        // Clean query string
        if (request()->has('filter-submit')) {
            $tmp = [];
            foreach ($query as $key => $value) {
                if ($key == 'filter' && $value) {
                    $tmp['filter'] = [];
                    foreach ($query['filter'] as $k => $v) {
                        if ($v) {
                            $tmp['filter'][$key] = $value;
                        }
                    }
                }
                if ($value) {
                    $tmp[$key] = $value;
                }
            }

            redirect()->to($url . (empty($tmp) ?: '?' . http_build_query($tmp)))->send();
        }

        $model = $this->query->getModel();
        $defaultSortField = property_exists($model, 'defaultSortField') ? $model->defaultSortField : array_keys($this->columns)[0];
        $defaultSortOrder = property_exists($model, 'defaultSortOrder') ? $model->defaultSortOrder : 'asc';
        $sort = request()->get('sort', $defaultSortField);
        $descending = strpos($sort, '-') === 0;
        $sortKey = ltrim($sort, '-');

        if ($this->columns) {
            foreach ($this->columns as $key => $options) {
                // Filter
                if (isset($options['filter']) && $options['filter']) {
                    switch ($options['type']) {
                        case 'number':
                        case 'string':
                            $this->filter[] = \LaravelCms\Form\Fields\Input::make('filter[' . $key .']')
                                ->title($options['label'])
                                ->placeholder($options['label'])
                                ->value($this->getFilterValue($key))
                                ->render();
                            break;

                        case 'multiple':
                            $select = \LaravelCms\Form\Fields\Select::make('filter[' . $key .']')
                                ->multiple()
                                ->title($options['label'])
                                ->value($this->getFilterValue($key));
                            if (Arr::has($options, 'options')) {
                                $select->options(Arr::get($options, 'options'));
                            } elseif (Arr::has($options, 'class')) {
                                $select->fromModel(Arr::get($options, 'class'), 'name');
                            }
                            $this->filter[] = $select->render();
                            break;

                        case 'boolean':
                            $select = \LaravelCms\Form\Fields\Select::make('filter[' . $key .']')
                                ->title($options['label'])
                                ->options([
                                    false => '??????',
                                    true => '????'
                                ])
                                ->empty('???????????? ???? ??????????????')
                                ->value($this->getFilterValue($key));
                            if (Arr::has($options, 'options')) {
                                $select->options(Arr::get($options, 'options'));
                            } elseif (Arr::has($options, 'class')) {
                                $select->fromModel(Arr::get($options, 'class'), 'name');
                            }
                            $this->filter[] = $select->render();
                            break;
                    }
                }

                // Sorting
                if (isset($options['is-sortable']) && $options['is-sortable'] && $options['type'] != 'multiple') {
                    if ($key == $sortKey) {
                        $this->columns[$key]['active'] = true;
                        $this->columns[$key]['direction'] = $descending ? 'desc' : 'asc';
                        $query['sort'] = $descending ? $key : '-' . $key;

                    } else {
                        $query['sort'] = $key;
                    }

                    $this->columns[$key]['url'] = $url . '?' . http_build_query($query);
                }
            }
        }

        // Sort
        if (!request()->has('sort'))
            $this->query->orderBy($defaultSortField, $defaultSortOrder);

        $this->items = $this->all()
            ? $this->query->get()
            : $this->query->paginate($this->getOption('limit', 20));

        // Fill data columns
        $data = [];
        if ($this->items) {
            foreach ($this->items AS $item) {
                $itemData = [];

                foreach ($this->columns AS $column => $options) {
                    $itemData[$column] = '';

                    if (method_exists($item, 'text' . Str::studly($column))) {
                        $method = 'text' . Str::studly($column);
                        $itemData[$column] = $item->$method();

                    } else if (in_array($column, array_keys($item->getAttributes()))) {
                        $itemData[$column] = $item->$column;

                    } else if (method_exists($item, $column)) {
                        $relation = $item->$column();

                        if ($relation instanceof BelongsTo) {
                            $itemData[$column] = $item->$column->name;

                        }  elseif ($relation instanceof BelongsToMany) {
                            $values = [];
                            foreach ($item->$column as $i) {
                                array_push($values, $i->name);
                            }

                            $itemData[$column] = join(', ', $values);
                        }
                    }
                }

                $itemData['attributes'] = [
                    'id' => $item->id
                ];

                array_push($data, $itemData);
            }
        }

        $this->data = $data;

//         dd($this->filter);
    }

    /**
     *
     * @param string $key
     * @param string $default
     * @return string|array|null
     */
    public function getFilterValue(string $key, $default = ''): mixed
    {
        return Arr::get(request()->get('filter', []), $key, '');
    }

    /**
     * Return items
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public function items(): Collection
    {
        return $this->items;
    }

    /**
     * Return total items amount
     * @return int
     */
    public function count(): int
    {
        return $this->items->count();
    }

    /**
     * Return columns
     * @return array
     */
    public function columns(): array
    {
        return $this->columns;
    }

    /**
     * Return HTML form filters
     * @return array
     */
    public function filter(): array
    {
        return $this->filter;
    }

    public function paginate()
    {
        return $this->all()
            ? ''
            : $this->items->withQueryString()->links('cms::partials.pagination');
    }

    /**
     * Return data
     * @return array
     */
    public function data(): array
    {
        return $this->data;
    }

    public function urlCreate(): string
    {
        return route('cms.module.create', ['controller' => $this->section->folder]);
    }

    public function urlEdit(int $id): string
    {
        return route('cms.module.edit', ['controller' => $this->section->folder, 'objectId' => $id]);
    }

    public function urlDelete(int $id): string
    {
        return route('cms.module.destroy', ['controller' => $this->section->folder, 'objectId' => $id]);
    }

    public function isAllowedAdd(): bool
    {
        return $this->getOption('add', false);
    }

    public function isAllowedDelete(): bool
    {
        return $this->getOption('delete', false);
    }

    public function sortable(): bool
    {
        return $this->getOption('sortable', false);
    }

    public function multipleDelete()
    {
        return $this->getOption('multiple-delete', false);
    }

    public function all(): bool
    {
        return $this->getOption('all', false);
    }

    public function linkAll(): string
    {
        $url = request()->url();
        $query = request()->query();

        $query['all'] = 'true';

        return $url . (empty($query) ? '' : '?' . http_build_query($query));
    }

    public function linkRemoveAll(): string
    {
        $url = request()->url();
        $query = request()->query();

        if (Arr::has($query, 'all')) {
            unset($query['all']);
        }

        return $url . (empty($query) ? '' : '?' . http_build_query($query));
    }
}
