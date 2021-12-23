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

@section('modal_scripts')
<script>

</script>
@endsection