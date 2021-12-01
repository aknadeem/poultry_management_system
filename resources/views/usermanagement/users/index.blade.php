@php
$load_css = Array('tables','sweetAlert', 'jquery-confirm');
$load_js = Array('tables','tippy','sweetAlert', 'jquery-confirm');
@endphp
@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item active">UserManagement</li>
                        <li class="breadcrumb-item active">User</li>
                    </ol>
                </div>
                <h4 class="page-title">Users</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4>Users</h4>
                        </div>
                        <div class="col-6 align-self-end text-end mb-2">
                            <a class="btn btn-secondary btn-sm openUserModal" href="javascript:void(0);" UserId="0"
                                title="Click to add new user" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-plus"></i>
                                User
                            </a>
                        </div>
                    </div>

                    <table id="User-Datatable" class="table table-striped dt-responsive w-100">
                        <thead>
                            <th>#</th>
                            <th>Name</th>
                            <th>UserLevel</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div>
        <!-- end col-->
    </div>
</div>

@endsection

@section('custom_scripts')

<script>
    $(function() {
        $('.ModalClosed').click(function () {
            $('.modal').modal('hide'); 
            $(this).find('form').trigger('reset');
        });

        $('#User-Datatable').DataTable({
            processing: true,
            serverSide: true,
            info: true,
            ajax: "{{ route('getUsersList')}}",
            // "start": "0",
            // "length": "10",
            "pageLength":10,
            "aLengthMenu":[[10,30,50,-1],[10,30,50,"all"]],
            columns:[
                // {data:'id', name:'id'},
                {data:'DT_RowIndex'},
                {data:'name'},
                {data:'user_level_id'},
                {data:'email'},
                {data:'Actions'},
            ]
        });
    });
</script>
@endsection