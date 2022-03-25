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

                <form method="post" action="{{ route('productpurchases.store') }}" enctype="multipart/form-data"
                    id="ProductPurchaseForm" class="form_loader" autocomplete="off">
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
                                <div class="col-12 mb-2">
                                    <label class="font_bold" for="CompanySelect"> Select Company* </label>
                                    <select name="company_id" required id="CompanySelect" class="form-control mySelect"
                                        data-toggle="select2" data-width="100%" id="">
                                        <option value=""> Select company </option>
                                        @forelse ($companies as $item)
                                        <option value="{{ $item?->id }}"> {{ $item?->company_name }} </option>
                                        @empty
                                        @endforelse
                                    </select>
                                    @error('company_id')
                                    <span class="text-danger company_id_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="font_bold" for="CategorySelect"> Select Category* </label>
                                    <select name="product_category_id" required id="CategorySelect"
                                        class="form-control mySelect" data-toggle="select2" data-width="100%" id="">
                                        <option value=""> Select category</option>
                                        @forelse ($categories as $item)
                                        <option value="{{ $item?->id }}"> {{ $item?->name }} </option>
                                        @empty
                                        @endforelse
                                    </select>
                                    @error('product_category_id')
                                    <span class="text-danger product_category_id_error"> {{ $message }} </span>
                                    @enderror

                                    <span class="text-danger" id="CompanySelectError"></span>
                                </div>
                                <div class="col-6 mb-2 pe-0">
                                    <label class="font_bold" for="ExpiryDate"> Expiry Date </label>
                                    <input type="date" class="form-control" name="expiry_date" value=""
                                        placeholder="Purchase date" id="ExpiryDate">

                                    @error('expiry_date')
                                    <span class="text-danger expiry_date_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-6 mb-3 ps-1">
                                    <label class="font_bold" for="PurchaseDate"> Purchase Date </label>
                                    <input type="date" class="form-control" name="purchase_date"
                                        value="{{ today()->format('Y-m-d')}}" placeholder="Purchase date"
                                        id="PurchaseDate">

                                    @error('purchase_date')
                                    <span class="text-danger purchase_date_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                            </div>
                        </div>
                        <div class="col-8 border border-2">
                            <span id="TotalProductList">
                                <div id="row-0" class="row mt-2 border border-3 p-1 mx-1 mb-2">

                                    <div class="col-6 mb-2">
                                        <label class="font_bold" for="SelectProduct"> Select Product * </label>
                                        <a href="javascript:void(0);" class="ViewProductDetail" id="ViewProductDetail-0"
                                            style="float: right !important; display: none;"> View Product
                                            Detail </a>
                                        <select name="product_id" required id="SelectProduct"
                                            class="form-control mySelect SelectProduct" data-toggle="select2"
                                            data-width="100%">
                                            <option value=""> Select product</option>
                                        </select>
                                        @error('product_id')
                                        <span class="text-danger product_id_error"> {{ $message }} </span>
                                        @enderror
                                    </div>

                                    <div class="col-sm-3 mb-2 px-1">
                                        <label for="ProductCode" class="font_bold"> Product Code* </label>
                                        <input type="text" placeholder="Product Code" name="product_code"
                                            class="form-control"
                                            value="{{ $pruchase?->product_code ?? old('product_code') }}" readonly
                                            id="ProductCode">
                                        @error('product_code')
                                        <span class="text-danger product_code_error"> {{ $message }} </span>
                                        @enderror
                                    </div>

                                    <div class="col-3 mb-1 px-1">
                                        <label class="font_bold" for="ProductPrice"> Product Price </label>
                                        <input type="number" min="0" step="any" class="form-control"
                                            name="product_price" readonly placeholder="Product Price" id="ProductPrice">
                                        @error('product_price')
                                        <span class="text-danger product_price_error">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-3 mb-2 px-1">
                                        <label class="font_bold" for="ProductQuantity"> Quantity </label>
                                        <input type="number" min="0" class="form-control" name="quantity"
                                            placeholder="Quantity" id="ProductQuantity">

                                        @error('quantity')
                                        <span class="text-danger quantity_error"> {{ $message }} </span>
                                        @enderror
                                    </div>

                                    <div class="col-3 mb-2 px-1">
                                        <label class="font_bold" for="BonusQuantity"> Bonus Qty </label>
                                        <input type="number" value="0" min="0" class="form-control"
                                            name="bonus_quantity" placeholder="Bonus qty" id="BonusQuantity">
                                        @error('bonus_quantity')
                                        <span class="text-danger bonus_quantity_error"> {{ $message }} </span>
                                        @enderror
                                    </div>

                                    {{-- <div class="col-2 mb-2  px-1">
                                        <label class="font_bold" for="PurchasePrice"> Purchase Price</label>
                                        <input type="number" step="any" min="0" class="form-control"
                                            name="purchase_price" placeholder="Purchase Price" id="PurchasePrice">

                                        @error('purchase_price')
                                        <span class="text-danger purchase_price_error"> {{ $message }} </span>
                                        @enderror
                                    </div> --}}

                                    <div class="col-3 mb-2 px-1">
                                        <label class="font_bold" for="TotalPrice"> Total Price</label>
                                        <input type="number" step="any" min="0" class="form-control" name="total_price"
                                            placeholder="Total Price" readonly id="TotalPrice">

                                        @error('total_price')
                                        <span class="text-danger total_price_error"> {{ $message }} </span>
                                        @enderror

                                        <span class="text-danger h5" id="QtyPriceError"> </span>
                                    </div>

                                    <div class="col-3 mb-2 px-1">
                                        <label class="font_bold" for="DiscountPercentage"> Discount % </label>
                                        <input type="number" step="any" min="0" step="any" class="form-control"
                                            name="discount_percentage" value="0" placeholder="Discount %"
                                            id="DiscountPercentage">

                                        @error('discount_percentage')
                                        <span class="text-danger discount_percentage_error"> {{ $message }} </span>
                                        @enderror
                                    </div>

                                    <div class="col-3 mb-2 px-1">
                                        <label class="font_bold" for="DiscountAmount"> Discount Amount</label>
                                        <input type="number" step="any" min="0" step="any" class="form-control"
                                            name="discount_amount" value="0" placeholder="Discount Amount"
                                            id="DiscountAmount">

                                        @error('discount_amount')
                                        <span class="text-danger discount_amount_error"> {{ $message }} </span>
                                        @enderror
                                    </div>
                                    <div class="col-3 mb-2 px-1">
                                        <label class="font_bold" for="TaxAmount"> Tax Amount </label>
                                        <input type="number" step="any" min="0" step="any" class="form-control"
                                            name="tax_amount" value="" placeholder="Tax Amount" id="TaxAmount"
                                            value="0">

                                        @error('tax_amount')
                                        <span class="text-danger tax_amount_error"> {{ $message }} </span>
                                        @enderror
                                    </div>

                                    <div class="col-3 mb-2 px-1">
                                        <label class="font_bold" for="TaxPercentage"> Tax % </label>
                                        <input type="number" step="any" min="0" step="any" class="form-control"
                                            name="tax_percentage" value="0" placeholder="Tax percentage"
                                            id="TaxPercentage">

                                        @error('tax_percentage')
                                        <span class="text-danger tax_percentage_error"> {{ $message }} </span>
                                        @enderror
                                    </div>

                                    <div class="col-3 mb-2 px-1">
                                        <label class="font_bold" for="FinalPrice"> Final Price </label>
                                        <input type="number" min="0" step="any" class="form-control" name="final_price"
                                            readonly placeholder="Final price" id="FinalPrice">

                                        @error('final_price')
                                        <span class="text-danger final_price_error"> {{ $message }} </span>
                                        @enderror
                                    </div>

                                    {{-- <div class="col-4 mb-3">
                                        <label class="font_bold" for="WarrantyPeriod"> Warranty Period </label>
                                        <input type="text" class="form-control" name="warranty_period" value=""
                                            placeholder="Warranty period" id="WarrantyPeriod">

                                        @error('warranty_period')
                                        <span class="text-danger warranty_period_error"> {{ $message }} </span>
                                        @enderror
                                    </div> --}}
                                </div>
                            </span>

                            <div class="row mt-2">
                                <div class="col-sm-4 offset-sm-3 text-end">
                                    <span class="btn btn-secondary" id="AddProductButton">
                                        <i class="fa fa-plus"></i> Add More
                                    </span>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-4 mb-2">
                                    <label class="font_bold" for="InvoicePicture"> Invoice Picture </label>
                                    <input type="file" name="invoice_picture" class="form-control" id="InvoicePicture">

                                    @error('invoice_picture')
                                    <span class="text-danger invoice_picture_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-4 mb-2">
                                    <label class="font_bold" for="Description"> Description </label>
                                    <input type="text" name="description" class="form-control" placeholder="Description"
                                        id="Description">

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

<div id="ProductDetail" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ProductDetailLabel"
    aria-hidden="true">
    <div class="modal-dialog align-center">
        <div class="modal-content" id="ProductDetailModalBody">
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
@endsection

@section('custom_scripts')
<script>
    $(function() {
        var product_list = {};
        $("#CategorySelect").change(function(){
            let company_id = parseInt($('#CompanySelect').val()) || 0
            if(company_id < 1){
                $('#CompanySelectError').html("Company field is required, For Product Selections")
            }else{
                $('#CompanySelectError').html("")
                let category_id = parseInt($(this).val())
                let url_addr = "{{ url('productManagement/productfilter')}}/"+company_id+"/cat/"+category_id;
                $.get(url_addr , function(response, status){
                    product_list = response.data;
                    var products = response.data;
                    if (products != '')
                    {
                        for (i in products) {                        
                            $('.SelectProduct').append('<option value='+products[i]?.id+'>'+products[i]?.product_name+' ['+ products[i]?.product_code +']</option>');
                        }
                    }else{
                        $('#SelectProduct').append('<option value="">No Data Found </option>');
                    }
                });
            } 
        });

        

        $("#AddProductButton").click(function(){
            var products = product_list;
            var Sub = ''

            console.log(products)
            if (products != '')
            {
                for (i in products) {                        
                   Sub += '<option value='+products[i]?.id+'>'+products[i]?.product_name+' ['+ products[i]?.product_code +']</option>';
                }
            }else{
                Sub = '<option value="">No Data Found </option>';
            }
            var html_code = `
                <div id="row-1" class="row mt-2 border border-3 p-1 mx-1 mb-2">
                    <div class="col-6 mb-2">
                        <label class="font_bold" for="SelectProduct1"> Select Product * </label>
                        <a href="javascript:void(0);" id="ViewProductDetail-1"
                            style="float: right !important; display: none;"> View Product
                            Detail </a>
                        <select name="product_id" required id="SelectProduct1"
                            class="form-control mySelect SelectProduct" data-toggle="select2" data-width="100%">
                           ${Sub}  
                        </select>
                        @error('product_id')
                        <span class="text-danger product_id_error"> {{ $message }} </span>
                        @enderror
                    </div>

                    <div class="col-sm-3 mb-2 px-1">
                        <label for="ProductCode" class="font_bold"> Product Code* </label>
                        <input type="text" placeholder="Product Code" name="product_code"
                            class="form-control"
                            value="{{ $pruchase?->product_code ?? old('product_code') }}" readonly
                            id="ProductCode">
                        @error('product_code')
                        <span class="text-danger product_code_error"> {{ $message }} </span>
                        @enderror
                    </div>

                    <div class="col-3 mb-1 px-1">
                        <label class="font_bold" for="ProductPrice"> Product Price </label>
                        <input type="number" min="0" step="any" class="form-control"
                            name="product_price" readonly placeholder="Product Price" id="ProductPrice">
                        @error('product_price')
                        <span class="text-danger product_price_error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-3 mb-2 px-1">
                        <label class="font_bold" for="ProductQuantity"> Quantity </label>
                        <input type="number" min="0" class="form-control" name="quantity"
                            placeholder="Quantity" id="ProductQuantity">

                        @error('quantity')
                        <span class="text-danger quantity_error"> {{ $message }} </span>
                        @enderror
                    </div>
                    <div class="col-3 mb-2 px-1">
                        <label class="font_bold" for="BonusQuantity"> Bonus Qty </label>
                        <input type="number" min="0" class="form-control" name="bonus_quantity"
                            placeholder="Bonus qty" id="BonusQuantity">
                        @error('bonus_quantity')
                        <span class="text-danger bonus_quantity_error"> {{ $message }} </span>
                        @enderror
                    </div>
                    <div class="col-3 mb-2 px-1">
                        <label class="font_bold" for="TotalPrice"> Total Price</label>
                        <input type="number" step="any" min="0" class="form-control" name="total_price"
                            placeholder="Total Price" readonly id="TotalPrice">

                        @error('total_price')
                        <span class="text-danger total_price_error"> {{ $message }} </span>
                        @enderror

                        <span class="text-danger h5" id="QtyPriceError"> </span>
                    </div>
                    <div class="col-3 mb-2 px-1">
                        <label class="font_bold" for="DiscountPercentage"> Discount % </label>
                        <input type="number" step="any" min="0" step="any" class="form-control"
                            name="discount_percentage" value="" placeholder="Discount %"
                            id="DiscountPercentage">

                        @error('discount_percentage')
                        <span class="text-danger discount_percentage_error"> {{ $message }} </span>
                        @enderror
                    </div>
                    <div class="col-3 mb-2 px-1">
                        <label class="font_bold" for="DiscountAmount"> Discount Amount</label>
                        <input type="number" step="any" min="0" step="any" class="form-control"
                            name="discount_amount" value="" placeholder="Discount Amount"
                            id="DiscountAmount">

                        @error('discount_amount')
                        <span class="text-danger discount_amount_error"> {{ $message }} </span>
                        @enderror
                    </div>
                    <div class="col-3 mb-2 px-1">
                        <label class="font_bold" for="TaxAmount"> Tax Amount </label>
                        <input type="number" step="any" min="0" step="any" class="form-control"
                            name="tax_amount" value="" placeholder="Tax Amount" id="TaxAmount">

                        @error('tax_amount')
                        <span class="text-danger tax_amount_error"> {{ $message }} </span>
                        @enderror
                    </div>
                    <div class="col-3 mb-2 px-1">
                        <label class="font_bold" for="TaxPercentage"> Tax % </label>
                        <input type="number" step="any" min="0" step="any" class="form-control"
                            name="tax_percentage" value="" placeholder="Tax percentage"
                            id="TaxPercentage">

                        @error('tax_percentage')
                        <span class="text-danger tax_percentage_error"> {{ $message }} </span>
                        @enderror
                    </div>
                    <div class="col-3 mb-2 px-1">
                        <label class="font_bold" for="FinalPrice"> Final Price </label>
                        <input type="number" min="0" step="any" class="form-control" name="final_price"
                            readonly placeholder="Final price" id="FinalPrice">

                        @error('final_price')
                        <span class="text-danger final_price_error"> {{ $message }} </span>
                        @enderror
                    </div>
                </div>`;
            $('#TotalProductList').append(html_code);
        });

        function dateFormat(date, format) {
            // Calculate date parts and replace instances in format string accordingly
            format = format.replace("d", (date.getDate() < 10 ? '0' : '') + date.getDate()); // Pad with '0' if needed
            format = format.replace("m", (date.getMonth() < 9 ? '0' : '') + (date.getMonth() + 1)); // Months are zero-based
            format = format.replace("Y", date.getFullYear());
            return format;
        }

        $(".SelectProduct").change(function(){
            let product_id = parseInt($(this).val())
            var single_product = product_list?.find(x => x.id === product_id);
            // alert(product_id);
            console.log(single_product)
            $('#ProductCode').val(single_product?.product_code)
            $('#ProductPrice').val(single_product?.purchase_price)

            $('#ViewProductDetail-0').show()
            $('#ViewProductDetail-0').attr('ProductId', product_id)

            $('#ViewProductDetail-0').attr('title', 'Click to view Detail')
            $('#ViewProductDetail-0').attr('ProductId', product_id)

            if(single_product?.remaining_quantity > 0){
                $('#ProductQuantity').attr('max', single_product?.remaining_quantity)
            }else{
                $('#ProductQuantity').attr('max', single_product?.max_inventory_level)
            }
            if(single_product?.purchase_price > 0){
                $('#PurchasePrice').val(single_product?.purchase_price)
                $('#PurchasePrice').attr('max', single_product?.purchase_price)
            }
            
            if(single_product?.warranty_period > 0){

                // alert(single_product?.expiry_date_value)

                $('#ExpiryDate').val(dateFormat(new Date(single_product?.expiry_date_value), "Y-m-d"))
                
            }

            if(single_product?.tax_amount > 0){
                $('#TaxAmount').val(single_product?.tax_amount)
                $('#TaxAmount').attr('max', single_product?.tax_amount)
            }
            if(single_product?.tax_percentage > 0){
                $('#TaxPercentage').val(single_product?.tax_percentage)
                $('#TaxPercentage').attr('max', single_product?.tax_percentage)
            }
            if(single_product?.discount_amount > 0){
                $('#DiscountAmount').val(single_product?.discount_amount)
                $('#DiscountAmount').attr('max', single_product?.discount_amount)
            }
            if(single_product?.discount_percentage > 0){
                $('#DiscountPercentage').val(single_product?.discount_percentage)
                $('#DiscountPercentage').attr('max', single_product?.discount_percentage)
            }
            // let url_addr = "{{ url('productManagement/productfilter')}}/"+company_id+"/cat/"+category_id;
            // $.get(url_addr , function(response, status){
            //     var products = response.data;
            //     if (products != '')
            //     {
            //         for (i in products) {                        
            //             $("#SelectProduct").append("<option value="+products[i]?.id+">"+products[i]?.product_name+"</option>");
            //         }
            //     }else{
            //         $("#SelectProduct").append("<option value=''>No Data Found </option>");
            //     }
            // });
        });

        $(".ViewProductDetail").click(function(){
            let product_id = parseInt($(this).attr('ProductId'))
            $("#ProductDetail").modal('show')
            let url_addr = "{{ url('productManagement/products')}}/"+product_id;
            $.get(url_addr , function(response, status){
                var single_product = response.html_data;
                if (single_product != '')
                {
                    $("#ProductDetailModalBody").html(single_product)
                }else{
                    $("#ProductDetailModalBody").html("No Data Found")
                }
            });
        });

        $('#ProductQuantity').on('keyup', function() {
            let Quantity = Number($(this).val());
            let PurchasePrice = parseFloat($('#PurchasePrice').val())
            let discountAmount = parseFloat($('#DiscountAmount').val()) || 0
            let TaxAmount = Number($('#TaxAmount').val()) || 0
            if (Quantity > 0 && PurchasePrice > 0) {
                $("#QtyPriceError").html('');
                $("#QtyPriceError").hide();
                var priceWithOutDiscount = Quantity*PurchasePrice;
                $("#TotalPrice").val(priceWithOutDiscount);
                var Fprice = priceWithOutDiscount;
                if(discountAmount > 0){
                    Fprice = Fprice-discountAmount;
                }
                if(TaxAmount > 0){
                    Fprice = Fprice+TaxAmount;
                }
                $("#FinalPrice").val(Fprice);
            } else {
                $("#QtyPriceError").show();
                $("#QtyPriceError").html('Please add Quantity and Price for total price');
                $("#TotalPrice").val('');
                $("#FinalPrice").val('');
            }
        });


        $('#PurchasePrice').on('keyup', function() {
            let ProductQuantity = Number($('#ProductQuantity').val())
            let PurchasePrice = parseFloat($('#PurchasePrice').val())

            if (ProductQuantity > 0 && PurchasePrice > 0) {
                $("#QtyPriceError").html('');
                $("#QtyPriceError").hide();
                var priceWithOutDiscount = ProductQuantity*PurchasePrice;
                $("#TotalPrice").val(priceWithOutDiscount);
                $("#FinalPrice").val(priceWithOutDiscount);
            } else {
                $("#QtyPriceError").show();
                $("#QtyPriceError").html('Please add Quantity and Price for total price');
                $("#TotalPrice").val('');
                $("#FinalPrice").val('');
            }
        });

        $('#DiscountAmount').on('keyup', function() {
            let total_price = Number($('#TotalPrice').val())
            let TaxAmount = Number($('#TaxAmount').val()) || 0
            let discountAmount = parseFloat($(this).val());
            let priceWithOutDiscount = 0
            if(discountAmount > 0){
                if (total_price > 0) {
                    $("#QtyPriceError").html('');
                    $("#QtyPriceError").hide();
                    priceWithOutDiscount = total_price;
                    let discount_percent = (discountAmount / priceWithOutDiscount) * 100;
                    if(discountAmount >= priceWithOutDiscount){
                        $(".discount_amount_error").html('Discount Amount Must be less than total amount');
                    }else{
                        $(".discount_amount_error").html('');
                        $("#DiscountPercentage").val(discount_percent.toFixed(2));
                        let pricewithdiscount = priceWithOutDiscount - discountAmount;
                        $("#FinalPrice").val(pricewithdiscount + TaxAmount);
                    }
                } else {
                    $("#QtyPriceError").show();
                    $("#QtyPriceError").html('Price and Quantity is required');
                    $("#FinalPrice").val(total_price+TaxAmount);
                }
            }else{
                $("#DiscountPercentage").val('');
                $("#FinalPrice").val(total_price+TaxAmount);
            }
        });
        
        $('#DiscountPercentage').on('keyup', function() {
            let total_price = Number($('#TotalPrice').val())
            let discountPercentage = parseFloat($(this).val()) || 0
            let TaxAmount = Number($('#TaxAmount').val()) || 0
            let priceWithOutDiscount = 0
            $(".discount_percentage_error").html('')
            if(discountPercentage > 0 && discountPercentage < 100){
                if (total_price > 0) {
                    $("#QtyPriceError").html('');
                    $("#QtyPriceError").hide();
                    priceWithOutDiscount = total_price;
                    let discount_amt = (discountPercentage /100 ) * priceWithOutDiscount;
                    $("#DiscountAmount").val(discount_amt.toFixed(2));
                    let pricewithdiscount = priceWithOutDiscount - discount_amt;
                    $("#FinalPrice").val(pricewithdiscount + TaxAmount);
                } else {
                    $("#QtyPriceError").show();
                    $("#QtyPriceError").html('Price and Quantity is required');
                    $("#FinalPrice").val(total_price+TaxAmount);
                }
            }else{
                $("#DiscountAmount").val('');
                $("#FinalPrice").val(total_price+TaxAmount);
                $(".discount_percentage_error").html('Discount Percentage Must be less than 100 (Total Amount)');
            }
        });

        $('#TaxAmount').on('keyup', function() {
            let TotalPrice = parseFloat($('#TotalPrice').val())
            let DiscountAmount = parseFloat($('#DiscountAmount').val())
            let taxAmount = parseFloat($(this).val());
            let priceWithOutDiscount = 0
            if(taxAmount > 0){
                if (TotalPrice > 0) {
                    $("#TotalPriceError").html('');
                    $("#TotalPriceError").hide();
                    let taxt_percent = (taxAmount / TotalPrice) * 100;
                    $(".tax_amount_error").html('');
                    $("#TaxPercentage").val(taxt_percent.toFixed(2));
                    let priceWithTax = TotalPrice + taxAmount;
                    $("#FinalPrice").val(priceWithTax-DiscountAmount);
                } else {
                    $("#TotalPriceError").show();
                    $("#TotalPriceError").html('Total Price is required');
                    $("#FinalPrice").val(TotalPrice - DiscountAmount);
                }
            }else{
                $("#TaxPercentage").val('');
                $("#FinalPrice").val(TotalPrice - DiscountAmount);
            }
        });
        
        $('#TaxPercentage').on('keyup', function() {
            let Total_Price = parseFloat($('#TotalPrice').val())
            let DiscountAmount = parseFloat($('#DiscountAmount').val())

            let taxPercentage = parseFloat($(this).val()) || 0;
            let priceWithOutTax = 0
            $(".tax_percentage_error").html('');
            if(taxPercentage > 0){
                if (Total_Price > 0) {
                    $("#TotalPriceError").html('');
                    $("#TotalPriceError").hide();
                    priceWithOutTax = Total_Price;
                    let tax_amt = (taxPercentage /100 ) * priceWithOutTax;
                    $("#TaxAmount").val(tax_amt.toFixed(2));

                    let priceWithTax = priceWithOutTax + tax_amt;
                    $("#FinalPrice").val(priceWithTax-DiscountAmount);
                } else {
                    $("#TotalPriceError").show();
                    $("#FinalPrice").val(Total_Price - DiscountAmount);
                    $("#TotalPriceError").html('Total Price is required');
                }
            }else{
                $("#TaxAmount").val('');
                $("#FinalPrice").val(Total_Price - DiscountAmount);
            }
        });

        $('.ModalClosed').click(function () {
            $('.modal').modal('hide');
        });
    });
</script>
@endsection