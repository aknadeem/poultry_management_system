<?php

namespace App\Queries;

use App\Models\ProductPurchase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class ProductPurchaseQuery
{
    /**
     * @var list<string>
     */
    private const ALLOWED_SORTS = [
        'id',
        'purchase_date',
        'total_amount',
        'final_amount',
        'is_active',
        'created_at',
    ];

    /**
     * @var list<int>
     */
    private const ALLOWED_PER_PAGE = [10, 25, 50];

    public function __construct(private Request $request)
    {
    }

    public function paginate(): LengthAwarePaginator
    {
        $query = ProductPurchase::query()->with([
            'company:id,party_id,company_name',
            'productcategory:id,name',
        ]);

        $search = trim((string) $this->request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('purchase_code', 'like', '%'.$search.'%')
                    ->orWhere('manual_number', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%')
                    ->orWhereHas('company', fn ($company) => $company->where('company_name', 'like', '%'.$search.'%'))
                    ->orWhereHas('productcategory', fn ($category) => $category->where('name', 'like', '%'.$search.'%'));
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
