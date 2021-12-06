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

                    <li class="nav-item ">
                        <a class="nav-link" href="{{ route('employee.index') }}" id="topnav-dashboard">
                            <i class="fas fa-id-card"></i> Employee
                        </a>
                    </li>

                    <li class="nav-item ">
                        <a class="nav-link" href="{{ route('customer.index') }}">
                            <i class="fas fa-users"></i> Customers
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-components" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-hotel"></i> Companies <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-components">
                            <div class="dropdown">
                                <a class="dropdown-item arrow-none" href="{{ route('company.index') }}" id="topnav-form"
                                    role="button">
                                    <i class="fe-bookmark"></i> Companies
                                </a>
                                <a class="dropdown-item arrow-none" href="{{ route('companybalance.index') }}"
                                    id="topnav-form">
                                    <i class="fe-bookmark"></i> Companies Balance
                                </a>
                            </div>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('expense.index') }}" id="topnav-dashboard">
                            <i class="fas fa-hotel"></i> Expenses
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-components" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-layer-group"></i> Inventory <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-components">
                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-form"
                                    role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fe-bookmark"></i> Chickens <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-form">
                                    <a href="{{ route('sale.index') }}" class="dropdown-item">Sales</a>
                                    <a href="{{ route('purchase.index') }}" class="dropdown-item">Purchase</a>
                                    {{-- <a href="#l" class="dropdown-item">Stock</a> --}}
                                </div>
                            </div>

                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-form"
                                    role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fe-bookmark"></i> Feed <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-form">
                                    {{-- <a href="#" class="dropdown-item">Sales</a> --}}
                                    <a href="{{ route('feed.index') }}" class="dropdown-item">Purchase</a>
                                    {{-- <a href="#" class="dropdown-item">Consumption</a> --}}
                                </div>
                            </div>
                        </div>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-components" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-file-alt"></i> Reports <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-components">
                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-form"
                                    role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fe-bookmark"></i> Chickens <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-form">
                                    <a href="#" class="dropdown-item">Daily</a>
                                    <a href="#" class="dropdown-item">Monthly</a>
                                    <a href="#l" class="dropdown-item">Yearly</a>
                                </div>
                            </div>
                        </div>
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