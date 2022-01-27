<div id="AddPartyDocumentModal" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="AddPartyDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h4 class="modal-title" id="standard-modalLabel"> <span class="AddUpdate"> Add </span> Documents
                </h4>
                <button type="button" class="btn-close ModalClosed" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form autocomplete="off" method="post" enctype="multipart/form-data" id="PartyDocumentForm"
                    class="form_loader">
                    @csrf
                    <div class="row form-group">
                        <input type="hidden" name="party_document_id" id="PartyDocumentId">
                        <input type="hidden" name="party_id" id="PartyIdDocument">

                        <div class="col-sm-6 mb-2">
                            <label for="pDocumentTitle"> Title *</label>
                            <input type="text" placeholder="Enter Document title" name="document_title" required
                                class="form-control" id="pDocumentTitle">
                            <span class="text-danger document_title_error"> </span>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="pDocuments"> document *</label>
                            <input type="file" name="document_name" class="form-control" required id="pDocuments">
                            <span class="text-danger farm_name_error"> </span>
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

@section('modal_scripts2')
<script>
    $(function() {
        $('#AddPartyDocumentModal').modal({backdrop: 'static', keyboard: false}) 
        $(document).on("click", ".OpenPartyDocumentModal", function(event) {
            let customer_id = parseInt($(this).attr('CustomerId')) || 0;
            let party_id = parseInt($(this).attr('PartyId')) || 0;
            alert(party_id)
            $('#AddPartyDocumentModal').modal('show');
            $('#PartyIdDocument').val(party_id);

            $(document).on("submit", "#PartyDocumentForm", function(e) {
            // alert('hello')
            e.preventDefault();
            let party_document_id = parseInt($('#PartyDocumentId').val());
            let party_id = parseInt($('#PartyIdDocument').val());
            let form_type = 'POST'
            let form_url = "{{ url('/partymanagement/partydocuments/')}}"
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
                    $('#PartyDocumentForm').find('span.text-danger').text('')
                },
                success: function(msg) {
                    console.log(msg)
                    if(msg?.success == 'no'){
                        // console.log(msg.error)
                        $.each(msg?.error, function(prefix, val){
                            // console.log(prefix)
                            $('#PartyDocumentForm').find('span.'+prefix+'_error').text(val[0]);
                        });
                    }else{
                        $("#PartyDocumentForm").trigger("reset");
                        $('#AddPartyDocumentModal').modal('hide');
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