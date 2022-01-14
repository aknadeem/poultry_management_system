<div class="modal-header border-bottom">
    <h4 class="modal-title"> Detail Data </h4>
    <button type="button" class="btn-close ModalClosed" aria-label="Close"></button>
</div>
<div class="card-body">
    <div class="d-flex align-items-start mb-1">
        <div class="w-100">
            <h4 class="mt-0 mb-1">Name: {{$result?->store_name}} </h4>
            <p class="">Code: {{$result?->store_code}}</p>
            @if ($result?->total_racks > 0)
            <p class=""><i class="mdi mdi-office-building"></i> {{$result?->total_racks}} Racks </p>
            @endif

        </div>
    </div>
    {{-- <h5 class="text-uppercase bg-light p-2"><i class="mdi mdi-account-circle me-1"></i> Personal
        Information </h5> --}}
    <div class="mt-1">
        <h4 class="font-13 text-uppercase">Description:</h4>
        <p class="mb-2">
            @if ($result?->description !='')
            {{ $result?->description}}
            @endif
        </p>

        @if ($result?->store_type !='')
        <h4 class="font-13 text-uppercase mb-1">Store Type :</h4>
        <p class="mb-2">{{$result?->store_type}}</p>
        @endif
        @if ($result?->store_area !='')
        <h4 class="font-13 text-uppercase mb-1">Store Area :</h4>
        <p class="mb-2">{{$result?->store_area}}</p>
        @endif

        <h4 class="font-13 text-uppercase mb-1">Added :</h4>

        @if ($result?->created_at)
        <p class="mb-2"> {{$result?->created_at->format('M d, Y')}}</p>
        @endif

        <h4 class="font-13 text-uppercase mb-1">Updated :</h4>
        @if ($result?->updated_at)
        <p class="mb-0"> {{$result?->updated_at->format('M d, Y')}}</p>
        @endif

    </div>
</div>

<script>
    $('.ModalClosed').click(function () {
        $('.modal').modal('hide');
    });
</script>