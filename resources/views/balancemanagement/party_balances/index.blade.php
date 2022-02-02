@php
$load_css = Array('tables','sweetAlert', 'jquery-confirm','select2');
$load_js = Array('tables','tippy','sweetAlert', 'jquery-confirm','select2','select2model')
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
                        <li class="breadcrumb-item">BalanceManagement</li>
                        <li class="breadcrumb-item active">PartyBalance</li>
                    </ol>
                </div>
                <h6 class="page-title">Balance List</h6>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4>Party Balance</h4>
                        </div>
                        <div class="col-6 align-self-end text-end mb-2">

                            <a class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#AddCompanyModal"
                                href="javascript:void(0);" CustomerId="0" title="Click to add new company"
                                data-plugin="tippy" data-tippy-animation="scale" data-tippy-arrow="true"><i
                                    class="fa fa-plus"></i>
                                Company
                            </a>
                        </div>
                    </div>
                    <table id="Balance-Datatable" class="table table-striped dt-responsive  w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Party</th>
                                <th>Total Amount</th>
                                <th>Paid Amount</th>
                                <th>Remaining Amount</th>
                                <th>Status</th>
                                <th>narration</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            @forelse ($balances as $key=>$item)
                            <tr>
                                <td>{{++$key}}</td>
                                <td class="text-{{$item->type_value['color']}} fw-bold fs-5">
                                    {{$item->type_value['val']}}
                                </td>
                                <td>{{$item->party?->name}}</td>
                                <td>@money($item->total_amount)</td>
                                <td>@money($item->paid_amount ?? 0)</td>
                                <td class="text-danger fw-bold fs-5">@money($item->remaining_amount)</td>
                                <td class="fs-4">
                                    <span
                                        class="badge bg-{{$item->payment_status_val['color']}}">{{$item->payment_status_val['val']}}</span>
                                </td>
                                <td>{{$item->narration}}</td>
                                <td>{{$item->transaction_date?->format('d M, Y')}}</td>
                                <td>
                                    <a class="btn btn-{{$item->type_value['color']}} btn-bold btn-sm openAddPaymentModal"
                                        data-id="{{$item->id}}" href="javascript:void(0);"
                                        title="Click to add payment"><i class="fa fa-plus"></i>
                                        Payment
                                    </a>
                                    <a class="btn btn-info btn-bold btn-sm" data-id="{{$item->id}}"
                                        href="{{ route('getBalancePayments', $item->id) }}"
                                        title="Click to add payment"><i class="fa fa-eye"></i>
                                        view
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

<div id="AddPaymentModal" class="modal fade MyModalClass" tabindex="-1" role="dialog" aria-labelledby="AddPaymentModal"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h4 class="modal-title"> <span class="AddUpdate"> Add </span> Payment </h4>
                <button type="button" class="btn-close ModalClosed" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form autocomplete="off" method="post" enctype="multipart/form-data" id="AddPaymentForm"
                    class="form_loader">
                    @csrf
                    <div class="row form-group">
                        <input type="hidden" name="balance_id" id="BalanceId">

                        <input type="hidden" name="party_id" id="PartyIdModal">

                        <div class="col-sm-4 mb-2">
                            <label for="cno">Total Amount: <span class="fw-bold fs-4" id="TotalPaymentLabel"></span>
                            </label>
                        </div>
                        <div class="col-sm-4 mb-2">
                            <label for="cno">Paid Amount: <span class="fw-bold fs-4" id="PaidAmountLabel"
                                    class="text-success"></span> </label>
                        </div>
                        <div class="col-sm-4 mb-2">
                            <label for="cno">Remaining Amount: <span class="fw-bold fs-4 text-danger"
                                    id="RemAmountLabel" class="text-danger"> 2500
                                </span></label>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="AddAmount">Amount</label>
                            <input type="number" name="amount_payment" class="form-control" placeholder="Enter amount"
                                id="AddAmount" min="0" required>
                            <span class="text-danger amount_payment_error"> </span>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="payment_option">Payment Option</label>
                            <select name="payment_option" id="payment_option" class="form-control mySelectModal"
                                data-toggle="select2" required data-width="100%">
                                <option value="cheque">Cheque</option>
                                <option value="cash">Cash</option>
                                <option value="other">Other</option>
                            </select>
                            <span class="text-danger farm_name_error"> </span>
                        </div>

                        <div class="col-sm-4 mb-2">
                            <label for="cheque_date">Cheque Date</label>
                            <input type="date" name="cheque_date" class="form-control" placeholder="Enter amount"
                                id="cheque_date">
                            <span class="text-danger cheque_date_error"> </span>
                        </div>

                        <div class="col-sm-4 mb-2">
                            <label for="cno">Cheque Bank</label>
                            <input type="text" name="bank_name" class="form-control" placeholder="Enter amount"
                                id="BankName">
                            <span class="text-danger bank_name_error"> </span>
                        </div>
                        <div class="col-sm-4 mb-2">
                            <label for="cheque_picture">Cheque Picture</label>
                            <input type="file" name="cheque_picture" class="form-control" placeholder="Enter amount"
                                id="ChequePicture">
                            <span class="text-danger cheque_picture_error"> </span>
                        </div>

                        <div class="col-sm-4 mb-2">
                            <label for="reference_no"> Reference Number </label>
                            <input type="text" name="reference_no" class="form-control" placeholder="Enter amount"
                                id="reference_no">
                            <span class="text-danger reference_no_error"> </span>
                        </div>

                        <div class="col-sm-4 mb-2">
                            <label for="PaidDate"> Payment Date *</label>
                            <input type="date" name="paid_date" class="form-control" required id="PaidDate">
                            <span class="text-danger paid_date_error"> </span>
                        </div>

                        <div class="col-sm-4 mb-2">
                            <label for="image_file"> Picture </label>
                            <input type="file" name="image_file" class="form-control" placeholder="Enter amount"
                                id="image_file">
                            <span class="text-danger image_file_error"> </span>
                        </div>

                        <div class="col-sm-12 mb-2">
                            <label for="description"> Description </label>
                            <input type="text" name="description" class="form-control" placeholder="Enter amount"
                                id="description">
                            <span class="text-danger description_error"> </span>
                        </div>
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
        </div>
    </div>
</div>


@endsection

@section('custom_scripts')
<script>
    $(function() {
        $('#AddPaymentModal').modal({backdrop: 'static', keyboard: false}) 
        $(document).on("click", ".openAddPaymentModal", function(event) {
            let balance_id = parseInt($(this).attr('data-id')) || 0
            // alert(balance_id)
            let page_url = "{{ route('partybalance.index')}}/"+balance_id
            $.get(page_url, function(data, status){
                console.log(data)
                if(data?.balance){
                    $('#BalanceId').val(balance_id)
                    $('#PartyIdModal').val(data?.balance?.party_id)
                    $('#TotalPaymentLabel').html(data?.balance?.total_amount)
                    $('#PaidAmountLabel').html(data?.balance?.paid_amount || 0)
                    $('#RemAmountLabel').html(data?.balance?.remaining_amount)
                    $('#AddAmount').attr('max', data?.balance?.remaining_amount)
                    $('#AddPaymentModal').modal('show')
                }else{
                    return 0;
                }
            });
        });

        $(document).on("submit", "#AddPaymentForm", function(e) {
            e.preventDefault();
            let balance_id = parseInt($(this).attr('data-id')) || 0
            // alert(balance_id)x
            let form_url = "{{ route('partybalance.store')}}"
            let form_type = "POST"
            $.ajax({
                type: form_type,
                url: form_url,
                data: new FormData(this),
                dataType:'JSON',
                contentType: false,
                cache: false,
                processData: false,
                // data: $('#CustomerForm').serialize(),
                beforeSend : function(msg) {
                    $('#AddPaymentForm').find('span.invalid-feedback').text('')
                },
                success: function(msg) {
                    console.log(msg)
                    if(msg?.success == 'no'){
                        // console.log(msg.error)
                        $.each(msg?.error, function(prefix, val){
                            // console.log(prefix)
                            $('#AddPaymentForm').find('span.'+prefix+'_error').text(val[0]);
                        });
                    }else{
                        $("#AddPaymentForm").trigger("reset");
                        $('#AddPaymentModal').modal('hide');
                        $('#Balance-Datatable').DataTable().ajax.reload(null, false);
                        swal.fire({
                            title: "Success",
                            text: msg.message,
                            icon: "success",
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
    });
</script>
@endsection