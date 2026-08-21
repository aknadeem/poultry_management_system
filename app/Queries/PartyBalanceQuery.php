<?php

namespace App\Queries;

use App\Models\PartyBalance;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class PartyBalanceQuery
{
    /**
     * @var list<string>
     */
    private const ALLOWED_SORTS = ['id', 'total_amount', 'remaining_amount', 'transaction_date', 'created_at'];

    /**
     * @var list<int>
     */
    private const ALLOWED_PER_PAGE = [10, 25, 50];

    public function __construct(private Request $request)
    {
    }

    public function paginate(): LengthAwarePaginator
    {
        $query = PartyBalance::query()->with('party:id,name,cnic_no');

        $search = trim((string) $this->request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('narration', 'like', '%'.$search.'%')
                    ->orWhereHas('party', function ($party) use ($search): void {
                        $party->where('name', 'like', '%'.$search.'%')
                            ->orWhere('cnic_no', 'like', '%'.$search.'%');
                    });
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
     * @return array{search: string, sort: string, direction: string}
     */
    public function filters(): array
    {
        $sort = (string) $this->request->input('sort', 'id');
        if (! in_array($sort, self::ALLOWED_SORTS, true)) {
            $sort = 'id';
        }

        return [
            'search' => (string) $this->request->input('search', ''),
            'sort' => $sort,
            'direction' => $this->request->input('direction') === 'asc' ? 'asc' : 'desc',
        ];
    }
}
