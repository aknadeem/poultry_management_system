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

                        <form autocomplete="off" method="post" enctype="multipart/form-data" id="EmployeeForm">
                            @csrf
                            <div class="row form-group">
                                <input type="hidden" name="employee_id_modal" id="employeeIdModal">
                                <div class="col-sm-3 mb-2">
                                    <label for="name"> Name * </label>
                                    <input type="text" placeholder="Enter Employee name" name="name"
                                        class="form-control" required id="employeeName">
                                    <span class="text-danger name_error"></span>
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="employeeFatherName"> Father Name * </label>
                                    <input type="text" placeholder="Enter Employee father name" name="father_name"
                                        required class="form-control" id="employeeFatherName">
                                    <span class="text-danger father_name_error"></span>
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="employeeContactNo"> Contact Number *</label>
                                    <input type="number" placeholder="Enter contact number" name="contact_no" required
                                        class="form-control" id="employeeContactNo">
                                    <span class="text-danger contact_no_error"></span>
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="cno"> Email *</label>
                                    <input type="text" placeholder="Enter email" name="email" class="form-control"
                                        id="employeeEmail">
                                    <span class="text-danger email_error"></span>
                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label for="cnic"> CNIC *</label>
                                    <input type="number" placeholder="Enter cnic" name="cnic" class="form-control"
                                        id="employeeCnic">
                                    <span class="text-danger cnic_error"></span>
                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label for="employeeDate_of_birth"> Date of birth </label>
                                    <input type="date" placeholder="Enter date_of_birth" name="date_of_birth"
                                        class="form-control" id="employeeDate_of_birth">
                                    <span class="text-danger date_of_birth_error"></span>
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="employeeBasicSalary"> Basic Salaray* </label>
                                    <input type="number" step="any" min="0" placeholder="Enter Basic Salary" required
                                        name="basic_salary" class="form-control" id="employeeBasicSalary">
                                    <span class="text-danger basic_salary_error"></span>
                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label for="EmployeeAllownce"> Select Allownces </label>
                                    <select name="allownce_id" required class="form-control mySelect"
                                        data-toggle="select2" data-width="100%" id="EmployeeAllownce" multiple>
                                        <option value="" selected> Select Allownces </option>
                                        <option value="1"> abc </option>
                                    </select>
                                    <span class="text-danger allownce_id_error"></span>
                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label for="employeeBasicSalary"> Salary* </label>
                                    <input type="number" step="any" min="0" placeholder="Enter Basic Salary" required
                                        name="basic_salary" class="form-control" id="employeeBasicSalary">
                                    <span class="text-danger basic_salary_error"></span>
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="empDesignation"> Designation *</label>
                                    <input type="text" placeholder="Enter designation" name="designation" required
                                        class="form-control" id="empDesignation">
                                    <span class="text-danger designation_error"></span>
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="empContractPeriod"> Contract Period </label>
                                    <input type="text" placeholder="Enter Contract Period" name="contract_period"
                                        class="form-control" id="empContractPeriod">
                                    <span class="text-danger contract_period_error"></span>
                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label for="empJoiningDate"> Joining Date </label>
                                    <input type="date" placeholder="Enter Contract Period" name="joining_date" required
                                        class="form-control" id="empJoiningDate">
                                    <span class="text-danger joining_date_error"></span>
                                </div>
                                <div class="col-sm-3 mb-2">
                                    <label for="empFarm"> Select Farm </label>
                                    <select name="farm_id" id="empFarm" class="form-control mySelect" required
                                        data-toggle="select2" data-width="100%">
                                        <option value=""> Select Farm</option>
                                        <option value="1"> abc </option>
                                    </select>
                                    <span class="text-danger farm_id_error"></span>
                                </div>

                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="Country"> Select Country* </label>
                                    <select name="country_id" class="form-control mySelect" required
                                        data-toggle="select2" data-width="100%" id="Country">
                                        <option value=""> Select Country</option>
                                        <option value="1"> Pakistan</option>
                                    </select>
                                    <span class="text-danger country_id_error"> </span>
                                </div>
                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="Province"> Select Province* </label>
                                    <select name="province_id" required class="form-control mySelect"
                                        data-toggle="select2" data-width="100%" id="Province">
                                        <option value=""> Select Province</option>
                                        <option value="1"> Punjab</option>
                                    </select>
                                    <span class="text-danger province_id_error"> </span>
                                </div>
                                <div class="col-3 mb-2">
                                    <label class="font_bold" for="City"> Select City* </label>
                                    <select name="city_id" class="form-control mySelect" required data-toggle="select2"
                                        data-width="100%" id="City">
                                        <option value=""> Select City</option>
                                        <option value="1"> Lahore</option>
                                    </select>
                                    <span class="text-danger city_id_error"> </span>
                                </div>

                                <div class="col-6 mb-2">
                                    <label for="address">Address*</label>
                                    <input type="text" placeholder="Enter address" name="address" class="form-control"
                                        id="employeeAddress">
                                    <span class="text-danger address_error"> </span>
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="ProfileImage">Profile Image</label>
                                    <input type="file" class="form-control" name="image_file" id="ProfileImage">
                                    <span class="text-danger image_file_error"> </span>
                                </div>

                                <div class="col-sm-3 mb-2">
                                    <label for="empSignature">Employee Signature</label>
                                    <input type="file" class="form-control" name="employee_signature" id="empSignature">
                                    <span class="text-danger employee_signature_error"> </span>
                                </div>

                                <div class="col-sm-12">
                                    <label for="description">Description</label>
                                    <textarea class="form-control ckeditor" name="description" id="employeeDescription"
                                        cols="80" rows="2"></textarea>
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
@endsection

@section('custom_scripts')
<script>
    $(function() {
        
    });
</script>
@endsection