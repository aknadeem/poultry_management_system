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
                        <li class="breadcrumb-item active">Feed</li>
                    </ol>
                </div>
                <h4 class="page-title">Feed Inventory</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4>Feed list</h4>
                        </div>
                        {{-- data-bs-toggle="modal"
                        data-bs-target="#AddFeedModal" --}}
                        <div class="col-6 align-self-end text-end mb-2">
                            <a class="btn btn-secondary btn-sm" href="{{ route('feed.create') }}"
                                title="Click to add Feed" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-plus"></i>
                                Feed
                            </a>
                        </div>
                    </div>
                    <table id="Feed-datatable" class="table table-striped dt-responsive w-100">
                        <thead>
                            <th>#</th>
                            <th>picture</th>
                            <th>Feed Name</th>
                            <th>Purchase Date</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Dicount Amount</th>
                            <th>total Price</th>
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

@include('inventorymanagement._FeedModal')

<!-- View Detail modal content -->
<div id="EmployeeDetailModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="EmployeeDetailModal"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="CustomerDetailData">
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

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
            $.get("{{ url('/inventory/feed')}}/"+employee_id, function(result) {
                console.log(result)
                $('#EmployeeDetailModal').modal('show');
                $('#CustomerDetailData').html(result?.html_data);
            });
        });
        $('#Feed-datatable').DataTable({
            processing: true,
            info: true,
            ajax: "{{ url('inventory/get-feed-list')}}",
            "pageLength":10,
            "aLengthMenu":[[10,20,30,40,-1],[10,20,30,40,"all"]],
            columns:[
                // {data:'id', name:'id'},
                {data:'DT_RowIndex'},
                {data:'picture'},
                {data:'feed_name'},
                {data:'purchase_date'},
                {data:'price'},
                {data:'quantity'},
                {data:'discount_amount'},
                {data:'total_price'},
                {data:'Actions'},
            ]
        });
    });
</script>
@endsection