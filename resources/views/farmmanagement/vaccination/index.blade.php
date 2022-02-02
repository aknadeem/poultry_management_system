@php
$load_css = Array('tables','sweetAlert', 'jquery-confirm','select2');
$load_js = Array('tables','tippy','sweetAlert', 'jquery-confirm','select2');
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
                        <li class="breadcrumb-item">FarmManagement</li>
                        <li class="breadcrumb-item">Vaccination</li>
                        <li class="breadcrumb-item active">Schedule</li>
                    </ol>
                </div>
                <h4 class="page-title">Farm Management</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4>Vaccination Schedule</h4>
                        </div>
                        <div class="col-6 align-self-end text-end mb-2">
                            <a class="btn btn-secondary btn-sm OpenAddStoreModal" StoreId="0" FromPage="productstores"
                                title="Click to add new Schedule" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-plus"></i>
                                Add Vaccination Schedule
                            </a>
                        </div>
                    </div>

                    <table id="store_datatable" class="table table-striped dt-responsive w-100">
                        <thead>
                            <th>#</th>
                            <th>Farm</th>
                            <th>Product</th>
                            <th>Schedule date</th>
                            <th>Is Vaccinated</th>
                            <th>Description</th>
                            <th>Is Active</th>
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

@include('farmmanagement.vaccination._AddVaccinationSchedule')
@include('farmmanagement.vaccination._AddVaccinationForm')

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
            ajax: "{{ route('getScheduleList')}}",
            "pageLength":10,
            "aLengthMenu":[[10,40,50,-1], [10,40,50,"all"]],
            columns:[
                // {data:'id', name:'id'},
                {data:'DT_RowIndex'},
                {data:'party_farm_id'},
                {data:'product_id'},
                {data:'schedule_date'},
                {data:'is_vaccinated'},
                {data:'description'},
                {data:'is_active'},
                {data:'Actions'},
            ],columnDefs: [ {
                targets: [3],
                className: 'text-danger fs-5 bolded'
                }
            ],
        });  
    });
</script>
@endsection