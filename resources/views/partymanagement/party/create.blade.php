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
                        <li class="breadcrumb-item"> <a href="{{ route('purchase.index') }}"> PartyManagement </a>
                        </li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
                <h4 class="page-title">Create Party</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4>Create Party </h4>
                        </div>

                        <div class="col-6 align-self-end text-end mb-2">
                            <a class="btn btn-secondary btn-sm" href="{{ route('parties.index') }}"
                                title="Click to back" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-arrow-left"></i>
                                Back
                            </a>
                        </div>

                        <form method="post" action="{{ ($party->id) ? route('parties.update', $party->id ) :
                        route('parties.store') }}" enctype="multipart/form-data" autocomplete="off" id="PartyForm"
                            class="form_loader">
                            @csrf

                            @if ($party?->id)
                            @method('PUT')
                            @endif
                            <div class="row form-group">
                                <div class="col-sm-3 mb-2">
                                    <label for="PartyName" class="font_bold"> Name * </label>
                                    <input type="text" required placeholder="Enter name" name="name"
                                        value="{{ old('name', $party?->name)}}" class="form-control" id="PartyName">

                                    @error('name')
                                    <span class="text-danger name_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="PartyguardianName"> Father Name/ Guardian name
                                    </label>
                                    <input type="text" placeholder="Enter Father/Gardian name" name="guardian_name"
                                        value="{{ old('guardian_name', $party?->guardian_name)}}" class="form-control"
                                        id="PartyguardianName">
                                    @error('guardian_name')
                                    <span class="text-danger guardian_name_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="PartyCnicNo"> CNIC *</label>
                                    <input type="text" placeholder="Enter CNIC number" name="cnic_no"
                                        value="{{ old('cnic_no', $party?->cnic_no)}}" required class="form-control"
                                        id="PartyCnicNo">
                                    @error('cnic_no')
                                    <span class="text-danger cnic_no_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-3 mb-2">
                                    <label class="font_bold" for=""> Select Type* </label> <br>
                                    <div class="form-check mb-2 mt-1 form-check-inline">
                                        <input class="form-check-input"
                                            style="width: 1.7em !important; height: 1.7em !important;" type="checkbox"
                                            name="is_vendor" value="1" id="VendorCheckBox" {{ (! empty(old('is_vendor',
                                            $party?->is_vendor))
                                        ? 'checked' : '' ) }}>
                                        <label class="font_bold" class="form-check-label " for="VendorCheckBox"
                                            style="padding: 5px;">
                                            Vendor</label>
                                    </div>

                                    <div class="form-check mb-2 form-check-inline form-check-success">
                                        <input class="form-check-input" style=" width: 1.7em;
                                        height: 1.7em;" type="checkbox" name="is_customer" value="1"
                                            id="CustomerCheckBox" {{ (! empty(old('is_customer', $party?->is_customer))
                                        ? 'checked'
                                        : '' ) }}>
                                        <label class="font_bold" class="form-check-label" style="padding: 5px;"
                                            for="CustomerCheckBox">
                                            Customer</label>
                                    </div>
                                    <span class="text-danger name_error"></span>
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="customerEmail"> Email </label>
                                    <input type="text" placeholder="Enter email" name="email"
                                        value="{{ old('email', $party?->email)}}" class="form-control"
                                        id="customerEmail">
                                    @error('email')
                                    <span class="text-danger email_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="PartyContactNo"> Contact Number *</label>
                                    <input type="number" placeholder="Enter contact number" name="contact_no" required
                                        class="form-control" value="{{ old('contact_no', $party?->contact_no)}}"
                                        id="PartyContactNo">
                                    @error('contact_no')
                                    <span class="text-danger contact_no_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="PartyBusinessNo"> Business Number </label>
                                    <input type="number" placeholder="Enter business number" name="business_no"
                                        class="form-control" value="{{ old('business_no', $party?->business_no)}}"
                                        id="PartyBusinessNo">
                                    @error('business_no')
                                    <span class="text-danger business_no_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="PartyManualNumber">Manual Series Number</label>
                                    <input type="text" placeholder="Enter Manual Series Number" name="manual_number"
                                        class="form-control" value="{{ old('manual_number', $party?->manual_number)}}"
                                        id="PartyManualNumber">
                                    @error('manual_number')
                                    <span class="text-danger manual_number_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mt-1">

                                @include('layouts._partial.country_province_city',[
                                'countries' => $countries,
                                'updateData' => $party
                                ])

                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="PartyAddress"> Address </label>
                                    <input type="text" class="form-control" name="address" placeholder="Enter Address"
                                        value="{{ old('address', $party?->address)}}" id="PartyAddress">
                                    @error('address')
                                    <span class="text-danger address_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                            </div>

                            <div id="CustomerFarmRow" class="row border border-2 border-dark rounded-2 mt-1">
                                <h4 class="my-2"> Customer Data </h4>

                                <div class="col-4 mb-2">
                                    <label class="font_bold" for="CustomerDivision"> Select Division </label>
                                    <div class="input-group">
                                        <select name="customer_division_id" id="CustomerDivision"
                                            class="form-control mySelect" data-toggle="select2" data-width="89%">
                                            <option value=""> Select division </option>
                                            @forelse ($divisions as $item)
                                            <option {{ (! empty(old('customer_division_id', $party?->
                                                customer_division_id
                                                )==$item->id) ?
                                                'selected'
                                                : 'selected' ) }} value="{{$item->id}}">{{$item->name}}</option>

                                            @empty

                                            @endforelse

                                        </select>
                                        <a href="#"
                                            class="btn input-group-text btn-dark waves-effect waves-light OpenaddTypeModal"
                                            title="Click to add new" SelectBoxId="CustomerDivision"
                                            TableName="divisions" type="button">
                                            <i class="fa fa-plus"></i>
                                        </a>
                                    </div>
                                    @error('customer_division_id')
                                    <span class="text-danger customer_division_id_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-4 mb-2">
                                    <label class="font_bold" for="PartyCustomerType"> Select Customer Type* </label>

                                    <div class="input-group">
                                        <select name="customer_type_id" id="PartyCustomerType"
                                            class="form-control mySelect" data-toggle="select2" data-width="89%" id="">
                                            <option value=""> Select Customer Type</option>
                                            @forelse ($customer_types as $item)
                                            <option {{ (! empty(old('customer_type_id', $party?->
                                                customer_type_id)==$item->id) ?
                                                'selected'
                                                : '' ) }} value="{{$item->id}}">{{$item->name}}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                        <a href="#"
                                            class="btn input-group-text btn-dark waves-effect waves-light OpenaddTypeModal"
                                            TableName="customer_types" SelectBoxId="PartyCustomerType"
                                            title="Click to add new" type="button"> <i class="fa fa-plus"></i> </a>
                                    </div>
                                    @error('customer_type_id')
                                    <span class="text-danger customer_type_id_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-4 mb-2">
                                    <label class="font_bold" for="CustomerFarmType"> Select Farm Type * </label>
                                    <div class="input-group">
                                        <select name="farm_type_id" id="CustomerFarmType" class="form-control mySelect"
                                            data-toggle="select2" data-width="89%">
                                            <option value=""> Select Farm Type</option>
                                            @forelse ($farm_types as $item)
                                            <option {{ (! empty(old('farm_type_id', $party?->farm_type_id)==$item->id) ?
                                                'selected'
                                                : 'selected' ) }} value="{{$item->id}}">{{$item->name}}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                        <a href="#"
                                            class="btn input-group-text btn-dark waves-effect waves-light OpenaddTypeModal"
                                            SelectBoxId="CustomerFarmType" TableName="farm_types"
                                            title="Click to add new" type="button"> <i class="fa fa-plus"></i> </a>
                                    </div>
                                    @error('farm_type_id')
                                    <span class="text-danger farm_type_id_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-4 mb-2">
                                    <label class="font_bold" for="PartyFarmSubtype"> Select Farm Subtype * </label>
                                    <div class="input-group">
                                        <select name="farm_subtype_id" id="PartyFarmSubtype"
                                            class="form-control mySelect" data-toggle="select2" data-width="89%">
                                            <option value=""> Select Farm subtype</option>
                                            @forelse ($farm_subtypes as $item)
                                            <option {{ (! empty(old('farm_subtype_id', $party?->
                                                farm_subtype_id)==$item->id) ? 'selected'
                                                : 'selected' ) }} value="{{$item->id}}">{{$item->name}}</option>
                                            @empty
                                            @endforelse
                                        </select>

                                        <a href="#"
                                            class="btn input-group-text btn-dark waves-effect waves-light OpenaddTypeModal"
                                            SelectBoxId="PartyFarmSubtype" TableName="farm_subtypes"
                                            title="Click to add new" type="button"> <i class="fa fa-plus"></i> </a>
                                    </div>
                                    @error('farm_subtype_id')
                                    <span class="text-danger farm_subtype_id_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-4 mb-2">
                                    <label class="font_bold" for="customerFarmName">Farm Name *</label>
                                    <input type="text" placeholder="Enter Farm Name" name="farm_name"
                                        value="{{ old('farm_name', $party?->farm_name) }}" class="form-control"
                                        id="customerFarmName">
                                    @error('farm_name')
                                    <span class="text-danger farm_name_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-4 mb-2">
                                    <label class="font_bold" for="FarmNOC">Farm NOC *</label>
                                    <input type="text" placeholder="Enter Farm NOC number" name="farm_noc"
                                        value="{{ old('farm_noc',$party?->darm_noc) }}" class="form-control"
                                        id="FarmNOC">
                                    @error('farm_noc')
                                    <span class="text-danger farm_noc_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-4 mb-2">
                                    <label class="font_bold" for="FarmImage">Farm Image *</label>
                                    <input type="file" name="farm_image" class="form-control" id="FarmImage">
                                    @error('farm_image')
                                    <span class="text-danger farm_image_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-4 mb-2">
                                    <label class="font_bold" for="CustomerFarmAddress">Farm Address</label>
                                    <input type="text" placeholder="Enter Farm Address" name="farm_address"
                                        value="{{ old('farm_address', $party?->farm_address) }}" class="form-control"
                                        id="CustomerFarmAddress">
                                    @error('farm_address')
                                    <span class="text-danger farm_address_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                            </div>

                            <div id="VendorCompanyRow" class="row border border-2 rounded-2 border-primary mt-2">
                                <h4 class="my-2"> Company data </h4>

                                <div class="col-4 mb-2">
                                    <label class="font_bold" for="PartyVendorDivision"> Select Division* </label>
                                    <div class="input-group">
                                        <select name="vendor_division_id" id="PartyVendorDivision"
                                            class="form-control mySelect" data-toggle="select2" data-width="89%">
                                            <option value=""> Select division </option>
                                            @forelse ($divisions as $item)
                                            <option {{ (! empty(old('vendor_division_id', $party?->
                                                vendor_division_id)==$item->id) ?
                                                'selected'
                                                : '' ) }} value="{{$item->id}}">{{$item->name}}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                        <a href="#"
                                            class="btn input-group-text btn-dark waves-effect waves-light OpenaddTypeModal"
                                            SelectBoxId="PartyVendorDivision" TableName="divisions"
                                            title="Click to add new" type="button"> <i class="fa fa-plus"></i> </a>
                                    </div>
                                    @error('vendor_division_id')
                                    <span class="text-danger vendor_division_id_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-4 mb-2">
                                    <label class="font_bold" for="PartyVendorType"> Select Vendor Type * </label>
                                    <div class="input-group">
                                        <select name="vendor_type_id" id="PartyVendorType" class="form-control mySelect"
                                            data-toggle="select2" data-width="89%" id="">
                                            <option value=""> Select Vendor Type </option>
                                            @forelse ($vendor_types as $item)
                                            <option {{ (! empty(old('vendor_type_id', $party?->
                                                vendor_type_id)==$item->id) ?
                                                'selected'
                                                : 'selected' ) }} value="{{$item->id}}">{{$item->name}}</option>
                                            @empty
                                            @endforelse
                                            <option value="1"> abc</option>
                                        </select>
                                        <a href="#"
                                            class="btn input-group-text btn-dark waves-effect waves-light OpenaddTypeModal"
                                            SelectBoxId="PartyVendorType" TableName="vendor_types"
                                            title="Click to add new" type="button"> <i class="fa fa-plus"></i> </a>
                                    </div>
                                    @error('vendor_type_id')
                                    <span class="text-danger vendor_type_id_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-4 mb-2">
                                    <label class="font_bold" for="PartyCompanyName">Company Name*</label>
                                    <input type="text" placeholder="Enter Company Name" name="company_name"
                                        value="{{ old('company_name', $party?->company_name) }}" class="form-control"
                                        id="PartyCompanyName">
                                    @error('company_name')
                                    <span class="text-danger company_name_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-4 mb-2">
                                    <label class="font_bold" for="PartyBusinessType"> Select Business Type * </label>
                                    <div class="input-group">
                                        <select name="business_type_id" id="PartyBusinessType"
                                            class="form-control mySelect" data-toggle="select2" data-width="89%" id="">
                                            <option value=""> Select Business Type </option>

                                            @forelse ($business_types as $item)
                                            <option {{ (! empty(old('business_type_id', $party?->
                                                business_type_id)==$item->id) ?
                                                'selected'
                                                : 'selected' ) }} value="{{$item->id}}">{{$item->name}}</option>
                                            @empty
                                            @endforelse
                                        </select>

                                        <a href="#"
                                            class="btn input-group-text btn-dark waves-effect waves-light OpenaddTypeModal"
                                            SelectBoxId="PartyBusinessType" TableName="business_types"
                                            title="Click to add new" type="button"> <i class="fa fa-plus"></i> </a>
                                    </div>
                                    @error('business_type_id')
                                    <span class="text-danger business_type_id_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-4 mb-2">
                                    <label class="font_bold" for="CompanyLogo">Company logo *</label>
                                    <input type="file" name="company_logo" class="form-control" id="CompanyLogo">
                                    @error('company_logo')
                                    <span class="text-danger company_logo_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-4 mb-2">
                                    <label class="font_bold" for="CompanyAddress">Company Address *</label>
                                    <input type="text" placeholder="Enter Company Address" name="company_address"
                                        value="{{ old('company_address', $party?->company_address) }}"
                                        class="form-control" id="CompanyAddress">
                                    @error('company_address')
                                    <span class="text-danger company_address_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="PartyProfileImage"> Profile Picture </label>
                                    <input type="file" name="profile_picture" class="form-control"
                                        id="PartyProfileImage">
                                    @error('profile_picture')
                                    <span class="text-danger profile_picture_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="CnicFront">Cnic Front</label>
                                    <input type="file" name="cnic_front" class="form-control" required id="CnicFront">
                                    @error('cnic_front')
                                    <span class="text-danger cnic_front_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="CnicBack">Cnic Back</label>
                                    <input type="file" required name="cnic_back" class="form-control" id="CnicBack">
                                    @error('cnic_back')
                                    <span class="text-danger cnic_back_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="PartySignatureImage">Signature</label>
                                    <input type="file" name="signature_image" class="form-control"
                                        id="PartySignatureImage">
                                    @error('signature_image')
                                    <span class="text-danger signature_image_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mt-2">
                                {{-- <div class="col-3 mb-2">
                                    <label class="font_bold" for="PartyAgreement"> Agreement Pictures * </label>
                                    <input type="file" name="party_agreement[]" id="PartyAgreement"
                                        class="form-control">
                                    <span class="text-danger party_agreement_error"> </span>
                                </div> --}}
                                <div class="col-4 mb-2">
                                    <label class="font_bold" for="PartyContactPerson"> Select contact Person</label>
                                    <select name="contact_person_id" id="PartyContactPerson"
                                        class="form-control mySelect" data-toggle="select2" data-width="100%">
                                        <option value=""> Select contact Person </option>
                                        @forelse ($contact_persons as $item)
                                        <option {{ (! empty(old('contact_person_id', $party?->
                                            contact_person_id)==$item->id) ?
                                            'selected'
                                            : 'selected' ) }} value="{{ $item->id }}"> {{ $item->name }}</option>
                                        @empty
                                        @endforelse

                                    </select>
                                    @error('contact_person_id')
                                    <span class="text-danger contact_person_id_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-8">
                                    <label class="font_bold" for="Description">Description</label>
                                    <textarea class="form-control ckeditor" name="description" id="Description"
                                        placeholder="Description" cols="80"
                                        rows="2">{{ old('description', $party?->description) }}</textarea>
                                    @error('description')
                                    <span class="text-danger description_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                {{-- <div class="col-sm-6 mt-2">
                                    <label class="font_bold" for="image">Image</label>
                                    <input type="file" class="form-control" name="image_file">
                                    <span class="text-danger image_file_error"> </span>
                                </div>
                                <div class="col-sm-6 mt-2 img-holder">
                                    <img class="d-flex me-3 avatar-lg" src="../assets/images/users/user-8.jpg"
                                        alt="Generic placeholder image">
                                </div> --}}
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-4 mb-3">
                                    <button type="submit" id="sub"
                                        class="btn btn-secondary btn-sm waves-effect waves-light mt-3 AddUpdate">
                                        Submit
                                    </button>
                                    <a clas href="#"
                                        class="btn btn-light btn-sm waves-effect waves-light mt-3 ModalClosed">
                                        Cancel
                                    </a>
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
        
        $("#CustomerCheckBox").click(function() {
            if($(this).is(":checked")) {
                $("#CustomerFarmRow").fadeIn();
                if(! $('#VendorCheckBox').is(":checked")){
                    $("#VendorCompanyRow").hide();
                }
            } else {
                $("#CustomerFarmRow").hide();
                if(! $('#VendorCheckBox').is(":checked")){
                    $("#VendorCompanyRow").hide();
                }
            }
        });
        $("#VendorCheckBox").click(function() {
            if($(this).is(":checked")) {
                $("#VendorCompanyRow").fadeIn();
                if(! $('#CustomerCheckBox').is(":checked")){
                    $("#CustomerFarmRow").hide();
                }
            } else {
                $("#VendorCompanyRow").hide();
                if(! $('#CustomerCheckBox').is(":checked")){
                    $("#CustomerFarmRow").hide();
                }
            }
        });
    });
</script>
@endsection