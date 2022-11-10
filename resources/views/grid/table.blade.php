<div class="row">
	<div class="col-12">
		<div class="card">
            @if ($grid->filter())
                <div class="card-header">
                    <h3 class="card-title">
                        <a href="" data-toggle-filter>
                            <i class="fas fa-filter"></i>
                        </a>
                    </h3>

                    <form method="get" class="hidden" data-filter>
                        <input type="hidden" name="sort" value="{{ request()->get('sort') }}" />
                        <input type="hidden" name="all" value="{{ request()->get('all') }}" />

                        <div class="row">
                            @foreach ($grid->filter() as $key => $field)
                                <div class="col-sm-4">
                                    {!! $field !!}
                                </div>
                            @endforeach
                        </div>
                        <div class="btn-group filter-submit-buttons-container">
                            <button name="filter-submit" type="submit" class="btn btn-primary">Выбрать</button>
                            <button name="filter-reset" type="submit" class="btn btn-default">Сбросить</button>
                            <button name="filter-close" type="submit" class="btn btn-default">Закрыть</button>
                        </div>

                        @foreach ($grid->columns() as $key => $column)
                            @if (isset($column['active']))
                                <input type="hidden" name="sort" value="{{ $column['direction'] == 'desc' ? '-' : '' }}{{ $key }}" />
                            @endif
                        @endforeach
                    </form>
                </div>
                <!-- /.card-header -->
            @endif

			<div class="card-body table-responsive{{ $grid->data() ? ' p-0' : ''}}">
    			@if ($grid->data())
    				<table class="table table-hover{{ $grid->sortable() ? ' sortable-table' : '' }}">
    					<thead>
    						<tr>
    							@foreach ($grid->columns() as $key => $column)
    								<th class="text-gray-dark{{ isset($column['active']) && $column['active'] ? ' sortable ' . $column['direction'] : '' }}">
    									@if ($column['is-sortable'] && $column['type'] != 'multiple')
    										<a href="{{ $column['url'] }}" class="text-gray-dark">{{ $column['label'] }}</a>
    									@else
    										{{ $column['label'] }}
    									@endif
    								</th>
    							@endforeach
                                @if ($grid->sortable() || $grid->isAllowedEdit() || $grid->isAllowedView() || $grid->isAllowedDelete())
    							    <th class="text-gray-dark">Действие</th>
                                @endif
    							@if ($grid->multipleDelete())
    								<th class="centered">
                						<a href="" class="toggle-all-delete-checkbox">#</a>
                					</th>
    							@endif
    						</tr>
    					</thead>
    					<tbody>
    						@foreach ($grid->data() as $row)
    							@include('cms::grid.row')
    						@endforeach
    					</tbody>
    				</table>
    			@else
    				<h3>Данных не нейдено.</h3>
    			@endif
			</div>
            @if ($grid->sortable() || $grid->multipleDelete() || $grid->isAllowedAdd())
                <div class="card-footer clearfix">
                    @if ($grid->isAllowedAdd())
                        <a href="{{ $grid->urlCreate() }}" class="btn btn-sm btn-success float-left" title="Добавить" data-title="Добавление">
                            <span class="fa fa-plus"></span>
                        </a>
                    @endif

                    <form method="post" enctype="multipart/form-data" id="sort-form">
                        @csrf
                        @method('post')
                        <input type="hidden" name="items" value="" />
                        <input type="hidden" name="save-sorting" value="true" />
                    </form>

                    <form method="post" enctype="multipart/form-data" id="delete-form">
                        @csrf
                        @method('post')
                        <input type="hidden" name="items" value="" />
                        <input type="hidden" name="multiple-delete" value="true" />
                    </form>

                    @if (($grid->sortable() || $grid->multipleDelete()) && $grid->count() > 0)
                        <div class="btn-group-sm float-right" role="group">
                            @if ($grid->sortable())
                                @if ($grid->all())
                                    <a href="{{ $grid->linkRemoveAll() }}" class="btn btn-default btn-lg" tabindex="-1" role="button" aria-disabled="true">Постранично</a>
                                @else
                                    <a href="{{ $grid->linkAll() }}" class="btn btn-default btn-lg" tabindex="-1" role="button" aria-disabled="true">Показать все</a>
                                @endif

                                <button class="btn btn-default" type="submit" onclick="$('#sort-form').submit(); return false;" name="save-sorting" value="true" disabled="disabled">Сохранить</button>
                            @endif

                            @if ($grid->multipleDelete())
                                <button class="btn btn-danger" type="submit" confirm="#delete-form" name="delete-items" value="true" disabled="disabled">Удалить</button>
                            @endif
                        </div>
                    @endif

                    {!! $grid->paginate() !!}
                </div>
            @endif
		</div>
		<!-- /.card -->
	</div>
</div>
