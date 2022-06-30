@extends('cms::layouts.app')

@section('content')
    @if ($data)
        <div class="row">
            @foreach ($data as $controller => $sectionData)
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header border-transparent">
                            <h3 class="card-title">{{ $sectionData['title'] }}</h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table m-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Название</th>
                                            <th>Дата создания</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sectionData['list'] as $item)
                                            <tr>
                                                <td>{{ $item->getKey() }}</td>
                                                <td>
                                                    <a href="{{ route('cms.module.edit', ['controller' => $controller, 'objectId' => $item->getKey()], false) }}">{{ $item->name }}</a>
                                                </td>
                                                <td>{{ $item->created_at}}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer clearfix">
                            <a href="{{ route('cms.module.create', ['controller' => $controller], false) }}" class="btn btn-sm btn-success float-left"><span class="fa fa-plus"></span></a>
                            <a href="{{ route('cms.module.index', ['controller' => $controller], false) }}" class="btn btn-sm btn-secondary float-right"><span class="fa fa-list-ul"></span></a>
                        </div>
                        <!-- /.card-footer -->
                    </div>
                    <!-- /.card -->
                </div>
            @endforeach
        </div>
    @endif
@endsection
