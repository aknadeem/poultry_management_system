<!-- Standard modal content -->
<div id="AddCompanyModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="AddCompanyModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h4 class="modal-title" id="standard-modalLabel"> <span class="AddUpdate"> Add </span> Company </h4>
                <button type="button" class="btn-close ModalClosed" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form autocomplete="off" method="post" enctype="multipart/form-data" id="CompanyForm">
                    @csrf
                    <div class="row form-group">
                        <input type="hidden" name="company_id_modal" id="companyIdModal">
                        <div class="col-sm-6 mb-2">
                            <label for="name"> Name * </label>
                            <input type="text" placeholder="Enter Company name" name="name" class="form-control"
                                id="companyName">
                            <span class="text-danger name_error"></span>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="cno"> Contact Number *</label>
                            <input type="number" placeholder="Enter contact number" name="contact_no"
                                class="form-control" id="companyContactNo">
                            <span class="text-danger contact_no_error"></span>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="cno"> Email *</label>
                            <input type="text" placeholder="Enter email" name="email" class="form-control"
                                id="companyEmail">
                            <span class="text-danger email_error"></span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label for="address">Address*</label>
                            <input type="text" placeholder="Enter address" name="address" class="form-control"
                                id="companyAddress">
                            <span class="text-danger address_error"> </span>
                        </div>
                        <div class="col-sm-12">
                            <label for="description">Description</label>
                            <textarea class="form-control ckeditor" name="description" id="companyDescription" cols="80"
                                rows="2"></textarea>
                            <span class="text-danger description_error"> </span>
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
                                Submit</button>
                            <button
                                class="btn btn-light btn-sm waves-effect waves-light mt-3 ModalClosed">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- /.modal -->
@section('modal_scripts')

<script>
    $(function() {
        $('#AddCompanyModal').modal({backdrop: 'static', keyboard: false}) 

        $('.openCompanyModal').click(function () {
            let company_id = parseInt($(this).attr('CompanyId')) || 0;
            $('#AddCompanyModal').modal('show');
            $('#customerIdModal').val(company_id);
            if(company_id > 0){
                $('.AddUpdate').html('Update');
                $.get("{{ url('/company')}}/"+company_id+"/edit" , function(cdata, status){
                    $('#companyIdModal').val(cdata?.company?.id)
                    $('#companyName').val(cdata?.company?.name)
                    $('#companyContactNo').val(cdata?.company?.contact_no)
                    $('#companyEmail').val(cdata?.company?.email)
                    $('#companyAddress').val(cdata?.company?.address)
                    $('#companyDescription').val(cdata?.company?.description)
                    let img_url = "{{ asset('storage/companies/')}}"
                    if(cdata?.company?.company_logo !=''){
                        $(".img-holder").empty();
                        img_url = img_url+'/'+cdata?.company?.company_logo;
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

        $('#CompanyForm').on('submit', function(e) {
            e.preventDefault();
            let EcompanyId = parseInt($('#companyIdModal').val());
            let form_type = 'POST'
            let form_url = "{{ url('/company')}}"
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
                    $('#CompanyForm').find('div.invalid-feedback').text('')
                },
                success: function(msg) {
                    if(msg?.success == 'no'){
                        // console.log(msg.error)
                        $.each(msg?.error, function(prefix, val){
                            // console.log(prefix)
                            $('#CompanyForm').find('span.'+prefix+'_error').text(val[0]);
                        });
                    }else{
                        $("#CompanyForm").trigger("reset");
                        $('#AddCompanyModal').modal('hide');
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
    });
</script>
@endsection