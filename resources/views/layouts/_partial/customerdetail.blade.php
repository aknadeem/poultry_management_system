<div class="modal-header border-bottom">
    <h4 class="modal-title"> Customer Detail </h4>
    <button type="button" class="btn-close ModalClosed" aria-label="Close"></button>
</div>
<div class="card-body">
    <div class="d-flex align-items-start mb-1">
        @if ($customer?->image !='')
        <img class="d-flex me-3 rounded-circle avatar-lg" src="{{ asset('storage/customers/'.$customer?->image) ?? ''}}"
            alt="No image">
        @elseif ($customer?->company_logo !='')
        <img class="d-flex me-3 rounded-circle avatar-lg"
            src="{{ asset('storage/companies/'.$customer?->company_logo) ?? ''}}" alt="No image">
        @elseif ($customer?->employee_image !='')
        <img class="d-flex me-3 rounded-circle avatar-lg"
            src="{{ asset('storage/employees/'.$customer?->employee_image) ?? ''}}" alt="No image">
        @endif
        <div class="w-100">
            <h4 class="mt-0 mb-1"> {{$customer?->name ?? ''}} </h4>
            <p class=""> {{$customer?->designation ?? ''}}</p>
            @if ($customer?->department)
            <p class=""><i class=" mdi mdi-office-building"></i> {{$customer?->department ?? ''}}</p>
            @else
            <p class=""><i class=" mdi mdi-office-building"></i> {{$customer?->farm_name ?? ''}}</p>
            @endif

        </div>
    </div>
    <h5 class="text-uppercase bg-light p-2"><i class="mdi mdi-account-circle me-1"></i> Personal
        Information </h5>
    <div class="mt-1">
        <h4 class="font-13 text-uppercase">Description:</h4>
        <p class="mb-2">
            @if ($customer?->description !='')
            {{ $customer?->description ?? ''}}
            @endif
        </p>
        @if ($customer?->date_of_birth !='')
        <h4 class="font-13 text-uppercase mb-1">Date of Birth :</h4>
        <p class="mb-2"> {{ $customer?->date_of_birth->format('M d, Y')}} ( {{
            \Carbon\Carbon::parse($customer?->date_of_birth)->age}} Years) </p>
        @endif

        @if ($customer?->farm_name !='')
        <h4 class="font-13 text-uppercase mb-1">Company :</h4>
        <p class="mb-2">{{$customer?->farm_name ?? ''}}</p>
        @endif
        @if ($customer?->department !='')
        <h4 class="font-13 text-uppercase mb-1">Department :</h4>
        <p class="mb-2">{{$customer?->department ?? ''}}</p>
        @endif

        <h4 class="font-13 text-uppercase mb-1">Added :</h4>

        @if ($customer?->created_at)
        <p class="mb-2"> {{$customer?->created_at->format('M d, Y') ?? ''}}</p>
        @endif

        <h4 class="font-13 text-uppercase mb-1">Updated :</h4>
        @if ($customer?->updated_at)
        <p class="mb-0"> {{$customer?->updated_at->format('M d, Y') ?? ''}}</p>
        @endif

    </div>
</div>

<script>
    $('.ModalClosed').click(function () {
            $('.modal').modal('hide');
        });
</script>