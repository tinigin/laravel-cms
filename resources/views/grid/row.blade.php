<tr>
	@foreach ($grid->columns() as $key => $options)
		<td class="{{ $key == 'id' ? 'text-gray-dark text-bold' : '' }}">
			@if ($options['type'] == 'boolean')
				@if ($row[$key])
					<span class="badge bg-success">Да</span>
				@else
					<span class="badge bg-danger">Нет</span>
				@endif
			@else
				{{ $row[$key] }}
			@endif
		</td>
	@endforeach
    @if ($grid->sortable() || $grid->isAllowedEdit() || $grid->isAllowedView() || $grid->isAllowedDelete())
        <td class="text-nowrap">
            @if ($row['attributes']['is_approved'])
                <a href="{{ $grid->urlView($row['attributes']['id']) }}"><span class="far fa-eye text-primary"></span></a>
            @else
                @if ($grid->sortable())
                    <input type="hidden" name="sort-order[]" value="{{ $row['attributes']['id'] }}" />
                @endif

                @if ($grid->isAllowedEdit())
                    <a href="{{ $grid->urlEdit($row['attributes']['id']) }}" title="Редактировать" data-title="Редактирование"><span class="fa fa-edit text-primary"></span></a>
                @endif

                @if ($grid->isAllowedView())
                    <a href="{{ $grid->urlView($row['attributes']['id']) }}"><span class="far fa-eye text-primary"></span></a>
                @endif

                @if ($grid->isAllowedDelete())
                    <a href="{{ $grid->urlDelete($row['attributes']['id']) }}" confirm="true">
                        <button type="button" class="border-0 p-0 bg-transparent">
                            <span class="ml-3 fa fa-trash text-danger"></span>
                        </button>
                    </a>
                @endif
            @endif
        </td>
    @endif
	@if ($grid->multipleDelete())
	    <td class="centered">
            <div class="custom-control custom-checkbox">
                <input
                    id="item-to-delete-{{$row['attributes']['id']}}"
                    class="custom-control-input multiple-delete"
                    type="checkbox"
                    name="items-to-delete[]"
                    value="{{ $row['attributes']['id'] }}"
                />
                <label for="item-to-delete-{{$row['attributes']['id']}}" class="custom-control-label"> </label>
            </div>
		</td>
	@endif
</tr>
