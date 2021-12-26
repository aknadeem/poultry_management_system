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
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item"> <a href="{{ route('parties.index') }}"> PartyManagement </a>
                        </li>
                        <li class="breadcrumb-item"> <a href="{{ route('brokers.index') }}"> Contact Persons </a>
                        </li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
                <h4 class="page-title">Broker</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4>Add Broker Entry </h4>
                        </div>

                        <div class="col-6 align-self-end text-end mb-2">
                            <a class="btn btn-secondary btn-sm" href="{{ route('brokers.index') }}"
                                title="Click to go back" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-arrow-left"></i>
                                Back
                            </a>
                        </div>

                        <form method="post" action="{{ ($broker->id) ? route('brokers.update', $broker->id ) :
                        route('brokers.store') }}" enctype="multipart/form-data" id="BrokerForm" class="form_loader"
                            autocomplete="off">
                            @csrf
                            @php
                            $required = 'required';
                            $exProvince_id = 0;
                            $exCity_id = 0;
                            if($broker->id){
                            $exProvince_id = $broker->province_id;
                            $exCity_id = $broker->city_id;
                            $required = '';
                            }
                            @endphp

                            @if($broker->id)
                            @method('PUT')
                            @endif

                            <div class="row form-group">
                                <input type="hidden" name="conduct_person_id" id="conduct_person_id" value="0">
                                <div class="col-sm-3 mb-2">
                                    <label for="name" class="font_bold"> Name * </label>
                                    <input type="text" placeholder="Enter name" name="name" class="form-control"
                                        value="{{ $broker?->name ?? old('name') }}" id="customerName" required>

                                    @error('name')
                                    <span class="text-danger name_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="GuardianName"> Father Name/ Guardian name * </label>
                                    <input type="text" placeholder="Enter Father/Gardian name" name="guardian_name"
                                        value="{{ $broker?->guardian_name ?? old('guardian_name') }}"
                                        class="form-control" id="GuardianName" required>

                                    @error('guardian_name')
                                    <span class="text-danger guardian_name_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="CNIC_NO"> CNIC *</label>
                                    <input type="number" placeholder="Enter CNIC number Without Dashes(-)"
                                        name="cnic_no" value="{{ $broker?->cnic_no ?? old('cnic_no') }}"
                                        class="form-control" id="CNIC_NO" required>

                                    @error('cnic_no')
                                    <span class="text-danger cnic_no_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="ConductPEmail"> Email </label>
                                    <input type="text" placeholder="Enter email" name="email" class="form-control"
                                        value="{{ $broker?->email ?? old('email') }}" id="ConductPEmail">

                                    @error('email')
                                    <span class="text-danger email_error"> {{ $message }} </span>
                                    @enderror

                                    <span class="text-danger email_error"></span>
                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="ContactNo"> Contact Number </label>
                                    <input type="number" placeholder="Enter contact number" name="contact_number"
                                        value="{{ $broker?->contact_number ?? old('contact_number') }}"
                                        class="form-control" id="ContactNo">

                                    @error('contact_number')
                                    <span class="text-danger contact_number_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label class="font_bold" for="BrokerOpenBalance"> Opening Balance </label>
                                    <input type="number" min="0" step="any" placeholder="Enter Balance"
                                        name="opening_balance"
                                        value="{{ $broker?->opening_balance ?? old('opening_balance') }}"
                                        class="form-control" id="BrokerOpenBalance">

                                    @error('opening_balance')
                                    <span class="text-danger opening_balance_error"> {{ $message }} </span>
                                    @enderror
                                </div>
                                {{-- start include 3 column, fileds Country, Province, City --}}
                                @include('layouts._partial.country_province_city',[
                                'countries' => $countries,
                                'updateData' => $broker
                                ])

                                {{-- End include 3 column, fileds Country, Province, City --}}

                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="PartyAddress"> Address</label>
                                    <input type="text" class="form-control" name="address"
                                        value="{{ $broker?->address ?? old('address') }}" placeholder="Enter Address">

                                    @error('address')
                                    <span class="text-danger address_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="ProfileImage"> Profile Picture </label>
                                    <input type="file" name="image_file" class="form-control" id="ProfileImage">

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
    $(function() {
    });
</script>
@endsection