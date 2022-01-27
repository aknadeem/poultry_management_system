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
                    class="form_loader">
                    @csrf
                    <div class="row form-group">
                        <input type="hidden" name="customer_id_modal" id="customerIdModal">
                        <div class="col-sm-6 mb-2">
                            <label for="name"> Name * </label>
                            <input type="text" placeholder="Enter customer name" name="name" class="form-control"
                                id="customerName">
                            <span class="text-danger name_error"></span>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="cno"> Contact Number *</label>
                            <input type="number" placeholder="Enter contact number" name="contact_no"
                                class="form-control" id="customerContactNo">
                            <span class="text-danger contact_no_error"></span>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="cno"> Email *</label>
                            <input type="text" placeholder="Enter email" name="email" class="form-control"
                                id="customerEmail">
                            <span class="text-danger email_error"></span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label for="cno">Farm Name/ Shop *</label>
                            <input type="text" placeholder="Enter Farm/Shop Name" name="farm_name" class="form-control"
                                id="customerFarmName">
                            <span class="text-danger farm_name_error"> </span>
                        </div>
                        <div class="col-sm-12">
                            <label for="address">Address*</label>
                            <textarea class="form-control ckeditor" name="address" id="customerAddress" cols="80"
                                rows="2"></textarea>
                            <span class="text-danger address_error"> </span>
                        </div>
                        <div class="col-sm-6 mt-2">
                            <label for="image">Image</label>
                            <input type="file" class="form-control" name="image_file">
                            <span class="text-danger image_file_error"> </span>
                        </div>
                        <div class="col-sm-6 mt-2 img-holder">
                            {{-- <img class="d-flex me-3 avatar-lg" src="../assets/images/users/user-8.jpg"
                                alt="Generic placeholder image"> --}}
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
</div><!-- /.modal -->
@section('modal_scripts')

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

        $(".form_loader").on("submit", function () {
            $("#pageloader").fadeIn();
        });

    });
</script>
@endsection