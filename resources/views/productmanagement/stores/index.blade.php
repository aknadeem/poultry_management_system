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
                        <li class="breadcrumb-item">ProductManagement</li>
                        <li class="breadcrumb-item active">Stores</li>
                    </ol>
                </div>
                <h4 class="page-title">Product Management</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4>Stores</h4>
                        </div>
                        <div class="col-6 align-self-end text-end mb-2">
                            <a class="btn btn-secondary btn-sm OpenAddStoreModal" StoreId="0" FromPage="productstores"
                                title="Click to add new store" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-plus"></i>
                                Store
                            </a>
                        </div>
                    </div>

                    <table id="store_datatable" class="table table-striped dt-responsive w-100">
                        <thead>
                            <th>#</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Area</th>
                            <th>Total Racks</th>
                            <th>is_active</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <!-- end card body-->
            </div>
            <!-- end card -->
        </div>
        <!-- end col-->
    </div>
</div>

@include('productmanagement._addStoreModal')

<!-- View Detail modal content -->
<div id="DetailModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="DetailModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="DetailData">
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection

@section('custom_scripts')

<script>
    $(function() {
        $('.ModalClosed').click(function () {
            $('.modal').modal('hide'); 
            $(this).find('form').trigger('reset');
        });

        $(document).on('click', '.ViewDetailModal', function(){
            let StoreId = parseInt($(this).attr('StoreId')) || 0;
            $.get("{{ url('/productManagement/productstores')}}/"+StoreId, function(result) {
                $('#DetailModal').modal('show');
                $('#DetailData').html(result?.html_data);
            });
        });

        $('#store_datatable').DataTable({
            processing: true,
            info: true,
            serverSide: true,
            ajax: "{{ route('storelist')}}",
            "pageLength":10,
            "aLengthMenu":[[10,40,50,-1], [10,40,50,"all"]],
            columns:[
                // {data:'id', name:'id'},
                {data:'DT_RowIndex'},
                {data:'store_name'},
                {data:'store_code'},
                {data:'store_type'},
                {data:'store_area'},
                {data:'total_racks'},
                {data:'is_active'},
                {data:'created_at'},
                {data:'Actions'},
            ]
        });

        // $(document).on('click', '.openEmployeeModal', function(){
        //     var empId = $(this).data('id');
        //     alert(empId);
        // });

        
    });
</script>
@endsection