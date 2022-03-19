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
                        <li class="breadcrumb-item"> <a href="{{ route('purchase.index') }}"> ChicksPurchases </a>
                        </li>
                        <li class="breadcrumb-item active">{{ ($purchase->id > 0 ? "Update" : "Create") }} </li>
                    </ol>
                </div>
                <h4 class="page-title">Chicks Purchase</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4>{{ ($purchase->id > 0 ? "Update" : "Create") }} Purchase </h4>
                        </div>
                        {{-- data-bs-toggle="modal"
                        data-bs-target="#AddFeedModal" --}}
                        <div class="col-6 align-self-end text-end mb-2">
                            <a class="btn btn-secondary btn-sm" href="{{ route('purchase.index') }}"
                                title="Click to see Purchases" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-arrow-left"></i>
                                Purchases
                            </a>
                        </div>

                        <form autocomplete="off"
                            action="{{ ($purchase->id) ? route('purchase.update', $purchase->id ) : route('purchase.store') }}"
                            method="post" class="loader" enctype="multipart/form-data" id="ChickenPurchaseEntryForm">
                            @csrf
                            @if($purchase->id > 0)
                            @method('PUT')
                            @endif

                            <div class="row form-group mt-2 fs-5">
                                <div class="col-sm-3 mb-2">
                                    <label for="pdate"> Purchase Date *</label>
                                    <input type="date" required placeholder="Enter date" name="purchase_date"
                                        value="{{ today()->format('Y-m-d') }}" {{--
                                        value="{{ $purchase->purchase_date ?? old('purchase_date') }}" --}}
                                        class="form-control" id="chikPurchaseDate">
                                    @error('purchase_date')
                                    <span class="text-danger purchase_date_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="ChickGrade"> Select Chick Grade* </label>
                                    <div class="input-group">
                                        <select name="chick_grade_id" id="ChickGrade" class="form-control mySelect"
                                            data-toggle="select2" data-width="85%">
                                            <option value=""> Select chick grade </option>
                                            @forelse ($chick_grades as $item)
                                            <option {{ (! empty(old('chick_grade_id', $purchase?->
                                                chick_grade_id)==$item->id) ?
                                                'selected'
                                                : '' ) }} value="{{$item->id}}">{{$item->name}}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                        <a href="#"
                                            class="btn input-group-text btn-dark btn-sm waves-effect waves-light OpenaddTypeModal"
                                            SelectBoxId="ChickGrade" TableName="chick_grades" title="Click to add new"
                                            type="button"> <i class="fa fa-plus pt-1"></i> </a>
                                    </div>
                                    @error('chick_grade_id')
                                    <span class="text-danger chick_grade_id_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-3 mb-2">
                                    <label for="feedCompanyId">Select Company *</label>
                                    <select class="form-control mySelect" id="feedCompanyId" required name="company_id"
                                        data-placeholder="Select Company" data-toggle="select2" data-width="100%">
                                        <option value="" selected> Select Company </option>
                                        @forelse ($compaines as $company)
                                        <option {{ old('company_id') || $purchase?->company_id ? 'selected' : '' }}
                                            value="{{$company->id}}">
                                            {{$company->company_name}} </option>
                                        @empty
                                        @endforelse
                                    </select>

                                    @error('company_id')
                                    <span class="text-danger company_id_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <input type="hidden" name="vendor_id" id="PartyVendorId">
                                    <label for="CompanyVendorName"> Vendor Name </label>
                                    <input type="text" placeholder="Vendor Name" name="vendor_name" readonly disabled
                                        class="form-control"
                                        value="{{ $purchase?->company?->vendor?->name ?? old('vendor_name') }}"
                                        id="CompanyVendorName">
                                    @error('vendor_name')
                                    <span class="text-danger vendor_name_error"> {{ $message }} </span>
                                    @enderror

                                </div>
                                {{-- <div class="col-sm-3 mb-2">
                                    <label for="VendorGuardianName"> Vendor Guardian Name</label>
                                    <input type="text" placeholder="Vendor Guardian Name" name="vendor_guardian_name"
                                        value="{{ $purchase?->company?->vendor?->guardian_name ?? old('vendor_guardian_name') }}"
                                        class="form-control" readonly disabled id="VendorGuardianName">

                                    @error('vendor_guardian_name')
                                    <span class="text-danger vendor__error"> {{ $message }} </span>
                                    @enderror

                                </div> --}}
                            </div>
                            <div class="row fs-5">
                                <div class="col-3 mb-2">
                                    <label for="CustomerSelect">Select customer *</label>
                                    <select class="form-control mySelect" id="CustomerSelect" required
                                        name="customer_id" data-placeholder="Select customer" data-toggle="select2"
                                        data-width="100%">
                                        <option value="" selected disabled> Select Customer </option>
                                        @forelse ($customers as $item)
                                        <option {{ old('customer_id') || $purchase?->customer_id ? 'selected'
                                            : '' }} value="{{$item->id}}">{{$item->name}} [{{$item->cnic_no}}] </option>
                                        @empty

                                        @endforelse
                                    </select>

                                    @error('customer_id')
                                    <span class="text-danger customer_id_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-3 mb-2">
                                    <label for="CustomerFarmName">Farm Name *</label>
                                    <input type="hidden" name="customer_farm_id" id="CustomerFarmId">
                                    <input type="text" placeholder="Farm Name" name="customer_farm_name"
                                        class="form-control" id="CustomerFarmName"
                                        value="{{ $purchase?->customer_farm_name ?? old('customer_farm_name') }}"
                                        readonly>

                                    @error('customer_farm_name')
                                    <span class="text-danger customer_farm_name_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="PersonalFarmCapacity"> Farm Capacity </label>
                                    <input type="number" step="any" min="0" placeholder="Farm Capacity"
                                        name="personal_farm_capacity" class="form-control" id="PersonalFarmCapacity"
                                        value="{{ $purchase?->personal_farm_capacity ?? old('personal_farm_capacity') }}"
                                        readonly>

                                    @error('personal_farm_capacity')
                                    <span class="text-danger personal_farm_capacity_error"> {{ $message }} </span>
                                    @enderror

                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="PersonalFarmAddress"> Farm Address </label>
                                    <input type="text" placeholder="Farm Address" name="Personal_farm_address"
                                        class="form-control" id="PersonalFarmAddress"
                                        value="{{ $purchase?->Personal_farm_address ?? old('Personal_farm_address') }}"
                                        readonly>

                                    @error('Personal_farm_address')
                                    <span class="text-danger Personal_farm_address_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row fs-5">
                                <div class="col-sm-3 mb-2">
                                    <label for="ChickAgeSelect">Select Chick Age </label>

                                    <select class="form-control mySelect" id="ChickAgeSelect" required
                                        name="chick_entry_age" data-placeholder="Select chick age" data-toggle="select2"
                                        data-width="100%">
                                        <option value="" selected disabled> Select chick age </option>
                                        @php
                                        $days = '';
                                        for($i = 1; $i <= 10; $i++) { if($i> 1){
                                            $days = $i.' Days';
                                            }else{
                                            $days = $i.' Day';
                                            }

                                            echo '<option value="'.$i.'"> '.$days.'</option>';
                                            }

                                            @endphp
                                    </select>

                                    @error('chick_entry_age')
                                    <span class="text-danger chick_entry_age_error"> {{ $message }} </span>
                                    @enderror

                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="ChickWeight"> Weight in Grams (gm) </label>
                                    <input type="number" step="any" min="0" max="50" placeholder="Enter weight"
                                        name="chick_weight" class="form-control" id="ChickWeight"
                                        value="{{ $purchase?->weight ?? old('chick_weight') }}" required>

                                    @error('chick_weight')
                                    <span class="text-danger chick_weight_error"> {{ $message }} </span>
                                    @enderror

                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="feedQuantity"> Quantity </label>
                                    <input type="number" step="any" min="0" placeholder="Enter quantity" name="quantity"
                                        class="form-control" id="feedQuantity"
                                        value="{{ $purchase?->quantity ?? old('quantity') }}" required>

                                    @error('quantity')
                                    <span class="text-danger quantity_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="feedPrice"> Price </label>
                                    <input type="number" min="0" step="any" placeholder="Enter price" name="price"
                                        class="form-control" id="feedPrice"
                                        value="{{ $purchase?->price ?? old('price') }}" required>
                                    @error('price')
                                    <span class="text-danger price_error"> {{ $message }} </span>
                                    @enderror

                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="feedDiscountAmount"> Discount Amount </label>
                                    <input type="number" min="0" step="any" placeholder="Discount Amount"
                                        name="discount_amount"
                                        value="{{ $purchase?->discount_amount ?? old('discount_amount') }}"
                                        class="form-control" id="feedDiscountAmount">

                                    @error('discount_amount')
                                    <span class="text-danger discount_amount_error"> {{ $message }} </span>
                                    @enderror

                                </div>
                                <span class="text-danger h6" id="PriceQtyError" style="display: none;"></span>
                                <div class="col-3 mb-2">
                                    <label for="feedDiscountPercentage"> Discount Percentage % </label>
                                    <input type="number" min="0" step="any" placeholder="Discount Percentage %"
                                        name="discount_percentage"
                                        value="{{ $purchase?->discount_percentage ?? old('discount_percentage') }}"
                                        class="form-control" id="feedDiscountPercentage">

                                    @error('discount_percentage')
                                    <span class="text-danger discount_percentage_error"> {{ $message }} </span>
                                    @enderror

                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label for="feedTotalPrice"> Total Price </label>
                                    <input type="number" min="0" step="any" placeholder="Total price" name="total_price"
                                        value="{{ $purchase?->total_price ?? old('total_price') }}" class="form-control"
                                        id="feedTotalPrice">

                                    @error('total_price')
                                    <span class="text-danger total_price_error"> {{ $message }} </span>
                                    @enderror

                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="BiltyNumber"> Bilty Number</label>
                                    <input type="text" placeholder="Enter Bilty Number" name="bilty_number"
                                        value="{{ $purchase?->bilty_number ?? old('bilty_number') }}"
                                        class="form-control" id="BiltyNumber">

                                    @error('bilty_number')
                                    <span class="text-danger bilty_number_error"> {{ $message }} </span>
                                    @enderror

                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="BiltyCharges"> Bilty Charges</label>
                                    <input type="number" min="0" step="any" placeholder="Enter Bilty Charges"
                                        name="bilty_charges"
                                        value="{{ $purchase?->bilty_charges ?? old('bilty_charges') }}"
                                        class="form-control" id="BiltyCharges">

                                    @error('bilty_charges')
                                    <span class="text-danger bilty_charges_error"> {{ $message }} </span>
                                    @enderror

                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="Vnumber"> Vehicle Number </label>
                                    <input type="text" placeholder="Vehicle number" name="vehicle_number"
                                        value="{{ $purchase?->vehicle_number ?? old('vehicle_number') }}"
                                        class="form-control" id="Vnumber">

                                    @error('vehicle_number')
                                    <span class="text-danger vehicle_number_error"> {{ $message }} </span>
                                    @enderror

                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="Dname"> Driver Name </label>
                                    <input type="text" placeholder="Driver number" name="driver_name"
                                        value="{{ $purchase?->driver_name ?? old('driver_name') }}" class="form-control"
                                        id="Dname">

                                    @error('driver_name')
                                    <span class="text-danger driver_name_error"> {{ $message }} </span>
                                    @enderror

                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="Dcontact"> Driver Contact </label>
                                    <input type="number" min="0" placeholder="Driver number" name="driver_contact"
                                        value="{{ $purchase?->driver_contact ?? old('driver_contact') }}"
                                        class="form-control" id="Dcontact">

                                    @error('driver_contact')
                                    <span class="text-danger driver_contact_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-3 mb-2">
                                    <label for="SoNumber"> Sale Order Number </label>
                                    <input type="text" placeholder="Enter Sale Order Number" name="sale_order_number"
                                        value="{{ $purchase?->sale_order_number ?? old('sale_order_number') }}"
                                        class="form-control" id="SoNumber">

                                    @error('sale_order_number')
                                    <span class="text-danger sale_order_number_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-3 mb-2">
                                    <label for="Do_Number"> Delivery Order Number </label>
                                    <input type="text" placeholder="Enter Delivery Order Number"
                                        name="delivery_order_number"
                                        value="{{ $purchase?->delivery_order_number ?? old('delivery_order_number') }}"
                                        class="form-control" id="Do_Number">

                                    @error('delivery_order_number')
                                    <span class="text-danger delivery_order_number_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="Remarks"> Remarks </label>
                                    <input type="text" placeholder="Enter Remarks If any" name="remarks"
                                        value="{{ $purchase?->remarks ?? old('remarks') }}" class="form-control"
                                        id="Remarks">

                                    @error('remarks')
                                    <span class="text-danger remarks_error"> {{ $message }} </span>
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
                                    @if ($purchase?->picture !='')
                                    <a href="{{ asset('storage/chicks/'.$purchase?->picture) }}" target="_blank"> <img
                                            class="d-flex me-3 avatar-lg" target="_blank"
                                            src="{{ asset('storage/chicks/'.$purchase?->picture) }}" alt="No image">
                                    </a>
                                    @endif
                                </div>
                            </div>
                            <div class="row form-group fs-5">
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

        var companies_list = <?php echo json_encode($compaines) ?>;
        var customer_list = <?php echo json_encode($customers) ?>;
        console.log(companies_list)
        $( "#feedCompanyId" ).change(function() {
            let company_id_modal = parseInt($(this).val())
            let find_company = companies_list?.find(x => x.id === company_id_modal);
            if(find_company){
                $('#PartyVendorId').val(find_company?.vendor?.id || '');
                $('#CompanyVendorName').val(find_company?.vendor?.name || '');
                $('#VendorGuardianName').val(find_company?.vendor?.guardian_name || '');
            }
        }); 
        
        $( "#CustomerSelect" ).change(function() {
            let customer_id = parseInt($(this).val())
            let customer = customer_list?.find(x => x.id === customer_id);

            console.log(customer.farm)
            if(customer){
                $('#CustomerFarmId').val(customer?.farm?.id);
                $('#CustomerFarmName').val(customer?.farm?.farm_name);
                $('#PersonalFarmCapacity').val(customer?.farm?.farm_capacity);
                $('#PersonalFarmAddress').val(customer?.farm?.farm_address || '');
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