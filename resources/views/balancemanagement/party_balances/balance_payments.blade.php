@php
$load_css = Array('tables','sweetAlert', 'jquery-confirm','select2');
$load_js = Array('tables','tippy','sweetAlert', 'jquery-confirm','select2','select2model')
;
@endphp
@extends('layouts.app')
@section('content')
@if ($payments->count() > 0)
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item active"> PartyBalancePayments </li>
                    </ol>
                </div>
                <h6 class="page-title">
                    @if ($payments)
                    {{ $payments[0]?->party?->name ?? ''}}
                    @endif
                </h6>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 align-self-start">
                            <h4>Balance Payments</h4>
                        </div>
                        <div class="col-6 align-self-end text-end mb-2">
                            @if($payments[0]?->party?->profile_picture)
                                <a href="{{ asset('storage/party/'.$payments[0]?->party?->profile_picture) }}"
                                    target="_blank" title="click to view">
                                    <img src="{{ asset('storage/party/'.$payments[0]?->party?->profile_picture) }}" alt="Party Profile">
                                        alt="No image" width="10%">
                                </a>
                            @endif
                        </div>
                    </div>
                    <table id="Balance-Datatables" class="table table-striped dt-responsive  w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Received Amount</th>
                                <th>Received through</th>
                                <th>Status</th>
                                <th>Received By </th>
                                <th>Received At </th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payments as $key=>$item)
                            <tr>
                                <td>{{++$key}}</td>
                                <td>{{$item->paid_amount}}</td>
                                <td>{{$item->payment_option}}</td>
                                <td>{{$item->payment_status ?? 'posted'}}</td>
                                <td>{{$item?->user?->name}} </td>
                                <td>{{$item->created_at?->format('d M, Y h:i:s A')}}</td>
                                <td>
                                    @if(($item->payment_status ?? 'posted') !== 'reversed')
                                        <button type="button" class="btn btn-sm btn-outline-danger reversePartyPayment"
                                            data-url="{{ route('partybalance.payments.reverse', $item->id) }}">
                                            Reverse
                                        </button>
                                    @else
                                        <span class="text-muted">{{ $item->reversal_reason }}</span>
                                    @endif
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
<!-- View Detail modal content -->
<div id="CompanyDetailModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="CompanyDetailModal"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="CustomerDetailData">
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
@else
<h4 class="mt-5 text-center text-danger"> No Payments Found</h4>
<h4 class="mt-2 text-center text-danger"> <a href="{{URL::previous()}}"> <i class="fa fa-arrow-left" aria-hidden="true">
        </i>
        Go back </a> </h4>
@endif
@endsection
@section('custom_scripts')
<script>
    $(function() {


        $('.ModalClosed').click(function () {
            // $(this).find('modal').hide();
            $('.modal').modal('hide');
            $(this).find('form').trigger('reset');
        });

        $(document).on('click', '.reversePartyPayment', function () {
            const url = $(this).data('url');
            const reason = window.prompt('Optional reversal reason:');
            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    reversal_reason: reason || null,
                },
                success: function (response) {
                    if (response.success === 'yes') {
                        window.location.reload();
                    } else {
                        alert(response.message || 'Unable to reverse payment');
                    }
                },
                error: function (xhr) {
                    const message = xhr.responseJSON?.error?.payment?.[0]
                        || xhr.responseJSON?.message
                        || 'Unable to reverse payment';
                    alert(message);
                }
            });
        });
    });
</script>
@endsection