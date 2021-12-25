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
                            <h4>Parties</h4>
                        </div>
                        <div class="col-6 align-self-end text-end mb-2">

                            <a class="btn btn-secondary btn-sm" href="{{ route('parties.create') }}"
                                title="Click to add new Party" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-plus"></i>
                                Create Party
                            </a>
                        </div>
                    </div>
                    <table id="basic-datatable" class="table table-striped dt-responsive  w-100">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th> Type</th>
                                <th> Name </th>
                                <th> CNIC </th>
                                <th> Account Detail </th>
                                <th> Documents </th>
                                <th> Debit/Credit Limit </th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($parties as $key=>$party)
                            <tr>
                                <td>{{++$key}}</td>
                                <td> <b>
                                        {{($party->is_vendor) ? 'Vendor' : 'Customer'}} {{ ($party->is_customer) ?
                                        '/ Customer' : '/ Vendor'}}
                                    </b>
                                </td>
                                <td>{{$party->name}}</td>
                                <td>{{$party->cnic_no}}</td>
                                <td>
                                    <a class="btn btn-secondary btn-sm viewCustomerDetailModal" CustomerId=""
                                        href="javascript:void(0);" title="Click to View Accounts "><i
                                            class="fa fa-eye"></i>
                                        View
                                    </a>
                                    <a class="btn btn-success btn-sm OpenAccountModal" PartyId="{{ $party->id}}"
                                        href="javascript:void(0);" title="Click to Add New Account"><i
                                            class="fa fa-plus"></i>
                                        add
                                    </a>
                                </td>

                                <td>
                                    <a class="btn btn-secondary btn-sm viewCustomerDetailModal" CustomerId=""
                                        href="javascript:void(0);" title="Click to View DOcuments "><i
                                            class="fa fa-eye"></i>
                                        View
                                    </a>
                                    <a class="btn btn-success btn-sm OpenPartyDocumentModal" PartyId="{{ $party->id}}"
                                        CustomerId="" href="javascript:void(0);" title="Click to Add New Document"><i
                                            class="fa fa-plus"></i>
                                        add
                                    </a>
                                </td>

                                <td>
                                    <a class="btn btn-secondary btn-sm viewCustomerDetailModal" CustomerId=""
                                        href="javascript:void(0);" title="Click to View DOcuments "><i
                                            class="fa fa-eye"></i>
                                        View
                                    </a>
                                    <a class="btn btn-success btn-sm OpenPartyLimitsModal" PartyId="{{ $party->id}}"
                                        CustomerId="" href="javascript:void(0);" title="Click to Add New Limit"><i
                                            class="fa fa-plus"></i>
                                        add
                                    </a>
                                </td>

                                <td>
                                    {{-- <a class="btn btn-secondary btn-sm" href="javascript:void(0);"
                                        title="View Details" tabindex="0" data-plugin="tippy"
                                        data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-eye"></i>
                                        View
                                    </a> --}}
                                    <a class="btn btn-info btn-sm" href="{{route('parties.edit', $party->id ?? 0)}}"
                                        title="Click to edit" tabindex="0" data-plugin="tippy"
                                        data-tippy-animation="scale" data-tippy-arrow="true"><i
                                            class="fa fa-pencil-alt"></i>
                                        Edit
                                    </a>
                                    <a class="btn btn-danger btn-sm delete-confirm"
                                        href="{{route('parties.destroy', $party->id ?? 0)}}"
                                        del_title="Party Fame {{$party?->name}}" title="Click to delete" tabindex="0"
                                        data-plugin="tippy" data-tippy-animation="scale" data-tippy-arrow="true"><i
                                            class="fa fa-trash"></i>

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

@include('partymanagement._AddPartyAccounts')
@include('partymanagement._AddPartyDocuments')
@include('partymanagement._AddPartyBalanceLimit')
@endsection

@section('custom_scripts')
<script>
    $(function() {
        $('.ModalClosed').click(function () {
            // $(this).find('modal').hide();
            $('.modal').modal('hide'); 
            $(this).find('form').trigger('reset');
        });
    });
</script>
@endsection