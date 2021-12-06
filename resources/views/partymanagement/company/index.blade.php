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
                        <li class="breadcrumb-item active">Company</li>
                    </ol>
                </div>
                <h4 class="page-title">Company</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4>Companies</h4>
                        </div>
                        <div class="col-6 align-self-end text-end mb-2">

                            <a class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#AddCompanyModal"
                                href="javascript:void(0);" CustomerId="0" title="Click to add new company"
                                data-plugin="tippy" data-tippy-animation="scale" data-tippy-arrow="true"><i
                                    class="fa fa-plus"></i>
                                Company
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
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($companies as $key=>$company)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>
                                    @if ($company?->company_logo)
                                    <img class="rounded-circle avatar-lg"
                                        src="{{ asset('storage/companies/'.$company?->company_logo) ?? ''}}"
                                        alt="No image">
                                    @else
                                    <b>No Image</b>
                                    @endif

                                </td>
                                <td>{{ $company?->name ?? '' }}</td>
                                <td>{{ $company?->email ?? '' }}</td>
                                <td>{{ $company?->contact_no ?? '' }}</td>
                                <td>{{ $company?->address ?? '' }}</td>
                                <td>
                                    <a class="btn btn-secondary btn-sm ViewCompanyModal"
                                        CompanyId="{{ $company?->id ?? 0}}" href="javascript:void(0);"
                                        title="View Details" tabindex="0" data-plugin="tippy"
                                        data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-eye"></i>
                                        View
                                    </a>
                                    <a class="btn btn-info btn-sm openCompanyModal" data-bs-toggle="modal"
                                        data-bs-target="#AddCustomerModal" CompanyId="{{ $company?->id ?? 0}}"
                                        href="javascript:void(0);" title="Click to edit" tabindex="0"
                                        data-plugin="tippy" data-tippy-animation="scale" data-tippy-arrow="true"><i
                                            class="fa fa-pencil-alt"></i>
                                        Edit
                                    </a>
                                    <a class="btn btn-danger btn-sm delete-confirm"
                                        href="{{route('company.destroy', $company?->id ?? 0)}}"
                                        del_title="Company {{$company?->name ?? ''}}" title="Click to delete"
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

@include('partymanagement._CompanyModal')

<!-- View Detail modal content -->
<div id="CompanyDetailModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="CompanyDetailModal"
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
            // $(this).find('modal').hide();
            $('.modal').modal('hide'); 
            $(this).find('form').trigger('reset');
        });

        $('.ViewCompanyModal').click(function () {
            let company_id = parseInt($(this).attr('CompanyId')) || 0;
            $.get("{{ url('/company')}}/"+company_id, function(result) {
                // console.log(result)
                $('#CompanyDetailModal').modal('show');
                $('#CustomerDetailData').html(result?.html_data);
            });
        });
    });
</script>
@endsection