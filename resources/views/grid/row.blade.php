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
	<td class="text-nowrap">
		@if ($grid->sortable())
			<input type="hidden" name="sort-order[]" value="{{ $row['attributes']['id'] }}" />
		@endif
		<a href="{{ $grid->urlEdit($row['attributes']['id']) }}"><span class="fa fa-edit text-primary"></span></a>

		@if ($grid->isAllowedDelete())
            <a href="{{ $grid->urlDelete($row['attributes']['id']) }}" confirm="true">
                <button type="button" class="border-0 p-0 bg-transparent">
                    <span class="ml-3 fa fa-trash text-danger"></span>
                </button>
            </a>
		@endif
	</td>
	@if ($grid->multipleDelete())
	<td class="centered">
			<input type="checkbox" name="items-to-delete[]" class="multiple-delete" value="{{ $row['attributes']['id'] }}" />
		</td>
	@endif
</tr>
