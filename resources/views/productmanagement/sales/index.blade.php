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
                        <li class="breadcrumb-item">Products</li>
                        <li class="breadcrumb-item active">index</li>
                    </ol>
                </div>
                <h4 class="page-title"> Product Management </h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4> Product Sales </h4>
                        </div>
                        <div class="col-6 align-self-end text-end mb-2">
                            <a class="btn btn-secondary btn-sm" href="{{ route('productsales.create') }}"
                                title="Click to add new" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-plus"></i>
                                Add Sale
                            </a>
                        </div>
                    </div>
                    <table id="basic-datatable" class="table table-striped dt-responsive  w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product code</th>
                                <th>Product Name</th>
                                <th>Company</th>
                                <th>Date</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Total Price</th>
                                <th>Discount</th>
                                <th>Tax</th>
                                <th>Final Price</th>
                                {{-- <th>Status</th> --}}
                                {{-- <th>Options</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($product_sales as $key=>$row)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $row->product?->product_code }}</td>
                                <td>{{ $row->product?->product_name }}</td>
                                <td>{{ $row->product?->company?->company_name }}</td>
                                <td>{{ $row->purchase_date->format('M d, Y') }}</td>
                                <td>{{ $row->quantity }}</td>
                                <td class="fw-bold fs-6">@money($row->purchase_price)</td>
                                <td class="fw-bold fs-5"> @money($row->total_price)</td>
                                <td class="text-success fw-bold fs-6">@money($row->discount_amount)</td>
                                <td class="text-warning fw-bold fs-6">@money($row->tax_amount)</td>
                                <td class="text-danger fw-bold fs-5">@money($row->final_price)</td>
                                {{-- <td>
                                    <a href="{{ route('updatestatus', ['id'=> $row->id, 'tag' => 'product_purchases']) }}"
                                        title="Click to update Status" class="confirm-status">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" id="customSwitch1"
                                                {{($row?->is_active) ? 'checked' : ''}}>
                                            <label class="form-check-label" for="customSwitch1"></label>
                                        </div>
                                    </a>
                                </td> --}}
                                {{-- <td>
                                    <a class="btn btn-info btn-sm" href="{{ route('productpurchases.edit', $row->id) }}"
                                        title="Click to edit" tabindex="0" data-plugin="tippy"
                                        data-tippy-animation="scale" data-tippy-arrow="true"><i
                                            class="fa fa-pencil-alt"></i>
                                        Edit
                                    </a>
                                    <a class="btn btn-danger btn-sm delete-confirm"
                                        href="{{route('productpurchases.destroy', $row?->id)}}"
                                        del_title="Purchase id {{$row?->id}}" title="Click to delete" tabindex="0"
                                        data-plugin="tippy" data-tippy-animation="scale" data-tippy-arrow="true"><i
                                            class="fa fa-trash"></i>
                                        Delete
                                    </a>
                                </td> --}}
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

@section('custom_scripts')

<script>
    $(function() {

    });
</script>

@endsection