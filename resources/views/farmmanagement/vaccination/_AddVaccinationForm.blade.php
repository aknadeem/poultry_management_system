<div id="AddVaccinationModal" class="modal fade MyModalClass" tabindex="-1" role="dialog"
    aria-labelledby="AddVaccinationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h4 class="modal-title" id="standard-modalLabel"> <span class="AddUpdate"> Add </span> Vaccination
                    Schedule
                </h4>
                <button type="button" class="btn-close ModalClosed" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form autocomplete="off" method="post" id="AddVaccinationForm" class="form_loader">
                    @csrf
                    <div class="row form-group">
                        <input type="hidden" name="schedule_id" id="ScheduleId">
                        <div class="col-sm-12 mb-2">
                            <label for="FarmName"> Farm Name</label>
                            <input type="text" placeholder="Farm name" disabled class="form-control fs-4" id="FarmName">
                        </div>
                        <div class="col-sm-12 mb-2">
                            <label for="ProductName">Product</label>
                            <input type="text" placeholder="Product name" disabled class="form-control fs-4"
                                id="ProductName">
                        </div>
                        <div class="col-sm-12 mb-2">
                            <label for="VaccinatedDate">Vaccination Date *</label>
                            <input type="date" placeholder="Vaccination date" name="vaccination_date"
                                value="{{today()->format('Y-m-d')}}" required class="form-control" id="VaccinatedDate">
                            <span class="text-danger vaccination_date_error">
                            </span>
                        </div>

                        <div class="col-sm-12 mb-2">
                            <label for="Remarks"> Remarks *</label>
                            <input type="text" placeholder="Enter remarks" name="remarks" required class="form-control"
                                id="Remarks">
                            <span class="text-danger remarks_error"> </span>
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

@section('modal_scripts2')
<script>
    $(function() {

        $('#AddVaccinationModal').modal({backdrop: 'static', keyboard: false})
        $(document).on('click', '.OpenAddVaccinationModal', function(){
            let ScheduleId = parseInt($(this).attr('ScheduleId')) || 0;
            let FarmName = $(this).attr('FarmName') || 'Not Found'
            let ProductName = $(this).attr('ProductName') || 'Not Found'
            let ProductCode = $(this).attr('ProductCode') || ''
            // alert(ScheduleId)
            $("#AddVaccinationModal").modal('show');
            $('#ScheduleId').val(ScheduleId);
            $('#FarmName').val(FarmName);
            $('#ProductName').val("["+ProductCode+"] "+ProductName);
        });
        
        $('#AddVaccinationForm').on('submit', function(e) {
            e.preventDefault();
            let form_type = 'POST'
            let form_url = "{{ route('addVaccination')}}"
            // alert(form_url)
            $.ajax({
                type: form_type,
                url: form_url,
                data: $('#AddVaccinationForm').serialize(),
                dataType:'JSON',
                cache: false,
                beforeSend : function(msg) {
                    $('#AddVaccinationForm').find('div.invalid-feedback').text('')
                },
                success: function(msg) {
                    console.log(msg);
                    if(msg?.title != 'Success'){
                        $.each(msg?.error, function(prefix, val){
                            $('#AddVaccinationForm').find('span.'+prefix+'_error').text(val[0]);
                        });
                    }else{
                        $("#AddVaccinationForm").trigger("reset");
                        $('#AddVaccinationModal').modal('hide');

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