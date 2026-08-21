<?php

namespace App\Queries;

use App\Models\Party;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class PartyQuery
{
    /**
     * @var list<string>
     */
    private const ALLOWED_SORTS = ['id', 'name', 'cnic_no', 'contact_no', 'created_at'];

    /**
     * @var list<int>
     */
    private const ALLOWED_PER_PAGE = [10, 25, 50];

    public function __construct(private Request $request)
    {
    }

    public function paginate(?string $type = null): LengthAwarePaginator
    {
        $query = Party::query()->with([
            'farm:id,party_id,farm_name',
            'company:id,party_id,company_name',
            'accounts:id,party_id,account_title,account_number,bank_name,opening_balance',
            'documents:id,party_id,title,document_name',
            'balanceLimits:id,party_id,start_date,end_date,debit_limit,credit_limit',
        ]);

        $scope = $type ?? (string) $this->request->input('party_type', '');
        if ($scope === 'customer') {
            $query->where('is_customer', 1);
        }
        if ($scope === 'vendor') {
            $query->where('is_vendor', 1);
        }

        $search = trim((string) $this->request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', '%'.$search.'%')
                    ->orWhere('cnic_no', 'like', '%'.$search.'%')
                    ->orWhere('contact_no', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        }

        $sort = (string) $this->request->input('sort', 'id');
        if (! in_array($sort, self::ALLOWED_SORTS, true)) {
            $sort = 'id';
        }

        $direction = $this->request->input('direction') === 'asc' ? 'asc' : 'desc';
        $perPage = (int) $this->request->input('per_page', 10);
        if (! in_array($perPage, self::ALLOWED_PER_PAGE, true)) {
            $perPage = 10;
        }

        return $query
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->onEachSide(1)
            ->withQueryString();
    }

    /**
     * @return array{search: string, sort: string, direction: string, party_type: string}
     */
    public function filters(?string $type = null): array
    {
        $sort = (string) $this->request->input('sort', 'id');
        if (! in_array($sort, self::ALLOWED_SORTS, true)) {
            $sort = 'id';
        }

        return [
            'search' => (string) $this->request->input('search', ''),
            'sort' => $sort,
            'direction' => $this->request->input('direction') === 'asc' ? 'asc' : 'desc',
            'party_type' => $type ?? (string) $this->request->input('party_type', ''),
        ];
    }
}
