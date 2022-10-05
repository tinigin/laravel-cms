<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ isset($title) ? $title : '' }}</h1>
            </div>
            @if (isset($navigation))
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('cms.dashboard', [], false) }}">{{ __("Go Home") }}</a></li>
                        @foreach ($navigation as $group)
                            @if (isset($group['sections']))
                                @foreach ($group['sections'] as $section)
                                        @if ($section['current'])
                                            <li class="breadcrumb-item{{ $section['current'] ? ' active' : '' }}">
                                                {{ $section['name'] }}
                                            </li>
                                        @elseif($section['active'])
                                            <li class="breadcrumb-item{{ $section['current'] ? ' active' : '' }}">
                                                <a href="{{ $section['url'] }}">{{ $section['name'] }}</a>
                                            </li>
                                        @endif
                                    </li>
                                @endforeach
                            @endif
                        @endforeach
                    </ol>
                </div>
            @endif
        </div>
    </div><!-- /.container-fluid -->
</section>
