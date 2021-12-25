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
                        <li class="breadcrumb-item"> <a href="{{ route('parties.index') }}"> PartyManagement </a>
                        </li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
                <h4 class="page-title">Vendor</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4>Create Vendor </h4>
                        </div>

                        <div class="col-6 align-self-end text-end mb-2">
                            <a class="btn btn-secondary btn-sm" href="{{ route('vendors.index') }}"
                                title="Click to go back" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-arrow-left"></i>
                                Back
                            </a>
                        </div>

                        <form method="post" action="{{ ($party->id) ? route('parties.update', $party->id ) :
                            route('parties.store') }}" enctype="multipart/form-data" autocomplete="off" id="PartyForm"
                            class="form_loader">
                            @csrf
                            <div class="row form-group">
                                <input type="hidden" name="party_id" id="PartyId" value="0">
                                <input type="hidden" name="from_vendor" id="from_vendor" value="from_vendor">
                                <div class="col-sm-3 mb-2">
                                    <label for="PartyName" class="font_bold"> Name * </label>
                                    <input type="text" required placeholder="Enter name" name="name"
                                        value="{{ old('name')}}" class="form-control" id="PartyName">

                                    @error('name')
                                    <span class="text-danger name_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="PartyguardianName"> Father Name/ Guardian name
                                    </label>
                                    <input type="text" placeholder="Enter Father/Gardian name" name="guardian_name"
                                        value="{{ old('guardian_name')}}" class="form-control" id="PartyguardianName">
                                    @error('guardian_name')
                                    <span class="text-danger guardian_name_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="PartyCnicNo"> CNIC *</label>
                                    <input type="text" placeholder="Enter CNIC number" name="cnic_no"
                                        value="{{ old('cnic_no')}}" required class="form-control" id="PartyCnicNo">
                                    @error('cnic_no')
                                    <span class="text-danger cnic_no_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-3 mb-2">
                                    <label class="font_bold" for=""> Type* </label>
                                    <input type="hidden" name="is_vendor" value="1">
                                    <br>
                                    <div class="form-check mb-2 mt-1 form-check-inline">
                                        <input class="form-check-input"
                                            style="width: 1.7em !important; height: 1.7em !important;" type="checkbox"
                                            name="is_vendor" value="1" readonly disabled id="VendorCheckBox" {{ (!
                                            empty(old('is_vendor')) ? 'checked' : 'checked' ) }}>
                                        <label class="font_bold" class="form-check-label " for="VendorCheckBox"
                                            style="padding: 5px;">
                                            Vendor</label>
                                    </div>
                                    <span class="text-danger name_error"></span>
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="customerEmail"> Email </label>
                                    <input type="text" placeholder="Enter email" name="email" value="{{ old('email')}}"
                                        class="form-control" id="customerEmail">
                                    @error('email')
                                    <span class="text-danger email_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="PartyContactNo"> Contact Number *</label>
                                    <input type="number" placeholder="Enter contact number" name="contact_no" required
                                        class="form-control" value="{{ old('contact_no')}}" id="PartyContactNo">
                                    @error('contact_no')
                                    <span class="text-danger contact_no_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="PartyBusinessNo"> Business Number </label>
                                    <input type="number" placeholder="Enter business number" name="business_no"
                                        class="form-control" value="{{ old('business_no')}}" id="PartyBusinessNo">
                                    @error('business_no')
                                    <span class="text-danger business_no_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="PartyManualNumber">Manual Series Number</label>
                                    <input type="text" placeholder="Enter Manual Series Number" name="manual_number"
                                        class="form-control" value="{{ old('manual_number')}}" id="PartyManualNumber">
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
                                        value="{{ old('address')}}" id="PartyAddress">
                                    @error('address')
                                    <span class="text-danger address_error"> {{ $message }} </span>
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
                                            <option {{ (! empty(old('vendor_division_id')==$item->id) ? 'selected'
                                                : 'selected' ) }} value="{{$item->id}}">{{$item->name}}</option>
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
                                            <option {{ (! empty(old('vendor_type_id')==$item->id) ? 'selected'
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
                                        value="{{ old('company_name') }}" class="form-control" id="PartyCompanyName">
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
                                            <option {{ (! empty(old('business_type_id')==$item->id) ? 'selected'
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
                                        value="{{ old('company_address') }}" class="form-control" id="CompanyAddress">
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
                                    <label class="font_bold" for="CnicFront">Cnic Front*</label>
                                    <input type="file" name="cnic_front" class="form-control" required id="CnicFront">
                                    @error('cnic_front')
                                    <span class="text-danger cnic_front_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="CnicBack">Cnic Back*</label>
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
                                        <option {{ (! empty(old('contact_person_id')==$item->id) ? 'selected'
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
                                        rows="2">{{ old('company_name') }}</textarea>
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
@endsection

@section('custom_scripts')
<script>
    $(function() {
        
    });
</script>
@endsection