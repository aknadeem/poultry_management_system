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
                        <li class="breadcrumb-item">ShedManagement</li>
                        <li class="breadcrumb-item"> PersonalSheds </li>
                        <li class="breadcrumb-item active"> index </li>
                    </ol>
                </div>
                <h4 class="page-title">ShedManagement</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4>Personal Sheds</h4>
                        </div>
                        <div class="col-6 align-self-end text-end mb-2">
                            <a class="btn btn-secondary btn-sm" href="{{ route('poultryshed.create') }}"
                                title="Click to add new shed" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-plus"></i>
                                Create Shed
                            </a>
                        </div>
                    </div>
                    <table id="basic-datatable" class="table table-striped dt-responsive  w-100">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th> Name </th>
                                <th> CNIC </th>
                                <th> Account Detail </th>
                                <th> Documents </th>
                                <th> Debit/Credit Limit </th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>11</td>
                                <td>Nadeem Ahmed</td>
                                <td>434354234341</td>
                                <td>
                                    <a class="btn btn-secondary btn-sm viewCustomerDetailModal" CustomerId=""
                                        href="javascript:void(0);" title="Click to View Accounts "><i
                                            class="fa fa-eye"></i>
                                        View
                                    </a>
                                    <a class="btn btn-success btn-sm OpenAccountModal" data-id="1" CustomerId=""
                                        href="javascript:void(0);" title="Click to Add New Account"><i
                                            class="fa fa-plus"></i>
                                        add
                                    </a>
                                </td>

                                <td>
                                    <a class="btn btn-secondary btn-sm viewCustomerDetailModal" CustomerId=""
                                        href="javascript:void(0);" title="Click to View DOcuments "><i
                                            class="fa fa-eye"></i>
                                        View
                                    </a>
                                    <a class="btn btn-success btn-sm OpenPartyDocumentModal" CustomerId=""
                                        href="javascript:void(0);" title="Click to Add New Document"><i
                                            class="fa fa-plus"></i>
                                        add
                                    </a>
                                </td>

                                <td>
                                    <a class="btn btn-secondary btn-sm viewCustomerDetailModal" CustomerId=""
                                        href="javascript:void(0);" title="Click to View DOcuments "><i
                                            class="fa fa-eye"></i>
                                        View
                                    </a>
                                    <a class="btn btn-success btn-sm OpenPartyLimitsModal" CustomerId=""
                                        href="javascript:void(0);" title="Click to Add New Limit"><i
                                            class="fa fa-plus"></i>
                                        add
                                    </a>
                                </td>

                                <td>11</td>
                            </tr>
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