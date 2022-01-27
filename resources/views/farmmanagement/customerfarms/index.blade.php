@php
$load_css = Array('tables','sweetAlert', 'jquery-confirm');
$load_js = Array('tables','tippy','sweetAlert', 'jquery-confirm');
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
                        <li class="breadcrumb-item">Farm Management</li>
                        <li class="breadcrumb-item"> CUstomerFarms </li>
                        <li class="breadcrumb-item active"> index </li>
                    </ol>
                </div>
                <h4 class="page-title">Farm Management</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4>Customer Farms</h4>
                        </div>
                        <div class="col-6 align-self-end text-end mb-2">
                            <a class="btn btn-secondary btn-sm" href="{{ route('customerfarms.create') }}"
                                title="Click to add new Farm" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-plus"></i>
                                Add Farm
                            </a>
                        </div>
                    </div>
                    <table id="basic-datatable" class="table table-striped dt-responsive  w-100">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th> farm type </th>
                                <th> farm subtype </th>
                                <th> Farm Name </th>
                                <th> Customer Name </th>
                                <th> customer cnic </th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($farms as $key=>$farm)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $farm?->type?->name}}</td>
                                <td>{{ $farm?->subtype?->name}}</td>
                                <td>{{ $farm->farm_name}}</td>
                                <td>{{ $farm?->party?->name}}</td>
                                <td>{{ $farm?->party?->cnic_no}}</td>

                                <td>
                                    {{-- <a class="btn btn-secondary btn-sm" href="javascript:void(0);"
                                        title="View Details" tabindex="0" data-plugin="tippy"
                                        data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-eye"></i>
                                        View
                                    </a> --}}
                                    <a class="btn btn-info btn-sm" data-bs-toggle="modal" href="javascript:void(0);"
                                        title="Click to edit" tabindex="0" data-plugin="tippy"
                                        data-tippy-animation="scale" data-tippy-arrow="true"><i
                                            class="fa fa-pencil-alt"></i>
                                        Edit
                                    </a>
                                    <a class="btn btn-danger btn-sm delete-confirm"
                                        href="{{route('customerfarms.destroy', $farm->id ?? 0)}}"
                                        del_title="Customer Fame {{$farm?->farm_name}}" title="Click to delete"
                                        tabindex="0" data-plugin="tippy" data-tippy-animation="scale"
                                        data-tippy-arrow="true"><i class="fa fa-trash"></i>

                                    </a>

                                </td>
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


@endsection

@section('custom_scripts')

<script>
    $(function() {
    });
</script>

@endsection