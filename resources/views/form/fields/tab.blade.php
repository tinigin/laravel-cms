<div class="tab-pane {{$active ? 'active' : ''}}" id="{{$name}}">
    @foreach($fields as $field)
        {!! $field !!}
    @endforeach
</div>