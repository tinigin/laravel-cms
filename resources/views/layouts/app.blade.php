@spaceless
@include('cms::partials.header')

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        @include('cms::partials.navbar')
        @include('cms::partials.sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            @include('cms::partials.content-header')

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>

        @include('cms::partials.copyright')
        @include('cms::partials.control-sidebar')

    </div>
    <!-- ./wrapper -->

    @include('cms::partials.footer')
    @include('cms::partials.toast')
</body>
</html>
@endspaceless
