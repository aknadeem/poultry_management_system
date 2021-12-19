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

                        <form autocomplete="off" method="post" enctype="multipart/form-data" id="VendorForm"
                            class="form_loader">
                            @csrf
                            <div class="row form-group">
                                <input type="hidden" name="party_id" id="PartyId" value="0">
                                <div class="col-sm-3 mb-2">
                                    <label for="VendorName" class="font_bold"> Name * </label>
                                    <input type="text" placeholder="Enter name" name="name" class="form-control"
                                        id="VendorName">
                                    <span class="text-danger name_error"></span>
                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="GuardianName"> Father Name/ Guardian name * </label>
                                    <input type="text" placeholder="Enter Father/Gardian name" name="guardian_name"
                                        class="form-control" id="GuardianName">
                                    <span class="text-danger guardian_name_error"></span>
                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="CnicNo">CNIC *</label>
                                    <input type="text" placeholder="Enter CNIC number" name="cnic_no"
                                        class="form-control" id="CnicNo">
                                    <span class="text-danger cnic_no_error"> </span>
                                </div>
                                <div class="col-3 mb-2">
                                    <label class="font_bold" for=""> Select Type* </label> <br>
                                    {{-- <div class="form-check mb-2 mt-1 form-check-inline">
                                        <input class="form-check-input"
                                            style="width: 1.7em !important; height: 1.7em !important;" type="checkbox"
                                            name="is_vendor" value="1" id="customckeck1" checked="">
                                        <label class="font_bold" class="form-check-label " for="customckeck1"
                                            style="padding: 5px;">
                                            Vendor</label>
                                    </div> --}}

                                    <div class="form-check mb-2 form-check-inline form-check-success">
                                        <input class="form-check-input" style=" width: 1.7em;
                                        height: 1.7em;" type="checkbox" name="is_customer" value="1" id="customckeck2"
                                            checked="true" readaonly disabled>
                                        <label class="font_bold" class="form-check-label" style="padding: 5px;"
                                            readaonly for="customckeck2">
                                            Customer</label>
                                    </div>
                                    <span class="text-danger name_error"></span>
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="VendorEmail"> Email *</label>
                                    <input type="text" placeholder="Enter email" name="email" class="form-control"
                                        id="VendorEmail">
                                    <span class="text-danger email_error"></span>
                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="ContactNo"> Contact Number *</label>
                                    <input type="number" placeholder="Enter contact number" name="contact_no"
                                        class="form-control" id="ContactNo">
                                    <span class="text-danger contact_no_error"></span>
                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="BusinessNo"> Business Number *</label>
                                    <input type="number" placeholder="Enter business number" name="business_no"
                                        class="form-control" id="BusinessNo">
                                    <span class="text-danger business_no_error"></span>
                                </div>
                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="ManualSrNo">Manual Series *</label>
                                    <input type="text" placeholder="Enter Manual Series Number" name="manual_number"
                                        class="form-control" id="ManualSrNo">
                                    <span class="text-danger manual_number_error"> </span>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="PartyCountry"> Select Country* </label>
                                    <select name="country_id" class="form-control mySelect" id="PartyCountry" required
                                        data-toggle="select2" data-width="100%" id="">
                                        <option value=""> Select Country</option>
                                        <option value="1"> Pakistan</option>
                                    </select>
                                    <span class="text-danger country_id_error"> </span>
                                </div>
                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="PartyProvince"> Select Province* </label>
                                    <select name="province_id" required id="PartyProvince" class="form-control mySelect"
                                        data-toggle="select2" data-width="100%" id="">
                                        <option value=""> Select Province</option>
                                        <option value="1"> Punjab</option>
                                    </select>
                                    <span class="text-danger province_id_error"> </span>
                                </div>
                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="PartyCity"> Select City* </label>
                                    <select name="city_id" id="PartyCity" class="form-control mySelect" required
                                        data-toggle="select2" data-width="100%" id="">
                                        <option value=""> Select City</option>
                                        <option value="1"> Lahore</option>
                                    </select>
                                    <span class="text-danger city_id_error"> </span>
                                </div>

                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="PartyAddress"> Address</label>
                                    <input type="text" name="PartyAddress" class="form-control" name="address"
                                        placeholder="Enter Address">
                                    <span class="text-danger address_error"> </span>
                                </div>
                            </div>

                            <div id="VendorCompanyRow" class="row border border-2 rounded-2 border-primary mt-2">
                                <h4 class="my-2">Company data </h4>

                                <div class="col-4 mb-2">
                                    <label class="font_bold" for="PartyVendorDivision"> Select Division* </label>
                                    <select name="vendor_division_id" id="PartyVendorDivision"
                                        class="form-control mySelect" data-toggle="select2" data-width="100%">
                                        <option value=""> Select division </option>
                                        <option value="1"> abc</option>
                                    </select>
                                    <span class="text-danger vendor_division_id_error"> </span>
                                </div>

                                <div class="col-4 mb-2">
                                    <label class="font_bold" for="PartyVendorType"> Select Vendor Type * </label>
                                    <select name="vendor_type_id" id="PartyVendorType" class="form-control mySelect"
                                        required data-toggle="select2" data-width="100%" id="">
                                        <option value=""> Select Vendor Type </option>
                                        <option value="1"> abc</option>
                                    </select>
                                    <span class="text-danger vendor_type_id_error"> </span>
                                </div>

                                <div class="col-4 mb-2">
                                    <label class="font_bold" for="PartyCompanyName">Company Name*</label>
                                    <input type="text" placeholder="Enter Company Name" name="company_name"
                                        class="form-control" id="PartyCompanyName">
                                    <span class="text-danger company_name_error"> </span>
                                </div>
                                <div class="col-4 mb-2">
                                    <label class="font_bold" for="PartyBusinessType"> Select Business Type * </label>
                                    <select name="business_type_id" id="PartyBusinessType" class="form-control mySelect"
                                        required data-toggle="select2" data-width="100%" id="">
                                        <option value=""> Select Business Type </option>
                                        <option value="1"> abc</option>
                                    </select>
                                    <span class="text-danger business_type_id_error"> </span>
                                </div>
                                <div class="col-4 mb-2">
                                    <label class="font_bold" for="CompanyLogo">Company logo *</label>
                                    <input type="file" name="company_logo_image" class="form-control" id="CompanyLogo">
                                    <span class="text-danger company_logo_image_error"> </span>
                                </div>
                                <div class="col-4 mb-2">
                                    <label class="font_bold" for="CompanyAddress">Company Address *</label>
                                    <input type="text" placeholder="Enter Company Address" name="company_address"
                                        class="form-control" id="CompanyAddress">
                                    <span class="text-danger company_address_error"> </span>
                                </div>
                            </div>


                            <div class="row mt-2">
                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="cno"> Profile Picture *</label>
                                    <input type="file" name="farm_name" class="form-control" id="customerFarmName">
                                    <span class="text-danger farm_name_error"> </span>
                                </div>
                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="cno">Cnic Front *</label>
                                    <input type="file" name="farm_name" class="form-control" id="customerFarmName">
                                    <span class="text-danger farm_name_error"> </span>
                                </div>
                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="cno">Cnic Back *</label>
                                    <input type="file" name="farm_name" class="form-control" id="customerFarmName">
                                    <span class="text-danger farm_name_error"> </span>
                                </div>
                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="cno">Signature*</label>
                                    <input type="file" name="farm_name" class="form-control" id="customerFarmName">
                                    <span class="text-danger farm_name_error"> </span>
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
                                    <label class="font_bold" for="PartyContactPerson"> Select Contact Person * </label>
                                    <select name="contact_person_id" id="PartyContactPerson"
                                        class="form-control mySelect" required data-toggle="select2" data-width="100%">
                                        <option value=""> Select Contact Person </option>
                                        <option value="1"> abc</option>
                                    </select>
                                    <span class="text-danger contact_person_id_error"> </span>
                                </div>

                                <div class="col-8">
                                    <label class="font_bold" for="address">Description*</label>
                                    <textarea class="form-control ckeditor" name="description" id="customerdescription"
                                        cols="80" rows="2"></textarea>
                                    <span class="text-danger description_error"> </span>
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
                                    <button class="btn btn-light btn-sm waves-effect waves-light mt-3 ModalClosed">
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
@endsection

@section('custom_scripts')
<script>
    $(function() {
        
    });
</script>
@endsection