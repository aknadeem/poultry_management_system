<div class="left-side-menu">
    <div class="h-100" data-simplebar>
        <!-- User box -->
        <div class="user-box text-center">
            <img src="{{ asset('assets/images/logo/poultryLogo.png') }}" alt="user-image"
                class="rounded avatar-lg">
            <div class="dropdown">
                <a href="javascript: void(0);"
                    class="text-dark font-weight-normal dropdown-toggle h5 mt-2 mb-1 d-block"
                    data-bs-toggle="dropdown">Nadeem Ahmed</a>
                <div class="dropdown-menu user-pro-dropdown">
                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="fe-user mr-1"></i>
                        <span>My Account</span>
                    </a>
                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="fe-settings mr-1"></i>
                        <span>Settings</span>
                    </a>
                    <!-- item-->
                    <a href="javascript:void(0);" class="btn dropdown-item " data-bs-toggle="modal"
                        data-bs-target="#danger-alert-modal">
                        <i class="fe-log-out mr-1"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
            <p class="text-muted">Software Engineer</p>
        </div>
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <ul id="side-menu">
                <li class="menu-title">Navigation</li>
                <li>
                    <a href="#">
                        <i data-feather="airplay"></i>
                        <span> Dashboards </span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-university"></i>
                        <span> Branches </span>
                    </a>
                </li>
                <li>
                    <a href="#sidebarClassesSections" data-bs-toggle="collapse">
                        <i class="fas fa-th"></i>
                        <span> Classes & Sections </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarClassesSections">
                        <ul class="nav-second-level">
                            <li>
                                <a href="#">Classes & Sections</a>
                            </li>
                            <li>
                                <a href="#">Classes & Teachers</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-user-friends"></i>
                        <span> Students </span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- End Sidebar -->
        <div class="clearfix"></div>
    </div>
    <!-- Sidebar -left -->
</div>