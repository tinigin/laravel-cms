@if ($view == 'card')
	<form
        class="form-horizontal"
        action="{{ $action }}"
        method="post"
        enctype="multipart/form-data"
        data-images-url="{{ $images ? $images : '' }}"
    >
        @csrf
        @if ($method)
            @method($method)
        @endif
		@if ($groups)
    		<div class="card-header p-2">
    			<ul class="nav nav-pills">
    				@foreach ($groups as $key => $group)
                        <li class="nav-item">
                            <a class="nav-link {{ $group['active'] ? 'active' : ''}}" href="#{{$key}}" data-tab-id="{{$key}}" data-toggle="tab">{{$group['title']}}</a>
                        </li>
    				@endforeach
    			</ul>
    		</div>
		@endif
		<div class="card-body">
			@if ($groups)
				<div class="tab-content">
					@foreach ($groups as $key => $group)
                        <div class="tab-pane {{$group['active'] ? 'active' : ''}}" id="{{$key}}">
                            @foreach($fields[$key] as $field)
                                {!! $field !!}
                            @endforeach
                        </div>
					@endforeach
				</div>
			@elseif ($fields)
				@foreach ($fields as $field)
					{!! $field !!}
				@endforeach
			@endif
		</div>
		@if ($buttons)
    		<div class="card-footer">
                @foreach ($buttons as $button)
                    {!! $button !!}
                @endforeach
    		</div>
		@endif
	</form>
@endif
