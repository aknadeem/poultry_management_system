<div id="AddStoreModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="AddStoreModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h4 class="modal-title" id="standard-modalLabel"> <span class="AddUpdate"> Add </span> Store
                </h4>
                <button type="button" class="btn-close ModalClosed" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form autocomplete="off" method="post" id="ProductStoreForm" class="form_loader">
                    @csrf
                    <div class="row form-group">
                        <input type="hidden" name="store_id" id="StorIdModal">
                        <input type="hidden" id="FromPage">

                        <div class="col-sm-6 mb-2 pe-0">
                            <label for="StoreName"> Store Name*</label>
                            <input type="text" placeholder="Enter store name" name="store_name" required
                                class="form-control" id="StoreName">
                            <span class="text-danger store_name_error"> </span>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="StoreType"> Store Type * </label>
                            <input type="text" placeholder="Enter store type" name="store_type" required
                                class="form-control" id="StoreType">
                            <span class="text-danger store_type_error"> </span>
                        </div>

                        <div class="col-sm-6 mb-2 pe-0">
                            <label for="StoreRacks"> Total Number of Racks * </label>
                            <input type="number" min="0" placeholder="Enter total Racks" name="total_racks" required
                                class="form-control" id="StoreRacks">
                            <span class="text-danger total_racks_error"> </span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label for="StoreArea"> Store Area(sqft) </label>
                            <input type="number" min="0" step="any" placeholder="Store area" name="store_area" required
                                class="form-control" id="StoreArea">
                            <span class="text-danger store_area_error"> </span>
                        </div>

                        <div class="col-sm-12 mb-2">
                            <label for="StoreDesciption"> Description *</label>
                            <input type="text" placeholder="Enter Description" name="store_desciption" required
                                class="form-control" id="StoreDesciption">
                            <span class="text-danger store_desciption_error"> </span>
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
        $('#AddStoreModal').modal({backdrop: 'static', keyboard: false})
        $(".OpenAddStoreModal").click(function() {
            let Storeid = parseInt($(this).attr('StoreId')) || 0;
            let FromPage = $(this).attr('FromPage') || '';
            $("#AddStoreModal").modal('show');
            $('#StoreIdModal').val(Storeid);
            $('#FromPage').val(FromPage);
        });
        
        $('#ProductStoreForm').on('submit', function(e) {
            e.preventDefault();
            let store_id = parseInt($('#StoreIdModal').val());
            let FromPage = $('#FromPage').val() || '';
            let form_type = 'POST'
            let form_url = "{{ url('/ProductManagement/productstores')}}"
            $.ajax({
                type: form_type,
                url: form_url,
                data: new FormData(this),
                dataType:'JSON',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend : function(msg) {
                    $('#ProductStoreForm').find('div.invalid-feedback').text('')
                },
                success: function(msg) {
                    console.log(msg);
                    if(msg?.success == 'no'){
                        $.each(msg?.error, function(prefix, val){
                            $('#ProductStoreForm').find('span.'+prefix+'_error').text(val[0]);
                        });
                    }else{
                        $("#ProductStoreForm").trigger("reset");
                        $('#AddStoreModal').modal('hide');

                        swal.fire({
                                title: "Success",
                                text: msg.message,
                                icon: "success",
                                confirmButtonText: "Ok",
                                // closeOnConfirm: true,
                            }).then (() => {
                                // alert('hello');
                                var selectBox = $('#StoresSelect');
                                var option = new Option(msg?.data?.store_name, msg?.data?.id, true, true);
                                selectBox.append(option).trigger('change');
                                // $('.mySelect').select2();
                                // location.reload();
                            });
                    }
                }
            });
        });

    });
</script>
@endsection