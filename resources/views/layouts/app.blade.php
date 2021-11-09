<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PoultryManagementSystem') }}</title>

    <meta content="Nadeem" name="Nadeem Ahmed" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/favicon/apple-touch-icon.png')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicon/favicon-32x32.png')}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/favicon/favicon-16x16.png')}}">
    <link rel="mask-icon" href="{{ asset('assets/favicon/safari-pinned-tab.svg')}}" color="#a0390e">
    <meta name="msapplication-TileColor" content="#8d0e0e">
    <meta name="theme-color" content="#ffffff">
    <!-- App css -->
    <?php if(in_array('sweetAlert',$load_css)) { ?>
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <?php } ?>

    <?php if(in_array('jquery-confirm',$load_css)) { ?>
    <link href="{{ asset('assets/js/jquery-confirm/jquery-confirm.min.css') }}" rel="stylesheet" type="text/css" />
    <?php } ?>

    @include('layouts._partial.header')

</head>

<body class="loading" data-layout-mode="horizontal"
    data-layout='{"mode": "light", "width": "fluid", "menuPosition": "fixed", "sidebar": { "color": "dark", "size": "default", "showuser": true}, "topbar": {"color": "dark"}, "showRightSidebarOnPageLoad": false}'>
    <!-- Begin page -->
    <div id="wrapper">
        <!-- top bar st -->
        @include('layouts._partial.topbar')
        <!-- top bar end -->
        <div id="danger-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content modal-filled bg-danger">
                    <div class="modal-body p-4">
                        <div class="text-center">
                            <i class="dripicons-exit h1 text-white"></i>
                            <h4 class="mt-2 text-white">Please Confirm!</h4>
                            <p class="mt-3 text-white">Are you sure, you want to log out?</p>
                            <a href="logout" class="btn btn-dark my-2">Yes</a>
                            <button type="button" class="btn btn-light my-2" data-bs-dismiss="modal">No</button>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <!-- Left Side Menu -->
        @include('layouts._partial.topnav')
        <!-- Left Side Menu End -->

        <div class="content-page">
            <div class="content">
                @yield('content')
            </div>

            <!-- Footer Start -->
            @include('layouts._partial.footer')
            <!-- end Footer -->
        </div>
        <!-- ============================================= -->
        <!-- End Page content -->
        <!-- ========================================= -->
    </div>
    <!-- END wrapper -->
    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>
    <!-- App js -->
    <!-- Vendor js -->
    <?php if(in_array('tippy',$load_js)){ ?>
    <script src="{{ asset('assets/libs/tippy.js/tippy.all.min.js') }}"></script>
    <?php } ?>
    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('assets/libs/selectize/js/standalone/selectize.min.js') }}"></script>
    <!-- Plugins js-->
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <!-- Dashboar 1 init js-->
    <script src="{{ asset('assets/js/pages/dashboard-1.init.js') }}"></script>
    <!-- App js-->

    <?php if(in_array('sweetAlert',$load_js)){ ?>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <?php }?>
    <?php 
        if(in_array('tables',$load_js)){
    ?>
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
    <?php } ?>

    <?php if(in_array('parsley',$load_js)){ ?>
    <script src="{{ asset('assets/libs/parsleyjs/parsley.min.js') }}"></script>
    <!-- Validation init js-->
    <?php } ?>

    <?php if(in_array('jquery-confirm',$load_js)){ ?>
    <script src="{{ asset('assets/js/jquery-confirm/jquery-confirm.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <?php } ?>
    <script src="{{ asset('assets/js/app.min.js') }}"></script>

    @yield('custom_scripts')

</body>

</html>