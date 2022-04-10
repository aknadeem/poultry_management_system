@php
$load_css = Array('tables','sweetAlert', 'jquery-confirm');
$load_js = Array('tables','tippy','sweetAlert', 'jquery-confirm','buttons');
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
                            <h4>Product Sales</h4>
                        </div>
                        <div class="col-6 align-self-end text-end mb-2">
                            <a class="btn btn-warning btn-sm" href="{{ route('productsales.rebates') }}"
                                title="Click to view" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-eye"></i>
                                Rebates
                            </a>

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
                                <th class="fs-6">#</th>
                                <th class="fs-6">Division</th>
                                <th>Customer</th>
                                <th>Company</th>
                                <th class="fs-6">Category</th>
                                <th>Sale Date</th>
                                <th class="fs-6">Payment Status</th>
                                {{-- <th class="fs-6">Status</th> --}}
                                <th>Total Amount</th>
                                <th>Discount Amount</th>
                                <th>Other Charges</th>
                                <th class="text-danger">Rebate Amount</th>
                                <th>Final Amount</th>
                                {{-- <th>Paid Amount</th> --}}
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($product_sales as $key=>$row)
                            <tr>
                                <td class="fs-6">{{ ++$key }}</td>
                                <td class="fs-6">{{ $row?->party?->division?->name }}</td>
                                <td>{{ $row?->party?->name }}</td>
                                <td>{{ $row->company?->company_name }}</td>
                                <td class="fs-6">{{ $row?->productcategory?->name }}</td>
                                <td>{{ $row->sale_date?->format('d M, Y') }}</td>
                                <td class="text-{{ $row->status_value['color_name'] }} fs-6 fw-bold">{{
                                    $row->status_value['value']
                                    }}</td>
                                {{-- <td class="fs-6">
                                    <a href="{{ route('updatestatus', ['id'=> $row->id, 'tag' => 'product_sales']) }}"
                                        title="Click to {{($row?->is_active) ? 'Inactive' : 'Active'}} this sale"
                                        class="confirm-status">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" id="SaleStatus"
                                                {{($row?->is_active) ? 'checked' : ''}}>
                                            <label class="form-check-label" for="SaleStatus"></label>
                                        </div>
                                    </a>
                                </td> --}}
                                <td class="fw-bold fs-5">
                                    @money($row->total_amount)
                                </td>
                                <td class="fw-bold fs-6">
                                    @money($row->discount_amount)
                                </td>
                                <td class="fw-bold fs-6">
                                    @money($row->other_charges)
                                </td>
                                <td class="fw-bold fs-6 text-danger">
                                    @money($row->rebate_amount)
                                </td>
                                <td class="text-danger fw-bold fs-5">
                                    @money($row->final_amount)
                                </td>

                                {{-- <td class="text-success fw-bold fs-5">
                                    @money($row->final_amount)
                                </td> --}}
                                <td>
                                    <a class="btn btn-info btn-sm" href="{{ route('productsales.show', $row?->id) }}"
                                        title="click to view"><i class="fa fa-eye"></i> Detail
                                    </a>
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

@endsection

@section('custom_scripts')

<script>
    $(function() {

    });
</script>

@endsection