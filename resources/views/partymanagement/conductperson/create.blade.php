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
                        <li class="breadcrumb-item"> <a href="{{ route('purchase.index') }}"> PartyManagement </a>
                        </li>
                        <li class="breadcrumb-item">Conduct Person</li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
                <h4 class="page-title">Party Management</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4>Create Conduct Person </h4>
                        </div>

                        <div class="col-6 align-self-end text-end mb-2">
                            <a class="btn btn-secondary btn-sm" href="{{ route('conductpersons.index') }}"
                                title="Click to go back" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-arrow-left"></i>
                                Back
                            </a>
                        </div>

                        <form autocomplete="off" method="post" enctype="multipart/form-data" id="ConductPersonForm">
                            @csrf
                            <div class="row form-group">
                                <input type="hidden" name="conduct_person_id" id="conduct_person_id" value="0">
                                <div class="col-sm-3 mb-2">
                                    <label for="name" class="font_bold"> Name * </label>
                                    <input type="text" placeholder="Enter name" name="name" class="form-control"
                                        required id="customerName">

                                    @error('name')
                                    <span class="text-danger name_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="name"> Father Name/ Guardian name * </label>
                                    <input type="text" placeholder="Enter Father/Gardian name" name="guardian_name"
                                        class="form-control" id="Partyguardian_name">

                                    @error('guardian_name')
                                    <span class="text-danger guardian_name_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="cno">CNIC *</label>
                                    <input type="number" min="13" max="13"
                                        placeholder="Enter CNIC number Without Dashes(-)" name="cnic_no"
                                        class="form-control" id="Partycnic_mo">

                                    @error('cnic_no')
                                    <span class="text-danger cnic_no_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="cno"> Email *</label>
                                    <input type="text" placeholder="Enter email" name="email" class="form-control"
                                        id="customerEmail">

                                    @error('email')
                                    <span class="text-danger email_error"> {{ $message }} </span>
                                    @enderror

                                    <span class="text-danger email_error"></span>
                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="cno"> Contact Number *</label>
                                    <input type="number" placeholder="Enter contact number" name="contact_no"
                                        class="form-control" id="PartyContactNo">

                                    @error('contact_no')
                                    <span class="text-danger contact_no_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="PartyCountry"> Select Country* </label>
                                    <select name="country_id" class="form-control mySelect" id="PartyCountry" required
                                        data-toggle="select2" data-width="100%" id="">
                                        <option value=""> Select Country</option>
                                        <option value="1"> Pakistan</option>
                                    </select>

                                    @error('country_id')
                                    <span class="text-danger country_id_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="PartyProvince"> Select Province* </label>
                                    <select name="province_id" required id="PartyProvince" class="form-control mySelect"
                                        data-toggle="select2" data-width="100%" id="">
                                        <option value=""> Select Province</option>
                                        <option value="1"> Punjab</option>
                                    </select>

                                    @error('province_id')
                                    <span class="text-danger province_id_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="PartyCity"> Select City* </label>
                                    <select name="city_id" id="PartyCity" class="form-control mySelect" required
                                        data-toggle="select2" data-width="100%" id="">
                                        <option value=""> Select City</option>
                                        <option value="1"> Lahore</option>
                                    </select>

                                    @error('city_id')
                                    <span class="text-danger city_id_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="PartyAddress"> Address</label>
                                    <input type="text" name="PartyAddress" class="form-control" name="address"
                                        placeholder="Enter Address">

                                    @error('address')
                                    <span class="text-danger address_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="cno"> Profile Picture *</label>
                                    <input type="file" name="image_file" class="form-control" id="customerFarmName">

                                    @error('image_file')
                                    <span class="text-danger image_file_error"> {{ $message }} </span>
                                    @enderror
                                </div>
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

</script>
@endsection