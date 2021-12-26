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
                        <li class="breadcrumb-item"><a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item"> <a href="{{ route('feed.index') }}"> FeedManagement </a>
                        </li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
                <h4 class="page-title">Feeds</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4>Add Feed Entry</h4>
                        </div>

                        <div class="col-6 align-self-end text-end mb-2">
                            <a class="btn btn-secondary btn-sm" href="{{ route('feed.index') }}"
                                title="Click to see Sales" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-arrow-left"></i>
                                Back
                            </a>
                        </div>

                        <form method="post" action="{{ ($feed->id) ? route('feed.update', $feed->id ) :
                            route('feed.store') }}" autocomplete="off" enctype="multipart/form-data"
                            id="FeedEntryForm">
                            @csrf
                            <div class="row form-group">
                                <input type="hidden" name="feed_id_modal" id="feedIdModal">
                                <div class="col-sm-4 mb-2">
                                    <label for="feed_name"> Feed Name * </label>
                                    <input type="text" placeholder="Enter feed name" name="feed_name"
                                        class="form-control" id="feedName">
                                    <span class="text-danger feed_name_error"></span>
                                </div>

                                <div class="col-sm-4 mb-2">
                                    <label for="pdate"> Purchase Date *</label>
                                    <input type="date" placeholder="Enter date" name="purchase_date"
                                        class="form-control" id="feedPurchaseDate">
                                    <span class="text-danger purchase_date_error"></span>
                                </div>

                                <div class="col-4 mb-2">
                                    <label class="font_bold" for="FeedCategory"> Select category </label>
                                    <div class="input-group">
                                        <select name="feed_category_id" id="FeedCategory" class="form-control mySelect"
                                            data-toggle="select2" data-width="89%">
                                            <option value=""> Select category </option>
                                            @forelse ($categories as $item)
                                            <option {{ (! empty(old('feed_category_id')==$item->id) ? 'selected'
                                                : '' ) }} value="{{$item->id}}">{{$item->name}}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                        <a href="#"
                                            class="btn input-group-text btn-dark waves-effect waves-light OpenaddTypeModal"
                                            title="Click to add new" SelectBoxId="FeedCategory"
                                            TableName="feed_categories" type="button">
                                            <i class="fa fa-plus"></i>
                                        </a>
                                    </div>
                                    @error('feed_category_id')
                                    <span class="text-danger feed_category_id_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-4 mb-2">
                                    <label for="feedCompanyId">Select Company *</label>
                                    <select class="form-control mySelect" id="feedCompanyId" name="company_id"
                                        data-placeholder="Select Company" data-toggle="select2" data-width="100%">
                                        <option value=""> Select Company </option>
                                        @forelse ($companies as $item)
                                        <option value="{{$item->id}}">{{$item->company_name}}</option>
                                        @empty
                                        @endforelse
                                    </select>

                                    <span class="text-danger company_id_error"></span>
                                </div>

                                <div class="col-sm-4 mb-2">
                                    <label for="VendorName"> Vendor Name </label>
                                    <input type="text" placeholder="Company Address" name="vendor_name" readonly
                                        disabled class="form-control" id="VendorName">
                                    <span class="text-danger vendor_name_error"></span>
                                </div>
                                {{-- <div class="col-sm-2 mb-2">
                                    <label for="CompanyDebitLimit" class="text-danger">Debit Limit</label>
                                    <input type="text" placeholder="Enter Contact" name="company_debit_limit"
                                        class="form-control input-danger" readonly disabled id="CompanyDebitLimit">
                                    <span class="text-danger company_debit_limit_error"></span>
                                </div>

                                <div class="col-sm-2 mb-2">
                                    <label for="CompanyCreditLimit" class="text-warning"> Credit Limit </label>
                                    <input type="text" placeholder="Enter Contact" name="company_credit_limit"
                                        class="form-control" readonly disabled id="CompanyCreditLimit">
                                    <span class="text-danger company_credit_limit_error"></span>
                                </div> --}}

                                <div class="col-sm-4 mb-2">
                                    <label for="feedQuantity"> Quantity </label>
                                    <input type="text" placeholder="Enter quantity" name="quantity" class="form-control"
                                        id="feedQuantity" required>
                                    <span class="text-danger quantity_error"></span>
                                </div>

                                <div class="col-sm-4 mb-2">
                                    <label for="feedPrice"> Price </label>
                                    <input type="text" placeholder="Enter price" name="price" class="form-control"
                                        id="feedPrice" required>
                                    <span class="text-danger price_error"></span>
                                </div>

                                <div class="col-sm-4 mb-2">
                                    <label for="feedDiscountAmount"> Discount Amount </label>
                                    <input type="number" step="any" min="0" placeholder="Discount Amount"
                                        name="discount_amount" class="form-control" id="feedDiscountAmount">
                                    <span class="text-danger discount_amount_error"></span>
                                </div>
                                <span class="text-danger h6" id="PriceQtyError" style="display: none;"></span>
                                <div class="col-sm-4 mb-2">
                                    <label for="feedDiscountPercentage"> Discount Percentage % </label>
                                    <input type="text" placeholder="Discount Percentage %" name="discount_percentage"
                                        class="form-control" id="feedDiscountPercentage">
                                    <span class="text-danger discount_percentage_error"></span>
                                </div>
                                <div class="col-sm-4 mb-2">
                                    <label for="feedTotalPrice"> Total Price </label>
                                    <input type="text" placeholder="Total price" name="total_price" class="form-control"
                                        id="feedTotalPrice">
                                    <span class="text-danger total_price_error"></span>
                                </div>

                                <div class="col-sm-4 mb-2">
                                    <label for="BiltyNumber"> Bilty Number</label>
                                    <input type="text" placeholder="ENter Bilty Number" name="bilty_number" value=""
                                        class="form-control" id="BiltyNumber">

                                    @error('bilty_number')
                                    <span class="text-danger bilty_number_error"> {{ $message }} </span>
                                    @enderror

                                </div>

                                <div class="col-sm-4 mb-2">
                                    <label for="BiltyCharges"> Bilty Charges</label>
                                    <input type="number" step="any" min="0" placeholder="ENter Bilty Charges"
                                        name="bilty_charges" class="form-control" id="BiltyCharges">

                                    @error('bilty_charges')
                                    <span class="text-danger bilty_charges_error"> {{ $message }} </span>
                                    @enderror

                                </div>

                                <div class="col-sm-4 mb-2">
                                    <label for="BagDiscount"> Per Bag Discount </label>
                                    <input type="number" step="any" min="0" placeholder="Enter Bilty Charges"
                                        name="per_bag_discount" value="" class="form-control" id="BagDiscount">

                                    @error('per_bag_discount')
                                    <span class="text-danger per_bag_discount_error"> {{ $message }} </span>
                                    @enderror

                                </div>

                                <div class="col-sm-4 mb-2">
                                    <label for="SoNumber"> Sale Order Number </label>
                                    <input type="text" placeholder="Enter Sale Order Number" name="sale_order_number"
                                        value="" class="form-control" id="SoNumber">

                                    @error('sale_order_number')
                                    <span class="text-danger sale_order_number_error"> {{ $message }} </span>
                                    @enderror

                                </div>

                                <div class="col-sm-4 mb-2">
                                    <label for="Do_Number"> Delivery Order Number </label>
                                    <input type="text" placeholder="Enter Delivery Order Number"
                                        name="delivery_order_number" value="" class="form-control" id="Do_Number">

                                    @error('delivery_order_number')
                                    <span class="text-danger delivery_order_number_error"> {{ $message }} </span>
                                    @enderror

                                </div>

                                <div class="col-sm-4 mb-2">
                                    <label for="description">Description</label>
                                    <input type="text" class="form-control" name="description" id="description"
                                        placeholder="description if any">
                                    <span class="text-danger description_error"></span>
                                </div>

                                <div class="col-sm-4 mb-2">
                                    <label for="image">Image</label>
                                    <input type="file" class="form-control" name="image_file"
                                        accept="image/png,image/jpg,image/jpeg">
                                    <span class="text-danger image_file_error"> </span>
                                </div>
                                <div class="col-sm-4 mt-2 img-holder">
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-4 mb-3">
                                    <button type="submit" id="sub"
                                        class="btn btn-secondary btn-sm waves-effect waves-light mt-3 AddUpdate">
                                        Submit</button>
                                    <button
                                        class="btn btn-light btn-sm waves-effect waves-light mt-3 ModalClosed">Cancel</button>
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

@endsection

@section('custom_scripts')
<script>
    $(function() {

        var companies_list = {};
        companies_list = '<?php echo json_encode($companies); ?>';

        // console.log(companies_list)

        $( "#feedCompanyId" ).change(function() {
            let company_id = parseInt($(this).val())
            console.log(company_id)
            let find_company = companies_list.find(x => x.id === company_id);
            if(find_company){
                $('#VendorName').val(find_company?.vendor?.name || '');
                // $('#feedCompanyContact').val(find_company?.contact_no || '');
            }
        });

        
        $('#feedPrice').on('keyup', function() {
            let total_qty = Number($('#feedQuantity').val())
            let feed_price = parseFloat($('#feedPrice').val())

            if (total_qty > 0 && feed_price > 0) {
                $("#PriceQtyError").html('');
                $("#PriceQtyError").hide();
                priceWithOutDiscount = total_qty*feed_price;
                $("#feedTotalPrice").val(priceWithOutDiscount);
            } else {
                $("#PriceQtyError").show();
                $("#PriceQtyError").html('Price and Quantity Is required');
                $("#feedTotalPrice").val('');
            }
        });

        // $('#feedDiscountAmount').on('keyup', function() {
        $('#feedDiscountAmount').on('keyup', function() {
            let total_qty = Number($('#feedQuantity').val())
            let feed_price = parseFloat($('#feedPrice').val())
            let discountAmount = parseFloat($(this).val());
            let priceWithOutDiscount = 0
            if(discountAmount > 0){
                if (total_qty > 0 && feed_price > 0) {
                    $("#PriceQtyError").html('');
                    $("#PriceQtyError").hide();
                    priceWithOutDiscount = total_qty*feed_price;
                    let discount_percent = (discountAmount / priceWithOutDiscount) * 100;
                    if(discountAmount >= priceWithOutDiscount){
                        $(".discount_amount_error").html('Discount Amount Must be less than total amount');
                    }else{
                        $(".discount_amount_error").html('');
                        $("#feedDiscountPercentage").val(discount_percent.toFixed(2));
                        $("#feedTotalPrice").val(priceWithOutDiscount - discountAmount);
                    }
                } else {
                    $("#PriceQtyError").show();
                    $("#PriceQtyError").html('Price and Quantity Is required');
                }
            }else{
                $("#feedDiscountPercentage").val('');
                $("#feedTotalPrice").val(total_qty*feed_price);
            }
        });
        
        $('#feedDiscountPercentage').on('keyup', function() {
            let total_qty = Number($('#feedQuantity').val())
            let feed_price = parseFloat($('#feedPrice').val())
            let discountPercentage = parseFloat($(this).val()) || 0;
            let priceWithOutDiscount = 0
            $(".discount_percentage_error").html('');
            if(discountPercentage > 0 && discountPercentage < 100){
                if (total_qty > 0 && feed_price > 0) {
                    $("#PriceQtyError").html('');
                    $("#PriceQtyError").hide();
                    priceWithOutDiscount = total_qty*feed_price;
                    let discount_amt = (discountPercentage /100 ) * priceWithOutDiscount;
                    $("#feedDiscountAmount").val(discount_amt.toFixed(2));
                    $("#feedTotalPrice").val(priceWithOutDiscount - discount_amt);
                } else {
                    $("#PriceQtyError").show();
                    $("#PriceQtyError").html('Price and Quantity Is required');
                }
            }else{
                $("#feedDiscountAmount").val('');
                $("#feedTotalPrice").val(total_qty*feed_price);
                $(".discount_percentage_error").html('Discount Percentage Must be less than 100 (Total Amount)');
            }
        });
    });
</script>
@endsection