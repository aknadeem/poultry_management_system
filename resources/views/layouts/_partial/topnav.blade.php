<div class="topnav">
    <div class="container-fluid">
        <nav class="navbar navbar-light navbar-expand-lg topnav-menu">
            <style>
                .nav-link {
                    padding-right: 0px !important;
                }
            </style>
            <div class="collapse navbar-collapse" id="topnav-menu-content">
                <ul class="navbar-nav">
                    <li class="nav-item ">
                        <a class="nav-link pr-1" href="{{ url('/') }}" id="topnav-dashboard">
                            <i class="fas fa-home"></i> Dashboards
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-components" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-users-cog"></i> Party Management <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-components">
                            <div class="dropdown">
                                <a class="dropdown-item arrow-none" href="{{ route('parties.index') }}" id="topnav-form"
                                    role="button">
                                    <b> <i class="fas fa-users"></i> Parties </b>
                                </a>
                            </div>
                            <div class="dropdown">
                                <a class="dropdown-item" id="topnav-form" href="{{ route('customers.index') }}">
                                    <i class="fas fa-user-tie"></i> Customers
                                </a>
                            </div>
                            <div class="dropdown">
                                <a class="dropdown-item" id="topnav-form" href="{{ route('vendors.index') }}">
                                    <i class="fas fa-address-card"></i> Vendors
                                </a>
                            </div>
                            <div class="dropdown">
                                <a class="dropdown-item" id="topnav-form" href="{{ route('conductpersons.index') }}">
                                    <i class="fas fa-user-friends"></i> Contact Persons
                                </a>
                            </div>

                            <div class="dropdown">
                                <a class="dropdown-item" id="topnav-form" href="{{ route('brokers.index') }}">
                                    <i class="fas fa-money-check-alt"></i> Brokers
                                </a>
                            </div>

                            <div class="dropdown">
                                <a class="dropdown-item" id="topnav-form" href="{{ route('partybalance.index') }}">
                                    <i class="fas fa-dollar"></i> Party Balance
                                </a>
                            </div>

                            <div class="dropdown">
                                <a class="dropdown-item" id="topnav-form" href="{{ route('brokerbalance.index') }}">
                                    <i class="fas fa-money-check-alt"></i> Broker Balance
                                </a>
                            </div>

                        </div>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-components" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-pallet"></i> FarmManagement <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-components">
                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-form"
                                    role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-warehouse"></i> Farms <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-form">
                                    {{-- <a href="{{ route('personalfarms.index') }}" class="dropdown-item"> Personal
                                        Farms
                                    </a> --}}
                                    <a href="{{ route('customerfarms.index') }}" class="dropdown-item"> Customer Farms
                                    </a>
                                </div>
                            </div>

                            <div class="dropdown">
                                <a class="dropdown-item" href="{{ route('productstores.index') }}" id="topnav-form">
                                    <i class="fas fa-store-alt"></i> Store Mangement
                                </a>
                            </div>

                            <div class="dropdown">
                                <a class="dropdown-item" href="{{ route('employee.index') }}" id="topnav-form">
                                    <i class="fas fa-id-card"></i> Employee
                                </a>
                            </div>
                            <div class="dropdown">
                                <a class="dropdown-item" href="{{ route('products.index') }}" id="topnav-form">
                                    <i class="fas fa-store"></i> Products
                                </a>
                            </div>
                            <div class="dropdown">
                                <a class="dropdown-item" href="{{ route('productpurchases.index') }}" id="topnav-form">
                                    <i class="fas fa-shopping-bag"></i> Product Purchases
                                </a>
                            </div>

                            <div class="dropdown">
                                <a class="dropdown-item" href="{{ route('productsales.index') }}" id="topnav-form">
                                    <i class="fas fa-shopping-bag"></i> Product Sales
                                </a>
                            </div>

                            <div class="dropdown">
                                <a class="dropdown-item" href="{{ route('vaccination.index') }}" id="topnav-form">
                                    <i class="fas fa-syringe"></i> Vaccination
                                </a>
                            </div>
                        </div>
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
                                    <i class="fas fa-list-ul"></i> Chicks<div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-form">
                                    <a href="{{ route('sale.index') }}" class="dropdown-item"><i
                                            class="fas fa-paper-plane"></i> Sales</a>
                                    <a href="{{ route('purchase.index') }}" class="dropdown-item"><i
                                            class="fas fa-credit-card"></i> Purchase</a>
                                    {{-- <a href="#l" class="dropdown-item">Stock</a> --}}
                                </div>
                            </div>

                            <div class="dropdown">
                                <a class="dropdown-item" href="{{ route('feed.index') }}" id="topnav-form"
                                    role="button">
                                    <i class="fas fa-braille"> </i> Feed
                                    {{-- <div class="arrow-down"></div> --}}
                                </a>
                                {{-- <div class="dropdown-menu" aria-labelledby="topnav-form">
                                    <a href="#" class="dropdown-item">Sales</a>
                                    <a href="{{ route('feed.index') }}" class="dropdown-item"><i
                                            class="fas fa-credit-card"></i> Purchase</a>
                                </div> --}}
                            </div>

                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-form"
                                    role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-list"> </i> Payments
                                    <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-form">
                                    {{-- <a href="#" class="dropdown-item">Sales</a> --}}
                                    <a href="{{ route('payables.index') }}" class="dropdown-item"><i
                                            class="fas fa-credit-card"></i> Account Payable </a>
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
                                    <i class="fe-bookmark"></i> Chicks <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-form">
                                    <a href="{{ route('chickreport.index') }}" class="dropdown-item">Sale</a>
                                    <a href="{{ route('chickreport.purchases') }}" class="dropdown-item">Purchase</a>
                                    {{-- <a href="#l" class="dropdown-item">Yearly</a> --}}
                                </div>
                            </div>

                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-form"
                                    role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fe-file"></i> Products <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-form">
                                    <a href="{{ route('productreport.purchase') }}" class="dropdown-item">Product
                                        Purchase</a>
                                    <a href="{{ route('productreport.sale') }}" class="dropdown-item">Product
                                        Sale</a>
                                    <a href="{{ route('productreport.index') }}" class="dropdown-item">Products</a>
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
                                <a class="dropdown-item arrow-none" href="{{ route('userlevel.index') }}"
                                    id="topnav-form" role="button">
                                    <i class="fa fa-users"></i> UserLevels
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