@extends('cms::layouts.app')

@section('content')
	@if (isset($grid))
		<x-cms-grid-table :grid="$grid" />
    @elseif (isset($tree))
        <div class="col-12">
            <div class="card pb-2 pt-2" style="flex-direction: row; justify-content: space-between;">
                @include('cms::partials.tree', [
                    'controller' => $controller,
                    'type' => $type,
                    'tree' => $tree,
                    'url' => $url,
                    'type' => $type
                ])

                <div>
                    <a href="{{ route('cms.module.create', ['controller' => $controller]) }}" class="btn btn-sm btn-success mr-3 mt-2 mb-2 float-left">
                        <span class="fa fa-plus"></span>
                    </a>
                </div>
            </div>
        </div>
	@elseif (isset($form))
		<div class="col-12">
            @if (isset($description) && $description)
                <div class="callout callout-warning">
                    {!! $description !!}
                </div>
            @endif

            @include('cms::partials.alert')

			<div class="card">
    			{!! $form !!}
    		</div>
		</div>
	@endif
@endsection
