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

                            <div class="row form-group mt-2">
                                <input type="hidden" name="purchase_id">
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
                                    <label for="ChickGrade">Select Chick Grade *</label>
                                    <select class="form-control mySelect" id="ChickGrade" required name="chick_grade_id"
                                        data-placeholder="Select Company" data-toggle="select2" data-width="100%">
                                        <option value=""> Select Grade </option>
                                        <option value="1"> A Grade </option>
                                        <option value="2"> B Grade </option>
                                        <option value=3"> C Grade </option>
                                    </select>

                                    @error('chick_grade_id')
                                    <span class="text-danger chick_grade_id_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-3 mb-2">
                                    <label for="feedCompanyId">Select Vendor *</label>
                                    <select class="form-control mySelect" id="feedCompanyId" required name="company_id"
                                        data-placeholder="Select Company" data-toggle="select2" data-width="100%">
                                        <option value=""> Select Vendor </option>
                                        @forelse ($compaines as $company)
                                        <option {{ old('company_id') || $purchase?->company_id ? 'selected' : '' }} {{--
                                            @if
                                            ($ex_user==$user->id) selected @endif --}} value="{{$company->id}}">
                                            {{$company->name}} </option>
                                        @empty
                                        @endforelse
                                    </select>

                                    @error('company_id')
                                    <span class="text-danger company_id_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="feedCompanyAddr"> Company Address </label>
                                    <input type="text" placeholder="Company Address" name="company_address" readonly
                                        disabled class="form-control"
                                        value="{{ $purchase?->company?->address ?? old('company_address') }}"
                                        id="feedCompanyAddr">
                                    @error('company_address')
                                    <span class="text-danger company_address_error"> {{ $message }} </span>
                                    @enderror

                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label for="feedCompanyContact"> Company Contact </label>
                                    <input type="text" placeholder="Enter Contact" name="company_contact"
                                        value="{{ $purchase?->company?->contact_no ?? old('company_contact') }}"
                                        class="form-control" readonly disabled id="feedCompanyContact">

                                    @error('company_contact')
                                    <span class="text-danger company_contact_error"> {{ $message }} </span>
                                    @enderror

                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="ChickWeight"> Weight </label>
                                    <input type="text" placeholder="Enter weight" name="chick_weight"
                                        class="form-control" id="ChickWeight"
                                        value="{{ $purchase?->chick_weight ?? old('chick_weight') }}" required>

                                    @error('chick_weight')
                                    <span class="text-danger chick_weight_error"> {{ $message }} </span>
                                    @enderror

                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="feedQuantity"> Quantity </label>
                                    <input type="text" placeholder="Enter quantity" name="quantity" class="form-control"
                                        id="feedQuantity" value="{{ $purchase?->quantity ?? old('quantity') }}"
                                        required>

                                    @error('quantity')
                                    <span class="text-danger quantity_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="feedPrice"> Price </label>
                                    <input type="text" placeholder="Enter price" name="price" class="form-control"
                                        id="feedPrice" value="{{ $purchase?->price ?? old('price') }}" required>
                                    @error('price')
                                    <span class="text-danger price_error"> {{ $message }} </span>
                                    @enderror

                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="feedDiscountAmount"> Discount Amount </label>
                                    <input type="text" placeholder="Discount Amount" name="discount_amount"
                                        value="{{ $purchase?->discount_amount ?? old('discount_amount') }}"
                                        class="form-control" id="feedDiscountAmount">

                                    @error('discount_amount')
                                    <span class="text-danger discount_amount_error"> {{ $message }} </span>
                                    @enderror

                                </div>
                                <span class="text-danger h6" id="PriceQtyError" style="display: none;"></span>
                                <div class="col-3 mb-2">
                                    <label for="feedDiscountPercentage"> Discount Percentage % </label>
                                    <input type="text" placeholder="Discount Percentage %" name="discount_percentage"
                                        value="{{ $purchase?->discount_percentage ?? old('discount_percentage') }}"
                                        class="form-control" id="feedDiscountPercentage">

                                    @error('discount_percentage')
                                    <span class="text-danger discount_percentage_error"> {{ $message }} </span>
                                    @enderror

                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label for="feedTotalPrice"> Total Price </label>
                                    <input type="text" placeholder="Total price" name="total_price"
                                        value="{{ $purchase?->total_price ?? old('total_price') }}" class="form-control"
                                        id="feedTotalPrice">

                                    @error('total_price')
                                    <span class="text-danger total_price_error"> {{ $message }} </span>
                                    @enderror

                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="BiltyNumber"> Bilty Number</label>
                                    <input type="text" placeholder="ENter Bilty Number" name="bilty_number"
                                        value="{{ $purchase?->bilty_number ?? old('bilty_number') }}"
                                        class="form-control" id="BiltyNumber">

                                    @error('bilty_number')
                                    <span class="text-danger bilty_number_error"> {{ $message }} </span>
                                    @enderror

                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="BiltyCharges"> Bilty Charges</label>
                                    <input type="text" placeholder="ENter Bilty Charges" name="bilty_charges"
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
                                    <input type="text" placeholder="Driver number" name="driver_contact"
                                        value="{{ $purchase?->driver_contact ?? old('driver_contact') }}"
                                        class="form-control" id="Dcontact">

                                    @error('driver_contact')
                                    <span class="text-danger driver_contact_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-3 mb-2">
                                    <label for="SoNumber"> Sale Order Number </label>
                                    <input type="text" placeholder="Enter Sale Order Number" name="sale_order_number"
                                        value="" class="form-control" id="SoNumber">

                                    @error('sale_order_number')
                                    <span class="text-danger sale_order_number_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-3 mb-2">
                                    <label for="Do_Number"> Delivery Order Number </label>
                                    <input type="text" placeholder="Enter Delivery Order Number"
                                        name="delivery_order_number" value="" class="form-control" id="Do_Number">

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
                                    <a href="{{ asset('storage/chickens/'.$purchase?->picture) }}" target="_blank"> <img
                                            class="d-flex me-3 avatar-lg" target="_blank"
                                            src="{{ asset('storage/chickens/'.$purchase?->picture) }}" alt="No image">
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

        var companies_list = <?php echo json_encode($compaines) ?>;
        console.log(companies_list)
        $( "#feedCompanyId" ).change(function() {
            let company_id_modal = parseInt($(this).val())
            let find_company = companies_list?.find(x => x.id === company_id_modal);
            if(find_company){
                $('#feedCompanyAddr').val(find_company?.address || '');
                $('#feedCompanyContact').val(find_company?.contact_no || '');
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