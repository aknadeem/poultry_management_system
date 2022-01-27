<div id="AddPartyLimitsModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="AddPartyLimitsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h4 class="modal-title" id="standard-modalLabel"> <span class="AddUpdate"> Add </span> Debit/Credit
                    Limit
                </h4>
                <button type="button" class="btn-close ModalClosed" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form autocomplete="off" method="post" id="PartyBalanceLimitFrom" class="form_loader">
                    @csrf
                    <div class="row form-group">
                        <input type="hidden" name="party_account_id" id="PartyBalanceLimitId">
                        <input type="hidden" name="party_id" id="PartyIdBalanceLimit">

                        <div class="col-sm-6 mb-2">
                            <label for="pStartDate"> Start Date *</label>
                            <input type="date" placeholder="Enter Start Date" name="start_date" required
                                class="form-control" id="pStartDate">
                            <span class="text-danger start_date_error"> </span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label for="pEndDate"> End Date *</label>
                            <input type="date" placeholder="Enter End Date" name="end_date" required
                                class="form-control" id="pEndDate">
                            <span class="text-danger end_date_error"> </span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label for="pDebitLimit"> Debit Limit*</label>
                            <input type="number" step="any" min="0" placeholder="Enter Debit Limit" name="debit_limit"
                                required class="form-control" id="pDebitLimit">
                            <span class="text-danger debit_limit_error"> </span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label for="pCreditLimit"> Credit Limit *</label>
                            <input type="number" step="any" min="0" placeholder="Enter credit Limit" name="credit_limit"
                                required class="form-control" id="pCreditLimit">
                            <span class="text-danger credit_limit_error"> </span>
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

@section('modal_scripts3')
<script>
    $(function() {
        $('#AddPartyLimitsModal').modal({backdrop: 'static', keyboard: false}) 
        $('.OpenPartyLimitsModal').click(function () {
            let customer_id = parseInt($(this).attr('CustomerId')) || 0;
            let party_id = parseInt($(this).attr('PartyId')) || 0;
            $('#PartyIdBalanceLimit').val(party_id);
            $('#AddPartyLimitsModal').modal('show');
        });

        $(document).on("submit", "#PartyBalanceLimitFrom", function(e) {
            // alert('hello')
            e.preventDefault();
            let balance_limit_id = parseInt($('#PartyBalanceLimitId').val());
            let party_id = parseInt($('#PartyIdBalanceLimit').val());
            let form_type = 'POST'
            let form_url = "{{ url('/partymanagement/balancelimits/')}}"
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
                    $('#PartyBalanceLimitFrom').find('span.text-danger').text('')
                },
                success: function(msg) {
                    console.log(msg)
                    if(msg?.success == 'no'){
                        // console.log(msg.error)
                        $.each(msg?.error, function(prefix, val){
                            // console.log(prefix)
                            $('#PartyBalanceLimitFrom').find('span.'+prefix+'_error').text(val[0]);
                        });
                    }else{
                        $("#PartyBalanceLimitFrom").trigger("reset");
                        $('#AddPartyLimitsModal').modal('hide');
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
    });
</script>
@endsection