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
                        <li class="breadcrumb-item">PartyManagement</li>
                        <li class="breadcrumb-item">Brokers</li>
                        <li class="breadcrumb-item active">index</li>
                    </ol>
                </div>
                <h4 class="page-title">Brokers</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4> Brokers </h4>
                        </div>
                        <div class="col-6 align-self-end text-end mb-2">

                            <a class="btn btn-secondary btn-sm" href="{{ route('brokers.create') }}"
                                title="Click to add new Broker" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-plus"></i>
                                Broker
                            </a>
                        </div>
                    </div>
                    <table id="basic-datatable" class="table table-striped dt-responsive  w-100">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th> Name </th>
                                <th> Father / Guardian Name </th>
                                <th> CNIC </th>
                                <th> Contuct Number </th>
                                <th> Province </th>
                                <th> City </th>
                                <th> Status </th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($brokers as $key=>$row)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $row->name }}</td>
                                <td>{{ $row->guardian_name }}</td>
                                <td>{{ $row->cnic_no }}</td>
                                <td>{{ $row->contact_number }}</td>
                                <td>{{ $row->province->name }}</td>
                                <td>{{ $row->city->name }}</td>
                                <td>

                                    <a href="{{ route('updatestatus', ['id'=> $row->id, 'tag' => 'brokers']) }}"
                                        title="Click to update Status" class="confirm-status">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" id="customSwitch1"
                                                {{($row?->is_active) ? 'checked' : ''}}>
                                            <label class="form-check-label" for="customSwitch1"></label>
                                        </div>
                                    </a>
                                </td>
                                <td>
                                    <a class="btn btn-info btn-sm" href="{{ route('brokers.edit', $row->id) }}"
                                        title="Click to edit" tabindex="0" data-plugin="tippy"
                                        data-tippy-animation="scale" data-tippy-arrow="true"><i
                                            class="fa fa-pencil-alt"></i>
                                        Edit
                                    </a>
                                    <a class="btn btn-danger btn-sm delete-confirm"
                                        href="{{route('brokers.destroy', $row?->id ?? 0)}}"
                                        del_title="Broker {{$row?->name ?? ''}}" title="Click to delete" tabindex="0"
                                        data-plugin="tippy" data-tippy-animation="scale" data-tippy-arrow="true"><i
                                            class="fa fa-trash"></i>
                                        Delete
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