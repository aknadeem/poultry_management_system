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
                        <li class="breadcrumb-item">Contact Person</li>
                        <li class="breadcrumb-item active">index</li>
                    </ol>
                </div>
                <h4 class="page-title">Party Management</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4> Contact Persons </h4>
                        </div>
                        <div class="col-6 align-self-end text-end mb-2">

                            <a class="btn btn-secondary btn-sm" href="{{ route('conductpersons.create') }}"
                                title="Click to add new person" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-plus"></i>
                                Contact Person
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
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($conduct_people as $key=>$person)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $person->name }}</td>
                                <td>{{ $person->guardian_name }}</td>
                                <td>{{ $person->cnic_no }}</td>
                                <td>{{ $person->contact_number }}</td>
                                <td>{{ $person->province->name }}</td>
                                <td>{{ $person->city->name }}</td>
                                <td>
                                    <a class="btn btn-info btn-sm"
                                        href="{{ route('conductpersons.edit', $person->id) }}" title="Click to edit"
                                        tabindex="0" data-plugin="tippy" data-tippy-animation="scale"
                                        data-tippy-arrow="true"><i class="fa fa-pencil-alt"></i>
                                        Edit
                                    </a>
                                    <a class="btn btn-danger btn-sm delete-confirm"
                                        href="{{route('conductpersons.destroy', $person?->id ?? 0)}}"
                                        del_title="Conduct Person {{$person?->name ?? ''}}" title="Click to delete"
                                        tabindex="0" data-plugin="tippy" data-tippy-animation="scale"
                                        data-tippy-arrow="true"><i class="fa fa-trash"></i>
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