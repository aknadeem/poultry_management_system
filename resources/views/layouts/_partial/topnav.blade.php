<div class="topnav">
    <div class="container-fluid">
        <nav class="navbar navbar-light navbar-expand-lg topnav-menu">

            <div class="collapse navbar-collapse" id="topnav-menu-content">
                <ul class="navbar-nav">
                    <li class="nav-item ">
                        <a class="nav-link" href="{{ url('/') }}" id="topnav-dashboard">
                            <i class="fas fa-home me-1"></i> Dashboards
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link" href="#" id="topnav-dashboard">
                            <i class="fas fa-warehouse me-1"></i> Sheds
                        </a>
                    </li>

                    <li class="nav-item ">
                        <a class="nav-link" href="#" id="topnav-dashboard">
                            <i class="fas fa-id-card me-1"></i> Employee
                        </a>
                    </li>

                    <li class="nav-item ">
                        <a class="nav-link" href="{{ route('customer.index') }}">
                            <i class="fas fa-users me-1"></i> Customers
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link" href="#" id="topnav-dashboard">
                            <i class="fas fa-hotel me-1"></i> Companies
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-components" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-layer-group me-1"></i> Inventory Management <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-components">
                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-form"
                                    role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fe-bookmark me-1"></i> Chickens <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-form">
                                    <a href="#" class="dropdown-item">Sales</a>
                                    <a href="#" class="dropdown-item">Purchase</a>
                                    <a href="#l" class="dropdown-item">Stock</a>
                                </div>
                            </div>

                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-form"
                                    role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fe-bookmark me-1"></i> Feed <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-form">
                                    <a href="#" class="dropdown-item">Sales</a>
                                    <a href="#" class="dropdown-item">Purchase</a>
                                    <a href="#l" class="dropdown-item">Consumption</a>
                                </div>
                            </div>

                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-form"
                                    role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fe-bookmark me-1"></i> Forms <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-form">
                                    <a href="forms-elements.html" class="dropdown-item">General Elements</a>
                                    <a href="forms-advanced.html" class="dropdown-item">Advanced</a>
                                    <a href="forms-validation.html" class="dropdown-item">Validation</a>
                                    <a href="forms-pickers.html" class="dropdown-item">Pickers</a>
                                    <a href="forms-wizard.html" class="dropdown-item">Wizard</a>
                                    <a href="forms-masks.html" class="dropdown-item">Masks</a>
                                    <a href="forms-quilljs.html" class="dropdown-item">Quilljs Editor</a>
                                    <a href="forms-file-uploads.html" class="dropdown-item">File Uploads</a>
                                    <a href="forms-x-editable.html" class="dropdown-item">X Editable</a>
                                    <a href="forms-image-crop.html" class="dropdown-item">Image Crop</a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-charts"
                                    role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fe-bar-chart-2 me-1"></i> Charts <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-charts">
                                    <a href="charts-apex.html" class="dropdown-item">Apex Charts</a>
                                    <a href="charts-flot.html" class="dropdown-item">Flot Charts</a>
                                    <a href="charts-morris.html" class="dropdown-item">Morris Charts</a>
                                    <a href="charts-chartjs.html" class="dropdown-item">Chartjs Charts</a>
                                    <a href="charts-peity.html" class="dropdown-item">Peity Charts</a>
                                    <a href="charts-chartist.html" class="dropdown-item">Chartist Charts</a>
                                    <a href="charts-c3.html" class="dropdown-item">C3 Charts</a>
                                    <a href="charts-sparklines.html" class="dropdown-item">Sparklines Charts</a>
                                    <a href="charts-knob.html" class="dropdown-item">Jquery Knob Charts</a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-table"
                                    role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fe-grid me-1"></i> Tables <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-table">
                                    <a href="tables-basic.html" class="dropdown-item">Basic Tables</a>
                                    <a href="tables-datatables.html" class="dropdown-item">Data Tables</a>
                                    <a href="tables-editable.html" class="dropdown-item">Editable Tables</a>
                                    <a href="tables-responsive.html" class="dropdown-item">Responsive Tables</a>
                                    <a href="tables-footables.html" class="dropdown-item">FooTable</a>
                                    <a href="tables-bootstrap.html" class="dropdown-item">Bootstrap Tables</a>
                                    <a href="tables-tablesaw.html" class="dropdown-item">Tablesaw Tables</a>
                                    <a href="tables-jsgrid.html" class="dropdown-item">JsGrid Tables</a>
                                </div>
                            </div>
                        </div>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-components" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-file-alt me-1"></i> Reports <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-components">
                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-form"
                                    role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fe-bookmark me-1"></i> Chickens <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-form">
                                    <a href="#" class="dropdown-item">Daily</a>
                                    <a href="#" class="dropdown-item">Monthly</a>
                                    <a href="#l" class="dropdown-item">Yearly</a>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul> <!-- end navbar-->
            </div> <!-- end .collapsed-->
        </nav>
    </div> <!-- end container-fluid -->
</div>
<!-- end topnav-->