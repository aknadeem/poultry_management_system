<div id="RebateModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="RebateModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h4 class="modal-title" id="standard-modalLabel"> Claim Rebate
                </h4>
                <button type="button" class="btn-close ModalClosed" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form autocomplete="off" method="post" id="ProductStoreForm" class="form_loader"
                    action="{{ route('productRebate') }}">
                    @csrf
                    <div class="row form-group">
                        <input type="hidden" name="product_detail_id" id="ProductDetailId">
                        <input type="hidden" id="FromPage" name=from_page>

                        <div class="col-sm-6 mb-1 pe-0">
                            <label for="ProductCode"> Product Code: </label>
                            <input type="text" readonly disabled class="form-control" id="ProductCode">
                        </div>

                        <div class="col-sm-6 mb-1">
                            <label for="ProductName"> Product Name: </label>
                            <input type="text" readonly disabled class="form-control" id="ProductName">
                        </div>

                        <div class="col-sm-4 mb-1 pe-0">
                            <label for="ProductPrice"> Product Price: </label>
                            <input type="text" readonly disabled class="form-control" id="ProductPrice">
                        </div>

                        <div class="col-sm-2 mb-1 pe-0">
                            <label for="ProductTotalQty"> Qty: </label>
                            <input type="text" readonly disabled class="form-control" id="ProductTotalQty">
                        </div>

                        <div class="col-sm-6">
                            <label for="ProductTotalPrice"> Total Price: </label>
                            <input type="text" readonly disabled class="form-control" id="ProductTotalPrice">
                        </div>
                        <div class="col-12 m-0">
                            <hr style="height:2px;margin: 10px 0px;">
                        </div>
                        <div class="col-sm-8 mb-2 pe-0">
                            <label for="RebateReason"> Rebate Reason </label>
                            <input type="text" required class="form-control" id="RebateReason" name="rebate_reason"
                                placeholder="Enter rebate reason">
                        </div>

                        <div class="col-sm-4 mb-2">
                            <label for="RebateQty"> Rebate Qty </label>
                            <input type="number" min="0" required class="form-control" id="RebateQty" name="rebate_qty"
                                placeholder="Enter Qty">
                        </div>

                        <div class="col-sm-12 mb-2 pe-0">
                            <label for="rebateDescription"> Rebate Description* </label>
                            <textarea name="rebate_description" class="form-control" id="rebateDescription" rows="5"
                                required></textarea>
                        </div>

                    </div>
                    <div class="row form-group">
                        <div class="col-sm-4 mb-3">
                            <button type="submit" id="sub"
                                class="btn btn-secondary btn-sm waves-effect waves-light mt-3 AddUpdate">
                                Rebate
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

        $('#AddProductSaleModal').modal({backdrop: 'static', keyboard: false})
        $(".openRebateModal").click(function(){
            let ProductDetailId = parseInt($(this).attr('ProductDetailId')) ?? 0
            let type = $(this).attr('FromPage') ?? null
            $('#RebateModal').modal('toggle');

            if(ProductDetailId > 0){
                $("#ProductDetailId").val(ProductDetailId)
                $("#FromPage").val(type)
                let get_url = "{{ url('/ProductManagement/product-detail-item')}}/"+ProductDetailId;
                let url_addr = get_url+'/type/'+type;
                
                $.get(url_addr, function(response, status){
                    if(response?.success == 'yes'){
                        var data = response?.data
                        $("#ProductCode").val(data?.product_code)
                        $("#ProductName").val(data?.product_name)
                        $("#ProductPrice").val(data?.product_sale_price || data?.product_purchase_price)
                        $("#ProductTotalQty").val(data?.product_total_qty)
                        $("#RebateQty").attr({
                            "max" : data?.product_total_qty,
                        });
                        $("#ProductTotalPrice").val(data?.product_total_price)
                    }else{
                        $("#ProductCode").val('')
                        $("#ProductName").val('')
                        $("#ProductPrice").val('')
                        $("#ProductTotalQty").val('data?.product_total_qty')
                        $("#RebateQty").removeAttr('max');
                        $("#ProductTotalPrice").val()
                    }
                });
            }
        });

    });
</script>
@endsection