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
    <style>
        .bg-light-success {
            background-color: #c9f7f5 !important;
        }

        #pageloader {
            background: rgba(255, 255, 255, 0.8);
            display: none;
            height: 100%;
            position: fixed;
            width: 100%;
            z-index: 9999;
        }

        #pageloader img {
            left: 50% !important;
            position: absolute;
            top: 50% !important;
        }
    </style>

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

    <form method="post" id="delete-form">
        @method('DELETE')
        @csrf
    </form>

    <!-- App js -->
    <!-- Vendor js -->
    <?php if(in_array('tippy',$load_js)){ ?>
    <script src="{{ asset('assets/libs/tippy.js/tippy.all.min.js') }}"></script>
    <?php } ?>
    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('assets/libs/selectize/js/standalone/selectize.min.js') }}"></script>
    <!-- Plugins js-->

    <?php if(in_array('apexChart',$load_js)){ ?>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <?php }?>

    <?php if(in_array('dashboard',$load_js)){ ?>
    <!-- Dashboar 1 init js-->
    <script src="{{ asset('assets/js/pages/dashboard-1.init.js') }}"></script>

    <?php }?>



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

    @yield('modal_scripts')
    @yield('custom_scripts')


    <script>
        $(".confirm-logoutform").click(function (event) {
            /* Act on the event */
            event.preventDefault();
            var action = $(this).attr("href");
            var del_title = $(this).attr("del_title");

            $.confirm({
                columnClass: "col-md-5",
                autoClose: false,
                theme: "modern",
                title: "Confirm Please?",
                content:
                    " Are You Sure You Want to Delete <br> <b>" + del_title + " </b>?",
                type: "dark",
                typeAnimated: true,
                draggable: false,
                buttons: {
                    ok: {
                        useBootstrap: false,
                        text: "Yes",
                        btnClass: "btn-danger",
                        keys: ["enter"],
                        action: function () {
                            // var action = event.$target.attr('href');
                            $("form#logout-form").attr("action", action);
                            $("form#logout-form").submit();
                            // alert('heelo');
                            console.log("the user clicked confirm");
                        },
                    },
                    cancel: {
                        text: "Cancel",
                        keys: ["esc"],
                        cancel: function () {
                            console.log("the user clicked cancel");
                        },
                    },
                },
            });
        });

        $("#pageloader").fadeIn();

        $(document).ready(function () {
            $(".form_loader").on("submit", function () {
                $("#pageloader").fadeIn();
            });
        });

        //Reset input file in modal
        $('input[type="file"][name="image_file"]').val('');
            //Image preview on upload time
            $('input[type="file"][name="image_file"]').on('change', function(){
                var img_path = $(this)[0].value;
                var img_holder = $('.img-holder');
                var extension = img_path.substring(img_path.lastIndexOf('.')+1).toLowerCase();
                // alert(extension);
                if(extension == 'jpeg' || extension == 'jpg' || extension == 'png'){
                        if(typeof(FileReader) != 'undefined'){
                            img_holder.empty();
                            var reader = new FileReader();
                            reader.onload = function(e){
                                $('<img/>', {'src':e.target.result,'class':'','style':'max-width:20%;margin-bottom:1px;'}).appendTo(img_holder);
                            }
                            img_holder.show();
                            reader.readAsDataURL($(this)[0].files[0]);
                        }else{
                            $(img_holder).html('This browser does not support FileReader');
                        }
                }else{
                    $(img_holder).empty();
                }
            });

    </script>

</body>

</html>