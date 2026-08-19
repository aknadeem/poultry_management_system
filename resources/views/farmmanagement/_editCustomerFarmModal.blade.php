<div id="EditCustomerFarmModal" class="modal fade" tabindex="-1" role="dialog"
     aria-labelledby="EditCustomerFarmModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header border-bottom">
                <h4 class="modal-title" id="EditCustomerFarmModalLabel">
                    Edit Customer Farm
                </h4>

                <button type="button"
                        class="btn-close ModalClosed"
                        aria-label="Close">
                </button>
            </div>

            <div class="modal-body">

                <form autocomplete="off"
                      method="post"
                      id="CustomerFarmForm"
                      class="form_loader"
                      enctype="multipart/form-data">

                    @csrf
                    @method('PUT')

                    <input type="hidden" name="farm_id" id="FarmIdModal">

                    <div class="row form-group">

                        <div class="col-sm-6 mb-2 pe-0">
                            <label for="CustomerName">Customer Name</label>
                            <input type="text"
                                   readonly
                                   disabled
                                   class="form-control"
                                   id="CustomerName">
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="CustomerCnic">Customer CNIC</label>
                            <input type="text"
                                   readonly
                                   disabled
                                   class="form-control"
                                   id="CustomerCnic">
                        </div>

                        <div class="col-sm-6 mb-2 pe-0">
                            <label for="CustomerFarmType">Farm Type *</label>
                            <select name="farm_type_id"
                                    id="CustomerFarmType"
                                    class="form-control mySelect"
                                    data-toggle="select2"
                                    data-width="100%">
                                <option value="">Select Farm Type</option>
                                @foreach ($farm_types as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger farm_type_id_error"></span>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="CustomerFarmSubtype">Farm Subtype *</label>
                            <select name="farm_subtype_id"
                                    id="CustomerFarmSubtype"
                                    class="form-control mySelect"
                                    data-toggle="select2"
                                    data-width="100%">
                                <option value="">Select Farm Subtype</option>
                                @foreach ($farm_subtypes as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger farm_subtype_id_error"></span>
                        </div>

                        <div class="col-sm-6 mb-2 pe-0">
                            <label for="FarmName">Farm Name *</label>
                            <input type="text"
                                   placeholder="Enter farm name"
                                   name="farm_name"
                                   class="form-control"
                                   id="FarmName">
                            <span class="text-danger farm_name_error"></span>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="FarmNoc">Farm NOC *</label>
                            <input type="text"
                                   placeholder="Enter farm NOC"
                                   name="farm_noc"
                                   class="form-control"
                                   id="FarmNoc">
                            <span class="text-danger farm_noc_error"></span>
                        </div>

                        <div class="col-sm-12 mb-2">
                            <label for="FarmAddress">Farm Address *</label>
                            <input type="text"
                                   placeholder="Enter farm address"
                                   name="farm_address"
                                   class="form-control"
                                   id="FarmAddress">
                            <span class="text-danger farm_address_error"></span>
                        </div>

                        <div class="col-sm-4 mb-2 pe-0">
                            <label for="FarmArea">Farm Area</label>
                            <input type="number"
                                   step="any"
                                   min="0"
                                   placeholder="Farm area"
                                   name="farm_area"
                                   class="form-control"
                                   id="FarmArea">
                            <span class="text-danger farm_area_error"></span>
                        </div>

                        <div class="col-sm-4 mb-2 pe-0">
                            <label for="FeedRoomSize">Feed Room Size</label>
                            <input type="number"
                                   step="any"
                                   min="0"
                                   placeholder="Feed room size"
                                   name="feed_room_size"
                                   class="form-control"
                                   id="FeedRoomSize">
                            <span class="text-danger feed_room_size_error"></span>
                        </div>

                        <div class="col-sm-4 mb-2">
                            <label for="FarmCapacity">Farm Capacity</label>
                            <input type="number"
                                   min="0"
                                   placeholder="Farm capacity"
                                   name="farm_capacity"
                                   class="form-control"
                                   id="FarmCapacity">
                            <span class="text-danger farm_capacity_error"></span>
                        </div>

                        <div class="col-sm-6 mb-2 pe-0">
                            <label for="FarmImage">Farm Image *</label>
                            <input type="file"
                                   name="farm_image"
                                   class="form-control"
                                   id="FarmImage" required>
                            <span class="text-danger farm_image_error"></span>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label>Current Image</label>
                            <div id="CurrentFarmImageWrap" class="mt-1"></div>
                        </div>

                    </div>

                    <div class="row form-group">
                        <div class="col-sm-4 mb-3">
                            <button type="submit"
                                    id="CustomerFarmSubmit"
                                    class="btn btn-secondary btn-sm waves-effect waves-light mt-3">
                                Update
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

        $('#EditCustomerFarmModal').modal({
            backdrop: 'static',
            keyboard: false
        });

        $(document).on('click', '.OpenEditCustomerFarmModal', function () {

            const farmId = parseInt($(this).attr('FarmId')) || 0;

            resetCustomerFarmForm();
            $('#FarmIdModal').val(farmId);
            $('#EditCustomerFarmModal').modal('show');

            if (farmId <= 0) {
                return;
            }

            $.get(
                "{{ url('/farmManagement/customerfarms') }}/" + farmId + "/edit",
                function (response) {

                    const farm = response?.farm;
                    const party = response?.party;

                    if (!farm) {
                        return;
                    }

                    $('#FarmIdModal').val(farm.id);
                    $('#CustomerName').val(party?.name || '');
                    $('#CustomerCnic').val(party?.cnic_no || '');
                    $('#CustomerFarmType').val(farm.farm_type_id).trigger('change');
                    $('#CustomerFarmSubtype').val(farm.farm_subtype_id).trigger('change');
                    $('#FarmName').val(farm.farm_name);
                    $('#FarmNoc').val(farm.farm_noc);
                    $('#FarmAddress').val(farm.farm_address);
                    $('#FarmArea').val(farm.farm_area);
                    $('#FeedRoomSize').val(farm.feed_room_size);
                    $('#FarmCapacity').val(farm.farm_capacity);

                    if (farm.farm_image) {
                        $('#CurrentFarmImageWrap').html(
                            '<a href="{{ asset('storage/party/farm') }}/' + farm.farm_image + '" target="_blank">' +
                            '<img src="{{ asset('storage/party/farm') }}/' + farm.farm_image + '" width="80" alt="Farm image">' +
                            '</a>'
                        );
                    }
                }
            );
        });

        $('#CustomerFarmForm').on('submit', function (e) {

            e.preventDefault();

            const farmId = parseInt($('#FarmIdModal').val()) || 0;
            if (farmId <= 0) {
                return;
            }

            const form = this;
            clearCustomerFarmErrors();

            $.ajax({
                type: 'POST',
                url: "{{ url('/farmManagement/customerfarms') }}/" + farmId,
                data: new FormData(form),
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function () {
                    $('#CustomerFarmSubmit')
                        .prop('disabled', true)
                        .text('Saving...');
                },
                success: function (response) {

                    if (response?.success === 'no') {
                        $.each(response?.error || {}, function (field, messages) {
                            $('#CustomerFarmForm')
                                .find('span.' + field + '_error')
                                .text(messages[0]);
                        });

                        return;
                    }

                    resetCustomerFarmForm();
                    $('#EditCustomerFarmModal').modal('hide');

                    Swal.fire({
                        title: 'Success',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'Ok'
                    }).then(function () {
                        window.location.reload();
                    });
                },
                error: function (xhr) {
                    console.error(xhr);
                },
                complete: function () {
                    $('#CustomerFarmSubmit')
                        .prop('disabled', false)
                        .text('Update');
                }
            });
        });

        $(document).on('click', '.ModalClosed', function () {
            resetCustomerFarmForm();
            $('#EditCustomerFarmModal').modal('hide');
        });

        function resetCustomerFarmForm() {
            $('#CustomerFarmForm')[0].reset();
            $('#FarmIdModal').val('');
            $('#CustomerName').val('');
            $('#CustomerCnic').val('');
            $('#CustomerFarmType').val('').trigger('change');
            $('#CustomerFarmSubtype').val('').trigger('change');
            $('#CurrentFarmImageWrap').html('');
            clearCustomerFarmErrors();
        }

        function clearCustomerFarmErrors() {
            $('#CustomerFarmForm')
                .find('span[class$="_error"]')
                .text('');
        }
    });
</script>
@endsection
