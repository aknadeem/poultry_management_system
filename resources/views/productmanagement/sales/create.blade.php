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
                        <li class="breadcrumb-item"> <a href="{{ route('productpurchases.index') }}"> Sale </a>
                        </li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
                <h4 class="page-title"> Product Sale </h4>
            </div>
        </div>
    </div>
    <div class="row card">
        <div class="col-12 card-body">
            <div class="row mb-2">
                <div class="col-6 align-self-start">
                    <h4> Add Sale Entry </h4>
                </div>
                <div class="col-6 align-self-end text-end mb-2">
                    <a class="btn btn-secondary btn-sm" href="{{ route('productpurchases.index') }}"
                        title="Click to go back" data-plugin="tippy" data-tippy-animation="scale"
                        data-tippy-arrow="true"><i class="fa fa-arrow-left"></i>
                        Back
                    </a>
                </div>

                <form method="post" action="{{ route('productsales.store') }}" enctype="multipart/form-data"
                    id="ProductSaleForm" class="form_loader" autocomplete="off">
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
                                    <label class="font_bold" for="DivisionSelect"> Select Divisions </label>
                                    <select name="division_id" required id="DivisionSelect"
                                        class="form-control mySelect" data-toggle="select2" data-width="100%">
                                        <option value=""> Select division </option>
                                        @forelse ($divisions as $item)
                                        <option value="{{ $item?->id }}"> {{ $item?->name }} </option>
                                        @empty
                                        @endforelse
                                    </select>
                                    @error('division_id')
                                    <span class="text-danger division_id_error"> {{ $message }} </span>
                                    @enderror
                                    <span class="text-danger" id="DivisionSelectError"></span>
                                </div>

                                <div class="col-12 mb-2">
                                    <label class="font_bold" for="CustomerSelect"> Select Customer* </label>
                                    <select name="customer_id" required id="CustomerSelect"
                                        class="form-control mySelect" data-toggle="select2" data-width="100%">
                                        <option value=""> Select customer </option>
                                    </select>
                                    @error('customer_id')
                                    <span class="text-danger customer_id_error"> {{ $message }} </span>
                                    @enderror
                                    <span class="text-danger" id="CustomerSelectError"></span>
                                </div>

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

                                <div class="col-6 mb-2 px-1">
                                    <label class="font_bold" for="SaleDate">Sale Date</label>
                                    <input type="date" class="form-control" name="sale_date" id="SaleDate">
                                    @error('sale_date')
                                    <span class="text-danger sale_date_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-6 mb-2 px-1">
                                    <label class="font_bold" for="ManualNumber">Manual Number</label>
                                    <input type="text" class="form-control" name="manual_number"
                                        placeholder="Manual Number" id="ManualNumber">
                                    @error('manual_number')
                                    <span class="text-danger manual_number_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                            </div>
                        </div>
                        <div class="col-8 border border-2">
                            <span id="TotalProductList">
                                {{-- <div id="row-0" class="row mt-2 border border-3 p-1 mx-1 mb-2">
                                </div> --}}
                                <table class="table table-border" id="SaleItemsTable">
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Discount</th>
                                        <th>Total Price</th>
                                        <th></th>
                                    </tr>
                                    <tbody id="SaleItemLists">
                                    </tbody>

                                    <tfoot>
                                        <tr class="items_row ">
                                            <td colspan="1" class="text-start">
                                                <div class="card card-body">
                                                    <span class="btn btn-secondary" id="AddProductButton">
                                                        <i class="fa fa-plus"></i> Add Item
                                                    </span>
                                                </div>
                                            </td>
                                            <td colspan="5" class="text-end">
                                                <label class="pt-1"> <b>Total Amount: &nbsp; </b> </label>
                                                <input type="number" style="width:60%; float:right;" id="GrandTotal"
                                                    class="form-control" readonly placeholder="Total Amount">
                                                <br>
                                                <br>
                                                <label class="pt-1"> <b>Add Discount: &nbsp; </b> </label>
                                                <input type="number" style="width:60%; float:right;"
                                                    id="DiscountOnTotal" class="form-control"
                                                    placeholder="Add Discount">
                                                <span id="DiscountAmountGreaterError" Class="text-danger"></span>
                                                <br>
                                                <br>
                                                <label class="pt-1"> <b> Final Amount: &nbsp; </b> </label>
                                                <input type="number" style="width:60%; float:right;"
                                                    id="InvoiceFinalAmount" class="form-control" readonly>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </span>



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
                            <button type="submit" class="btn btn-secondary">
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
@include('productmanagement.sales._addProductForSale')

@endsection

@section('custom_scripts')
<script>
    $(function() {
        var product_list = {};
        var customer_list = {};
        $("#DivisionSelect").change(function(){
            let division_id = parseInt($(this).val())
            let url_addr = "{{ url('partymanagement/division-customers')}}/"+division_id;
            $.get(url_addr , function(response, status){
                if(response.success == 'yes'){
                    customer_list = response.data;
                    if (customer_list != '')
                    {
                        for (i in customer_list) {                        
                            $('#CustomerSelect').append('<option value='+customer_list[i]?.id+'>'+customer_list[i]?.name+' ['+ customer_list[i]?.cnic_no +']</option>');
                        }
                    }
                }else{
                    $('#CustomerSelect').append('<option value="">No Data Found </option>');
                }
            });
        });
        $("#AddProductButton").click(function(){
            
            let company_id = parseInt($('#CompanySelect').val()) || 0
            let category_id = parseInt($('#CategorySelect').val()) || 0
            $('#CompanyIdModal').val(company_id)
            $('#CategoryIdModal').val(category_id)
            if(company_id > 0 && category_id > 0 ){
                $('#AddProductSaleModal').modal('show');
                $('#CompanySelectError').html('')
                // let category_id = parseInt($(this).val())
                if(product_list.length > 0){
                    console.log(product_list)
                    // alert(product_list)
                    // drawSelectOptions(product_list)
                }else{
                    let url_addr = "{{ url('ProductManagement/productfilter')}}/"+company_id+"/cat/"+category_id;
                    $.get(url_addr , function(response, status){
                        product_list = response.data;
                        console.log(product_list)
                        drawSelectOptions(product_list)
                    });
                }
            }else{
                $('#CompanySelectError').html('Category and Company is Required to add Product ')
            }
        });

        function drawSelectOptions(product_list){
            if (product_list != '')
            {
                for (i in product_list) {                        
                    $('#SelectProduct').append('<option value='+product_list[i]?.id+'>'+product_list[i]?.product_name+' ['+ product_list[i]?.product_code +']</option>');
                }
            }else{
                $('#SelectProduct').append('<option value="">No Data Found </option>');
            }
        }

        $("#SelectProduct").change(function(){
            let product_id = parseInt($(this).val())
            var single_product = product_list?.find(x => x.id === product_id);
            // alert(product_id);
            console.log(single_product)
            $('#ProductNameModal').val(single_product?.product_name)
            $('#ProductCodeModal').val(single_product?.product_code)

            $('#SalePrice').val(single_product?.sale_price)
            $('#SalePrice').attr('min', single_product?.sale_price)
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

        $('#ProductQuantity').on('keyup', function() {
            let Quantity = Number($(this).val());
            let PurchasePrice = parseFloat($('#SalePrice').val())
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


        $('#AddProductFromButton').on('click', function(){
            let Mproduct_id = parseInt($('#SelectProduct').val()) || 0
            let Mproduct_name = $('#ProductNameModal').val() || ''
            let Mproduct_code = $('#ProductCodeModal').val() || ''
            let Mproduct_salePrice = parseFloat($('#SalePrice').val()) || 0
            let Mproduct_qty = Number($('#ProductQuantity').val()) || 0
            let Mproduct_totalPrice = parseFloat($('#TotalPrice').val()) || 0
            let Mproduct_discountAmount = parseFloat($('#DiscountAmount').val()) || 0
            let Mproduct_discountPercentage = parseFloat($('#DiscountPercentage').val()) || 0
            let Mproduct_FinalPrice = parseFloat($('#FinalPrice').val()) || 0

            if(Mproduct_qty > 0 && Mproduct_totalPrice > 0){
                const p_data = {
                    p_id:Mproduct_id, p_name:Mproduct_name, p_code:Mproduct_code, 
                    p_sprice:Mproduct_salePrice, p_qty:Mproduct_qty, p_tprice:Mproduct_totalPrice, 
                    p_discount:Mproduct_discountAmount, p_discountPer:Mproduct_discountPercentage, p_fprice: Mproduct_FinalPrice,
                };

                $("#SaleProductItemModal").trigger("reset");
                $('#AddProductSaleModal').modal('hide');
                addRowsInTable(p_data)
                SumAllPrice()   
            }else{
                $(".quantity_error").html("Quantity is Required, Please Select Quantity");
                $(".QtyPriceError").html("Quantity is Required, Please Select Quantity");
            }
        });

        $('.ModalClosed').click(function () {
            $('.modal').modal('hide');
        });

        function addRowsInTable(p_data){
            var html_row_code = `<tr class="items_row" id="row-${p_data?.p_id}">
                    <td style="width:40%" class="px-1">
                        <input type="hidden" name="product_id[]" value="${p_data?.p_id}">
                        <input type="hidden" name="product_code[]" value="${p_data?.p_code}">
                        <input type="text" class="form-control"
                            placeholder="Product name" name="product_name[]" value="${p_data?.p_name}" readonly>
                    </td>
                    <td class="px-1"><input type="number" class="form-control"
                            placeholder="Price" name="product_sale_price[]" value="${p_data?.p_sprice}" readonly>
                    </td>
                    <td class="px-1" style="width:10%"><input type="number" class="form-control"
                            placeholder="qty" name="product_qty[]" value="${p_data?.p_qty}" readonly>
                    </td>
                    <td class="px-1">
                        <input type="number" class="form-control"
                            placeholder="discount" name="product_discount[]" value="${p_data?.p_discount}" readonly>
                        <input type="hidden" name="product_discount_percentage[]" value="${p_data?.p_discountPer}" readonly>
                    </td>
                    <td class="px-1"><input type="number" class="form-control product_total_price"
                            placeholder="total Price" name="product_total_price[]" value="${p_data?.p_fprice}" readonly>
                    </td>
                    <td class="px-1 text-end" style="width:1%">
                        <span type="button" class="btn btn-danger btn-xs text-white"  title="Click to Remove">
                            <button type="button" class="btn-close btn-danger text-danger remove_item mt-1" DelRow="${p_data?.p_id}" title="Click to Remove" aria-label="Close"></button>
                        </span>
                    </td>
                </tr>`;

            $('#SaleItemLists').append(html_row_code)
        }

        function SumAllPrice(){
            var TotalPriceArr = $('.product_total_price').get()
            var GrandTotal = 0
            $(TotalPriceArr).each(function(){
                GrandTotal +=Number($(this).val())
            });
            $('#GrandTotal').val(GrandTotal)
            discountCalculation()
        }

        function discountCalculation(){
            let GrandTotalAmount = parseFloat($('#GrandTotal').val())
            $('#DiscountOnTotal').on('keyup', function() {
                let DiscountOnTotal = parseFloat($(this).val());
                if(DiscountOnTotal > 0 && DiscountOnTotal < GrandTotalAmount){
                    $("#DiscountAmountGreaterError").html('');
                    $("#InvoiceFinalAmount").val(GrandTotalAmount - DiscountOnTotal);
                }else{
                    $("#DiscountAmountGreaterError").html('Discount Amount Shoud be Less then Total Amount');
                }
            });
            $("#InvoiceFinalAmount").val(GrandTotalAmount);
        }

        $(document).on("click", ".remove_item", function () {
            let DelROwID = $(this).attr('DelRow')
            $('#row-'+DelROwID).remove();
            SumAllPrice()
    
            let GrandTotalAmount = parseFloat($('#GrandTotal').val())
            let getDiscount = parseFloat($('#DiscountOnTotal').val())
            if(getDiscount > 0){
                $("#InvoiceFinalAmount").val(GrandTotalAmount-getDiscount);
            }
        });
    });
</script>
@endsection