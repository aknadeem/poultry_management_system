<?php

namespace App\Queries;

use App\Models\ProductStore;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class ProductStoreQuery
{
    /**
     * @var list<string>
     */
    private const ALLOWED_SORTS = [
        'id',
        'store_name',
        'store_code',
        'store_type',
        'total_racks',
        'store_area',
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
        $query = ProductStore::query();

        $search = trim((string) $this->request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('store_name', 'like', '%'.$search.'%')
                    ->orWhere('store_code', 'like', '%'.$search.'%')
                    ->orWhere('store_type', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
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
