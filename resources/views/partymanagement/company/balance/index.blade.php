@php
$load_css = Array('tables','sweetAlert', 'jquery-confirm','select2');
$load_js = Array('tables','tippy','sweetAlert', 'jquery-confirm','select2','select2model')
;
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
                        <li class="breadcrumb-item active">Companies Balance</li>
                    </ol>
                </div>
                <h6 class="page-title">Companies Balance</h6>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4>Companies Balance</h4>
                        </div>
                        <div class="col-6 align-self-end text-end mb-2">
                            {{-- <a class="btn btn-secondary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#AddCompanyModal" href="javascript:void(0);" CustomerId="0"
                                title="Click to add new company" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-plus"></i>
                                Company
                            </a> --}}
                        </div>
                    </div>
                    <table id="Balance-Datatable" class="table table-striped dt-responsive  w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Company</th>
                                <th>Total Amount</th>
                                <th>Paid Amount</th>
                                <th>Remaining Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div>
        <!-- end col-->
    </div>
</div>

@include('partymanagement._AddPaymentModal')


<!-- View Detail modal content -->
<div id="CompanyDetailModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="CompanyDetailModal"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="CustomerDetailData">
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection

@section('custom_scripts')
<script>
    $(function() {
        $('.ModalClosed').click(function () {
            // $(this).find('modal').hide();
            $('.modal').modal('hide'); 
            $(this).find('form').trigger('reset');
        });

        $('#Balance-Datatable').DataTable({
            processing: true,
            serverSide: true,
            info: true,
            ajax: "{{ route('getCompaniesBalanceList')}}",
            "pageLength":10,
            dom: 'Blfrtip',
            "aLengthMenu":[[10,20,30,40,-1],[10,20,30,40,"all"]],
            columns:[
                // {data:'id', name:'id'},
                {data:'DT_RowIndex'},
                {data:'type'},
                {data:'company_id'},
                {data:'total_amount'},
                {data:'paid_amount'},
                {data:'remaining_amount'},
                {data:'status'},
                {data:'created_at'},
                {data:'Action'},
            ],columnDefs: [ {
                targets: [3,7],
                className: 'bolded'
                }
            ],
        });

        $('.ViewCompanyModal').click(function () {
            let company_id = parseInt($(this).attr('CompanyId')) || 0;
            $.get("{{ url('/company')}}/"+company_id, function(result) {
                // console.log(result)
                $('#CompanyDetailModal').modal('show');
                $('#CustomerDetailData').html(result?.html_data);
            });
        });
    });
</script>
@endsection