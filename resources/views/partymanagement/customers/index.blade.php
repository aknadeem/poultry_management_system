@php
$load_css = Array('tables','sweetAlert');
$load_js = Array('tables','tippy','parsley','sweetAlert');
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
                        <li class="breadcrumb-item active">Customer</li>
                    </ol>
                </div>
                <h4 class="page-title">Customers</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4>Customers</h4>
                        </div>
                        <div class="col-6 align-self-end text-end mb-2">

                            <a class="btn btn-secondary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#AddCustomerModal" href="javascript:void(0);" CustomerId="0"
                                title="Click to add new Customer" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-plus"></i>
                                Customer
                            </a>
                        </div>
                    </div>
                    <table id="basic-datatable" class="table table-striped dt-responsive  w-100">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Contact Number</th>
                                <th>ShopName</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($customers as $key=>$customer)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $customer->name ?? '' }}</td>
                                <td>{{ $customer->email ?? '' }}</td>
                                <td>{{ $customer->contact_no ?? '' }}</td>
                                <td>{{ $customer->farm_name ?? '' }}</td>
                                <td>
                                    <a class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#"
                                        href="javascript:void(0);" title="View Details" tabindex="0" data-plugin="tippy"
                                        data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-eye"></i>
                                        View
                                    </a>
                                    <a class="btn btn-info btn-sm openAddTaskModal" data-bs-toggle="modal"
                                        data-bs-target="#AddCustomerModal" CustomerId="{{ $customer->id ?? 0}}"
                                        href="javascript:void(0);" title="Click to edit" tabindex="0"
                                        data-plugin="tippy" data-tippy-animation="scale" data-tippy-arrow="true"><i
                                            class="fa fa-pencil-alt"></i>
                                        Edit
                                    </a>
                                    <a class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#scrollable-modal" href="javascript:void(0);"
                                        title="Click to delete" tabindex="0" data-plugin="tippy"
                                        data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-trash"></i>
                                        Delete
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

<!-- Standard modal content -->
<div id="AddCustomerModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="AddCustomerModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h4 class="modal-title" id="standard-modalLabel"> <span class="AddUpdate"> Add </span> Customer </h4>
                <button type="button" class="btn-close ModalClosed" aria-label="Close"></button>

                {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
            </div>
            <div class="modal-body">
                <form autocomplete="off" method="post" enctype="multipart/form-data" id="CustomerForm"
                    data-parsley-validate>
                    @csrf
                    <div class="row form-group">
                        <input type="hidden" name="customerIdModal" id="customerIdModal">
                        <div class="col-sm-6 mb-2">
                            <label for="name"> Name * </label>
                            <input type="text" required placeholder="Enter customer name" name="name"
                                class="form-control" id="customerName">
                            <div class="invalid-feedback name_error"></div>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="cno"> Contact Number *</label>
                            <input type="text" required placeholder="Enter contact number" name="contact_no"
                                class="form-control" id="customerContactNo">
                            <div class="invalid-feedback contact_no_error"></div>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="cno"> Email *</label>
                            <input type="text" required placeholder="Enter email" name="email" class="form-control"
                                id="customerEmail">
                            <div class="invalid-feedback email_error"> </div>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="cno">Farm Name/ Shop *</label>
                            <input type="text" required placeholder="Enter Farm/Shop Name" name="farm_name"
                                class="form-control" id="customerFarmName">
                            <div class="invalid-feedback farm_name_error"> </div>
                        </div>
                        <div class="col-sm-12">
                            <label for="address">Address*</label>
                            <textarea class="form-control ckeditor" name="address" id="customerAddress" required
                                cols="80" rows="2"></textarea>
                            <div class="invalid-feedback address_error"> </div>
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

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection

@section('custom_scripts')
<script>
    $(function() {
        $('.openAddTaskModal').click(function () {
            let customer_id = parseInt($(this).attr('CustomerId')) || 0;
            $('#AddCustomerModal').modal('show');
            $('#customerIdModal').val(customer_id);
            if(customer_id > 0){
                $('.AddUpdate').html('Update');
                $.get("/customer/"+customer_id+"/edit", function(cdata, status){
                    console.log(cdata)
                    // alert(Task_AssignTo);
                    $('#customerIdModal').val(cdata?.customer?.id)
                    $('#customerName').val(cdata?.customer?.name)
                    $('#customerContactNo').val(cdata?.customer?.contact_no)
                    $('#customerEmail').val(cdata?.customer?.email)
                    $('#customerFarmName').val(cdata?.customer?.farm_name)
                    $('#customerAddress').val(cdata?.customer?.address)
                });
            }else{
                $('.AddUpdate').html('Add');  
            }
        });
        $('#CustomerForm').on('submit', function(e) {
            e.preventDefault();
            let EcustomerId = parseInt($('#customerIdModal').val());
            let form_type = 'POST'
            let form_url = "{{ url('/customer')}}"
            if(EcustomerId > 0){
                form_type = 'PUT'
                form_url = "{{ url('/customer')}}/"+EcustomerId
            }
            $.ajax({
                type: form_type,
                url: form_url,
                data: $(this).serialize(),
                beforeSend : function(msg) {
                    $('#CustomerForm').find('div.invalid-feedback').text('')
                },
                success: function(msg) {
                   
                    if(msg?.success == 'no'){
                        console.log(msg)
                        $.each(msg?.errors, function(prefix, val){
                            console.log(prefix)
                            $('#CustomerForm').find('div.'+prefix+'_error').html(val[0])
                        });
                        console.log(prefix)
                        // $('#customerNameE').html(msg?.error?.name)
                        // $('#customerContactNoE').html(msg?.error?.contact_no)
                        // $('#customerEmailE').html(msg?.error?.email)
                        // $('#customerFarmNameE').html(msg?.error?.farm_name)
                        // $('#customerAddressE').html(msg?.error?.address)
                    }else{
                        $("#CustomerForm").trigger("reset");
                        $('#AddCustomerModal').modal('hide');
                        swal.fire({
                            title: "Success",
                            text: msg.message,
                            type: "success",
                            confirmButtonText: "Ok",
                            closeOnConfirm: true
                        }, function () {
                            location.reload();
                        });
                    }
                },
                success: function(error_msg) {
                    console.log(error_msg)
                }
            });
        });

        $('.ModalClosed').click(function () {
            // $(this).find('modal').hide();
            $('.modal').modal('hide'); 
            $(this).find('form').trigger('reset');
        });
    });
</script>
@endsection