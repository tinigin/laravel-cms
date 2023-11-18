<tr>
	@foreach ($grid->columns() as $key => $options)
		<td class="{{ $key == 'id' ? 'text-gray-dark text-bold' : '' }}">
            @if ($loop->first)
                @if ($grid->sortable())
                    <input type="hidden" name="sort-order[]" value="{{ $row['attributes']['id'] }}" />
                @endif
            @endif

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

    @php ($buttons = $grid->getItemActions($row))

    @if ($buttons)
        <td class="text-nowrap">
            @foreach ($buttons as $button)
                <a
                    href="{{ $button['url'] }}"
                    title="{{ $button['title'] }}"
                    data-title="{{ $button['title'] }}"
                    @if (isset($button['confirm']) && $button['confirm']) confirm="true" @endif
                    class="mr-3"
                >
                    <span class="{{ $button['class'] }}"></span>
                </a>
            @endforeach
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
