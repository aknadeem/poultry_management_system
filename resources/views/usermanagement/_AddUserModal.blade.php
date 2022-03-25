<!-- Standard modal content -->
<div id="AddModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="AddModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h4 class="modal-title" id="standard-modalLabel"> <span class="AddUpdate"> Add </span> User </h4>
                <button type="button" class="btn-close ModalClosed" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form autocomplete="off" method="post" enctype="multipart/form-data" id="UserForm">
                    @csrf
                    <div class="row form-group">
                        <input type="hidden" name="user_id_modal" id="UserIdModal">
                        <div class="col-sm-6 mb-2 pe-0">
                            <label for="name"> Name * </label>
                            <input type="text" placeholder="Enter User name" name="name" class="form-control"
                                id="UserName">
                            <span class="text-danger name_error"></span>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="cno"> Contact Number *</label>
                            <input type="number" placeholder="Enter contact number" name="contact_no"
                                class="form-control" id="UserContactNo">
                            <span class="text-danger contact_no_error"></span>
                        </div>

                        <div class="col-sm-6 mb-2 pe-0">
                            <label for="cno"> Email *</label>
                            <input type="text" placeholder="Enter email" name="email" class="form-control"
                                id="UserEmail">
                            <span class="text-danger email_error"></span>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="UserPassword"> Password *</label>
                            <input type="text" placeholder="Enter password" name="password" class="form-control"
                                id="UserPassword">
                            <span class="text-danger password_error"></span>
                        </div>

                        <div class="col-6 pe-0">
                            <label for="UserLevelSelect">Select Userlevel*</label>
                            <div class="input-group">
                                <select class="form-control mySelectModal" id="UserLevelSelect" name="user_level_id"
                                    data-placeholder="Select Userlevel" data-toggle="select2" data-width="90%">
                                    <option value="0"> Select Userlevel </option>
                                </select>
                                <span
                                    class="btn input-group-text btn-dark btn-sm waves-effect waves-light AddUserLevelModal"
                                    title="Click to add new UserLevel" data-plugin="tippy" data-tippy-animation="scale"
                                    data-tippy-arrow="true"><i class="fa fa-plus pt-1"></i>
                                </span>
                            </div>
                            <span class="text-danger user_level_id_error"></span>
                        </div>
                        <div class="col-sm-6">
                            <label for="image">Image</label>
                            <input type="file" class="form-control" name="image_file">
                            <span class="text-danger image_file_error"> </span>
                        </div>
                        <div class="col-6 mt-2">
                        </div>
                        <div class="col-6 mt-2 img-holder">
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
</div><!-- /.modal -->
@section('modal_scripts')

<script>
    $(function() {
        $(document).ready(function () {
            $(".mySelectModal").select2({
                dropdownParent: $("#AddModal")
            });
        });

        var userlevels_list = {};

        $('#AddModal').modal({backdrop: 'static', keyboard: false}) 
        $(document).on('click', '.openUserModal', function(){
            // alert('hello');
            $("#UserForm").trigger("reset");
            let User_id = parseInt($(this).attr('UserId')) || 0;
            $('#AddModal').modal('show');

            getUserLevelList()

            $('#UserIdModal').val(User_id);
            if(User_id > 0){
                $('.AddUpdate').html('Update');
                $.get("{{ url('/usermanagement/users')}}/"+User_id+"/edit" , function(cdata, status){
                    console.log(cdata?.user)
                    $('#UserIdModal').val(cdata?.user?.id)
                    $('#UserName').val(cdata?.user?.name)
                    $('#UserContactNo').val(cdata?.user?.contact_no)
                    $('#UserEmail').val(cdata?.user?.email)
                    $('#UserLevelSelect').val(cdata?.user?.user_level_id)
                    $('#UserLevelSelect').change()
                    $('#UserPassword').val('')
                    if(cdata?.user?.picture != null){
                        let img_url = "{{ asset('storage/users/')}}"
                        $(".img-holder").empty();
                        img_url = img_url+'/'+cdata?.user?.picture;
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

        $('#UserForm').on('submit', function(e) {
            e.preventDefault();
            let EUserId = parseInt($('#UserIdModal').val());
            let form_type = 'POST'
            let form_url = "{{ url('/usermanagement/users')}}"
            $.ajax({
                type: form_type,
                url: form_url,
                data: new FormData(this),
                dataType:'JSON',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend : function(msg) {
                    $('#UserForm').find('div.invalid-feedback').text('')
                },
                success: function(msg) {
                    if(msg?.success == 'no'){
                        $.each(msg?.error, function(prefix, val){
                            $('#UserForm').find('span.'+prefix+'_error').text(val[0]);
                        });
                    }else{
                        $("#UserForm").trigger("reset");
                        $('#AddModal').modal('hide');
                        $('#User-Datatable').DataTable().ajax.reload(null, false);
                        Swal.fire(
                            'Saved',
                            msg.message,
                            'success'
                        )
                    }
                }
            });
        });
        $('.ModalClosed').click(function () {
            $(this).find('form').trigger('reset');
            $('.modal').modal('hide'); 
        });

        function getUserLevelList(){
            var html_code = '';
            $.get("{{ route('getUserLevelList') }}", function(data, status){
                console.log(data)
                userlevels_list = data?.userlevels
                if(userlevels_list?.length > 0){
                    html_code='<option value="" Selected disabled> Select UserLevel </option>';
                    for (var i = 0; i < userlevels_list?.length; i++) {
                        html_code+='<option value='+userlevels_list[i].id+'>'+userlevels_list[i].name+'</option>'; 
                    }
                }
                $('#UserLevelSelect').html(html_code);
                // $('.kt-selectpicker').selectpicker("refresh");
            });
        }
    });

    $(".mySelectModal").select2({
        dropdownParent: $("#AddModal")
    });
</script>
@endsection