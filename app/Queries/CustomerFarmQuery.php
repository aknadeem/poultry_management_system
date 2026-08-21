<?php

namespace App\Queries;

use App\Models\PartyFarm;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class CustomerFarmQuery
{
    /**
     * @var list<string>
     */
    private const ALLOWED_SORTS = ['id', 'farm_name', 'farm_code', 'farm_capacity', 'created_at'];

    /**
     * @var list<int>
     */
    private const ALLOWED_PER_PAGE = [10, 25, 50];

    public function __construct(private Request $request)
    {
    }

    public function paginate(): LengthAwarePaginator
    {
        $query = PartyFarm::query()
            ->whereHas('party', fn ($party) => $party->where('is_customer', 1))
            ->with([
                'party:id,name,cnic_no',
                'type:id,name',
                'subtype:id,name',
            ]);

        $search = trim((string) $this->request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('farm_name', 'like', '%'.$search.'%')
                    ->orWhere('farm_code', 'like', '%'.$search.'%')
                    ->orWhere('farm_noc', 'like', '%'.$search.'%')
                    ->orWhere('farm_address', 'like', '%'.$search.'%')
                    ->orWhereHas('party', function ($party) use ($search): void {
                        $party->where('name', 'like', '%'.$search.'%')
                            ->orWhere('cnic_no', 'like', '%'.$search.'%');
                    });
            });
        }

        return $query
            ->orderBy($this->sort(), $this->direction())
            ->paginate($this->perPage())
            ->onEachSide(1)
            ->withQueryString();
    }

    /**
     * @return array{search: string, sort: string, direction: string}
     */
    public function filters(): array
    {
        return [
            'search' => (string) $this->request->input('search', ''),
            'sort' => $this->sort(),
            'direction' => $this->direction(),
        ];
    }

    private function sort(): string
    {
        $sort = (string) $this->request->input('sort', 'id');

        return in_array($sort, self::ALLOWED_SORTS, true) ? $sort : 'id';
    }

    private function direction(): string
    {
        return $this->request->input('direction') === 'asc' ? 'asc' : 'desc';
    }

    private function perPage(): int
    {
        $perPage = (int) $this->request->input('per_page', 10);

        return in_array($perPage, self::ALLOWED_PER_PAGE, true) ? $perPage : 10;
    }
}
