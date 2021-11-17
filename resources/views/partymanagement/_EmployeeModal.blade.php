<!-- Standard modal content -->
<div id="AddEmployeeModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="AddEmployeeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h4 class="modal-title" id="standard-modalLabel"> <span class="AddUpdate"> Add </span> Employee </h4>
                <button type="button" class="btn-close ModalClosed" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form autocomplete="off" method="post" enctype="multipart/form-data" id="EmployeeForm">
                    @csrf
                    <div class="row form-group">
                        <input type="hidden" name="employee_id_modal" id="employeeIdModal">
                        <div class="col-sm-6 mb-2">
                            <label for="name"> Name * </label>
                            <input type="text" placeholder="Enter Employee name" name="name" class="form-control"
                                id="employeeName">
                            <span class="text-danger name_error"></span>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="cno"> Contact Number *</label>
                            <input type="number" placeholder="Enter contact number" name="contact_no"
                                class="form-control" id="employeeContactNo">
                            <span class="text-danger contact_no_error"></span>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="cno"> Email *</label>
                            <input type="text" placeholder="Enter email" name="email" class="form-control"
                                id="employeeEmail">
                            <span class="text-danger email_error"></span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label for="cnic"> CNIC *</label>
                            <input type="number" placeholder="Enter cnic" name="cnic" class="form-control"
                                id="employeeCnic">
                            <span class="text-danger cnic_error"></span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label for="cnic"> Date of birth *</label>
                            <input type="date" placeholder="Enter date_of_birth" name="date_of_birth"
                                class="form-control" id="employeeDate_of_birth">
                            <span class="text-danger date_of_birth_error"></span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label for="cno"> Designation *</label>
                            <input type="text" placeholder="Enter designation" name="designation" class="form-control"
                                id="employeeDesignation">
                            <span class="text-danger designation_error"></span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label for="department"> Department </label>
                            <input type="text" placeholder="Enter department" name="department" class="form-control"
                                id="employeeDepartment">
                            <span class="text-danger department_error"></span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label for="address">Address*</label>
                            <input type="text" placeholder="Enter address" name="address" class="form-control"
                                id="employeeAddress">
                            <span class="text-danger address_error"> </span>
                        </div>
                        <div class="col-sm-12">
                            <label for="description">Description</label>
                            <textarea class="form-control ckeditor" name="description" id="employeeDescription"
                                cols="80" rows="2"></textarea>
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
</div><!-- /.modal -->
@section('modal_scripts')

<script>
    $(function() {
        $(document).on('click', '.openEmployeeModal', function(){
            $("#EmployeeForm").trigger("reset");
            let employee_id = parseInt($(this).attr('EmployeeId')) || 0;
            $('#AddEmployeeModal').modal('show');
            $('#employeeIdModal').val(employee_id);
            if(employee_id > 0){
                $('.AddUpdate').html('Update');
                $.get("{{ url('/employee')}}/"+employee_id+"/edit" , function(cdata, status){
                    console.log(cdata?.employee?.date_of_birth)
                    $('#employeeIdModal').val(cdata?.employee?.id)
                    $('#employeeName').val(cdata?.employee?.name)
                    $('#employeeContactNo').val(cdata?.employee?.contact_no)
                    $('#employeeEmail').val(cdata?.employee?.email)
                    $('#employeeCnic').val(cdata?.employee?.cnic)
                    $('#employeeDate_of_birth').val(cdata?.employee?.date_of_birth)
                    $('#employeeDesignation').val(cdata?.employee?.designation)
                    $('#employeeDepartment').val(cdata?.employee?.department)
                    $('#employeeAddress').val(cdata?.employee?.address)
                    $('#employeeDescription').val(cdata?.employee?.description)
                    let img_url = "{{ asset('storage/employees/')}}"
                    if(cdata?.employee?.employee_image !=''){
                        $(".img-holder").empty();
                        img_url = img_url+'/'+cdata?.employee?.employee_image;
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

        $('#EmployeeForm').on('submit', function(e) {
            e.preventDefault();
            let EemployeeId = parseInt($('#employeeIdModal').val());
            let form_type = 'POST'
            let form_url = "{{ url('/employee')}}"
            $.ajax({
                type: form_type,
                url: form_url,
                data: new FormData(this),
                dataType:'JSON',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend : function(msg) {
                    $('#EmployeeForm').find('div.invalid-feedback').text('')
                },
                success: function(msg) {
                    if(msg?.success == 'no'){
                        $.each(msg?.error, function(prefix, val){
                            $('#EmployeeForm').find('span.'+prefix+'_error').text(val[0]);
                        });
                    }else{
                        $("#EmployeeForm").trigger("reset");
                        $('#AddEmployeeModal').modal('hide');
                        $('#employee-datatable').DataTable().ajax.reload(null, false);
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
    });
</script>
@endsection