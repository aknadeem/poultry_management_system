@php
$load_css = Array('tables','sweetAlert', 'jquery-confirm');
$load_js = Array('tables','tippy','sweetAlert', 'jquery-confirm');
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
                        <li class="breadcrumb-item">FarmManagement</li>
                        <li class="breadcrumb-item"> PersonalFarms </li>
                        <li class="breadcrumb-item active"> index </li>
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
                            <h4>Personal Farms</h4>
                        </div>
                        <div class="col-6 align-self-end text-end mb-2">
                            <a class="btn btn-secondary btn-sm" href="{{ route('personalfarms.create') }}"
                                title="Click to add new farm" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-plus"></i>
                                Create Farm
                            </a>
                        </div>
                    </div>
                    <table id="basic-datatable" class="table table-striped dt-responsive  w-100">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th> Image </th>
                                <th> farm type </th>
                                <th> farm subtype </th>
                                <th> Farm Name </th>
                                <th>Area</th>
                                <th> Capacity </th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($farms as $key=>$farm)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td> <a href="{{ asset('storage/personalfarms/'.$farm?->farm_image) ?? ''}}"
                                        target="_blank" title="Click to view">
                                        <img src="{{ asset('storage/personalfarms/'.$farm?->farm_image)}}" width="50"
                                            alt="No Image">
                                    </a>
                                </td>
                                <td>{{ $farm?->type?->name}}</td>
                                <td>{{ $farm?->subtype?->name}}</td>
                                <td>{{ $farm->farm_name}}</td>
                                <td>{{ $farm?->farm_area}}</td>
                                <td>{{ $farm?->farm_capacity}}</td>

                                <td>
                                    <a class="btn btn-secondary btn-sm" href="javascript:void(0);" title="View Details"
                                        tabindex="0" data-plugin="tippy" data-tippy-animation="scale"
                                        data-tippy-arrow="true"><i class="fa fa-eye"></i>
                                        View
                                    </a>
                                    <a class="btn btn-info btn-sm" href="{{route('personalfarms.edit', $farm?->id)}}"
                                        title="Click to edit" tabindex="0" data-plugin="tippy"
                                        data-tippy-animation="scale" data-tippy-arrow="true"><i
                                            class="fa fa-pencil-alt"></i>
                                        Edit
                                    </a>

                                    <a class="btn btn-danger btn-sm delete-confirm"
                                        href="{{route('personalfarms.destroy', $farm?->id )}}"
                                        del_title="Customer Fame {{$farm?->farm_name}}" title="Click to delete"
                                        tabindex="0" data-plugin="tippy" data-tippy-animation="scale"
                                        data-tippy-arrow="true"><i class="fa fa-trash"></i>

                                    </a>

                                </td>
                            </tr>
                            @empty

                            @endforelse

                        </tbody>
                    </table>
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div>
        <!-- end col-->
    </div>
</div>

<div id="AddAccountModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="AddAccountModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h4 class="modal-title" id="standard-modalLabel"> <span class="AddUpdate"> Add </span> Bank account
                </h4>
                <button type="button" class="btn-close ModalClosed" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form autocomplete="off" method="post" enctype="multipart/form-data" id="CustomerForm"
                    class="form_loader">
                    @csrf
                    <div class="row form-group">
                        <input type="hidden" name="party_id_modal" id="party_id_modal">

                        <div class="col-sm-6 mb-2">
                            <label for="pAccountTitle">Account Title *</label>
                            <input type="text" placeholder="Enter account_title number" name="account_title" required
                                class="form-control" id="pAccountTitle">
                            <span class="text-danger farm_name_error"> </span>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="pAccountNo">Account Number *</label>
                            <input type="text" placeholder="Enter Account number" name="account_number"
                                class="form-control" required id="pAccountNo">
                            <span class="text-danger farm_name_error"> </span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label for="pAccountBankName">Bank Name *</label>
                            <input type="text" placeholder="Enter Bank name" name="bank_name" class="form-control"
                                required id="pAccountBankName">
                            <span class="text-danger bank_name_error"> </span>
                        </div>

                        <div class="col-6 mb-2">
                            <label for="pOpeningBalance">Opening Balance *</label>
                            <input type="number" step="any" min="0" placeholder="Enter opening balance"
                                name="opening_balance" class="form-control" id="pOpeningBalance">
                            <span class="text-danger opening_balance_error"> </span>
                        </div>
                        {{-- <div class="col-sm-6 mt-2">
                            <label for="image">Image</label>
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
                            <button class="btn btn-light btn-sm waves-effect waves-light mt-3 ModalClosed"> Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

@endsection

@section('custom_scripts')

<script>
    $(function() {
        $('#AddAccountModal').modal({backdrop: 'static', keyboard: false}) 
        $('.OpenAccountModal').click(function () {
            let customer_id = parseInt($(this).attr('CustomerId')) || 0;
            $('#AddAccountModal').modal('show');
        });

        $('.ModalClosed').click(function () {
            // $(this).find('modal').hide();
            $('.modal').modal('hide'); 
            $(this).find('form').trigger('reset');
        });
    });
</script>
@endsection