@php
$load_css = Array('select2', 'sweetAlert');
$load_js = Array('tippy','select2', 'sweetAlert')
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
                        <li class="breadcrumb-item"> <a href="{{ route('products.index') }}"> ProductManagement </a>
                        </li>
                        <li class="breadcrumb-item"> <a href="{{ route('products.index') }}"> Products </a>
                        </li>
                    </ol>
                </div>
                <h4 class="page-title">ProductManagement</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4>Add Product Entry </h4>
                        </div>

                        <div class="col-6 align-self-end text-end mb-2">
                            <a class="btn btn-secondary btn-sm" href="{{ route('products.index') }}"
                                title="Click to go back" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-arrow-left"></i>
                                Back
                            </a>
                        </div>

                        <form method="post" action="{{ ($product->id) ? route('products.update', $product->id ) :
                            route('products.store') }}" enctype="multipart/form-data" id="ProductForm"
                            class="form_loader" autocomplete="off">
                            @csrf
                            @php
                            $required = 'required';
                            if($product->id){
                            $company_id = $product->company_id;
                            $required = '';
                            }
                            @endphp

                            @if($product->id)
                            @method('PUT')
                            @endif

                            <div class="row">
                                <div class="col-4 border border-2">
                                    <div class="row mt-2">
                                        <div class="col-12 mb-3">
                                            <label class="font_bold" for="CompanySelect"> Select Company* </label>
                                            <select name="company_id" required id="CompanySelect"
                                                class="form-control mySelect" data-toggle="select2" data-width="100%">
                                                <option value=""> Select company</option>
                                                @forelse ($companies as $item)
                                                <option value="{{ $item->id }}">{{ $item->company_name }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                            @error('company_id')
                                            <span class="text-danger company_id_error"> {{ $message }} </span>
                                            @enderror
                                        </div>

                                        <div class="col-12 mb-3">
                                            <label class="font_bold" for="CategorySelect"> Select Category* </label>
                                            <div class="input-group">
                                                <select name="product_category_id" required id="CategorySelect"
                                                    class="form-control mySelect" data-toggle="select2"
                                                    data-width="88%">
                                                    <option value=""> Select category</option>
                                                    @forelse ($product_categories as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                    @empty
                                                    @endforelse
                                                </select>
                                                <a href="#"
                                                    class="btn input-group-text btn-dark waves-effect waves-light OpenaddTypeModal"
                                                    SelectBoxId="CategorySelect" TableName="product_categories"
                                                    title="Click to add new" type="button"> <i
                                                        class="fa fa-plus"></i></a>
                                            </div>

                                            @error('product_category_id')
                                            <span class="text-danger product_category_id_error"> {{ $message }} </span>
                                            @enderror
                                        </div>

                                        <div class="col-sm-12 mb-3">
                                            <label for="ProductName" class="font_bold"> Product Name* </label>
                                            <input type="text" placeholder="Product Name" name="product_name"
                                                class="form-control"
                                                value="{{ $product?->product_name ?? old('product_name') }}"
                                                id="ProductName" required>

                                            @error('product_name')
                                            <span class="text-danger product_name_error"> {{ $message }} </span>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3 pe-0">
                                            <label for="BatchNumber" class="font_bold"> Batch Number </label>
                                            <input type="text" placeholder="Batch Number" name="batch_number"
                                                class="form-control"
                                                value="{{ $product?->batch_number ?? old('batch_number') }}"
                                                id="BatchNumber" required>

                                            @error('batch_number')
                                            <span class="text-danger batch_number_error"> {{ $message }} </span>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="SerialNumber" class="font_bold"> Serial Number </label>
                                            <input type="text" placeholder="Serial number" name="serial_number"
                                                class="form-control"
                                                value="{{ $product?->serial_number ?? old('serial_number') }}"
                                                id="serialNumber" required>

                                            @error('serial_number')
                                            <span class="text-danger serial_number_error"> {{ $message }} </span>
                                            @enderror
                                        </div>

                                        <div class="col-sm-12 col-md-12 mb-3">
                                            <label class="font_bold" for="ProductType"> Product Type* </label>
                                            <select name="product_type" required id="ProductType"
                                                class="form-control mySelect" data-toggle="select2" data-width="100%"
                                                id="">
                                                <option value=""> Select Type</option>
                                                <option value="import">Import</option>
                                                <option value="export">Export</option>
                                                <option value="local">Local</option>
                                                <option value="other">Other</option>
                                            </select>
                                            @error('product_type')
                                            <span class="text-danger product_type_error"> {{ $message }} </span>
                                            @enderror
                                        </div>

                                        <div class="col-sm-12 col-md-12 mb-3">
                                            <label class="font_bold" for="VaccinationGroup"> Vaccination Group </label>

                                            <div class="input-group">
                                                <select name="vaccination_group" required id="VaccinationGroup"
                                                    class="form-control mySelect" data-toggle="select2"
                                                    data-width="89%">
                                                    <option value=""> Select Group</option>
                                                    @forelse ($vaccination_groups as $item)
                                                    <option value="{{ $item->id }}"> {{ $item->name }} </option>
                                                    @empty
                                                    @endforelse
                                                </select>

                                                <a href="#"
                                                    class="btn input-group-text btn-dark waves-effect waves-light OpenaddTypeModal"
                                                    SelectBoxId="VaccinationGroup" TableName="vaccination_groups"
                                                    title="Click to add new" type="button"> <i class="fa fa-plus"></i>
                                                </a>
                                            </div>

                                            @error('vaccination_group')
                                            <span class="text-danger vaccination_group_error"> {{ $message }} </span>
                                            @enderror
                                        </div>

                                        <div class="col-sm-12 col-md-6 mb-3">
                                            <label class="font_bold" for="PackSizeUnit"> Pack Size(units) </label>
                                            <input type="number" step="any" min="0" class="form-control"
                                                name="pack_size_unit" value="" placeholder="Pack size in unit"
                                                id="PackSizeUnit">

                                            @error('pack_size_unit')
                                            <span class="text-danger pack_size_unit_error"> {{ $message }} </span>
                                            @enderror
                                        </div>

                                        <div class="col-sm-12 col-md-6 mb-3 ps-0">
                                            <label class="font_bold" for="PackSizeUnitType"> Select Unit type </label>
                                            <select name="pack_size_unit_type" required id="PackSizeUnitType"
                                                class="form-control mySelect" data-toggle="select2" data-width="100%"
                                                id="">
                                                <option value=""> Select Type</option>
                                                <option value="gram">Gram</option>
                                                <option value="kilo_gram">Kg</option>
                                            </select>
                                            @error('pack_size_unit_type')
                                            <span class="text-danger pack_size_unit_type_error"> {{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-8 border border-2">
                                    <div class="row mt-2">
                                        <div class="col-6 mb-3">
                                            <label class="font_bold" for="StoresSelect"> Select Store* </label>
                                            <div class="input-group">
                                                <select name="store_id" required id="StoresSelect"
                                                    class="form-control mySelect" data-toggle="select2"
                                                    data-width="88%">
                                                    <option value="" selected> Select Store</option>
                                                    @forelse ($product_stores as $store)
                                                    <option value="{{ $store->id }}"> {{ $store->store_name }} </option>
                                                    @empty
                                                    @endforelse
                                                </select>

                                                <a href="#" class="btn input-group-text btn-dark OpenAddStoreModal"
                                                    title="Click to add new" type="button">
                                                    <i class="fa fa-plus"></i>
                                                </a>
                                            </div>

                                            @error('store_id')
                                            <span class="text-danger store_id_error"> {{ $message }} </span>
                                            @enderror
                                        </div>

                                        <div class="col-2 mb-3 ps-0">
                                            <label class="font_bold" for="RackNumber"> Rack Number </label>
                                            <input type="number" min="0" class="form-control" name="rack_number"
                                                placeholder="Rack number" id="RackNumber">

                                            @error('rack_number')
                                            <span class="text-danger rack_number_error"> {{ $message }} </span>
                                            @enderror
                                        </div>

                                        <div class="col-4 mb-3">
                                            <label class="font_bold" for="InventoryLevel"> Inventory Level </label>
                                            <div class="row">
                                                <div class="col-6">
                                                    <input type="number" min="0" class="form-control" name="min_level"
                                                        placeholder="Min level" id="MinLevel">
                                                </div>
                                                <div class="col-6">
                                                    <input type="number" min="0" class="form-control" name="max_level"
                                                        placeholder="Max level" id="MaxLevel">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-4 mb-3">
                                            <label class="font_bold" for="MrpPrice"> MRP Price</label>
                                            <input type="number" step="any" min="0" class="form-control"
                                                name="mrp_price" placeholder="MRP Price" id="MrpPrice">

                                            @error('mrp_price')
                                            <span class="text-danger mrp_price_error"> {{ $message }} </span>
                                            @enderror
                                        </div>

                                        <div class="col-4 mb-3">
                                            <label class="font_bold" for="WholeSalePrice"> Whole Sale Price</label>
                                            <input type="number" step="any" min="0" class="form-control"
                                                name="whole_sale_price" value="" placeholder="MRP Price"
                                                id="WholeSalePrice">

                                            @error('whole_sale_price')
                                            <span class="text-danger whole_sale_price_error"> {{ $message }} </span>
                                            @enderror
                                        </div>

                                        <div class="col-4 mb-3">
                                            <label class="font_bold" for="FullLessPrice">Full Less Price</label>
                                            <input type="number" step="any" min="0" class="form-control"
                                                name="full_less_price" placeholder="Full less Price" id="FullLessPrice">

                                            @error('full_less_price')
                                            <span class="text-danger full_less_price_error"> {{ $message }} </span>
                                            @enderror
                                        </div>

                                        <div class="col-4 mb-3">
                                            <label class="font_bold" for="StorePrice">Store Price</label>
                                            <input type="number" step="any" min="0" class="form-control"
                                                name="store_price" placeholder="Store wise price" id="StorePrice">

                                            @error('store_price')
                                            <span class="text-danger store_price_error"> {{ $message }} </span>
                                            @enderror
                                        </div>

                                        <div class="col-4 mb-3">
                                            <label class="font_bold" for="RetailPrice"> Retail Price</label>
                                            <input type="number" step="any" min="0" class="form-control"
                                                name="retail_price" value="" placeholder="Retail Price"
                                                id="RetailPrice">

                                            @error('retail_price')
                                            <span class="text-danger retail_price_error"> {{ $message }} </span>
                                            @enderror
                                        </div>

                                        <div class="col-4 mb-3">
                                            <label class="font_bold" for="TradePrice"> Trade Price </label>
                                            <input type="number" step="any" min="0" class="form-control"
                                                name="trade_price" value="" placeholder="Trade Price" id="TradePrice">

                                            @error('trade_price')
                                            <span class="text-danger trade_price_error"> {{ $message }} </span>
                                            @enderror
                                        </div>

                                        <div class="col-4 mb-3">
                                            <label class="font_bold" for="PurchasePrice"> Purchase Price</label>
                                            <input type="number" step="any" min="0" class="form-control"
                                                name="purchase_price" value="" placeholder="Purchase Price"
                                                id="PurchasePrice">

                                            @error('purchase_price')
                                            <span class="text-danger purchase_price_error"> {{ $message }} </span>
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
                                                name="tax_percentage" value="" placeholder="Discount Amount"
                                                id="TaxPercentage">

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
                                            <label class="font_bold" for="DiscountPercentage"> Discount Percentage %
                                            </label>
                                            <input type="number" step="any" min="0" step="any" class="form-control"
                                                name="discount_percentage" value="" placeholder="Discount Percentage"
                                                id="DiscountPercentage">

                                            @error('discount_percentage')
                                            <span class="text-danger discount_percentage_error"> {{ $message }} </span>
                                            @enderror
                                        </div>

                                        <div class="col-4 mb-3">
                                            <label class="font_bold" for="WarrantyPeriod"> Warranty Period </label>
                                            <input type="text" class="form-control" name="warranty_period"
                                                placeholder="Discount Percentage" id="WarrantyPeriod">

                                            @error('warranty_period')
                                            <span class="text-danger warranty_period_error"> {{ $message }} </span>
                                            @enderror
                                        </div>
                                        <div class="col-4 mb-2">
                                            <label class="font_bold" for="ProductPicture"> Product Picture </label>
                                            <input type="file" name="product_picture" class="form-control"
                                                id="ProductPicture">

                                            @error('product_picture')
                                            <span class="text-danger product_picture_error"> {{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row border border-solid mx-0">
                                        <div class="col-1 mb-2 pe-0">
                                            <label class="font_bold" for=""> Is Tax: </label>
                                            <div class="form-check mb-2 mt-1 form-check-inline">
                                                <input class="form-check-input"
                                                    style="width: 1.7em !important; height: 1.7em !important;"
                                                    type="checkbox" name="is_taxable" value="1" id="TaxableCheckBox">
                                            </div>
                                            <span class="text-danger is_taxable_error"></span>
                                        </div>
                                        <div class="col-2 mb-2">
                                            <label class="font_bold" for=""> Is sale on TP: </label>
                                            <div class="form-check mb-2 mt-1 form-check-inline">
                                                <input class="form-check-input"
                                                    style="width: 1.7em !important; height: 1.7em !important;"
                                                    type="checkbox" name="is_sale_on_tp" value="1" id="VendorCheckBox">
                                            </div>
                                            <span class="text-danger is_sale_on_tp_error"></span>
                                        </div>
                                        <div class="col-2 mb-2">
                                            <label class="font_bold" for=""> Is Clamable: </label>
                                            <div class="form-check mb-2 mt-1 form-check-inline">
                                                <input class="form-check-input"
                                                    style="width: 1.7em !important; height: 1.7em !important;"
                                                    type="checkbox" name="is_claimable" value="1"
                                                    id="ClaimableCheckBox">
                                            </div>
                                            <span class="text-danger name_error"></span>
                                        </div>
                                        <div class="col-2 mb-2">
                                            <label class="font_bold" for=""> Is Fridged: </label>
                                            <div class="form-check mb-2 mt-1 form-check-inline">
                                                <input class="form-check-input"
                                                    style="width: 1.7em !important; height: 1.7em !important;"
                                                    type="checkbox" name="is_fridged" value="1" id="FridgedCheckBox">
                                            </div>
                                            <span class="text-danger name_error"></span>
                                        </div>
                                        <div class="col-2 mb-2">
                                            <label class="font_bold" for=""> Is Narcotic: </label>
                                            <div class="form-check mb-2 mt-1 form-check-inline">
                                                <input class="form-check-input"
                                                    style="width: 1.7em !important; height: 1.7em !important;"
                                                    type="checkbox" name="is_narcotic" value="1" id="NarcoticCheckBox">
                                            </div>
                                            <span class="text-danger"></span>
                                        </div>
                                        <div class="col-3 mb-2">
                                            <label class="font_bold" for=""> Is Un-Waranted: </label> <br>
                                            <div class="form-check mb-2 mt-1 form-check-inline">
                                                <input class="form-check-input"
                                                    style="width: 1.7em !important; height: 1.7em !important;"
                                                    type="checkbox" name="is_unwaranted" value="1"
                                                    id="UnwarantedCheckBox">
                                            </div>
                                            <span class="text-danger"></span>
                                        </div>
                                    </div>


                                    <div class="row mt-3">
                                        <div class="col-12 mb-2">
                                            <label class="font_bold" for="Description"> Description </label> <br>
                                            <textarea name="description" id="Description" style="width:100%" rows="5"
                                                placeholder="Description"></textarea>
                                            @error('description')
                                            <span class="text-danger description_error"> {{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row mt-2">
                                <div class="col-sm-2 offset-sm-10 text-end">
                                    <button type="submit" id="sub" class="btn btn-secondary AddUpdate">
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
        <!-- end col-->
    </div>
</div>

@include('layouts._partial.add_types_modal')
@include('productmanagement._addStoreModal')

@endsection

@section('custom_scripts')
<script>
</script>
@endsection