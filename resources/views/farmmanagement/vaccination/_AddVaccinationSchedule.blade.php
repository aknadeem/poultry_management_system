<div id="AddStoreModal" class="modal fade MyModalClass" tabindex="-1" role="dialog" aria-labelledby="AddStoreModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h4 class="modal-title" id="standard-modalLabel"> <span class="AddUpdate"> Add </span> Vaccination
                    Schedule
                </h4>
                <button type="button" class="btn-close ModalClosed" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form autocomplete="off" method="post" id="AddVaccinationScheduleForm" class="form_loader">
                    @csrf
                    <div class="row form-group">
                        <input type="hidden" name="store_id" id="StorIdModal">
                        <input type="hidden" id="FromPage">

                        <div class="col-sm-6 mb-2 pe-0">
                            <label for="FarmSelect"> Select Farm *</label>
                            <select name="farm_id" id="FarmSelect" class="form-control mySelectModal" required
                                data-toggle="select2" data-width="100%">
                                <option value="">dasd</option>
                                <option value="">dasd</option>
                                <option value="">dasd</option>
                            </select>
                            <span class="text-danger farm_id_error"> </span>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="VaccineSelect"> Select Vaccine *</label>
                            <select name="product_id" id="VaccineSelect" class="form-control mySelectModal" required
                                data-toggle="select2" data-width="100%">
                                <option value="">dasd</option>
                                <option value="">dasd</option>
                                <option value="">dasd</option>
                            </select>
                            <span class="text-danger product_id_error"> </span>
                        </div>

                        <div class="col-sm-6 mb-2 pe-0">
                            <label for="VaccinationDate"> Date *</label>
                            <input type="date" placeholder="Vaccination date" name="schedule_date" required
                                class="form-control" id="VaccinationDate">
                            <span class="text-danger schedule_date_error"> </span>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="Description"> Description *</label>
                            <input type="text" placeholder="Enter Description" name="description" required
                                class="form-control" id="Description">
                            <span class="text-danger description_error"> </span>
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

            $.get("{{ route('vaccination.create')}}", function(res, status){
            var product_html = '';
            var farm_html = '';
            console.log(res)
            if(res.success == 'yes'){
                product_html ='<option value="">Select Vaccination</option>'; 
                farm_html ='<option value="">Select Farm</option>'; 
                for (var i = 0; i < res?.products.length; i++) {
                    product_html+='<option value='+res?.products[i]?.id+'>['+res?.products[i].product_code+'] '+ res?.products[i].product_name +'</option>'; 
                }

                for (var i = 0; i < res?.farms?.length; i++) {
                    farm_html+='<option value='+res?.farms[i]?.id+'>'+res?.farms[i]?.farm_name+'</option>'; 
                }
            }else{
                product_html+='<option value="">No Data Found</option>'; 
                farm_html+='<option value="">No Data Found</option>'; 
            }
            $('#VaccineSelect').html(product_html);
            $('#FarmSelect').html(farm_html);
            // $('.kt-selectpicker').selectpicker("refresh");
        });

        });
        
        $('#AddVaccinationScheduleForm').on('submit', function(e) {
            e.preventDefault();
            let store_id = parseInt($('#StoreIdModal').val());
            let FromPage = $('#FromPage').val() || '';
            let form_type = 'POST'
            let form_url = "{{ route('vaccination.store')}}"
            // alert(form_url)
            $.ajax({
                type: form_type,
                url: form_url,
                data: $('#AddVaccinationScheduleForm').serialize(),
                dataType:'JSON',
                cache: false,
                beforeSend : function(msg) {
                    $('#AddVaccinationScheduleForm').find('div.invalid-feedback').text('')
                },
                success: function(msg) {
                    console.log(msg);
                    if(msg?.title != 'Success'){
                        $.each(msg?.error, function(prefix, val){
                            $('#AddVaccinationScheduleForm').find('span.'+prefix+'_error').text(val[0]);
                        });
                    }else{
                        $("#AddVaccinationScheduleForm").trigger("reset");
                        $('#AddStoreModal').modal('hide');

                        swal.fire({
                                title: "Success",
                                text: msg.message,
                                icon: "success",
                                confirmButtonText: "Ok",
                                // closeOnConfirm: true,
                            }).then (() => {
                                // alert('hello');
                                $('#store_datatable').DataTable().ajax.reload();
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