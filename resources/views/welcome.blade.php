@php
$load_css = Array('select2');
$load_js = Array('dashboard','select2');
@endphp
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    {{-- <form class="d-flex align-items-center mb-3">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control border" id="dash-daterange">
                            <span class="input-group-text bg-blue border-blue text-white">
                                <i class="mdi mdi-calendar-range"></i>
                            </span>
                        </div>
                        <a href="javascript: void(0);" class="btn btn-blue btn-sm ms-2">
                            <i class="mdi mdi-autorenew"></i>
                        </a>
                        <a href="javascript: void(0);" class="btn btn-blue btn-sm ms-1">
                            <i class="mdi mdi-filter-variant"></i>
                        </a>
                    </form> --}}

                    <a href="javascript: void(0);" class="btn btn-success btn-sm ms-2" id="openCashInModal">
                        <i class="fas fa-arrow-down"></i> Cash in
                    </a>

                    {{-- <a href="javascript: void(0);" class="btn btn-danger btn-sm ms-2" id="openCashOutModal">
                        <i class="fas fa-arrow-up"></i> Cash Out
                    </a> --}}
                </div>
                <h4 class="page-title">Home</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-lg rounded-circle bg-soft-primary border-primary border">
                                <i class="fe-dollar-sign font-22 avatar-title text-primary"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <h3 class="text-dark mt-1">$<span data-plugin="counterup">58,947</span></h3>
                                <p class="text-muted mb-1 text-truncate">Total Revenue</p>
                            </div>
                        </div>
                    </div> <!-- end row-->
                </div>
            </div> <!-- end widget-rounded-circle-->
        </div> <!-- end col-->

        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-lg rounded-circle bg-soft-success border-success border">
                                <i class="fe-shopping-cart font-22 avatar-title text-success"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <h3 class="text-dark mt-1"><span data-plugin="counterup">127</span></h3>
                                <p class="text-muted mb-1 text-truncate">Today's Sales</p>
                            </div>
                        </div>
                    </div> <!-- end row-->
                </div>
            </div> <!-- end widget-rounded-circle-->
        </div> <!-- end col-->

        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-lg rounded-circle bg-soft-info border-info border">
                                <i class="fe-bar-chart-line- font-22 avatar-title text-info"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <h3 class="text-dark mt-1"><span data-plugin="counterup">58</span>$</h3>
                                <p class="text-muted mb-1 text-truncate">Expense</p>
                            </div>
                        </div>
                    </div> <!-- end row-->
                </div>
            </div> <!-- end widget-rounded-circle-->
        </div> <!-- end col-->

        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-lg rounded-circle bg-soft-warning border-warning border">
                                <i class="fe-eye font-22 avatar-title text-warning"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <h3 class="text-dark mt-1"><span data-plugin="counterup">78.41</span>k</h3>
                                <p class="text-muted mb-1 text-truncate">Today's Visits</p>
                            </div>
                        </div>
                    </div> <!-- end row-->
                </div>
            </div> <!-- end widget-rounded-circle-->
        </div> <!-- end col-->
    </div>
    <!-- end row-->

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <div class="dropdown float-end">
                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Edit Report</a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Export Report</a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Action</a>
                        </div>
                    </div>

                    <h4 class="header-title mb-3">Top 5 Users Balances</h4>

                    <div class="table-responsive">
                        <table class="table table-borderless table-hover table-nowrap table-centered m-0">

                            <thead class="table-light">
                                <tr>
                                    <th colspan="2">Profile</th>
                                    <th>Currency</th>
                                    <th>Balance</th>
                                    <th>Reserved in orders</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 36px;">
                                        <img src="../assets/images/users/user-2.jpg" alt="contact-img"
                                            title="contact-img" class="rounded-circle avatar-sm" />
                                    </td>

                                    <td>
                                        <h5 class="m-0 fw-normal">Tomaslau</h5>
                                        <p class="mb-0 text-muted"><small>Member Since 2017</small></p>
                                    </td>

                                    <td>
                                        <i class="mdi mdi-currency-btc text-primary"></i> BTC
                                    </td>

                                    <td>
                                        0.00816117 BTC
                                    </td>

                                    <td>
                                        0.00097036 BTC
                                    </td>

                                    <td>
                                        <a href="javascript: void(0);" class="btn btn-xs btn-light"><i
                                                class="mdi mdi-plus"></i></a>
                                        <a href="javascript: void(0);" class="btn btn-xs btn-secondary"><i
                                                class="mdi mdi-minus"></i></a>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="width: 36px;">
                                        <img src="../assets/images/users/user-3.jpg" alt="contact-img"
                                            title="contact-img" class="rounded-circle avatar-sm" />
                                    </td>

                                    <td>
                                        <h5 class="m-0 fw-normal">Erwin E. Brown</h5>
                                        <p class="mb-0 text-muted"><small>Member Since 2017</small></p>
                                    </td>

                                    <td>
                                        <i class="mdi mdi-currency-eth text-primary"></i> ETH
                                    </td>

                                    <td>
                                        3.16117008 ETH
                                    </td>

                                    <td>
                                        1.70360009 ETH
                                    </td>

                                    <td>
                                        <a href="javascript: void(0);" class="btn btn-xs btn-light"><i
                                                class="mdi mdi-plus"></i></a>
                                        <a href="javascript: void(0);" class="btn btn-xs btn-secondary"><i
                                                class="mdi mdi-minus"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 36px;">
                                        <img src="../assets/images/users/user-4.jpg" alt="contact-img"
                                            title="contact-img" class="rounded-circle avatar-sm" />
                                    </td>

                                    <td>
                                        <h5 class="m-0 fw-normal">Margeret V. Ligon</h5>
                                        <p class="mb-0 text-muted"><small>Member Since 2017</small></p>
                                    </td>

                                    <td>
                                        <i class="mdi mdi-currency-eur text-primary"></i> EUR
                                    </td>

                                    <td>
                                        25.08 EUR
                                    </td>

                                    <td>
                                        12.58 EUR
                                    </td>

                                    <td>
                                        <a href="javascript: void(0);" class="btn btn-xs btn-light"><i
                                                class="mdi mdi-plus"></i></a>
                                        <a href="javascript: void(0);" class="btn btn-xs btn-secondary"><i
                                                class="mdi mdi-minus"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 36px;">
                                        <img src="../assets/images/users/user-5.jpg" alt="contact-img"
                                            title="contact-img" class="rounded-circle avatar-sm" />
                                    </td>

                                    <td>
                                        <h5 class="m-0 fw-normal">Jose D. Delacruz</h5>
                                        <p class="mb-0 text-muted"><small>Member Since 2017</small></p>
                                    </td>

                                    <td>
                                        <i class="mdi mdi-currency-cny text-primary"></i> CNY
                                    </td>

                                    <td>
                                        82.00 CNY
                                    </td>

                                    <td>
                                        30.83 CNY
                                    </td>

                                    <td>
                                        <a href="javascript: void(0);" class="btn btn-xs btn-light"><i
                                                class="mdi mdi-plus"></i></a>
                                        <a href="javascript: void(0);" class="btn btn-xs btn-secondary"><i
                                                class="mdi mdi-minus"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 36px;">
                                        <img src="../assets/images/users/user-6.jpg" alt="contact-img"
                                            title="contact-img" class="rounded-circle avatar-sm" />
                                    </td>

                                    <td>
                                        <h5 class="m-0 fw-normal">Luke J. Sain</h5>
                                        <p class="mb-0 text-muted"><small>Member Since 2017</small></p>
                                    </td>

                                    <td>
                                        <i class="mdi mdi-currency-btc text-primary"></i> BTC
                                    </td>

                                    <td>
                                        2.00816117 BTC
                                    </td>

                                    <td>
                                        1.00097036 BTC
                                    </td>

                                    <td>
                                        <a href="javascript: void(0);" class="btn btn-xs btn-light"><i
                                                class="mdi mdi-plus"></i></a>
                                        <a href="javascript: void(0);" class="btn btn-xs btn-secondary"><i
                                                class="mdi mdi-minus"></i></a>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->

        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <div class="dropdown float-end">
                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Edit Report</a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Export Report</a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Action</a>
                        </div>
                    </div>

                    <h4 class="header-title mb-3">Revenue History</h4>

                    <div class="table-responsive">
                        <table class="table table-borderless table-nowrap table-hover table-centered m-0">

                            <thead class="table-light">
                                <tr>
                                    <th>Marketplaces</th>
                                    <th>Date</th>
                                    <th>Payouts</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <h5 class="m-0 fw-normal">Themes Market</h5>
                                    </td>

                                    <td>
                                        Oct 15, 2018
                                    </td>

                                    <td>
                                        $5848.68
                                    </td>

                                    <td>
                                        <span class="badge bg-soft-warning text-warning">Upcoming</span>
                                    </td>

                                    <td>
                                        <a href="javascript: void(0);" class="btn btn-xs btn-light"><i
                                                class="mdi mdi-pencil"></i></a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <h5 class="m-0 fw-normal">Freelance</h5>
                                    </td>

                                    <td>
                                        Oct 12, 2018
                                    </td>

                                    <td>
                                        $1247.25
                                    </td>

                                    <td>
                                        <span class="badge bg-soft-success text-success">Paid</span>
                                    </td>

                                    <td>
                                        <a href="javascript: void(0);" class="btn btn-xs btn-light"><i
                                                class="mdi mdi-pencil"></i></a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <h5 class="m-0 fw-normal">Share Holding</h5>
                                    </td>

                                    <td>
                                        Oct 10, 2018
                                    </td>

                                    <td>
                                        $815.89
                                    </td>

                                    <td>
                                        <span class="badge bg-soft-success text-success">Paid</span>
                                    </td>

                                    <td>
                                        <a href="javascript: void(0);" class="btn btn-xs btn-light"><i
                                                class="mdi mdi-pencil"></i></a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <h5 class="m-0 fw-normal">Envato's Affiliates</h5>
                                    </td>

                                    <td>
                                        Oct 03, 2018
                                    </td>

                                    <td>
                                        $248.75
                                    </td>

                                    <td>
                                        <span class="badge bg-soft-danger text-danger">Overdue</span>
                                    </td>

                                    <td>
                                        <a href="javascript: void(0);" class="btn btn-xs btn-light"><i
                                                class="mdi mdi-pencil"></i></a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <h5 class="m-0 fw-normal">Marketing Revenue</h5>
                                    </td>

                                    <td>
                                        Sep 21, 2018
                                    </td>

                                    <td>
                                        $978.21
                                    </td>

                                    <td>
                                        <span class="badge bg-soft-warning text-warning">Upcoming</span>
                                    </td>

                                    <td>
                                        <a href="javascript: void(0);" class="btn btn-xs btn-light"><i
                                                class="mdi mdi-pencil"></i></a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <h5 class="m-0 fw-normal">Advertise Revenue</h5>
                                    </td>

                                    <td>
                                        Sep 15, 2018
                                    </td>

                                    <td>
                                        $358.10
                                    </td>

                                    <td>
                                        <span class="badge bg-soft-success text-success">Paid</span>
                                    </td>

                                    <td>
                                        <a href="javascript: void(0);" class="btn btn-xs btn-light"><i
                                                class="mdi mdi-pencil"></i></a>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div> <!-- end .table-responsive-->
                </div>
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div>
    <!-- end row -->

</div> <!-- container -->


<div id="CashInModal" class="modal fade MyModalClass" tabindex="-1" role="dialog" aria-labelledby="CashInModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h4 class="modal-title" id="standard-modalLabel"> <span class="AddUpdate"> Receive </span> Payment From
                    Party:
                </h4>
                <button type="button" class="btn-close ModalClosed" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form autocomplete="off" method="post" id="AddVaccinationForm" class="form_loader">
                    @csrf
                    <div class="row form-group">
                        <input type="hidden" name="schedule_id" id="ScheduleId">
                        <div class="col-sm-12 mb-2 ps-1">
                            <label for="BalanceSelect"> Select Balance *</label>
                            <select name="party_balance_id" id="BalanceSelect" class="form-control mySelectModal"
                                required data-toggle="select2" data-width="100%">
                                <option value="">dasd</option>
                            </select>
                            <span class="text-danger party_balance_id_error"> </span>
                        </div>

                        <div class="col-sm-4 mb-2 pe-0">
                            <label for="TotalAmount" class="fw-bold fs-6">Total Amount</label>
                            <input type="number" step="any" min="0" placeholder="Total Amount" disabled
                                class="form-control py-1 fw-bold fs-6" id="TotalAmount">
                        </div>
                        <div class="col-sm-4 mb-2 px-1">
                            <label for="PaidAmount" class="text-success fw-bold fs-6">Paid Amount</label>
                            <input type="number" step="any" min="0" placeholder="Paid Amount" disabled
                                class="form-control text-success py-1 fw-bold  fs-6" id="PaidAmount">
                        </div>
                        <div class="col-sm-4 mb-2 ps-0">
                            <label for="RemainingAmount" class="text-danger fw-bold fs-6">Remaining Amount</label>
                            <input type="number" step="any" min="0" placeholder="Remaining Amount" disabled
                                class="form-control text-danger py-1 fw-bold fs-6" id="RemainingAmount">
                        </div>

                        <div class="col-sm-4 mb-2 pe-0">
                            <label for="AddAmount">Add Amount</label>
                            <input type="number" step="any" min="0" placeholder="Add Amount" class="form-control"
                                id="AddAmount">
                        </div>

                        <div class="col-sm-8 mb-2 ps-1">
                            <label for="PaymentOption"> Payment Option *</label>
                            <select name="payment_option" id="PaymentOption" class="form-control mySelectModal" required
                                data-toggle="select2" data-width="100%">
                                <option value="" selected disabled>Select Payment option </option>
                                <option value="1">Cheque</option>
                                <option value="2">Cash</option>
                                <option value="3">Other</option>
                            </select>
                            <span class="text-danger payment_option_error"> </span>
                        </div>

                        <div class="col-sm-4 mb-2 pe-0">
                            <label for="cheque_date" class="fs-6">Cheque Date</label>
                            <input type="date" name="cheque_date" class="form-control ps-1 py-1 fs-6"
                                placeholder="Enter amount" id="cheque_date">
                            <span class="text-danger cheque_date_error"> </span>
                        </div>

                        <div class="col-sm-4 mb-2 pe-0 ps-1">
                            <label for="cno" class="fs-6">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control py-1 fs-6"
                                placeholder="Enter bank name" id="BankName">
                            <span class="text-danger bank_name_error"> </span>
                        </div>
                        <div class="col-sm-4 mb-2 ps-1">
                            <label for="cheque_picture" class="fs-6">Cheque Picture</label>
                            <input type="file" name="cheque_picture" class="form-control ps-0 py-1 fs-6"
                                placeholder="Enter amount" id="ChequePicture">
                            <span class="text-danger cheque_picture_error"> </span>
                        </div>

                        <div class="col-sm-6 mb-2 pe-1">
                            <label for="ReffNumber">Reference Number *</label>
                            <input type="text" placeholder="Reference Number" name="reference_number" required
                                class="form-control" id="ReffNumber">
                            <span class="text-danger reference_number_error">
                            </span>
                        </div>

                        <div class="col-sm-6 mb-2 ps-1">
                            <label for="PaymentDate">Payment Date *</label>
                            <input type="date" placeholder="Payment date" name="vaccination_date"
                                value="{{today()->format('Y-m-d')}}" required class="form-control" id="PaymentDate">
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
@endsection

@section('custom_scripts')

<script>
    $(function() {
        $('#CashInModal').modal({backdrop: 'static', keyboard: false})
        $('.ModalClosed').click(function () {
            $('.modal').modal('hide'); 
            $(this).find('form').trigger('reset');
        });

        $('#openCashInModal').click(function () {
            $('#CashInModal').modal('show')
            var balance_list={}
            let uri = "{{ route('getPartyBalances')}}"
            $.get(uri, function(res, status){
                var html_code = '';
                console.log(res)
                if(res.message == 'yes'){
                    balance_list = res.balances
                    html_code ='<option value="">Select Party</option>'; 
                    for (var i = 0; i < res.balances.length; i++) {
                        html_code+='<option value='+res.balances[i]?.party?.id+'>'+res.balances[i]?.party.name+'</option>'; 
                    }
                }else{
                    html_code+='<option value="">No Data Found</option>'; 
                }
                $('#BalanceSelect').html(html_code);
            });

            $('#BalanceSelect').change(function () {
                let b_id = parseInt($(this).val())
                console.log(balance_list)
                // alert(b_id)
                let item = balance_list?.find(x => x.id == b_id)
                if(item !=''){
                    $('#TotalAmount').val(item?.total_amount || 0)
                    $('#PaidAmount').val(item?.paid_amount || 0)
                    $('#RemainingAmount').val(item?.remaining_amount || 0)
                    $('#AddAmount').attr('max', item?.remaining_amount || 0)
                    $('#AddAmount').val(item?.remaining_amount || 0)
                }else{
                    $('#AddAmount').attr('max', false)
                }
            });
        });

        $('#openCashOutModal').click(function () {
            alert('Cash Out')
        });
    });
</script>
@endsection