@php
$load_css = Array('tables','sweetAlert','select2');
$load_js = Array('tables','sweetAlert','select2','buttons')
;
@endphp
@extends('layouts.app')
@section('content')
<div class="container-fluid">

    <style>
        .dataTables_filter {
            margin-top: -40px !important;
        }

        .dataTables_wrapper .dataTables_processing {
            background: #1c5b90;
            border: 1px solid #1c5b90;
            border-radius: 3px;
            color: #fff;
        }
    </style>

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item">ReportManagement</li>
                        <li class="breadcrumb-item active">ProductPurchaseReport</li>
                    </ol>
                </div>
                <h4 class="page-title">Product Purchase Report</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-1">
                    <div class="row">
                        <div class="col-10 text-center">
                            <h4> Choose Dates: </h4>
                        </div>
                        {{-- data-bs-toggle="modal"
                        data-bs-target="#AddFeedModal" --}}
                        {{-- <div class="col-6 align-self-end text-end mb-2">
                            <a class="btn btn-secondary btn-sm" href="{{ route('sale.index') }}"
                                title="Click to see Sales" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-arrow-left"></i>
                                Sales
                            </a>
                        </div> --}}

                        <form autocomplete="off" action="" method="post" id="ReportSearchForm">
                            @csrf
                            <div class="row form-group mt-2">
                                <div class="col-2"></div>
                                <input type="hidden" name="sale_id">
                                <div class="col-sm-3 mb-2 fs-5">
                                    <label for="DateFrom" style="margin-bottom:2px;"> Date From: </label>
                                    <input type="date" required placeholder="Date from" class="form-control fs-5"
                                        id="DateFrom" value="{{ today()->format('Y-m-d') }}">
                                    @error('date_from')
                                    <span class="text-danger date_from_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3 mb-2 fs-5">
                                    <label for="DateTo" style="margin-bottom:2px;"> Date To: </label>
                                    <input type="date" required placeholder="Date to" name="date_to"
                                        value="{{ today()->format('Y-m-d') }}" class="form-control fs-5" id="DateTo">
                                    @error('date_to')
                                    <span class="text-danger date_to_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-sm-2">
                                    <button type="submit" id="sub" class="btn btn-success btn-block mt-3 AddUpdate">
                                        <i class="fas fa-search"></i> Search </button>
                                </div>
                            </div>
                            <div class="row form-group">

                            </div>
                        </form>
                    </div>
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div>
        <!-- end col-->
    </div>

    <div class="row" id="ReportDisplayDiv" style="display:none;">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    {{-- <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4> Sales </h4>
                        </div>
                        <div class="col-6 align-self-end text-end mb-2">
                            <a class="btn btn-secondary btn-sm" href="{{ route('sale.create') }}"
                                title="Click to add Sale" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-plus"></i>
                                Create
                            </a>
                        </div>
                    </div> --}}
                    <table id="SaleReport-datatable" class="table table-striped dt-responsive w-100">
                        <thead>
                            <tr>
                                <td class="form-group" colspan="5">
                                    <label class="form-control-label fs-5">Search by <b>Company</b></label>
                                    <select class="form-control mySelect filter-select px-1" data-column="3"
                                        data-width="100%">
                                        <option selected disabled value=""> Select Company </option>
                                        <option value="all"> All </option>
                                    </select>
                                </td>

                                <td class="form-group fs-5" colspan="5  ">
                                    <label class="form-control-label">Search by <b>Sale Code</b></label>

                                    <input type="text" class="form-control filter-input px-1" data-column="4"
                                        placeholder="Enter sale code">
                                </td>
                                {{-- <td class="form-group fs-5" colspan="2">
                                    <label class="form-control-label">Search by <b>Party</b></label>

                                    <input type="text" class="form-control filter-input px-1" data-column="6"
                                        placeholder="">
                                </td> --}}
                            </tr>
                            <tr>
                                <th>#</th>
                                <th> Date From </th>
                                <th> Date To </th>
                                <th> Company </th>
                                <th> Code </th>
                                <th> Purchase date </th>
                                <th> Total Amount </th>
                                <th> Discount Amount </th>
                                <th> Final Amount </th>
                            </tr>
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

        // 
        $('#ReportSearchForm').on('submit', function(e) {
            e.preventDefault();
            let Fromdate = $('#DateFrom').val()
            let toDate = $('#DateTo').val()

            // alert(Fromdate)
            var uri = "{{ url('reportmanagement/product-purchase-report') }}/"+Fromdate+"/"+toDate

            $('#ReportDisplayDiv').show()
            
            var DataTable = $('#SaleReport-datatable').DataTable({
                processing: true,
                info: true,
                ajax: uri,
                "pageLength":10,
                dom: 'Blfrtip',
                "bDestroy": true, //for Resolving Cannot reinitialise DataTable
                "aLengthMenu":[[10,30,40,-1],[10,30,40,"all"]],
                columns:[
                    {data: 'DT_RowIndex'},
                    {data: 'DateFrom'},
                    {data: 'DateTo'}, 
                    {data: 'party_company_id'}, 
                    {data: 'purchase_code'}, 
                    {data: 'purchase_date'}, 
                    {data: 'total_price'}, 
                    {data: 'discount_amount'}, 
                    {data: 'final_price'}, 
                ],
                buttons: [
                    'copy',
                    {
                        extend: 'csv',
                        title: 'Product Report'
                    },
                    {
                        extend: 'pdf',
                        title: 'Product Report'
                    },
                    {
                        extend: 'print',
                        title: 'Product Report'
                    },
                    // {
                    //     extend: 'csv',
                    //     title: 'Chicken Sale Report'
                    // }
                ],
            });

            let productGroup = DataTable.column(5);
            productGroup.visible(false);

            $(".filter-input").keyup(function () {
                if($(this).val() == ''){
                    DataTable.column($(this).data('column')).search('').draw();
                }else{
                    DataTable.column($(this).data('column')).search($(this).val()).draw();
                }
            });

            $(".filter-select").change(function () {
                if($(this).val() == 'all'){
                    DataTable.column($(this).data('column')).search('').draw();
                }else{
                    DataTable.column($(this).data('column')).search($(this).val()).draw();
                }
            });

        }); 
    });
</script>
@endsection