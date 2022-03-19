<form autocomplete="off" method="post" enctype="multipart/form-data" id="AddPaymentForm" class="form_loader">
    @csrf
    <div class="row form-group">
        <input type="hidden" name="company_balance_id" id="CompanyBalanceId">

        <input type="hidden" name="party_company_id" id="CompanyIdModal">

        <div class="col-sm-4 mb-2">
            <label for="cno">Total Amount: <span class="fw-bold fs-4" id="TotalPaymentLabel"></span>
            </label>
        </div>
        <div class="col-sm-4 mb-2">
            <label for="cno">Paid Amount: <span class="fw-bold fs-4" id="PaidAmountLabel" class="text-success"></span>
            </label>
        </div>
        <div class="col-sm-4 mb-2">
            <label for="cno">Remaining Amount: <span class="fw-bold fs-4 text-danger" id="RemAmountLabel"
                    class="text-danger"> 2500
                </span></label>
        </div>

        <div class="col-sm-6 mb-2">
            <label for="AddAmount">Amount</label>
            <input type="number" name="amount_payment" class="form-control" placeholder="Enter amount" id="AddAmount"
                min="0" required>
            <span class="text-danger amount_payment_error"> </span>
        </div>

        <div class="col-sm-6 mb-2">
            <label for="payment_option">Payment Option</label>
            <select name="payment_option" id="payment_option" class="form-control mySelectModal" data-toggle="select2"
                required data-width="100%">
                <option value="cheque">Cheque</option>
                <option value="cash">Cash</option>
                <option value="other">Other</option>
            </select>
            <span class="text-danger farm_name_error"> </span>
        </div>

        <div class="col-sm-4 mb-2">
            <label for="cheque_date">Cheque Date</label>
            <input type="date" name="cheque_date" class="form-control" placeholder="Enter amount" id="cheque_date">
            <span class="text-danger cheque_date_error"> </span>
        </div>

        <div class="col-sm-4 mb-2">
            <label for="cno">Cheque Bank</label>
            <input type="text" name="bank_name" class="form-control" placeholder="Enter amount" id="BankName">
            <span class="text-danger bank_name_error"> </span>
        </div>
        <div class="col-sm-4 mb-2">
            <label for="cheque_picture">Cheque Picture</label>
            <input type="file" name="cheque_picture" class="form-control" placeholder="Enter amount" id="ChequePicture">
            <span class="text-danger cheque_picture_error"> </span>
        </div>

        <div class="col-sm-6 mb-2">
            <label for="description"> Description </label>
            <input type="text" name="description" class="form-control" placeholder="Enter amount" id="description">
            <span class="text-danger description_error"> </span>
        </div>

        <div class="col-sm-6 mb-2">
            <label for="image_file"> Picture </label>
            <input type="file" name="image_file" class="form-control" placeholder="Enter amount" id="image_file">
            <span class="text-danger image_file_error"> </span>
        </div>

    </div>
    <div class="row form-group">
        <div class="col-sm-4 mb-3">
            <button type="submit" id="sub" class="btn btn-secondary btn-sm waves-effect waves-light mt-3 AddUpdate">
                Submit
            </button>
            <button class="btn btn-light btn-sm waves-effect waves-light mt-3 ModalClosed"> Cancel
            </button>
        </div>
    </div>
</form>