<div id="AddModal" class="modal fade MyModalClass" tabindex="-1" role="dialog" aria-labelledby="AddEmployeeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h4 class="modal-title" id="standard-modalLabel"> <span class="AddUpdate"> Add </span> Expense </h4>
                <button type="button" class="btn-close ModalClosed" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form autocomplete="off" method="post" enctype="multipart/form-data" id="AddExpenseForm">
                    @csrf
                    <div class="row form-group">
                        <input type="hidden" name="expense_id_modal" id="ExpenseIdModal">
                        <div class="col-6 mb-2 pe-0">
                            <label for="ExpenseCatSelect">Expense category *</label>

                            <div class="input-group">
                                <select class="form-control mySelectModal" id="ExpenseCatSelect" name="category_id"
                                    data-placeholder="Select Category" data-toggle="select2" data-width="90%">
                                    <option value="0"> Select Category </option>
                                </select>

                                <span
                                    class="btn input-group-text btn-dark btn-sm waves-effect waves-light AddCategoryModal"
                                    title="Click to add new category" data-plugin="tippy" data-tippy-animation="scale"
                                    data-tippy-arrow="true"><i class="fa fa-plus pt-1"></i>
                                </span>
                            </div>
                            <span class="text-danger category_id_error"></span>
                        </div>

                        <div class="col-6 mb-2">
                            <label for="ExpenseAmount"> Expense Amount *</label>
                            <input type="number" step="any" min="0" placeholder="EnterExpense Amount" name="amount"
                                class="form-control" id="ExpenseAmount">
                            <span class="text-danger amount_error"></span>
                        </div>
                        <div class="col-6 mb-2">
                            <label for="ExpenseDate"> Date *</label>
                            <input type="date" placeholder="Choose date" name="expense_date" class="form-control"
                                id="ExpenseDate">
                            <span class="text-danger expense_date_error"></span>
                        </div>
                        <div class="col-6 mb-2">
                            <label for="Remarks">Remarks</label>
                            <textarea class="form-control ckeditor" name="remarks" id="Remarks" cols="80"
                                rows="2"></textarea>
                            <span class="text-danger remarks_error"> </span>
                        </div>
                        <div class="col-sm-6 mt-2">
                            <label for="image">Image</label>
                            <input type="file" class="form-control" name="image_file">
                            <span class="text-danger image_file_error"> </span>
                        </div>
                        <div class="col-sm-6 mt-2 img-holder">
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

<div id="AddExpenseCategoryModal" class="modal fade MyModalClass" tabindex="-1" role="dialog"
    aria-labelledby="AddExpenseCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog bg-grey">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h4 class="modal-title" id="standard-modalLabel"> <span class="AddUpdate"> Add </span> Category </h4>
                <span type="button" class="btn-close CatModalClosed" aria-label="Close"></span>
            </div>
            <div class="modal-body">
                <form autocomplete="off" method="post" enctype="multipart/form-data" id="AddExpenseCategoryForm">
                    @csrf
                    <div class="row form-group">
                        <div class="col-12 mb-2">
                            <label for="ExpenseCategory"> Expense Category *</label>
                            <input type="text" required placeholder="Expense Category" name="cat_name"
                                class="form-control" id="ExpenseCategory">
                            <span class="text-danger cat_name_error"></span>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-4 mb-3">
                            <button type="submit" id="sub"
                                class="btn btn-secondary btn-sm waves-effect waves-light mt-3 AddUpdate">
                                Submit</button>
                            <span
                                class="btn btn-light btn-sm waves-effect waves-light mt-3 CatModalClosed">Cancel</span>
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

        var categories_list = {};
        $('#AddExpenseCategoryModal').modal({backdrop: 'static', keyboard: false})
        $('#AddModal').modal({backdrop: 'static', keyboard: false})
    
        $(document).on('click', '.AddCategoryModal', function(){
            $('#AddExpenseCategoryModal').modal('show');
        });

        $(document).on('click', '.openExpenseModal', function(){
            $("#AddExpenseForm").trigger("reset");
            let exp_id = parseInt($(this).attr('ExpenseId')) || 0;
            $('#AddModal').modal('show');

            getExpenseCategoryList();

            $('#ExpenseIdModal').val(exp_id);
            if(exp_id > 0){
                $('.AddUpdate').html('Update');
                $.get("{{ url('/inventory/expense')}}/"+exp_id+"/edit" , function(cdata, status){
                    console.log(cdata?.expense?.date_of_birth)
                    $('#ExpenseIdModal').val(cdata?.expense?.id)
                    $('#ExpenseCatSelect').val(cdata?.expense?.category_id)
                    $('#ExpenseCatSelect').change()
                    $('#ExpenseAmount').val(cdata?.expense?.amount)
                    $('#ExpenseDate').val(cdata?.expense?.expense_date)
                    $('#Remarks').val(cdata?.expense?.remarks)

                    let img_url = "{{ asset('storage/expenses/')}}"
                    if(cdata?.expense?.picture !=''){
                        $(".img-holder").empty();
                        img_url = img_url+'/'+cdata?.expense?.picture;
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

        $('#AddExpenseForm').on('submit', function(e) {
            e.preventDefault();
            let ExpId = parseInt($('#ExpenseIdModal').val());
            let form_type = 'POST'
            let form_url = "{{ url('/inventory/expense')}}"
            $.ajax({
                type: form_type,
                url: form_url,
                data: new FormData(this),
                dataType:'JSON',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend : function(msg) {
                    $('#AddExpenseForm').find('div.invalid-feedback').text('')
                },
                success: function(msg) {
                    if(msg?.success == 'no'){
                        $.each(msg?.error, function(prefix, val){
                            $('#AddExpenseForm').find('span.'+prefix+'_error').text(val[0]);
                        });
                    }else{
                        $("#AddExpenseForm").trigger("reset");
                        $('#AddModal').modal('hide');
                        $('#Expense-datatable').DataTable().ajax.reload(null, false);
                        Swal.fire(
                            'Saved',
                            msg.message,
                            'success'
                        )
                    }
                }
            });
        });
        
        $('#AddExpenseCategoryForm').on('submit', function(e) {
            e.preventDefault();
            // alert('hello')
            let form_type = 'POST'
            let form_url = "{{ route('storeExpenseCategrory')}}"
            $.ajax({
                type: form_type,
                url: form_url,
                data: new FormData(this),
                dataType:'JSON',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend : function(msg) {
                    $('#AddExpenseCategoryForm').find('div.invalid-feedback').text('')
                },
                success: function(msg) {

                    console.log(msg)
                    if(msg?.success == 'no'){
                        $.each(msg?.error, function(prefix, val){
                            $('#AddExpenseCategoryForm').find('span.'+prefix+'_error').text(val[0]);
                        });
                    }else{
                        $("#AddExpenseCategoryForm").trigger("reset");
                        $('#AddExpenseCategoryModal').modal('hide');
                        getExpenseCategoryList();
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
        
        $('.CatModalClosed').click(function () {
            $(this).find('form').trigger('reset');
            $('#AddExpenseCategoryModal').modal('hide'); 
        });

        function getExpenseCategoryList(){
            var html_code = '';
            $.get("{{ route('getExpenseCategoryList') }}", function(data, status){
                console.log(data)
                categories_list = data?.categories
                if(categories_list?.length > 0){
                    html_code='<option value="" Selected disabled> Select Category </option>';
                    for (var i = 0; i < categories_list?.length; i++) {
                        html_code+='<option value='+categories_list[i].id+'>'+categories_list[i].name+'</option>'; 
                    }
                }
                $('#ExpenseCatSelect').html(html_code);
                // $('.kt-selectpicker').selectpicker("refresh");
            });
        }
    });
</script>
@endsection