@php
$load_css = Array('select2','sweetAlert');
$load_js = Array('tippy','select2','sweetAlert')
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
                        <li class="breadcrumb-item"> <a href="{{ route('purchase.index') }}"> Employee </a>
                        </li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
                <h4 class="page-title">Create Employee</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4>Create</h4>
                        </div>

                        <div class="col-6 align-self-end text-end mb-2">
                            <a class="btn btn-secondary btn-sm" href="{{ route('sale.index') }}"
                                title="Click to see Employees" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-arrow-left"></i>
                                Back
                            </a>
                        </div>

                        <form method="post" action="{{ ($employee?->id) ? route('employee.update', $employee?->id ) :
                            route('employee.store') }}" enctype="multipart/form-data" id="EmployeeForm"
                            autocomplete="off">
                            @csrf

                            @if ($employee?->id)
                            @method('PUT')
                            @endif
                            <div class="row form-group">
                                <div class="col-sm-3 mb-2">
                                    <label for="name"> Name * </label>
                                    <input type="text" placeholder="Enter Employee name" name="name"
                                        value="{{ old('name', $employee?->name) }}" class="form-control" required
                                        id="employeeName">
                                    <span class="text-danger name_error"></span>
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="employeeFatherName"> Father Name * </label>
                                    <input type="text" placeholder="Enter Employee father name" name="guardian_name"
                                        value="{{ old('guardian_name', $employee?->guardian_name) }}" required
                                        class="form-control" id="employeeFatherName">
                                    <span class="text-danger guardian_name_error"></span>
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="employeeContactNo"> Contact Number *</label>
                                    <input type="number" placeholder="Enter contact number" name="contact_no" required
                                        value="{{ old('contact_no', $employee?->contact_no) }}" class="form-control"
                                        id="employeeContactNo">
                                    <span class="text-danger contact_no_error"></span>
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="employeeOtherNumber"> other Number *</label>
                                    <input type="number" placeholder="Enter other number" name="other_number" required
                                        value="{{ old('other_number', $employee?->other_number) }}" class="form-control"
                                        id="employeeOtherNumber">
                                    <span class="text-danger other_number_error"></span>
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="cno"> Email *</label>
                                    <input type="text" placeholder="Enter email" name="email" class="form-control"
                                        value="{{ old('email', $employee?->email) }}" id="employeeEmail">
                                    <span class="text-danger email_error"></span>
                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label for="cnic"> Employee CNIC *</label>
                                    <input type="number" placeholder="Enter cnic" name="cnic_no"
                                        value="{{ old('cnic_no', $employee?->cnic_no) }}" class="form-control"
                                        id="employeeCnic">
                                    <span class="text-danger cnic_error"></span>
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="employeeFatherCnic"> Employee Father CNIC</label>
                                    <input type="number" placeholder="Enter cnic" name="father_cnic_no"
                                        value="{{ old('father_cnic_no', $employee?->father_cnic_no ?? '') }}"
                                        class="form-control" id="employeeFatherCnic">
                                    <span class="text-danger father_cnic_no_error"></span>
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="employeeDate_of_birth"> Date of birth </label>
                                    <input type="date" placeholder="Enter date_of_birth" name="date_of_birth"
                                        value="{{ old('date_of_birth', $employee?->date_of_birth) }}"
                                        class="form-control" id="employeeDate_of_birth">
                                    <span class="text-danger date_of_birth_error"></span>
                                </div>

                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="EmployeeType"> Select Employee Type </label>
                                    <div class="input-group">
                                        <select name="employee_type_id" required class="form-control mySelect"
                                            data-toggle="select2" data-width="85%" id="EmployeeType">
                                            <option value="" selected> Select Type </option>
                                            @forelse ($employee_types as $item)
                                            <option {{ (! empty(old('employee_type_id')==$item->id) ? 'selected'
                                                : '') || ($employee?->employee_type_id==$item->id) ? 'selected'
                                                : '' }} value="{{ $item->id }}"> {{ $item->name }} </option>
                                            @empty
                                            @endforelse
                                        </select>
                                        <a href="#"
                                            class="btn input-group-text btn-dark waves-effect waves-light OpenaddTypeModal"
                                            title="Click to add new" SelectBoxId="EmployeeType"
                                            TableName="employee_types" type="button">
                                            <i class="fa fa-plus"></i>
                                        </a>
                                    </div>
                                    @error('employee_type_id')
                                    <span class="text-danger employee_type_id_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="EmployeeLevelId"> Select Employee Level </label>
                                    <div class="input-group">
                                        <select name="employee_level_id" required class="form-control mySelect"
                                            data-toggle="select2" data-width="85%" id="EmployeeLevelId">
                                            <option value="" selected> Select Level </option>
                                            @forelse ($employee_levels as $item)
                                            <option {{ (! empty(old('employee_level_id')==$item->id) ? 'selected'
                                                : '') || ($employee?->employee_level_id==$item->id) ? 'selected'
                                                : '' }} value="{{ $item->id }}"> {{ $item->name }} </option>
                                            @empty
                                            @endforelse
                                        </select>
                                        <a href="#"
                                            class="btn input-group-text btn-dark waves-effect waves-light OpenaddTypeModal"
                                            title="Click to add new" SelectBoxId="EmployeeLevelId"
                                            TableName="employee_levels" type="button">
                                            <i class="fa fa-plus"></i>
                                        </a>
                                    </div>
                                    @error('employee_level_id')
                                    <span class="text-danger employee_level_id_error"> {{ $message }} </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="employeeBasicSalary"> Basic Salaray* </label>
                                    <input type="number" step="any" min="0" placeholder="Enter Basic Salary" required
                                        name="basic_salary" value="{{ old('basic_salary', $employee?->basic_salary) }}"
                                        class="form-control" id="employeeBasicSalary">
                                    <span class="text-danger basic_salary_error"></span>
                                    <span class="text-danger" id="BasicSalaryError"></span>
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="empOtherAmount"> Other Amount</label>
                                    <input type="number" step="any" min="0" placeholder="Enter other amounts"
                                        name="other_amount" value="{{ old('other_amount', $employee?->other_amount) }}"
                                        class="form-control" id="empOtherAmount">
                                    <span class="text-danger other_amount_error"></span>
                                </div>

                                {{-- <div class="col-sm-3 mb-2">
                                    <label for="EmployeeAllownce"> Select Allownces </label>
                                    <select name="allownce_id" class="form-control mySelect" data-toggle="select2"
                                        data-width="100%" id="EmployeeAllownce" multiple>
                                        <option value="" selected> Select Allownces </option>
                                        <option value="1"> abc </option>
                                    </select>
                                    <span class="text-danger allownce_id_error"></span>
                                </div> --}}

                                <div class="col-sm-3 mb-2">
                                    <label for="employeeNetSalary"> Net Salary* </label>
                                    <input type="number" step="any" min="0" placeholder="Enter Basic Salary" readonly
                                        name="net_salary" value="{{ old('net_salary', $employee?->net_salary) }}"
                                        class="form-control" id="employeeNetSalary">
                                    <span class="text-danger basic_salary_error"></span>
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="empContractPeriod"> Contract Period </label>
                                    <input type="text" placeholder="Enter Contract Period" name="contract_period"
                                        value="{{ old('contract_period', $employee?->contract_period) }}"
                                        class="form-control" id="empContractPeriod">
                                    <span class="text-danger contract_period_error"></span>
                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label for="empJoiningDate"> Joining Date </label>
                                    <input type="date" placeholder="Enter Contract Period" name="joining_date" required
                                        value="{{ old('joining_date', $employee?->joining_date) }}" class="form-control"
                                        id="empJoiningDate">
                                    <span class="text-danger joining_date_error"></span>
                                </div>
                                {{-- <div class="col-sm-3 mb-2">
                                    <label for="empFarm"> Select Farm </label>
                                    <select name="personal_farm_id" id="empFarm" class="form-control mySelect"
                                        data-toggle="select2" data-width="100%">
                                        <option value=""> Select Farm</option>
                                        @forelse ($farms as $item)
                                        <option {{ (! empty(old('personal_farm_id')==$item->id) ? 'selected'
                                            : '') || ($employee?->personal_farm_id==$item->id) ? 'selected'
                                            : '' }} value="{{ $item?->id }}">
                                            <p>{{ $item?->farm_name }}</p> <br> [ {{
                                            $item?->farm_address }} ]
                                        </option>
                                        @empty
                                        @endforelse
                                    </select>
                                    <span class="text-danger personal_farm_id_error"></span>
                                </div> --}}

                                <div class="col-sm-3 mb-2">
                                    <label for="bloodGroup"> Blood Group </label>
                                    <select name="blood_group" class="form-control mySelect" required
                                        data-toggle="select2" data-width="100%" id="bloodGroup">
                                        <option value=""> Select Option</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                    </select>
                                    <span class="text-danger blood_group_error"></span>
                                </div>

                                @include('layouts._partial.country_province_city',[
                                'countries' => $countries,
                                'updateData' => $employee
                                ])

                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="PoliceRecord">Police Record* </label>
                                    <select name="is_police_record" class="form-control mySelect" required
                                        data-toggle="select2" data-width="100%" id="PoliceRecord">
                                        <option value=""> Select Option</option>
                                        <option {{ (! empty(old('is_police_record')==1) ? 'selected' : '' ) ||
                                            ($employee?->is_police_record==1) ? 'selected'
                                            : '' }} value="1"> Yes</option>
                                        <option {{ (! empty(old('is_police_record')==0) ? 'selected' : '' ) ||
                                            ($employee?->is_police_record==0) ? 'selected'
                                            : '' }} value="0"> No</option>
                                    </select>
                                    <span class="text-danger is_police_record_error"> </span>
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="ProfileImage">Profile Image</label>
                                    <input type="file" class="form-control" name="employee_image" id="ProfileImage">
                                    <span class="text-danger employee_image_error"> </span>
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="empSignature">Employee Signature</label>
                                    <input type="file" class="form-control" name="employee_signature" id="empSignature">
                                    <span class="text-danger employee_signature_error"> </span>
                                </div>

                                <div class="col-6 mb-2">
                                    <label for="address">Address*</label>
                                    <input type="text" placeholder="Enter address" name="address"
                                        value="{{ old('address', $employee?->address) }}" class="form-control"
                                        id="employeeAddress">
                                    <span class="text-danger address_error"> </span>
                                </div>

                                <div class="col-sm-12">
                                    <label for="description">Description</label>
                                    <textarea class="form-control ckeditor" name="description" id="employeeDescription"
                                        cols="80" rows="2">{{ old('description', $employee?->description) }}</textarea>
                                    <span class="text-danger description_error"> </span>
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

@include('layouts._partial.add_types_modal')
@endsection

@section('custom_scripts')
<script>
    $(function() {

        $('#empOtherAmount').on('keyup', function() {
        let employeeBasicSalary = parseFloat($('#employeeBasicSalary').val())
        let empOtherAmount = parseFloat($(this).val())
        if (employeeBasicSalary > 0) {
            $("#BasicSalaryError").html('');
            let net_salary = employeeBasicSalary+empOtherAmount;
            $("#employeeNetSalary").val(net_salary);
        } else {
            $("#BasicSalaryError").html('Please Enter Basic Salary, and should be greater then 0');
        }
    });
    });
</script>
@endsection