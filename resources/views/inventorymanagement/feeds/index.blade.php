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
                            {{-- <a class="btn btn-secondary btn-sm" href="{{ route('feed.create') }}"
                                title="Click to add Feed" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-plus"></i>
                                Feed
                            </a> --}}
                        </div>
                    </div>
                    <table id="basic-datatable" class="table table-striped dt-responsive  w-100">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th> Product Code </th>
                                <th> Product Group </th>
                                <th> Product Name </th>
                                <th> Company </th>
                                <th> Product Category </th>
                                <th> Quantity </th>
                                <th> Purchase Date </th>
                                <th> Status </th>
                                {{-- <th>Options</th> --}}
                            </tr>
                        </thead>
                        <tbody>

                            @forelse ($products as $key=>$row)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $row?->product_code }}</td>
                                <td>
                                    <span
                                        class="badge bg-{{ \App\Helpers\Constant::PRODUCT_GROUP_COLOR[$row->product_group] }} text-white fs-5">
                                        {{ \App\Helpers\Constant::PRODUCT_GROUP_VAL[$row->product_group] }}
                                    </span>
                                </td>
                                <td>{{ $row?->product_name }}</td>
                                <td>{{ $row?->company?->company_name }}</td>
                                <td>{{ $row?->category?->name }}</td>
                                <td>{{ $row?->quantity }}</td>
                                <td class="text-danger fw-bold">{{ $row?->purchase_date?->format('M d, Y') ?? 'Nil' }}
                                </td>
                                <td>

                                    <a href="{{ route('updatestatus', ['id'=> $row->id, 'tag' => 'products']) }}"
                                        title="Click to update Status" class="confirm-status">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" id="customSwitch1"
                                                {{($row?->is_active) ? 'checked' : ''}}>
                                            <label class="form-check-label" for="customSwitch1"></label>
                                        </div>
                                    </a>
                                </td>
                                {{-- <td>
                                    <a class="btn btn-info btn-sm" href="{{ route('products.edit', $row?->id) }}"
                                        title="Click to edit" tabindex="0" data-plugin="tippy"
                                        data-tippy-animation="scale" data-tippy-arrow="true"><i
                                            class="fa fa-pencil-alt"></i>
                                        Edit
                                    </a>
                                    <a class="btn btn-danger btn-sm delete-confirm"
                                        href="{{route('products.destroy', $row?->id)}}"
                                        del_title="Product {{$row?->product_code ?? ''}}" title="Click to delete"
                                        tabindex="0" data-plugin="tippy" data-tippy-animation="scale"
                                        data-tippy-arrow="true"><i class="fa fa-trash"></i>
                                        Delete
                                    </a>
                                </td> --}}
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
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
                {data:'feed_category_id'},
                {data:'feed_name'},
                {data:'total_quantity'},
                {data:'remaining_quantity'},
                {data:'Actions'},
            ]
        });
    });
</script>
@endsection