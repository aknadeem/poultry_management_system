<div id="AddProductSaleModal" class="modal fade MyModalClass" tabindex="-1" role="dialog"
    aria-labelledby="AddProductSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h4 class="modal-title" id="standard-modalLabel"> <span class="AddUpdate"> Add </span> Items
                </h4>
                <button type="button" class="btn-close ModalClosed" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form autocomplete="off" method="post" id="SaleProductItemForm" class="form_loader"
                    action="javascript: void(0)">
                    @csrf
                    <div class="row form-group">

                        <input type="hidden" name="company_id_modal" id="CompanyIdModal">
                        <input type="hidden" name="category_id_modal" id="CategoryIdModal">
                        {{-- <input type="text" name="company_id_modal" id="CompanyIdModal">
                        <input type="text" name="company_id_modal" id="CompanyIdModal"> --}}
                        <div class="col-6 mb-2 px-1">
                            <label class="font_bold" for="SelectProduct"> Select Product * </label>
                            <a href="javascript:void(0);" class="ViewProductDetail" id="ViewProductDetail-0"
                                style="float: right !important; display: none;"> View Product
                                Detail </a>
                            <select name="product_id" required id="SelectProduct" class="form-control mySelectModal"
                                data-toggle="select2" data-width="100%">
                                <option value=""> Select product</option>
                            </select>
                            @error('product_id')
                            <span class="text-danger product_id_error"> {{ $message }} </span>
                            @enderror
                        </div>
                        <input type="hidden" name="product_code_modal" id="ProductCodeModal">
                        <input type="hidden" name="product_name_modal" id="ProductNameModal">

                        <div class="col-4 mb-1 px-1">
                            <label class="font_bold" for="SalePrice"> Sale Price </label>
                            <input type="number" min="0" step="any" class="form-control" name="product_price"
                                placeholder="Product Price" id="SalePrice">
                            <span class="text-danger product_price_error"></span>
                        </div>
                        <div class="col-2 mb-2 px-1">
                            <label class="font_bold" for="ProductQuantity"> Quantity </label>
                            <input type="number" min="0" class="form-control" name="quantity" placeholder="Quantity"
                                required id="ProductQuantity">
                            <span class="text-danger quantity_error"></span>
                        </div>

                        <div class="col-2 mb-2 px-1">
                            <label class="font_bold" for="ProductBonusQuantity"> Bonus Quantity </label>
                            <input type="number" min="0" class="form-control" name="bonus_quantity"
                                placeholder="Bonus Qty" required id="ProductBonusQuantity">
                            <span class="text-danger quantity_error"></span>
                        </div>

                        <div class="col-2 mb-2 px-1">
                            <label class="font_bold" for="totalQuantity"> Total Quantity </label>
                            <input type="number" min="0" class="form-control" name="TotalQuantiy"
                                placeholder="total Qty" required id="totalQuantity">
                            <span class="text-danger total_quantity_error"></span>
                        </div>

                        <div class="col-3 mb-2 px-1">
                            <label class="font_bold" for="TotalPrice"> Total Price</label>
                            <input type="number" step="any" min="0" class="form-control" name="total_price"
                                placeholder="Total Price" readonly id="TotalPrice">

                            @error('total_price')
                            <span class="text-danger total_price_error"> {{ $message }} </span>
                            @enderror

                            <span class="text-danger h5" id="QtyPriceError"> </span>
                        </div>

                        <div class="col-3 mb-2 px-1">
                            <label class="font_bold" for="DiscountAmount"> Discount Amount</label>
                            <input type="number" step="any" min="0" step="any" class="form-control"
                                name="discount_amount" value="" placeholder="Discount Amount" id="DiscountAmount">

                            @error('discount_amount')
                            <span class="text-danger discount_amount_error"> {{ $message }} </span>
                            @enderror
                        </div>

                        <div class="col-2 mb-2 px-1">
                            <label class="font_bold" for="DiscountPercentage"> Discount % </label>
                            <input type="number" step="any" min="0" step="any" class="form-control"
                                name="discount_percentage" value="" placeholder="Discount %" id="DiscountPercentage">

                            @error('discount_percentage')
                            <span class="text-danger discount_percentage_error"> {{ $message }} </span>
                            @enderror
                        </div>


                        <div class="col-3 mb-2 px-1">
                            <label class="font_bold" for="FinalPrice"> Final Price </label>
                            <input type="number" min="0" step="any" class="form-control" name="final_price" readonly
                                placeholder="Final price" id="FinalPrice">

                            @error('final_price')
                            <span class="text-danger final_price_error"> {{ $message }} </span>
                            @enderror
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-4 mb-3">
                            <button type="submit" id="" class="btn btn-secondary btn-sm waves-effect waves-light mt-3">
                                Add
                            </button>
                            <button class="btn btn-light btn-sm waves-effect waves-light mt-3 ModalClosed">
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<div id="ProductDetail" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ProductDetailLabel"
    aria-hidden="true">
    <div class="modal-dialog align-center">
        <div class="modal-content" id="ProductDetailModalBody">
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
@section('modal_scripts')
<script>
    $(function() {

        $('#AddProductSaleModal').modal({backdrop: 'static', keyboard: false})

        // $(".mySelectModal").select2({
        //     dropdownParent: $("#AddProductSaleModal")
        // });

        $(".ViewProductDetail").click(function(){
            let product_id = parseInt($(this).attr('ProductId'))
            $("#ProductDetail").modal('show')
            let url_addr = "{{ url('ProductManagement/products')}}/"+product_id;
            $.get(url_addr , function(response, status){
                var single_product = response.html_data;
                if (single_product != '')
                {
                    $("#ProductDetailModalBody").html(single_product)
                }else{
                    $("#ProductDetailModalBody").html("No Data Found")
                }
            });
        });

    });
</script>
@endsection