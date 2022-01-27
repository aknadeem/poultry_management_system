<div id="AddTypeModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="AddTypeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog align-center">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h4 class="modal-title" id="ModalTitle"></h4>
                <button type="button" class="btn-close ModalClosed" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form autocomplete="off" method="post" id="TypeForm">
                    @csrf
                    <div class="row form-group">
                        <div class="col-sm-12 mb-2">
                            <input type="hidden" name="table_name" id="TableName">
                            <label for="TypeName"> Name * </label>
                            <input type="text" placeholder="Enter name" name="name" class="form-control" id="TypeName"
                                required>
                            <span class="text-danger name_error"></span>
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

@section('modal_scripts2')
<script>
    $(function() {  
        var SelectBoxId = '';
        $('.OpenaddTypeModal').click(function () {
            // $('#TypeForm').find('span.text-danger').text('')
            let form_title = 'Add new';
            SelectBoxId = $(this).attr('SelectBoxId') || '';
            let table_name = $(this).attr('TableName') || '';
            $('#AddTypeModal').modal('show');

            $("#TypeForm").trigger("reset");
            $('#TypeForm').find('span.text-danger').text('')

            $("#ModalTitle").html(form_title);
            $("#TableName").val(table_name);
            // alert(SelectBoxId)
            $('#TypeForm').on('submit', function(e) {
                e.preventDefault();
                let form_type = 'POST'
                let form_url = "{{ route('addalltypes')}}"
                // alert(form_url)
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
                        $('#TypeForm').find('span.text-danger').text('')
                    },
                    success: function(msg) {
                        console.log(msg);
                        if(msg?.success == 'no'){
                            // console.log(msg.error)
                            $.each(msg?.error, function(prefix, val){
                                // console.log(prefix)
                                $('#TypeForm').find('span.'+prefix+'_error').text(val[0]);
                            });

                            // swal.fire({
                            //     title: "Warning",
                            //     text: msg.message,
                            //     icon: "warning",
                            //     confirmButtonText: "Close",
                            // });
                        }else{
                            $("#TypeForm").trigger("reset");
                            $('#AddTypeModal').modal('hide');

                            swal.fire({
                                title: "Success",
                                text: msg.message,
                                icon: "success",
                                confirmButtonText: "Ok",
                                // closeOnConfirm: true,
                            }).then (() => {
                                // alert('hello');
                                console.log(SelectBoxId)
                                var selectBox = $('#'+SelectBoxId);
                                var option = new Option(msg?.data?.name, msg?.data?.id, true, true);
                                selectBox.append(option).trigger('change');
                                selectBox.autofocus();
                                // $('.mySelect').select2();
                                // location.reload();
                            });
                        }
                    }
                });
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