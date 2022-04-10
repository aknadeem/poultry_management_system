@php
$load_css = Array('tables','sweetAlert', 'jquery-confirm');
$load_js = Array('tables','tippy','sweetAlert', 'jquery-confirm','select2','buttons');
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
                        <li class="breadcrumb-item">Products</li>
                        <li class="breadcrumb-item active">Detail</li>
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
                            <h4>Product Sale Detail</h4>
                        </div>
                        <div class="col-6 align-self-end text-end mb-2">
                            <a class="btn btn-info btn-sm" href="{{ route('productsales.invoice', $sale->id) }}"
                                title="Click to view Invoice" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-file"></i>
                                Invoice
                            </a>

                            <a class="btn btn-secondary btn-sm" href="{{ route('productsales.index') }}"
                                title="Click to go back" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-arrow-left"></i>
                                Product Sales
                            </a>
                        </div>
                    </div>
                    <table id="basic-datatable" class="table table-striped dt-responsive  w-100">
                        <thead>
                            <tr>
                                <th style="width: 2%">#</th>
                                <th style="width: 12%">Item</th>
                                <th style="width: 5%">Price</th>
                                <th style="width: 3%">Qty</th>
                                <th style="width: 9%">Free Qty</th>
                                <th class="text-danger" style="width: 10%">Rebate Qty</th>
                                <th style="width: 9%">Total Qty</th>
                                <th style="width: 5%">Discount</th>
                                <th class="text-danger" style="width: 12%">Rebate Amount</th>
                                <th style="width: 10%">Total</th>
                                <th style="width: 10%" class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sale->detail as $key=>$item)
                            <tr>
                                <td>{{++$key}}</td>
                                <td>
                                    <b class="fs-5"> {{ $item?->product_code }} </b> <br />
                                    {{$item?->product_name}}
                                </td>
                                <td>@money($item?->product_sale_price)</td>
                                <td>{{$item?->product_qty}}</td>
                                <td>{{$item?->product_bonus_qty}}</td>
                                <td class="text-danger">{{$item?->rebate_qty}}</td>
                                <td>{{$item?->product_total_qty}}</td>
                                <td>@money($item?->product_discount)</td>
                                <td class="text-danger"> @money($item?->product_sale_price * $item?->rebate_qty)</td>
                                <td> @money($item?->product_total_price)</td>
                                <td>
                                    @if ($item?->product_total_qty > 0)
                                    <a class="btn btn-warning btn-sm openRebateModal" style="float:right;"
                                        ProductDetailId="{{ $item->id ?? 0 }}" FromPage="ProductSaleDetail"
                                        href="javascript:void(0);" title="click to Rebate"> <i
                                            class="fa fa-info-circle"></i> Rebate
                                    </a>
                                    @endif
                                </td>
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
@include('productmanagement._rebateProduct')
@endsection
@section('custom_scripts')
<script>
    $(function() {
        $(".openStatusModal").click(function(){
            alert('hello')
            // let product_sale_id = parseInt($(this).attr('ProductSaleId'))
            $("#UpdateProductStatusModal").modal('show')
        });
    });
</script>
@endsection