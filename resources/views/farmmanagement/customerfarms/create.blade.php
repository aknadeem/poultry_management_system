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
                        <li class="breadcrumb-item"> ShedManagement</li>
                        <li class="breadcrumb-item"> PersonalSheds </li>

                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
                <h4 class="page-title">Shed Management</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4>Create New Shed </h4>
                        </div>

                        <div class="col-6 align-self-end text-end mb-2">
                            <a class="btn btn-secondary btn-sm" href="{{ route('customerfarms.index') }}"
                                title="Click to go back" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-arrow-left"></i>
                                Back
                            </a>
                        </div>

                        <form autocomplete="off" method="post" enctype="multipart/form-data" id="ConductPersonForm"
                            class="form_loader">
                            @csrf
                            <div class="row form-group">
                                <input type="hidden" name="conduct_person_id" id="conduct_person_id" value="0">
                                <div class="col-sm-3 mb-2">
                                    <label for="name" class="font_bold"> Name * </label>
                                    <input type="text" placeholder="Enter name" name="name" class="form-control"
                                        id="customerName">
                                    <span class="text-danger name_error"></span>
                                </div>
                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="ShedType"> Select Type* </label>
                                    <select name="shed_type_id" required id="ShedType" class="form-control mySelect"
                                        data-toggle="select2" data-width="100%" id="">
                                        <option value=""> Select Type</option>
                                        <option value="1"> abc </option>
                                    </select>
                                    <span class="text-danger shed_type_id_error"> </span>
                                </div>

                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="ShedType"> Select Subtype* </label>
                                    <select name="shed_subtype_id" required id="ShedType" class="form-control mySelect"
                                        data-toggle="select2" data-width="100%" id="">
                                        <option value=""> Select Subtype</option>
                                        <option value="1"> abc </option>
                                    </select>
                                    <span class="text-danger shed_subtype_id_error"> </span>
                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="shedArea"> Area </label>
                                    <input type="number" step="any" min="0" placeholder="Enter Area" name="shed_area"
                                        class="form-control" id="shedArea">
                                    <span class="text-danger shed_area_error"> </span>
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="ShedCapacity"> Capacity *</label>
                                    <input type="number" step="any" min="0" placeholder="Enter Capacity"
                                        name="shed_capacity" class="form-control" id="ShedCapacity">
                                    <span class="text-danger shed_capacity_error"></span>
                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="FeedRoomSize"> Feed Room Size *</label>
                                    <input type="number" step="any" min="0" placeholder="Enter contact number"
                                        name="feed_room_size" class="form-control" id="FeedRoomSize">
                                    <span class="text-danger feed_room_size_error"></span>
                                </div>

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

                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="shedPicture"> Shed Picture *</label>
                                    <input type="file" name="shed_picture" class="form-control" id="shedPicture">
                                    <span class="text-danger farm_name_error"> </span>
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
    $(function() {
        
    });
</script>
@endsection