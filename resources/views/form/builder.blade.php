@if ($view == 'card')
	<form class="form-horizontal" action="{{ $action }}" method="post" enctype="multipart/form-data">
        @csrf
        @if ($method)
            @method($method)
        @endif
		@if ($tabs)
    		<div class="card-header p-2">
    			<ul class="nav nav-pills">
    				@foreach ($tabs as $tab)
    					{!! $tab->renderNavItem() !!}
    				@endforeach
    			</ul>
    		</div>
		@endif
		<div class="card-body">
			@if ($tabs)
				<div class="tab-content">
					@foreach ($tabs as $tab)
						{!! $tab !!}
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
