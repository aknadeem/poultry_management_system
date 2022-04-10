@php
$load_css = Array('sweetAlert', 'jquery-confirm');
$load_js = Array('sweetAlert', 'jquery-confirm');
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
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">ProductManagement</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('productsales.index') }}">ProductSales</a></li>
                        {{-- <li class="breadcrumb-item"><a href="javascript: void(0);">Extras</a></li> --}}
                        <li class="breadcrumb-item active">Invoice</li>
                    </ol>
                </div>
                <h4 class="page-title">Invoice</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Logo & title -->
                    <div class="clearfix">
                        <div class="float-start">
                            <div class="auth-logo">
                                <div class="logo logo-dark">
                                    <span class="logo-lg">
                                        <img src="{{ asset('assets/images/logo/poultryLogo.png') }}" alt="" width="50%">
                                    </span>
                                </div>

                                <div class="logo logo-light">
                                    <span class="logo-lg">
                                        <img src="../assets/images/logo-light.png" alt="" height="22">
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="float-end">
                            <h4 class="m-0 d-print-none">Invoice</h4>
                            <address>
                                Stanley Jones<br>
                                795 Folsom Ave, Suite 600<br>
                                San Francisco, CA 94107<br>
                                <abbr title="Phone">P:</abbr> (123) 456-7890
                            </address>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="mt-1">
                                <p class="fs-5">
                                    <b>{{ $sale?->party?->name}}</b>
                                    <b>[{{ $sale?->party?->cnic_no}}]</b>
                                </p>
                                <p class="text-muted">Thanks a lot because you keep purchasing our products. </p>
                            </div>

                        </div><!-- end col -->
                        <div class="col-6">
                            <div class="mt-1 float-end">
                                <p class="mb-1"><strong>Sale Date : </strong> <span class="float-end">
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                        {{$sale?->sale_date?->format('d M, Y')}} </span></p>
                                <p class="mb-1"><strong> Payment Status : </strong> <span class="float-end">
                                        <span
                                            class="badge bg-{{$sale?->status_value['color_name']}}">{{$sale?->status_value['value']}}</span>
                                    </span>
                                </p>
                                <p class="mb-1"><strong>Sale code : </strong> <span
                                        class="float-end">{{$sale?->sale_code}} </span>
                                </p>
                            </div>
                        </div><!-- end col -->
                    </div>
                    <!-- end row -->
                    {{-- <div class="row mt-3">
                        <div class="col-6">
                            <h6>Billing Address</h6>
                            <address>
                                Stanley Jones<br>
                                795 Folsom Ave, Suite 600<br>
                                San Francisco, CA 94107<br>
                                <abbr title="Phone">P:</abbr> (123) 456-7890
                            </address>
                        </div>
                        <div class="col-6">
                            <h6>Shipping Address</h6>
                            <address>
                                Stanley Jones<br>
                                795 Folsom Ave, Suite 600<br>
                                San Francisco, CA 94107<br>
                                <abbr title="Phone">P:</abbr> (123) 456-7890
                            </address>
                        </div>
                    </div> --}}
                    <!-- end row -->

                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table mt-2 table-centered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Item</th>
                                            <th style="width: 10%">Price</th>
                                            <th style="width: 10%">Qty</th>
                                            <th style="width: 10%">Free Qty</th>
                                            <th style="width: 10%">Discount</th>
                                            <th style="width: 10%" class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($items as $key=>$item)
                                        <tr>
                                            <td>{{++$key}}</td>
                                            <td>
                                                <b class="fs-5"> {{ $item?->product_code }} </b> <br />
                                                {{$item?->product_name}}
                                            </td>
                                            <td>@money($item?->product_sale_price)</td>
                                            <td>{{$item?->product_qty}}</td>
                                            <td>{{$item?->product_bonus_qty}}</td>
                                            <td>@money($item?->product_discount)</td>
                                            <td class="text-end"> @money($item?->product_total_price)</td>
                                        </tr>
                                        @empty
                                        @endforelse
                                    </tbody>
                                </table>
                            </div> <!-- end table-responsive -->
                        </div> <!-- end col -->
                    </div>
                    <!-- end row -->

                    <div class="row">
                        <div class="col-sm-6">
                            {{-- <div class="clearfix pt-5">
                                <h6 class="text-muted">Notes:</h6>
                                <small class="text-muted">
                                    All accounts are to be paid within 7 days from receipt of
                                    invoice. To be paid by cheque or credit card or direct payment
                                    online. If account is not paid within 7 days the credits details
                                    supplied as confirmation of work undertaken will be charged the
                                    agreed quoted fee noted above.
                                </small>
                            </div> --}}
                        </div>
                        <div class="col-sm-6">
                            <div class="float-end fs-5">
                                <p><b>Sub-total:</b> <span class="float-end">@money($sale?->total_amount)</span></p>

                                @php
                                $perr = $sale?->discount_amount/$sale?->total_amount*100;

                                @endphp
                                <p><b>Discount ({{number_format($perr, 2)}}%):</b> <span class="float-end">
                                        &nbsp;&nbsp;&nbsp;
                                        @money($sale?->discount_amount) </span></p>
                                <p><b>Other:</b> <span class="float-end">@money($sale?->other_charges)</span></p>

                                <h3 class="text-danger"><b>Total: </b> @money($sale?->final_amount)</h3>
                            </div>
                            <div class="clearfix"></div>
                        </div> <!-- end col -->
                    </div>
                    <!-- end row -->

                    <div class="mt-4 mb-1">
                        <div class="text-end d-print-none">
                            <a href="javascript:window.print()" class="btn btn-primary waves-effect waves-light"><i
                                    class="mdi mdi-printer me-1"></i> Print</a>
                            <a href="{{ url()->previous() }}" class="btn btn-info waves-effect waves-light">Go Back</a>
                        </div>
                    </div>
                </div>
            </div> <!-- end card -->
        </div> <!-- end col -->
    </div>
    <!-- end row -->
</div>
@endsection

@section('custom_scripts')

<script>
    $(function() {

    });
</script>

@endsection