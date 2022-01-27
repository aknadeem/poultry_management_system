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
                        <li class="breadcrumb-item active"> BalancePayments </li>
                    </ol>
                </div>
                <h6 class="page-title"> {{ $balance_payments[0]->company?->company_name}} </h6>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4>Balance Payments</h4>
                        </div>
                        <div class="col-6 align-self-end text-end mb-2">
                            <a href="{{ asset('storage/party/company/'.$balance_payments[0]->company?->company_logo) }}"
                                target="_blank" title="click to view">
                                <img src="{{ asset('storage/party/company/'.$balance_payments[0]->company?->company_logo) }}"
                                    alt="No image" width="10%">
                            </a>
                            {{-- <a class="btn btn-secondary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#AddCompanyModal" href="javascript:void(0);" CustomerId="0"
                                title="Click to add new company" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-plus"></i>
                                Company
                            </a> --}}
                        </div>
                    </div>
                    <table id="Balance-Datatables" class="table table-striped dt-responsive  w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Paid Amount</th>
                                <th>Paid through</th>
                                <th>Paid By </th>
                                <th>Paid At </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($balance_payments as $key=>$item)
                            <tr>
                                <td>{{++$key}}</td>
                                <td>{{$item->paid_amount}}</td>
                                <td>{{$item->payment_option}}</td>
                                <td>{{$item?->user?->name}} </td>
                                <td>{{$item->created_at?->format('d M, Y h:i:s A')}}</td>
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

        // $('.openAddPaymentModal').click(function () {
        //     alert('hello')
        //     // $('#AddPaymentModal').modal('show')
        // });

        $('.ModalClosed').click(function () {
            // $(this).find('modal').hide();
            $('.modal').modal('hide'); 
            $(this).find('form').trigger('reset');
        });
    });
</script>
@endsection