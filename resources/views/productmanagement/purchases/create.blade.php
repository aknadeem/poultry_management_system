@php
$load_css = Array('select2');
$load_js = Array('tippy','select2')
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
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item"> <a href="{{ route('productpurchases.index') }}"> ProductManagement
                            </a>
                        </li>
                        <li class="breadcrumb-item"> <a href="{{ route('productpurchases.index') }}"> Purchases </a>
                        </li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
                <h4 class="page-title"> Product Purchase </h4>
            </div>
        </div>
    </div>
    <div class="row card">
        <div class="col-12 card-body">
            <div class="row mb-2">
                <div class="col-6 align-self-start">
                    <h4> Add Purchase Entry </h4>
                </div>
                <div class="col-6 align-self-end text-end mb-2">
                    <a class="btn btn-secondary btn-sm" href="{{ route('productpurchases.index') }}"
                        title="Click to go back" data-plugin="tippy" data-tippy-animation="scale"
                        data-tippy-arrow="true"><i class="fa fa-arrow-left"></i>
                        Back
                    </a>
                </div>

                <form method="post" action="#" enctype="multipart/form-data" id="BrokerForm" class="form_loader"
                    autocomplete="off">
                    @csrf
                    @php
                    $required = 'required';
                    if($pruchase->id){
                    $exProvince_id = $pruchase->company_id;
                    $required = '';
                    }
                    @endphp

                    @if($pruchase->id)
                    @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-4 border border-2">
                            <div class="row mt-2">
                                <div class="col-12 mb-3">
                                    <label class="font_bold" for="CompanySelect"> Select Company* </label>
                                    <select name="company_id" required id="CompanySelect" class="form-control mySelect"
                                        data-toggle="select2" data-width="100%" id="">
                                        <option value=""> Select Subtype</option>
                                        <option value="1"> abc </option>
                                    </select>
                                    @error('company_id')
                                    <span class="text-danger company_id_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="font_bold" for="CompanySelect"> Select Category* </label>
                                    <select name="company_id" required id="CompanySelect" class="form-control mySelect"
                                        data-toggle="select2" data-width="100%" id="">
                                        <option value=""> Select category</option>
                                        <option value="1"> abc </option>
                                    </select>
                                    @error('company_id')
                                    <span class="text-danger company_id_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="font_bold" for="Select Product"> Select Product * </label>
                                    <select name="product_name" required id="Select Product"
                                        class="form-control mySelect" data-toggle="select2" data-width="100%" id="">
                                        <option value=""> Select Subtype</option>
                                        <option value="1"> abc </option>
                                    </select>
                                    @error('product_name')
                                    <span class="text-danger product_name_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-sm-12 mb-3">
                                    <label for="ProductCode" class="font_bold"> Product Code* </label>
                                    <input type="text" placeholder="Product Code" name="product_code"
                                        class="form-control"
                                        value="{{ $pruchase?->product_code ?? old('product_code') }}" id="ProductCode"
                                        required>

                                    @error('product_code')
                                    <span class="text-danger product_code_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-8 border border-2">
                            <div class="row mt-2">
                                <div class="col-sm-4 mb-3">
                                    <label for="ProductName" class="font_bold"> Product Name * </label>
                                    <input type="text" placeholder="Enter Product Name" name="product_name"
                                        class="form-control"
                                        value="{{ $pruchase?->product_name ?? old('product_name') }}" id="ProductName"
                                        required>

                                    @error('product_name')
                                    <span class="text-danger product_name_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-4 mb-3">
                                    <label class="font_bold" for="PurchaseDate"> Purchase Date </label>
                                    <input type="date" class="form-control" name="purchase_date" value=""
                                        placeholder="Purchase date" id="PurchaseDate">

                                    @error('purchase_date')
                                    <span class="text-danger purchase_date_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-4 mb-3">
                                    <label class="font_bold" for="ExpiryDate"> Expiry Date </label>
                                    <input type="date" class="form-control" name="expiry_date" value=""
                                        placeholder="Purchase date" id="ExpiryDate">

                                    @error('expiry_date')
                                    <span class="text-danger expiry_date_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-4 mb-3">
                                    <label class="font_bold" for="Quantity"> Quantity </label>
                                    <input type="number" min="0" class="form-control" name="quantity" value=""
                                        placeholder="Purchase date" id="Quantity">

                                    @error('quantity')
                                    <span class="text-danger quantity_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-4 mb-3">
                                    <label class="font_bold" for="TradePrice"> Trade Price </label>
                                    <input type="number" step="any" min="0" class="form-control" name="trade_price"
                                        value="" placeholder="Trade Price" id="TradePrice">

                                    @error('trade_price')
                                    <span class="text-danger trade_price_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-4 mb-3">
                                    <label class="font_bold" for="RetailPrice"> Retail Price</label>
                                    <input type="number" step="any" min="0" class="form-control" name="retail_price"
                                        value="" placeholder="Retail Price" id="RetailPrice">

                                    @error('retail_price')
                                    <span class="text-danger retail_price_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-4 mb-3">
                                    <label class="font_bold" for="RetailPrice"> Purchase Price</label>
                                    <input type="number" step="any" min="0" class="form-control" name="retail_price"
                                        value="" placeholder="Discount Price" id="RetailPrice">

                                    @error('retail_price')
                                    <span class="text-danger retail_price_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-4 mb-3">
                                    <label class="font_bold" for="DiscountAmount"> Discount Amount</label>
                                    <input type="number" step="any" min="0" step="any" class="form-control"
                                        name="discount_amount" value="" placeholder="Discount Amount"
                                        id="DiscountAmount">

                                    @error('discount_amount')
                                    <span class="text-danger discount_amount_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-4 mb-3">
                                    <label class="font_bold" for="TaxPercentage"> Tax % </label>
                                    <input type="number" step="any" min="0" step="any" class="form-control"
                                        name="tax_percentage" value="" placeholder="Discount Amount" id="TaxPercentage">

                                    @error('tax_percentage')
                                    <span class="text-danger tax_percentage_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-4 mb-3">
                                    <label class="font_bold" for="TaxAmount"> Tax Amount </label>
                                    <input type="number" step="any" min="0" step="any" class="form-control"
                                        name="tax_amount" value="" placeholder="Discount Amount" id="TaxAmount">

                                    @error('tax_amount')
                                    <span class="text-danger tax_amount_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-4 mb-3">
                                    <label class="font_bold" for="DiscountPercentage"> Discount Percentage % </label>
                                    <input type="number" step="any" min="0" step="any" class="form-control"
                                        name="discount_percentage" value="" placeholder="Discount Percentage"
                                        id="DiscountPercentage">

                                    @error('discount_percentage')
                                    <span class="text-danger discount_percentage_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-4 mb-3">
                                    <label class="font_bold" for="WarrantyPeriod"> Warranty Period </label>
                                    <input type="text" class="form-control" name="warranty_period" value=""
                                        placeholder="Discount Percentage" id="WarrantyPeriod">

                                    @error('warranty_period')
                                    <span class="text-danger warranty_period_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row border border-solid mx-0">
                                <div class="col-2 mb-2">
                                    <label class="font_bold" for=""> Is sale on TP: </label>
                                    <div class="form-check mb-2 mt-1 form-check-inline">
                                        <input class="form-check-input"
                                            style="width: 1.7em !important; height: 1.7em !important;" type="checkbox"
                                            name="is_sale_on_tp" value="1" id="VendorCheckBox">
                                    </div>
                                    <span class="text-danger name_error"></span>
                                </div>
                                <div class="col-2 mb-2">
                                    <label class="font_bold" for=""> Is Clamable: </label>
                                    <div class="form-check mb-2 mt-1 form-check-inline">
                                        <input class="form-check-input"
                                            style="width: 1.7em !important; height: 1.7em !important;" type="checkbox"
                                            name="is_claimable" value="1" id="ClaimableCheckBox">
                                    </div>
                                    <span class="text-danger name_error"></span>
                                </div>
                                <div class="col-2 mb-2">
                                    <label class="font_bold" for=""> Is Fridged: </label>
                                    <div class="form-check mb-2 mt-1 form-check-inline">
                                        <input class="form-check-input"
                                            style="width: 1.7em !important; height: 1.7em !important;" type="checkbox"
                                            name="is_fridged" value="1" id="FridgedCheckBox">
                                    </div>
                                    <span class="text-danger name_error"></span>
                                </div>
                                <div class="col-2 mb-2">
                                    <label class="font_bold" for=""> Is Narcotic: </label>
                                    <div class="form-check mb-2 mt-1 form-check-inline">
                                        <input class="form-check-input"
                                            style="width: 1.7em !important; height: 1.7em !important;" type="checkbox"
                                            name="is_narcotic" value="1" id="NarcoticCheckBox">
                                    </div>
                                    <span class="text-danger"></span>
                                </div>
                                <div class="col-3 mb-2">
                                    <label class="font_bold" for=""> Is Un-Waranted: </label> <br>
                                    <div class="form-check mb-2 mt-1 form-check-inline">
                                        <input class="form-check-input"
                                            style="width: 1.7em !important; height: 1.7em !important;" type="checkbox"
                                            name="is_unwaranted" value="1" id="UnwarantedCheckBox">
                                    </div>
                                    <span class="text-danger"></span>
                                </div>
                            </div>


                            <div class="row mt-3">
                                <div class="col-4 mb-2">
                                    <label class="font_bold" for="ProductPicture"> Product Picture </label>
                                    <input type="file" name="product_picture" class="form-control" id="ProductPicture">

                                    @error('product_picture')
                                    <span class="text-danger product_picture_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-4 mb-2">
                                    <label class="font_bold" for="InvoicePicture"> Invoice Picture </label>
                                    <input type="file" name="invoice_picture" class="form-control" id="InvoicePicture">

                                    @error('invoice_picture')
                                    <span class="text-danger invoice_picture_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row mt-2">
                        <div class="col-sm-2 offset-sm-10">
                            <button type="submit" id="sub" class="btn btn-secondary AddUpdate float-right">
                                Submit
                            </button>
                            <button class="btn btn-danger ModalClosed">
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div> <!-- end card body-->
    </div> <!-- end card -->
</div>

@endsection

@section('custom_scripts')
<script>
    $(function() {
    });
</script>
@endsection