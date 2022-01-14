<div class="modal-header border-bottom">
    <h4 class="modal-title"> Product Detail </h4>
    <button type="button" class="btn-close ModalClosed" aria-label="Close"></button>
</div>
<div class="card-body">
    <div class="d-flex align-items-start mb-1">
        @if ($result?->product_picture !='')
        <img class="d-flex me-3 rounded-circle avatar-lg"
            src="{{ asset('storage/products/'.$result?->product_picture) ?? ''}}" alt="No image">
        @endif

        <div class="w-100">
            <h4 class="mt-0 mb-1"> {{$result?->product_name}} </h4>
            <p class=""> {{$result?->product_code}}</p>
            @if ($result?->company_name)
            <p class=""><i class=" mdi mdi-office-building"></i> {{$result?->company_name ?? ''}}</p>
            @endif

        </div>
    </div>
    <h5 class="text-uppercase bg-light p-2"><i class="mdi mdi-account-circle me-1"></i> Detail </h5>
    <div class="mt-1">
        <h4 class="font-13 text-uppercase">Description:</h4>
        <p class="mb-2">
            @if ($result?->description !='')
            {{ $result?->description ?? ''}}
            @endif
        </p>
        @if ($result?->created_at !='')
        <h4 class="font-13 text-uppercase mb-1">Added Date :</h4>
        <p class="mb-2"> {{ $result?->created_at->format('M d, Y')}} </p>
        @endif

        @if ($result?->purchase_price !='')
        <h4 class="font-13 text-uppercase mb-1">Purchase Price :</h4>
        <p class="mb-2">{{$result?->purchase_price ?? ''}}</p>
        @endif

        @if ($result?->discount_amount !='')
        <h4 class="font-13 text-uppercase mb-1">Discount :</h4>
        <p class="mb-2">{{$result?->discount_amount}} - {{$result?->discount_percentage}} </p>
        @endif

        @if ($result?->tax_amount !='')
        <h4 class="font-13 text-uppercase mb-1">Tax :</h4>
        <p class="mb-2">{{$result?->tax_amount}} - {{$result?->tax_percentage}} </p>
        @endif

        @if ($result?->total_quantity !='')
        <h4 class="font-13 text-uppercase mb-1">Total Quantity:</h4>
        <p class="mb-2">{{$result?->total_quantity ?? ''}}</p>
        @endif

        @if ($result?->remaining_quantity !='')
        <h4 class="font-13 text-uppercase mb-1">Remaining Quantity:</h4>
        <p class="mb-2">{{$result?->remaining_quantity ?? ''}}</p>
        @endif

        @if ($result?->remaining_quantity !='')
        <h4 class="font-13 text-uppercase mb-1">Store :</h4>
        <p class="mb-2">{{$result?->productstore?->store_name}}</p>

        <h4 class="font-13 text-uppercase mb-1">Rack Number :</h4>
        <p class="mb-2">{{$result?->rack_number}}</p>
        @endif

        <h4 class="font-13 text-uppercase mb-1"> Added :</h4>
        @if ($result?->created_at)
        <p class="mb-2"> {{$result?->created_at->format('M d, Y') ?? ''}}</p>
        @endif

        <h4 class="font-13 text-uppercase mb-1">Updated :</h4>
        @if ($result?->updated_at)
        <p class="mb-0"> {{$result?->updated_at->format('M d, Y') ?? ''}}</p>
        @endif

    </div>
</div>

<script>
    $('.ModalClosed').click(function () {
        $('.modal').modal('hide');
    });
</script>