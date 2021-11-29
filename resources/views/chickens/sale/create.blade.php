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
                        <li class="breadcrumb-item"><a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item"> <a href="{{ route('purchase.index') }}"> ChickensSales </a>
                        </li>
                        <li class="breadcrumb-item active">{{ ($sale->id > 0 ? "Update" : "Create") }} </li>
                    </ol>
                </div>
                <h4 class="page-title">Chicken Sale</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4>{{ ($sale->id > 0 ? "Update" : "Create") }} Sale </h4>
                        </div>
                        {{-- data-bs-toggle="modal"
                        data-bs-target="#AddFeedModal" --}}
                        <div class="col-6 align-self-end text-end mb-2">
                            <a class="btn btn-secondary btn-sm" href="{{ route('sale.index') }}"
                                title="Click to see Sales" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-arrow-left"></i>
                                Sales
                            </a>
                        </div>

                        <form autocomplete="off"
                            action="{{ ($sale->id) ? route('sale.update', $sale->id ) : route('sale.store') }}"
                            method="post" class="loader" enctype="multipart/form-data" id="ChickenSaleEntryForm">
                            @csrf
                            @if($sale->id > 0)
                            @method('PUT')
                            @endif

                            <div class="row form-group mt-2">
                                <input type="hidden" name="sale_id">
                                <div class="col-sm-3 mb-2">
                                    <label for="chikSaleDate"> Sale Date *</label>
                                    <input type="date" required placeholder="Enter date" name="sale_date"
                                        value="{{ today()->format('Y-m-d') }}" {{--
                                        value="{{ $sale->sale_date ?? old('sale_date') }}" --}} class="form-control"
                                        id="chikSaleDate">
                                    @error('sale_date')
                                    <span class="text-danger sale_date_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-3 mb-2">
                                    <label for="customerId">Select Customer *</label>
                                    <select class="form-control mySelect" id="customerId" required name="customer_id"
                                        data-placeholder="Select Customer" data-toggle="select2" data-width="100%">
                                        <option value=""> Select Customer </option>
                                        @forelse ($customers as $customer)
                                        <option {{ ( old('customer_id') || $sale?->customer_id == $customer->id) ?
                                            'selected' : '' }} value="{{$customer->id}}">
                                            {{$customer->name}}</option>
                                        @empty
                                        @endforelse
                                    </select>

                                    @error('customer_id')
                                    <span class="text-danger customer_id_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="CustomerFarm"> Customer Farm </label>
                                    <input type="text" name="customer_farm_name"
                                        value="{{ $sale?->customer?->farm_name ?? old('customer_farm_name') }}"
                                        class="form-control" readonly disabled id="CustomerFarm">
                                    @error('customer_farm_name')
                                    <span class="text-danger customer_farm_name_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="CustomerContact"> Customer Contact </label>
                                    <input type="text" placeholder="Enter Contact" name="customer_contact"
                                        value="{{ $sale?->customer?->contact_no ?? old('customer_contact') }}"
                                        class="form-control" readonly disabled id="CustomerContact">

                                    @error('customer_contact')
                                    <span class="text-danger customer_contact_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="TotalWeight"> Total Weight </label>
                                    <input type="number" step="any" min="1" placeholder="Enter weight"
                                        name="total_weight" class="form-control" id="TotalWeight"
                                        value="{{ $sale?->total_weight ?? old('total_weight') }}" required>

                                    @error('total_weight')
                                    <span class="text-danger total_weight_error"> {{ $message }} </span>
                                    @enderror

                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="PerkgPrice"> Per/Kg Price </label>
                                    <input type="number" step="any" min="1" placeholder="Enter price"
                                        name="per_kg_price" class="form-control" id="PerkgPrice"
                                        value="{{ $sale?->per_kg_price ?? old('per_kg_price') }}" required>
                                    @error('per_kg_price')
                                    <span class="text-danger per_kg_price_error"> {{ $message }} </span>
                                    @enderror

                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="feedDiscountAmount"> Discount Amount </label>
                                    <input type="number" step="any" min="0" placeholder="Discount Amount"
                                        name="discount_amount"
                                        value="{{ $sale?->discount_amount ?? old('discount_amount') }}"
                                        class="form-control" id="feedDiscountAmount">

                                    <span class="text-danger discount_amount_error"></span>

                                </div>
                                <span class="text-danger h6" id="PriceQtyError" style="display: none;"></span>
                                <div class="col-3 mb-2">
                                    <label for="feedDiscountPercentage"> Discount Percentage % </label>
                                    <input type="number" step="any" min="0" placeholder="Discount Percentage %"
                                        name="discount_percentage"
                                        value="{{ $sale?->discount_percentage ?? old('discount_percentage') }}"
                                        class="form-control" id="feedDiscountPercentage">

                                    <span class="text-danger discount_percentage_error"></span>

                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label for="feedTotalPrice"> Total Price </label>
                                    <input type="number" step="any" min="1" placeholder="Total price" name="total_price"
                                        value="{{ $sale?->total_price ?? old('total_price') }}" class="form-control"
                                        id="feedTotalPrice">

                                    @error('total_price')
                                    <span class="text-danger total_price_error"> {{ $message }} </span>
                                    @enderror

                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="Vnumber"> Vehicle Number </label>
                                    <input type="text" placeholder="Vehicle number" name="vehicle_number"
                                        value="{{ $sale?->vehicle_number ?? old('vehicle_number') }}"
                                        class="form-control" id="Vnumber">

                                    @error('vehicle_number')
                                    <span class="text-danger vehicle_number_error"> {{ $message }} </span>
                                    @enderror

                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="Dname"> Driver Name </label>
                                    <input type="text" placeholder="Driver number" name="driver_name"
                                        value="{{ $sale?->driver_name ?? old('driver_name') }}" class="form-control"
                                        id="Dname">

                                    @error('driver_name')
                                    <span class="text-danger driver_name_error"> {{ $message }} </span>
                                    @enderror

                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="Dcontact"> Driver Contact </label>
                                    <input type="text" placeholder="Driver number" name="driver_contact"
                                        value="{{ $sale?->driver_contact ?? old('driver_contact') }}"
                                        class="form-control" id="Dcontact">

                                    @error('driver_contact')
                                    <span class="text-danger driver_contact_error"> {{ $message }} </span>
                                    @enderror

                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="image">Invoice Picture</label>
                                    <input type="file" class="form-control" name="image_file"
                                        accept="image/png,image/jpg,image/jpeg">

                                    @error('image_file')
                                    <span class="text-danger image_file_error"> {{ $message }} </span>
                                    @enderror

                                </div>
                                <div class="col-sm-6 mt-2 img-holder">
                                    @if ($sale?->picture !='')
                                    <a href="{{ asset('storage/chickens/'.$sale?->picture) }}" target="_blank"> <img
                                            class="d-flex me-3 avatar-lg" target="_blank"
                                            src="{{ asset('storage/chickens/'.$sale?->picture) }}" alt="No image">
                                    </a>
                                    @endif
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
@endsection

@section('custom_scripts')
<script>
    $(function() {
        var customers_list = <?php echo json_encode($customers) ?>;
        // console.log(customers_list)
    $( "#customerId" ).change(function() {
        let customer_id_modal = parseInt($(this).val())
        let find_customer = customers_list?.find(x => x.id === customer_id_modal);
        if(find_customer){
            $('#CustomerContact').val(find_customer?.contact_no || '');
            $('#CustomerFarm').val(find_customer?.farm_name || '');
        }
    });

    $('#PerkgPrice').on('keyup', function() {
        let total_weight = Number($('#TotalWeight').val())
        let per_kg_price = parseFloat($('#PerkgPrice').val())
        if (total_weight > 0 && per_kg_price > 0) {
            $("#PriceQtyError").html('');
            $("#PriceQtyError").hide();
            priceWithOutDiscount = total_weight*per_kg_price;
            $("#feedTotalPrice").val(priceWithOutDiscount);
        } else {
            $("#PriceQtyError").show();
            $("#PriceQtyError").html('Price and Quantity Is required');
            $("#feedTotalPrice").val('');
        }
    });

    // $('#feedDiscountAmount').on('keyup', function() {
    $('#feedDiscountAmount').on('keyup', function() {
        let total_weight = Number($('#TotalWeight').val())
        let per_kg_price = parseFloat($('#PerkgPrice').val())
        let discountAmount = parseFloat($(this).val());
        let priceWithOutDiscount = 0
        if(discountAmount > 0){
            if (total_weight > 0 && per_kg_price > 0) {
                $("#PriceQtyError").html('');
                $("#PriceQtyError").hide();
                priceWithOutDiscount = total_weight*per_kg_price;
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
            $("#feedTotalPrice").val(total_weight*per_kg_price);
        }
    });
    
    $('#feedDiscountPercentage').on('keyup', function() {
        let total_weight = Number($('#TotalWeight').val())
        let per_kg_price = parseFloat($('#PerkgPrice').val())
        let discountPercentage = parseFloat($(this).val()) || 0;
        let priceWithOutDiscount = 0
        $(".discount_percentage_error").html('');
        if(discountPercentage > 0 && discountPercentage < 100){
            if (total_weight > 0 && per_kg_price > 0) {
                $("#PriceQtyError").html('');
                $("#PriceQtyError").hide();
                priceWithOutDiscount = total_weight*per_kg_price;
                let discount_amt = (discountPercentage /100 ) * priceWithOutDiscount;
                $("#feedDiscountAmount").val(discount_amt.toFixed(2));
                $("#feedTotalPrice").val(priceWithOutDiscount - discount_amt);
            } else {
                $("#PriceQtyError").show();
                $("#PriceQtyError").html('Price and Quantity Is required');
            }
        }else{
            $("#feedDiscountAmount").val('');
            $("#feedTotalPrice").val(total_weight*per_kg_price);
            $(".discount_percentage_error").html('Discount Percentage Must be less than 100 (Total Amount)');
        }
    });
});
</script>
@endsection