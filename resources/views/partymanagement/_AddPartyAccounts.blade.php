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
                <form autocomplete="off" method="post" id="PartyAccountForm" class="form_loader">
                    @csrf
                    <div class="row form-group">
                        <input type="hidden" name="party_account_id" id="PartyAccountId">
                        <input type="hidden" name="party_id" id="PartyId">

                        <div class="col-sm-6 mb-2">
                            <label for="pAccountTitle">Account Title *</label>
                            <input type="text" placeholder="Enter Account Title" name="account_title" required
                                class="form-control" id="pAccountTitle">
                            <span class="text-danger account_title_error"> </span>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="pAccountNo">Account Number *</label>
                            <input type="text" placeholder="Enter Account number" name="account_number" required
                                class="form-control" id="pAccountNo">
                            <span class="text-danger account_number_error"> </span>
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

@section('modal_scripts')
<script>
    $(function() {
        $('#AddAccountModal').modal({backdrop: 'static', keyboard: false})
        $(document).on("click", ".OpenAccountModal", function(event) {
            let customer_id = parseInt($(this).attr('CustomerId')) || 0;
            let party_id = parseInt($(this).attr('PartyId')) || 0;
            $('#AddAccountModal').modal('show');
            $('#PartyId').val(party_id);

            $(document).on("submit", "#PartyAccountForm", function(e) {
                // alert('hello')
                e.preventDefault();
                let party_account_id = parseInt($('#PartyAccountId').val());
                let party_id = parseInt($('#PartyId').val());
                let form_type = 'POST'
                let form_url = "{{ url('/partymanagement/partyaccounts/')}}"
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
                        $('#PartyAccountForm').find('span.text-danger').text('')
                    },
                    success: function(msg) {
                        console.log(msg)
                        if(msg?.success == 'no'){
                            // console.log(msg.error)
                            $.each(msg?.error, function(prefix, val){
                                // console.log(prefix)
                                $('#PartyAccountForm').find('span.'+prefix+'_error').text(val[0]);
                            });
                        }else{
                            $("#PartyAccountForm").trigger("reset");
                            $('#AddAccountModal').modal('hide');
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
        
    });
</script>
@endsection