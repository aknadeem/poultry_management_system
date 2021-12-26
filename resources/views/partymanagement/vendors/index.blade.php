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
                        <li class="breadcrumb-item active">PartyManagement</li>
                    </ol>
                </div>
                <h4 class="page-title">Vendors</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4> Vendors </h4>
                        </div>
                        <div class="col-6 align-self-end text-end mb-2">

                            <a class="btn btn-secondary btn-sm" href="{{ route('vendors.create') }}"
                                title="Click to add new vendor" data-plugin="tippy" data-tippy-animation="scale"
                                data-tippy-arrow="true"><i class="fa fa-plus"></i>
                                Vendor
                            </a>
                        </div>
                    </div>
                    <table id="basic-datatable" class="table table-striped dt-responsive  w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Contact Number</th>
                                <th>ShopName</th>
                                <th>Account</th>
                                <th>Document</th>
                                <th>Balance Limit</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($customers as $key=>$vendor)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>
                                    @if ($vendor->profile_picture)
                                    <img class="d-flex me-3 rounded-circle avatar-lg"
                                        src="{{ asset('storage/party/'.$vendor->profile_picture) ?? ''}}"
                                        alt="No image">
                                    @else
                                    <b>No Image</b>
                                    @endif

                                </td>
                                <td>{{ $vendor->name ?? '' }}</td>
                                <td>{{ $vendor->email ?? '' }}</td>
                                <td>{{ $vendor->contact_no ?? '' }}</td>
                                <td>{{ $vendor->farm_name ?? '' }}</td>


                                <td>
                                    <a class="btn btn-secondary btn-sm viewCustomerDetailModal" CustomerId=""
                                        href="javascript:void(0);" title="Click to View Accounts "><i
                                            class="fa fa-eye"></i>
                                        View
                                    </a>
                                    <a class="btn btn-success btn-sm OpenAccountModal" PartyId="{{ $vendor->id}}"
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
                                    <a class="btn btn-success btn-sm OpenPartyDocumentModal" PartyId="{{ $vendor->id}}"
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
                                    <a class="btn btn-success btn-sm OpenPartyLimitsModal" PartyId="{{ $vendor->id}}"
                                        CustomerId="" href="javascript:void(0);" title="Click to Add New Limit"><i
                                            class="fa fa-plus"></i>
                                        add
                                    </a>
                                </td>

                                <td>
                                    <a class="btn btn-secondary btn-sm viewCustomerDetailModal"
                                        CustomerId="{{ $customer->id ?? 0}}" href="javascript:void(0);"
                                        title="View Details" tabindex="0" data-plugin="tippy"
                                        data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-eye"></i>
                                        View
                                    </a>
                                    <a class="btn btn-info btn-sm openCustomerModal" data-bs-toggle="modal"
                                        data-bs-target="#AddCustomerModal" CustomerId="{{ $customer->id ?? 0}}"
                                        href="javascript:void(0);" title="Click to edit" tabindex="0"
                                        data-plugin="tippy" data-tippy-animation="scale" data-tippy-arrow="true"><i
                                            class="fa fa-pencil-alt"></i>
                                        Edit
                                    </a>
                                    <a class="btn btn-danger btn-sm delete-confirm"
                                        href="{{route('customers.destroy', $customer->id ?? 0)}}"
                                        del_title="Cutomer abc" title="Click to delete" tabindex="0" data-plugin="tippy"
                                        data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-trash"></i>
                                        Delete
                                    </a>

                                    {{-- <a class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#scrollable-modal" href="javascript:void(0);"
                                        title="Click to delete" tabindex="0" data-plugin="tippy"
                                        data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-trash"></i>
                                        Delete
                                    </a> --}}
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