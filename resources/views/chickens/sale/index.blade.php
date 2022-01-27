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
                        <li class="breadcrumb-item active">Chickens</li>
                    </ol>
                </div>
                <h4 class="page-title">Chicken Sales</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4> Sales </h4>
                        </div>
                        {{-- data-bs-toggle="modal"
                        data-bs-target="#AddFeedModal" --}}
                        <div class="col-6 align-self-end text-end mb-2">
                            <a class="btn btn-secondary btn-sm" href="{{ route('sale.create') }}"
                                title="Click to add Sale" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-plus"></i>
                                Create
                            </a>
                        </div>
                    </div>
                    <table id="ChickenSale-datatable" class="table table-striped dt-responsive w-100">
                        <thead>
                            <th>#</th>
                            <th>picture</th>
                            <th>Customer</th>
                            <th>Sale Date</th>
                            <th>PerKgPrice</th>
                            <th>TotalWeight</th>
                            <th>Dicount Amount</th>
                            <th>total Price</th>
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
        $('#ChickenSale-datatable').DataTable({
            processing: true,
            info: true,
            ajax: "{{ route('getSalesList')}}",
            "pageLength":10,
            "aLengthMenu":[[20,30,40,-1],[20,30,40,"all"]],
            columns:[
                // {data:'id', name:'id'},
                {data:'DT_RowIndex'},
                {data:'picture'},
                {data:'customer_id'},
                {data:'sale_date'},
                {data:'per_kg_price'},
                {data:'total_weight'},
                {data:'discount_amount'},
                {data:'total_price'},
                {data:'Actions'},
            ]
        });
    });
</script>
@endsection