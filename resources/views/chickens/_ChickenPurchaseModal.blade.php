<!-- Standard modal content -->
<div id="AddChickenPurchase
Modal" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddFeedModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h4 class="modal-title" id="standard-modalLabel"> <span class="AddUpdate"> Add </span> Feed Entry </h4>
                <button type="button" class="btn-close ModalClosed" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form autocomplete="off" method="post" enctype="multipart/form-data" id="FeedEntryForm">
                    @csrf
                    <div class="row form-group">
                        <input type="hidden" name="feed_id_modal" id="feedIdModal">
                        <div class="col-sm-6 mb-2">
                            <label for="feed_name"> Feed Name * </label>
                            <input type="text" placeholder="Enter feed name" name="feed_name" class="form-control"
                                id="feedName">
                            <span class="text-danger feed_name_error"></span>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="pdate"> Purchase Date *</label>
                            <input type="date" placeholder="Enter date" name="purchase_date" class="form-control"
                                id="feedPurchaseDate">
                            <span class="text-danger purchase_date_error"></span>
                        </div>

                        <div class="col-6 mb-2">
                            <label for="feedCompanyId">Select Company *</label>
                            <select class="form-control mySelectModal" id="feedCompanyId" name="company_id"
                                data-placeholder="Select Company" data-toggle="select2" data-width="100%">
                                <option value="0"> Select Company </option>
                            </select>
                            <span class="text-danger company_id_error"></span>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="feedCompanyAddr"> Company Address </label>
                            <input type="text" placeholder="Company Address" name="company_address" readonly disabled
                                class="form-control" id="feedCompanyAddr">
                            <span class="text-danger company_address_error"></span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label for="feedCompanyContact"> Company Contact </label>
                            <input type="text" placeholder="Enter Contact" name="company_contact" class="form-control"
                                readonly disabled id="feedCompanyContact">
                            <span class="text-danger company_contact_error"></span>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="feedQuantity"> Quantity </label>
                            <input type="text" placeholder="Enter quantity" name="quantity" class="form-control"
                                id="feedQuantity" required>
                            <span class="text-danger quantity_error"></span>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="feedPrice"> Price </label>
                            <input type="text" placeholder="Enter price" name="price" class="form-control"
                                id="feedPrice" required>
                            <span class="text-danger price_error"></span>
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="feedDiscountAmount"> Discount Amount </label>
                            <input type="text" placeholder="Discount Amount" name="discount_amount" class="form-control"
                                id="feedDiscountAmount">
                            <span class="text-danger discount_amount_error"></span>
                        </div>
                        <span class="text-danger h6" id="PriceQtyError" style="display: none;"></span>
                        <div class="col-sm-6 mb-2">
                            <label for="feedDiscountPercentage"> Discount Percentage % </label>
                            <input type="text" placeholder="Discount Percentage %" name="discount_percentage"
                                class="form-control" id="feedDiscountPercentage">
                            <span class="text-danger discount_percentage_error"></span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label for="feedTotalPrice"> Total Price </label>
                            <input type="text" placeholder="Total price" name="total_price" class="form-control"
                                id="feedTotalPrice">
                            <span class="text-danger total_price_error"></span>
                        </div>
                        <div class="col-sm-6 mt-2">
                            <label for="image">Image</label>
                            <input type="file" class="form-control" name="image_file"
                                accept="image/png,image/jpg,image/jpeg">
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
@section('modal_scripts')

<script>
    $(function() {

        var companies_list = {};
        
        $('#AddFeedModal').modal({backdrop: 'static', keyboard: false})

        $(document).on('click', '.openFeedModal', function(e){
            $("#FeedEntryForm").trigger("reset");
            let feed_id = parseInt($(this).attr('FeedId')) || 0;
            // alert(feed_id);
            getCompanyList();
            $('#AddFeedModal').modal('show');
            $('#feedIdModal').val(feed_id);
            if(feed_id > 0){
                $('.AddUpdate').html('Update');
                $.get("{{ url('/inventory/feed')}}/"+feed_id+"/edit" , function(cdata, status){
                    console.log("hello "+ cdata?.feed?.company_id)
                    $('#feedIdModal').val(cdata?.feed?.id)
                    $('#feedName').val(cdata?.feed?.feed_name)
                    $('#feedPurchaseDate').val(cdata?.feed?.purchase_date)
                    $('#feedCompanyId').val(cdata?.feed?.company_id).trigger('change')

                    $('#feedCompanyAddr').val(cdata?.feed?.company?.address)
                    $('#feedCompanyContact').val(cdata?.feed?.company?.contact_no)

                    $('#feedQuantity').val(cdata?.feed?.quantity)
                    $('#feedPrice').val(cdata?.feed?.price)
                    $('#feedDiscountAmount').val(cdata?.feed?.discount_amount)
                    $('#feedDiscountPercentage').val(cdata?.feed?.discount_percentage)
                    $('#feedTotalPrice').val(cdata?.feed?.total_price)
                    var img_holder = $('.img-holder');
                    if(cdata?.feed?.picture != null){
                        let img_url = "{{ asset('storage/feeds/')}}"
                        $(".img-holder").empty();
                        img_url = img_url+'/'+cdata?.feed?.picture;
                        $('<img />', {'src':img_url,'class':'','style':'max-width:20%;      margin-bottom:1px;'}).appendTo(img_holder);
                    }else{
                        $(img_holder).empty();
                        $(".img-holder").empty();
                    }
                });
            }else{
                $('.AddUpdate').html('Add');
            }
        });

        $('#FeedEntryForm').on('submit', function(e) {
            e.preventDefault();
            let EfeedId = parseInt($('#feedIdModal').val());
            let form_type = 'POST'
            let form_url = "{{ url('/inventory/feed')}}"
            $.ajax({
                type: form_type,
                url: form_url,
                data: new FormData(this),
                dataType:'JSON',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend : function(msg) {
                    $('#FeedEntryForm').find('div.invalid-feedback').text('')
                },
                success: function(msg) {
                    if(msg?.success == 'no'){
                        $.each(msg?.error, function(prefix, val){
                        $('#FeedEntryForm').find('span.'+prefix+'_error').text(val[0]);
                        });
                    }else{
                        $("#FeedEntryForm").trigger("reset");
                        $('#AddFeedModal').modal('hide');
                        $('#Feed-datatable').DataTable().ajax.reload(null, false);
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

        function getCompanyList(){
            var html_code = '';
            $.get("{{ route('getCompaniesList') }}", function(data, status){
                let companies = data?.companies
                companies_list = companies
                if(companies?.length > 0){
                    html_code='<option value="" Selected disabled> Select Company </option>';
                    for (var i = 0; i < companies?.length; i++) {
                        html_code+='<option value='+companies[i].id+'>'+companies[i].name+'</option>'; 
                    }
                }
                $('#feedCompanyId').html(html_code);
                // $('.kt-selectpicker').selectpicker("refresh");
            });
        }

        $( "#feedCompanyId" ).change(function() {
            let company_id_modal = parseInt($(this).val())
            let find_company = companies_list?.find(x => x.id === company_id_modal);
            if(find_company){
                $('#feedCompanyAddr').val(find_company?.address || '');
                $('#feedCompanyContact').val(find_company?.contact_no || '');
            }
        });

        $('#feedPrice').on('keyup', function() {
            let total_qty = Number($('#feedQuantity').val())
            let feed_price = parseFloat($('#feedPrice').val())

            if (total_qty > 0 && feed_price > 0) {
                $("#PriceQtyError").html('');
                $("#PriceQtyError").hide();
                priceWithOutDiscount = total_qty*feed_price;
                $("#feedTotalPrice").val(priceWithOutDiscount);
            } else {
                $("#PriceQtyError").show();
                $("#PriceQtyError").html('Price and Quantity Is required');
                $("#feedTotalPrice").val('');
            }
        });

        // $('#feedDiscountAmount').on('keyup', function() {
        $('#feedDiscountAmount').on('keyup', function() {
            let total_qty = Number($('#feedQuantity').val())
            let feed_price = parseFloat($('#feedPrice').val())
            let discountAmount = parseFloat($(this).val());
            let priceWithOutDiscount = 0
            if(discountAmount > 0){
                if (total_qty > 0 && feed_price > 0) {
                    $("#PriceQtyError").html('');
                    $("#PriceQtyError").hide();
                    priceWithOutDiscount = total_qty*feed_price;
                    let discount_percent = (discountAmount / priceWithOutDiscount) * 100;
                    if(discountAmount >= priceWithOutDiscount){
                        $(".discount_amount_error").html('Discount Amount Must be less than total amount');
                    }else{
                        $(".discount_amount_error").html('');
                        $("#feedDiscountPercentage").val(discount_percent.toFixed(2));
                        $("#feedTotalPrice").val(priceWithOutDiscount - discountAmount);
                    }
                } else {
                    $("#PriceQtyError").show();
                    $("#PriceQtyError").html('Price and Quantity Is required');
                }
            }else{
                $("#feedDiscountPercentage").val('');
                $("#feedTotalPrice").val(total_qty*feed_price);
            }
        });
        
        $('#feedDiscountPercentage').on('keyup', function() {
            let total_qty = Number($('#feedQuantity').val())
            let feed_price = parseFloat($('#feedPrice').val())
            let discountPercentage = parseFloat($(this).val()) || 0;
            let priceWithOutDiscount = 0
            $(".discount_percentage_error").html('');
            if(discountPercentage > 0 && discountPercentage < 100){
                if (total_qty > 0 && feed_price > 0) {
                    $("#PriceQtyError").html('');
                    $("#PriceQtyError").hide();
                    priceWithOutDiscount = total_qty*feed_price;
                    let discount_amt = (discountPercentage /100 ) * priceWithOutDiscount;
                    $("#feedDiscountAmount").val(discount_amt.toFixed(2));
                    $("#feedTotalPrice").val(priceWithOutDiscount - discount_amt);
                } else {
                    $("#PriceQtyError").show();
                    $("#PriceQtyError").html('Price and Quantity Is required');
                }
            }else{
                $("#feedDiscountAmount").val('');
                $("#feedTotalPrice").val(total_qty*feed_price);
                $(".discount_percentage_error").html('Discount Percentage Must be less than 100 (Total Amount)');
            }
        });
    });
</script>
@endsection