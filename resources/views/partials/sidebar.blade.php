<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ config('app.url') }}" target="_blank" class="brand-link text-center">
        <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <span class="fa fa-user" style="font-size: 32px; color: #aaa;"></span>
            </div>
            <div class="info">
                <a href="{{ route('cms.module.edit', ['controller' => 'users', 'objectId' => Auth::id()], false) }}" class="d-block float-left">{{ $user->name }}</a>
                <a href="{{ route('cms.logout', [], false) }}" title="logout">
                    <i class="fas fa-sign-out-alt ml-3"></i>
                </a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <!--div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div-->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('cms.dashboard', [], false) }}" class="nav-link{{ request()->path() == config('cms.url_prefix') ? ' active' : '' }}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>{{ __("Go Home") }}</p>
                    </a>
                </li>
                @if (isset($navigation))
                    @foreach ($navigation as $group)
                        <li class="nav-item{{ $group['active'] ? ' menu-open' : '' }}">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-list-ul"></i>
                                <p>
                                    {{ $group['name'] }}
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>

                            <ul class="nav nav-treeview">
                                @foreach ($group['sections'] as $section)
                                    <li class="nav-item">
                                        <a href="{{ $section['url'] }}" class="nav-link{{ $section['active'] ? ' active' : '' }}">
                                            <i class="nav-icon fas fa-table"></i>
                                            <p>{{ $section['name'] }}</p>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                @endif
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
