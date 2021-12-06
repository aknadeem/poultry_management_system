<div class="topnav">
    <div class="container-fluid">
        <nav class="navbar navbar-light navbar-expand-lg topnav-menu">

            <div class="collapse navbar-collapse" id="topnav-menu-content">
                <ul class="navbar-nav">
                    <li class="nav-item ">
                        <a class="nav-link" href="{{ url('/') }}" id="topnav-dashboard">
                            <i class="fas fa-home"></i> Dashboards
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link" href="#" id="topnav-dashboard">
                            <i class="fas fa-warehouse"></i> Sheds
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-components" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-layer-group"></i> User Management <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-components">
                            <div class="dropdown">
                                <a class="dropdown-item arrow-none" href="{{ route('users.index') }}" id="topnav-form"
                                    role="button">
                                    <i class="fa fa-users"></i> Users
                                </a>
                            </div>

                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-form"
                                    role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fe-bookmark"></i> UserGroup <div class="arrow-down"></div>
                                </a>
                            </div>
                        </div>
                    </li>




                </ul> <!-- end navbar-->
            </div> <!-- end .collapsed-->
        </nav>
    </div> <!-- end container-fluid -->
</div>
<!-- end topnav-->