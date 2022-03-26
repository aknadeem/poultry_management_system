@php
$load_css = Array('tables','sweetAlert', 'jquery-confirm','select2');
$load_js = Array('tables','tippy','sweetAlert', 'jquery-confirm','select2','select2model')
;
@endphp

@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item active">ExpenseManagement</li>
                    </ol>
                </div>
                <h4 class="page-title">Expense</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4> Expenses </h4>
                        </div>
                        <div class="col-6 align-self-end text-end mb-2">
                            <a class="btn btn-secondary btn-sm openExpenseModal" href="javascript:void(0);"
                                ExpenseId="0" title="Click to add new Expene" data-plugin="tippy"
                                data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-plus"></i>
                                Expense
                            </a>
                        </div>
                    </div>

                    <table id="Expense-datatable" class="table table-striped dt-responsive w-100">
                        <thead>
                            <th>#</th>
                            <th>Image</th>
                            <th>Category</th>
                            <th>Expene Date</th>
                            <th>Amount</th>
                            <th>Remarks</th>
                            <th>Actions</th>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div>
        <!-- end col-->
    </div>
</div>

@include('inventorymanagement.expense._addExpenseModal')

<!-- View Detail modal content -->
<div id="EmployeeDetailModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="EmployeeDetailModal"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="CustomerDetailData">
        </div>
    </div>
</div>
<!-- /.modal -->

@endsection

@section('custom_scripts')

<script>
    $(function() {
        $('.ModalClosed').click(function () {
            $('.modal').modal('hide'); 
            $(this).find('form').trigger('reset');
        });

        $(document).on('click', '.ViewEmployeeModal', function(){
            let employee_id = parseInt($(this).attr('EmployeeId')) || 0;
            $.get("{{ url('/employee')}}/"+employee_id, function(result) {
                // console.log(result)
                $('#EmployeeDetailModal').modal('show');
                $('#CustomerDetailData').html(result?.html_data);
            });
        });
        $('#Expense-datatable').DataTable({
            processing: true,
            info: true,
            ajax: "{{ route('getExpenseList')}}",
            "pageLength":20,
            "aLengthMenu":[[30,40,50,-1],[30,40,50,"all"]],
            columns:[
                {data:'DT_RowIndex'},
                {data:'picture'},
                {data:'category_id'},
                {data:'expense_date'},
                {data:'amount'},
                {data:'remarks'},
                {data:'Actions'},
            ]
        });
    });
</script>
@endsection