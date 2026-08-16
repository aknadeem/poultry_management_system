<div id="AddStoreModal" class="modal fade" tabindex="-1" role="dialog"
     aria-labelledby="AddStoreModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header border-bottom">
                <h4 class="modal-title" id="AddStoreModalLabel">
                    <span class="AddUpdate">Add</span> Store
                </h4>

                <button type="button"
                        class="btn-close ModalClosed"
                        aria-label="Close">
                </button>
            </div>

            <div class="modal-body">

                <form autocomplete="off"
                      method="post"
                      id="ProductStoreForm"
                      class="form_loader">

                    @csrf

                    <div class="row form-group">

                        <input type="hidden"
                               name="store_id"
                               id="StoreIdModal">

                        <input type="hidden"
                               name="from_page"
                               id="FromPage">

                        <div class="col-sm-6 mb-2 pe-0">
                            <label for="StoreName">
                                Store Name *
                            </label>

                            <input type="text"
                                   placeholder="Enter store name"
                                   name="store_name"
                                   class="form-control"
                                   id="StoreName">

                            <span class="text-danger store_name_error"></span>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="StoreType">
                                Store Type *
                            </label>

                            <input type="text"
                                   placeholder="Enter store type"
                                   name="store_type"
                                   class="form-control"
                                   id="StoreType">

                            <span class="text-danger store_type_error"></span>
                        </div>

                        <div class="col-sm-6 mb-2 pe-0">
                            <label for="StoreRacks">
                                Total Number of Racks *
                            </label>

                            <input type="number"
                                   min="0"
                                   placeholder="Enter total racks"
                                   name="total_racks"
                                   class="form-control"
                                   id="StoreRacks">

                            <span class="text-danger total_racks_error"></span>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="StoreArea">
                                Store Area (sqft)
                            </label>

                            <input type="number"
                                   min="0"
                                   step="any"
                                   placeholder="Store area"
                                   name="store_area"
                                   class="form-control"
                                   id="StoreArea">

                            <span class="text-danger store_area_error"></span>
                        </div>

                        <div class="col-sm-12 mb-2">
                            <label for="StoreDescription">
                                Description *
                            </label>

                            <input type="text"
                                   placeholder="Enter Description"
                                   name="store_desciption"
                                   class="form-control"
                                   id="StoreDescription">

                            <span class="text-danger store_desciption_error"></span>
                        </div>

                    </div>

                    <div class="row form-group">
                        <div class="col-sm-4 mb-3">

                            <button type="submit"
                                    id="sub"
                                    class="btn btn-secondary btn-sm waves-effect waves-light mt-3 AddUpdate">
                                Submit
                            </button>

                            <button type="button"
                                    class="btn btn-light btn-sm waves-effect waves-light mt-3 ModalClosed">
                                Cancel
                            </button>

                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

@section('modal_scripts')

    <script>
        $(function () {

            $('#AddStoreModal').modal({
                backdrop: 'static',
                keyboard: false
            });

            /**
             * Open Add/Edit Store Modal
             */
            $(document).on('click', '.OpenAddStoreModal', function () {

                const storeId = parseInt($(this).attr('StoreId')) || 0;
                const fromPage = $(this).attr('FromPage') || '';

                // Reset form and validation
                resetStoreForm();

                $('#FromPage').val(fromPage);
                $('#StoreIdModal').val(storeId);
                $('#AddStoreModal').modal('show');

                if (storeId > 0) {
                    $('.AddUpdate').text('Update');

                    $.get(
                        "{{ url('/ProductManagement/productstores') }}/" + storeId + "/edit",
                        function (response) {

                            const store = response?.store;
                            console.log('store :: ', store);
                            if (!store) {
                                return;
                            }
                            $('#StoreIdModal').val(store.id);

                            $('#StoreName').val(store.store_name);
                            $('#StoreType').val(store.store_type);
                            $('#StoreRacks').val(store.total_racks);
                            $('#StoreArea').val(store.store_area);
                            $('#StoreDescription').val(store.description);

                        }
                    );

                } else {

                    $('.AddUpdate').text('Add');

                }
            });


            /**
             * Submit Add/Edit Store
             */
            $('#ProductStoreForm').on('submit', function (e) {

                e.preventDefault();

                const form = this;

                clearStoreErrors();

                $.ajax({

                    type: 'POST',

                    url: "{{ url('/ProductManagement/productstores') }}",

                    data: new FormData(form),

                    dataType: 'JSON',

                    contentType: false,

                    cache: false,

                    processData: false,

                    beforeSend: function () {

                        $('#sub')
                            .prop('disabled', true)
                            .text('Saving...');

                    },

                    success: function (response) {

                        if (response?.success === 'no') {

                            $.each(response?.error || {}, function (field, messages) {

                                $('#ProductStoreForm')
                                    .find('span.' + field + '_error')
                                    .text(messages[0]);

                            });

                            return;
                        }

                        resetStoreForm();

                        $('#AddStoreModal').modal('hide');

                        Swal.fire({
                            title: 'Success',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'Ok'
                        }).then(function () {

                            /**
                             * If the store select exists,
                             * add/update the selected store.
                             */
                            const selectBox = $('#StoresSelect');

                            if (selectBox.length && response?.data) {

                                const store = response.data;

                                const option = new Option(
                                    store.store_name,
                                    store.id,
                                    true,
                                    true
                                );

                                selectBox
                                    .find('option[value="' + store.id + '"]')
                                    .remove();

                                selectBox
                                    .append(option)
                                    .trigger('change');
                            }

                            /**
                             * If this modal is used from a DataTable,
                             * reload it here.
                             */
                            if ($.fn.DataTable.isDataTable('#Store-Datatable')) {
                                $('#Store-Datatable')
                                    .DataTable()
                                    .ajax.reload(null, false);
                            }

                        });

                    },

                    error: function (xhr) {

                        console.error(xhr);

                    },

                    complete: function () {

                        $('#sub')
                            .prop('disabled', false)
                            .text('Submit');

                    }

                });

            });


            /**
             * Close Modal
             */
            $(document).on('click', '.ModalClosed', function () {

                resetStoreForm();

                $('#AddStoreModal').modal('hide');

            });


            /**
             * Reset Store Form
             */
            function resetStoreForm() {

                $('#ProductStoreForm')[0].reset();

                $('#StoreIdModal').val('');

                $('#FromPage').val('');

                $('.AddUpdate').text('Add');

                clearStoreErrors();

            }


            /**
             * Clear Validation Errors
             */
            function clearStoreErrors() {

                $('#ProductStoreForm')
                    .find('span[class$="_error"]')
                    .text('');

            }

        });
    </script>

@endsection