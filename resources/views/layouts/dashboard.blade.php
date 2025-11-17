<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable">

<head>
    @include('partials.dashboard.head')
    @stack('styles')
</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

        @include('partials.dashboard.topbar')

        <!-- ========== App Menu ========== -->
        @include('partials.dashboard.navbar')
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>

        <div class="main-content">

            <div class="page-content">
                @yield('content')
            </div>
            <!-- End Page-content -->
            @include('partials.dashboard.footer')
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->
    @stack('modals')
    @include('partials.dashboard.settings')
    @include('partials.dashboard.scripts')
    @stack('scripts')
</body>

</html>
