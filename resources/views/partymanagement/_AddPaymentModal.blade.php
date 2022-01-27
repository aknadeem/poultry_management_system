<div id="InvoicePaymentModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="AddPaymentModal"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- Logo & title -->
                            <div class="clearfix">
                                <div class="float-start">
                                    <div class="auth-logo">
                                        <div class="logo logo-dark">
                                            <span class="logo-lg">
                                                <img src="{{ asset('assets/images/logo/poultryLogo.png') }}" alt=""
                                                    height="42">
                                            </span>
                                        </div>

                                        <div class="logo logo-light">
                                            <span class="logo-lg">
                                                <img src="{{ asset('assets/images/logo/poultryLogo.png') }}" alt=""
                                                    height="22">
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="float-end">
                                    <h4 class="m-0 d-print-none">Invoice</h4>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mt-3">
                                        <p><b>Hello, Stanley Jones</b></p>
                                        <p class="text-muted">Thanks a lot because you keep purchasing our products. Our
                                            company
                                            promises to provide high quality products for you as well as outstanding
                                            customer service for every transaction. </p>
                                    </div>

                                </div><!-- end col -->
                                <div class="col-md-4 offset-md-2">
                                    <div class="mt-3 float-end">
                                        <p><strong>Order Date : </strong> <span class="float-end">
                                                &nbsp;&nbsp;&nbsp;&nbsp; Jan 17, 2016</span></p>
                                        <p><strong>Order Status : </strong> <span class="float-end"><span
                                                    class="badge bg-danger">Unpaid</span></span></p>
                                        <p><strong>Order No. : </strong> <span class="float-end">000028 </span></p>
                                    </div>
                                </div><!-- end col -->
                            </div>
                            <!-- end row -->

                            {{-- <div class="row mt-3">
                                <div class="col-sm-6">
                                    <h6>Billing Address</h6>
                                    <address>
                                        Stanley Jones<br>
                                        795 Folsom Ave, Suite 600<br>
                                        San Francisco, CA 94107<br>
                                        <abbr title="Phone">P:</abbr> (123) 456-7890
                                    </address>
                                </div> <!-- end col -->

                                <div class="col-sm-6">
                                    <h6>Shipping Address</h6>
                                    <address>
                                        Stanley Jones<br>
                                        795 Folsom Ave, Suite 600<br>
                                        San Francisco, CA 94107<br>
                                        <abbr title="Phone">P:</abbr> (123) 456-7890
                                    </address>
                                </div> <!-- end col -->
                            </div> --}}
                            <!-- end row -->

                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table mt-4 table-centered">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Item</th>
                                                    <th style="width: 10%">Hours</th>
                                                    <th style="width: 10%">Hours Rate</th>
                                                    <th style="width: 10%" class="text-end">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>
                                                        <b>Web Design</b> <br />
                                                        2 Pages static website - my website
                                                    </td>
                                                    <td>22</td>
                                                    <td>$30</td>
                                                    <td class="text-end">$660.00</td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>
                                                        <b>Software Development</b> <br />
                                                        Invoice editor software - AB'c Software
                                                    </td>
                                                    <td>112.5</td>
                                                    <td>$35</td>
                                                    <td class="text-end">$3937.50</td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div> <!-- end table-responsive -->
                                </div> <!-- end col -->
                            </div>
                            <!-- end row -->

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="clearfix pt-5">
                                        <h6 class="text-muted">Notes:</h6>

                                        <small class="text-muted">
                                            All accounts are to be paid within 7 days from receipt of
                                            invoice. To be paid by cheque or credit card or direct payment
                                            online. If account is not paid within 7 days the credits details
                                            supplied as confirmation of work undertaken will be charged the
                                            agreed quoted fee noted above.
                                        </small>
                                    </div>
                                </div> <!-- end col -->
                                <div class="col-sm-6">
                                    <div class="float-end">
                                        <p><b>Sub-total:</b> <span class="float-end">$4597.50</span></p>
                                        <p><b>Discount (10%):</b> <span class="float-end"> &nbsp;&nbsp;&nbsp;
                                                $459.75</span></p>
                                        <h3>$4137.75 USD</h3>
                                    </div>
                                    <div class="clearfix"></div>
                                </div> <!-- end col -->
                            </div>
                            <!-- end row -->

                            <div class="mt-4 mb-1">
                                <div class="text-end d-print-none">
                                    <a href="javascript:window.print()"
                                        class="btn btn-primary waves-effect waves-light"><i
                                            class="mdi mdi-printer me-1"></i> Print</a>
                                    <a href="#" class="btn btn-info waves-effect waves-light">Submit</a>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end card -->
                </div> <!-- end col -->
            </div>
        </div>
    </div>
</div>

<div id="AddPaymentModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="AddPaymentModal"
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
                        <input type="hidden" name="company_balance_id" id="CompanyBalanceId">

                        <input type="hidden" name="party_company_id" id="CompanyIdModal">

                        <div class="col-sm-4 mb-2">
                            <label for="cno">Total Amount: <b id="TotalPaymentLabel"> 2500 </b> </label>
                        </div>
                        <div class="col-sm-4 mb-2">
                            <label for="cno">Paid Amount: <b id="PaidAmountLabel" class="text-success"> 0 </b> </label>
                        </div>
                        <div class="col-sm-4 mb-2">
                            <label for="cno">Remaining Amount: <b id="RemAmountLabel" class="text-danger"> 2500
                                </b></label>
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

                        <div class="col-sm-6 mb-2">
                            <label for="description"> Description </label>
                            <input type="text" name="description" class="form-control" placeholder="Enter amount"
                                id="description">
                            <span class="text-danger description_error"> </span>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="image_file"> Picture </label>
                            <input type="file" name="image_file" class="form-control" placeholder="Enter amount"
                                id="image_file">
                            <span class="text-danger image_file_error"> </span>
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

@section('modal_scripts')
<script>
    $(function() {
        $('#AddPaymentModal').modal({backdrop: 'static', keyboard: false}) 
        
        $(document).on("click", ".openAddPaymentModal", function(event) {
            let balance_id = parseInt($(this).attr('data-id')) || 0
            // alert(balance_id)x
            let page_url = "{{ url('/balancemanagement/balance-with-company')}}/"+balance_id

            $.get(page_url, function(data, status){
                console.log(data)
                if(data?.balance){
                    $('#CompanyBalanceId').val(balance_id)
                    $('#CompanyIdModal').val(data?.balance?.company_id)
                    $('#TotalPaymentLabel').html(data?.balance?.total_amount)
                    $('#PaidAmountLabel').html(data?.balance?.paid_amount)
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
            let form_url = "{{ route('companybalance.store')}}"
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

        $(".mySelectModal").select2({
            dropdownParent: $("#AddPaymentModal")
        });
    });
</script>
@endsection