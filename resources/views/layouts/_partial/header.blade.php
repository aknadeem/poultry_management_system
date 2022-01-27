<!-- Plugins css -->
<link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/selectize/css/selectize.bootstrap3.css')}}" rel="stylesheet" type="text/css" />

<link href="{{ asset('assets/css/config/creative/bootstrap.min.css')}}" rel="stylesheet" type="text/css"
    id="bs-default-stylesheet" />

<link href="{{ asset('assets/css/config/creative/app.min.css')}}" rel="stylesheet" type="text/css"
    id="app-default-stylesheet" />

<link href="{{ asset('assets/css/config/creative/bootstrap-dark.min.css')}}" rel="stylesheet" type="text/css"
    id="bs-dark-stylesheet" />
<link href="{{ asset('assets/css/config/creative/app-dark.min.css')}}" rel="stylesheet" type="text/css"
    id="app-dark-stylesheet" />
<link href="{{ asset('assets/libs/c3/c3.min.css')}}" rel="stylesheet" type="text/css" />
<!-- icons -->
<link href="{{ asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />

<?php if(in_array('tables',$load_css)) { ?>

{{--
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" /> --}}

<link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/libs/datatables.net-select-bs4/css/select.bootstrap4.min.css') }}" rel="stylesheet"
    type="text/css" />

<?php } ?>