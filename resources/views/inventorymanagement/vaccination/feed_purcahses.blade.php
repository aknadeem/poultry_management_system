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
                        <li class="breadcrumb-item active">Feed</li>
                    </ol>
                </div>
                <h6 class="page-title"> Feed Category - Feed name </h6>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4> Feed Purchases </h4>
                        </div>
                        {{-- data-bs-toggle="modal"
                        data-bs-target="#AddFeedModal" --}}
                        <div class="col-6 align-self-end text-end mb-2">
                            <a class="btn btn-secondary btn-sm" href="{{ route('feed.index') }}"
                                title="Click to go back" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-plus"></i>
                                Back
                            </a>
                        </div>
                    </div>
                    <table id="Feed-datatable" class="table table-striped dt-responsive w-100">
                        <thead>
                            <th>#</th>
                            <th>Purchase Date</th>
                            <th>Company</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Discount Amount</th>
                            <th>Total Amount</th>
                            <th>Sale Order Number</th>
                            <th>Delivery Order Number</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
                            @forelse ($feed->purchases as $key=>$item)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td> <b class="text-danger"> {{ $item->purchase_date->format('d M, Y') }}</b> </td>
                                <td> <b>{{ $item?->company?->company_name }}</b> </td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->price }}</td>
                                <td>{{ $item->discount_amount }}</td>
                                <td><b>{{ $item->total_price }}</b></td>
                                <td>{{ $item->sale_order_number }}</td>
                                <td>{{ $item->delivery_order_number }}</td>
                                <td>actions</td>
                            </tr>
                            @empty
                            @endforelse

                        </tbody>
                    </table>
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div>
        <!-- end col-->
    </div>
</div>
<!-- View Detail modal content -->
<div id="EmployeeDetailModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="EmployeeDetailModal"
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
            $('.modal').modal('hide'); 
            $(this).find('form').trigger('reset');
        });
    });
</script>
@endsection