@php
$load_css = Array('tables');
$load_js = Array('tables','tippy','buttons');
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
                        <li class="breadcrumb-item">ProductPurchase</li>
                        <li class="breadcrumb-item active">Rebates</li>
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
                            <h4>Product Purchase Rebates</h4>
                        </div>
                        <div class="col-6 align-self-end text-end mb-2">
                            <a class="btn btn-secondary btn-sm" href="{{ route('productpurchases.index') }}"
                                title="Click to go back" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-arrow-left"></i>
                                Product Purchases
                            </a>
                        </div>
                    </div>
                    <table id="basic-datatable" class="table table-striped dt-responsive  w-100">
                        <thead>
                            <tr>
                                <th class="fs-6">#</th>
                                <th class="fs-6">Purchase id</th>
                                <th>Product id</th>
                                <th>Rebate Reason</th>
                                <th>Rebate Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rebates as $key=>$row)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $row?->rebate_item_id }}</td>
                                <td>{{ $row?->product_id }}</td>
                                <td>{{ $row?->rebate_reason }}</td>
                                <td>{{ $row?->rebate_qty }}</td>
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
@endsection