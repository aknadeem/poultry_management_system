<div class="modal-header border-bottom">
    <h4 class="modal-title"> Company Detail </h4>
    <button type="button" class="btn-close ModalClosed" aria-label="Close"></button>
</div>
<div class="card-body">
    <div class="d-flex align-items-start mb-1">
        @if ($data?->company_logo !='')
        <img class="d-flex me-3 rounded-circle avatar-lg"
            src="{{ asset('storage/party/company/'.$data?->company_logo) ?? ''}}" alt="No image">
        @endif
        <div class="w-100">
            <h4 class="mt-0 mb-1"> {{$data?->company_name ?? ''}} </h4>
            <p class=""> <b> Business: </b> {{$data?->businesstype?->name ?? ''}}</p>
            @if ($data?->company_address)
            <p class=""><i class=" mdi mdi-office-building"></i> {{$data?->company_address}}</p>
            @endif

        </div>
    </div>
    <h5 class="text-uppercase bg-light p-2"><i class="mdi mdi-account-circle me-1"></i> Personal
        Information </h5>
    <div class="mt-1">
        {{-- <h4 class="font-13 text-uppercase">Description:</h4> --}}
        <p class="mb-2">
            @if ($data?->description !='')
            {{ $data?->description ?? ''}}
            @endif
        </p>
        @if ($data?->created_at !='')
        <h4 class="font-13 text-uppercase mb-1"> Entry Date :</h4>
        <p class="mb-2"> {{ $data?->created_at->format('M d, Y')}}</p>
        @endif

        @if ($data?->vendor !='')
        <h4 class="font-13 text-uppercase mb-1">Vendor :</h4>
        <p class="mb-2">{{$data?->vendor?->name ?? ''}}</p>
        @endif

        {{-- <h4 class="font-13 text-uppercase mb-1">Added :</h4>
        @if ($data?->created_at)
        <p class="mb-2"> {{$data?->created_at->format('M d, Y') ?? ''}}</p>
        @endif --}}

        <h4 class="font-13 text-uppercase mb-1">Updated :</h4>
        @if ($data?->updated_at)
        <p class="mb-0"> {{$data?->updated_at->format('M d, Y') ?? ''}}</p>
        @endif

    </div>
</div>

<script>
    $('.ModalClosed').click(function () {
            $('.modal').modal('hide');
        });
</script>