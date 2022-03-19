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
                        <li class="breadcrumb-item active">ChickPurchase</li>
                    </ol>
                </div>
                <h4 class="page-title">Chick Purchase Report </h4>
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
                    <table id="ChickenSale-datatable" class="table table-striped dt-responsive w-100">
                        <thead>
                            <tr>
                                <td class="form-group" colspan="4">
                                    <label class="form-control-label fs-5">Search by <b>Customer</b></label>
                                    <select class="form-control mySelect filter-select px-1" data-column="4"
                                        data-width="100%">
                                        <option selected disabled value=""> Select Customer </option>
                                        @forelse ($customers as $item)
                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                        @empty

                                        @endforelse
                                        <option value="all"> All </option>
                                    </select>
                                </td>

                                <td class="form-group" colspan="4">
                                    <label class="form-control-label">Search by <b>Manual Number</b></label>

                                    <input type="text" class="form-control filter-input px-1" data-column="5"
                                        placeholder="Enter manual number">
                                </td>
                                <td class="form-group" colspan="3">
                                    <label class="form-control-label">Search by <b>Sale date</b></label>
                                    <input type="date" class="form-control filter-select px-1" data-column="6"
                                        placeholder="Enter sale date">
                                </td>
                            </tr>
                            <tr>
                                <th>#</th>
                                <th>Date From</th>
                                <th>Date To </th>
                                <th>Company</th>
                                <th>Purchase Date</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Dicount Amount</th>
                                <th>total Price</th>
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
            var uri = "{{ url('reportmanagement/chick-purchase-report') }}/"+Fromdate+"/"+toDate

            $('#ReportDisplayDiv').show()

            // $('#ChickenSale-datatable').DataTable().ajax.reload()
            // $('#ChickenSale-datatable').dataTable().clear().draw();

            var DataTable = $('#ChickenSale-datatable').DataTable({
                processing: true,
                info: true,
                ajax: uri,
                "pageLength":10,
                dom: 'Blfrtip',
                "bDestroy": true, //for Resolving Cannot reinitialise DataTable
                "aLengthMenu":[[10,30,40,-1],[10,30,40,"all"]],
                columns:[
                    // {data:'id', name:'id'},
                    {data:'DT_RowIndex'},
                    {data:'DateFrom'},
                    {data:'DateTo'},
                    {data:'company_id'},
                    {data:'purchase_date'},
                    {data:'price'},
                    {data:'quantity'},
                    {data:'discount_amount'},
                    {data:'total_price'},
                ],
                buttons: [
                    'copy',
                    {
                        extend: 'excel',
                        title: 'Chicken Sale Report'
                    },
                    {
                        extend: 'pdf',
                        title: 'Chicken Sale Report'
                    },
                    {
                        extend: 'print',
                        title: 'Chicken Sale Report'
                    },
                    // {
                    //     extend: 'csv',
                    //     title: 'Chicken Sale Report'
                    // }
                ],
            });

            let columnSociety = DataTable.column(4);
            columnSociety.visible(false);

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