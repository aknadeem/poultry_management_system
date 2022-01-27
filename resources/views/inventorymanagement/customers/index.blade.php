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
                                <th>#</th>
                                <th>Image</th>
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
                                <td>
                                    @if ($customer->image)
                                    <img class="d-flex me-3 rounded-circle avatar-lg"
                                        src="{{ asset('storage/customers/'.$customer->image) ?? ''}}" alt="No image">
                                    @else
                                    <b>No Image</b>
                                    @endif

                                </td>
                                <td>{{ $customer->name ?? '' }}</td>
                                <td>{{ $customer->email ?? '' }}</td>
                                <td>{{ $customer->contact_no ?? '' }}</td>
                                <td>{{ $customer->farm_name ?? '' }}</td>
                                <td>
                                    <a class="btn btn-secondary btn-sm viewCustomerDetailModal"
                                        CustomerId="{{ $customer->id ?? 0}}" href="javascript:void(0);"
                                        title="View Details" tabindex="0" data-plugin="tippy"
                                        data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-eye"></i>
                                        View
                                    </a>
                                    <a class="btn btn-info btn-sm openCustomerModal" data-bs-toggle="modal"
                                        data-bs-target="#AddCustomerModal" CustomerId="{{ $customer->id ?? 0}}"
                                        href="javascript:void(0);" title="Click to edit" tabindex="0"
                                        data-plugin="tippy" data-tippy-animation="scale" data-tippy-arrow="true"><i
                                            class="fa fa-pencil-alt"></i>
                                        Edit
                                    </a>
                                    <a class="btn btn-danger btn-sm delete-confirm"
                                        href="{{route('customer.destroy', $customer->id ?? 0)}}" del_title="Cutomer abc"
                                        title="Click to delete" tabindex="0" data-plugin="tippy"
                                        data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-trash"></i>
                                        Delete
                                    </a>

                                    {{-- <a class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#scrollable-modal" href="javascript:void(0);"
                                        title="Click to delete" tabindex="0" data-plugin="tippy"
                                        data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-trash"></i>
                                        Delete
                                    </a> --}}
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

@include('partymanagement._CustomerModal')


<!-- View Detail modal content -->
<div id="ViewCustomerModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ViewCustomerModal"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="CustomerDetailData">
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection

@section('custom_scripts')

<script>
    $(function() {
        $('.openCustomerModal').click(function () {
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
                    let img_url = "{{ asset('storage/customers/')}}"
                    if(cdata?.customer?.image !=''){
                        $(".img-holder").empty();
                        img_url = img_url+'/'+cdata?.customer?.image;
                        var img_holder = $('.img-holder');
                        $('<img/>', {'src':img_url,'class':'','style':'max-width:20%;margin-bottom:1px;'}).appendTo(img_holder);
                    }else{
                        $(img_holder).empty();
                        $(".img-holder").empty();
                    }
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
            // if(EcustomerId > 0){
            //     form_type = 'PATCH'
            //     form_url = "{{ url('/customer')}}/"+EcustomerId
            // }
            let CustomerForm = this;
            $.ajax({
                type: form_type,
                url: form_url,
                data:new FormData(this),
                dataType:'JSON',
                contentType: false,
                cache: false,
                processData: false,
                // data: $('#CustomerForm').serialize(),
                beforeSend : function(msg) {
                    $('#CustomerForm').find('div.invalid-feedback').text('')
                },
                success: function(msg) {
                    if(msg?.success == 'no'){
                        // console.log(msg.error)
                        $.each(msg?.error, function(prefix, val){
                            // console.log(prefix)
                            $('#CustomerForm').find('span.'+prefix+'_error').text(val[0]);
                        });
                    }else{
                        $("#CustomerForm").trigger("reset");
                        $('#AddCustomerModal').modal('hide');
                        swal.fire({
                            title: "Success",
                            text: msg.message,
                            type: "success",
                            confirmButtonText: "Ok",
                            // closeOnConfirm: true,
                        }, function () {
                            location.reload();
                        });
                    }
                }
            });
        });
        
        $('.ModalClosed').click(function () {
            // $(this).find('modal').hide();
            $('.modal').modal('hide'); 
            $(this).find('form').trigger('reset');
        });

        $('.viewCustomerDetailModal').click(function () {
            let customer_id = parseInt($(this).attr('CustomerId')) || 0;
            $.get("{{ url('/customer')}}/"+customer_id, function(result) {
                // console.log(result)
                $('#ViewCustomerModal').modal('show');
                $('#CustomerDetailData').html(result?.html_data);
            });
        });
    });
</script>
@endsection