<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="index.html" class="logo logo-dark">
            <span class="logo-sm">
                <img src="assets/images/logo-sm.png" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="assets/images/logo-dark.png" alt="" height="17">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="index.html" class="logo logo-light">
            <span class="logo-sm">
                <img src="assets/images/logo-sm.png" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="assets/images/logo-light.png" alt="" height="17">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">Menu</span></li>

                @can('home')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="ri-honour-line"></i> <span data-key="t-widgets">Home</span>
                        </a>
                    </li>
                @endcan

                @can('kategori')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('kategori') ? 'active' : '' }}"
                            href="{{ route('kategori') }}">
                            <i class="ri-honour-line"></i> <span data-key="t-widgets">Kategori</span>
                        </a>
                    </li>
                @endcan

                @can('jenis')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('jenis') ? 'active' : '' }}" href="{{ route('jenis') }}">
                            <i class="ri-honour-line"></i> <span data-key="t-widgets">Jenis</span>
                        </a>
                    </li>
                @endcan

            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
