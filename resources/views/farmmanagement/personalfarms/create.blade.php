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
                        <li class="breadcrumb-item"> FarmManagement</li>
                        <li class="breadcrumb-item"> PersonalFarms </li>

                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
                <h4 class="page-title">Farm Management</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4>Add New Farm </h4>
                        </div>

                        <div class="col-6 align-self-end text-end mb-2">
                            <a class="btn btn-secondary btn-sm" href="{{ route('personalfarms.index') }}"
                                title="Click to go back" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-arrow-left"></i>
                                Back
                            </a>
                        </div>
                        <form method="post" action="{{ ($farm->id) ? route('personalfarms.update', $farm->id ) :
                            route('personalfarms.store') }}" autocomplete="off" enctype="multipart/form-data"
                            id="ConductPersonForm" class="form_loader">
                            @csrf
                            @if ($farm->id)
                            @method('PUT')
                            @endif
                            <div class="row form-group">
                                <input type="hidden" name="conduct_person_id" id="conduct_person_id" value="0">

                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="CustomerFarmType"> Select Farm Type * </label>
                                    <div class="input-group">
                                        <select name="farm_type_id" id="CustomerFarmType" class="form-control mySelect"
                                            data-toggle="select2" data-width="85%">
                                            <option value=""> Select Farm Type</option>
                                            @forelse ($farm_types as $item)
                                            <option {{ (! empty(old('farm_type_id')==$item->id) ? 'selected'
                                                : '') || ($farm?->farm_type_id==$item->id) ? 'selected'
                                                : '' }} value="{{$item->id}}">{{$item->name}}</option>
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

                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="PartyFarmSubtype"> Select Farm Subtype * </label>
                                    <div class="input-group">
                                        <select name="farm_subtype_id" id="PartyFarmSubtype"
                                            class="form-control mySelect" data-toggle="select2" data-width="85%">
                                            <option value=""> Select Farm subtype</option>
                                            @forelse ($farm_subtypes as $item)
                                            <option {{ (! empty(old('farm_subtype_id')==$item->id) ? 'selected'
                                                : '' ) || ($farm?->farm_subtype_id==$item->id) ? 'selected'
                                                : '' }} value="{{$item->id}}">{{$item->name}}</option>
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

                                <div class="col-sm-3 mb-2">
                                    <label for="FarmName" class="font_bold"> Farm Name * </label>
                                    <input type="text" placeholder="Enter name" name="farm_name" class="form-control"
                                        value="{{ old('farm_name', $farm?->farm_name) }}" required id="FarmName">
                                    <span class="text-danger name_error"></span>
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="FarmNoc" class="font_bold"> Farm Noc * </label>
                                    <input type="text" placeholder="Enter Noc Number" name="farm_noc" required
                                        class="form-control" value="{{ old('farm_noc', $farm?->farm_noc) }}"
                                        id="FarmNoc">
                                    <span class="text-danger name_error"></span>
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="shedArea"> Area </label>
                                    <input type="number" step="any" min="0" placeholder="Enter Area" name="farm_area"
                                        value="{{ old('farm_area', $farm?->farm_area) }}" required class="form-control"
                                        id="shedArea">
                                    <span class="text-danger farm_area_error"> </span>
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="ShedCapacity"> Capacity *</label>
                                    <input type="number" step="any" min="0" placeholder="Enter Capacity"
                                        name="farm_capacity" value="{{ old('farm_capacity', $farm?->farm_capacity) }}"
                                        required class="form-control" id="ShedCapacity">
                                    <span class="text-danger farm_capacity_error"></span>
                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="FeedRoomSize"> Feed Room Size *</label>
                                    <input type="number" step="any" min="0" placeholder="Enter Room size"
                                        name="feed_room_size"
                                        value="{{ old('feed_room_size', $farm?->feed_room_size) }}" class="form-control"
                                        id="FeedRoomSize">
                                    <span class="text-danger feed_room_size_error"></span>
                                </div>

                                @include('layouts._partial.country_province_city',[
                                'countries' => $countries,
                                'updateData' => $farm
                                ])

                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="FarmImage"> Farm Image *</label>
                                    <input type="file" name="farm_image" class="form-control" id="FarmImage">
                                    <span class="text-danger farm_image_error"> </span>
                                </div>

                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="PartyAddress"> Address</label>
                                    <input type="text" class="form-control" name="farm_address"
                                        value="{{ old('farm_address', $farm?->farm_address) }}"
                                        placeholder="Enter Address">
                                    <span class="text-danger farm_address_error"> </span>
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
@include('layouts._partial.add_types_modal')
@endsection

@section('custom_scripts')
<script>
    $(function() {
        
    });
</script>
@endsection