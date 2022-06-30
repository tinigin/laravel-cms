@extends('cms::layouts.app')

@section('content')
	@if (isset($grid))
		<x-cms-grid-table :grid="$grid" />
	@elseif (isset($form))
		<div class="col-12">
			<div class="card">
                @include('cms::partials.alert')

    			{!! $form !!}
    		</div>
		</div>
	@endif
@endsection
