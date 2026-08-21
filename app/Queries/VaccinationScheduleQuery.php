<?php

namespace App\Queries;

use App\Models\VaccinationSchedule;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class VaccinationScheduleQuery
{
    /**
     * @var list<string>
     */
    private const ALLOWED_SORTS = [
        'id',
        'schedule_date',
        'vaccination_date',
        'is_vaccinated',
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
        $query = VaccinationSchedule::query()->with([
            'farm:id,farm_name',
            'product:id,product_code,product_name',
        ]);

        $search = trim((string) $this->request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('description', 'like', '%'.$search.'%')
                    ->orWhere('vaccinated_remarks', 'like', '%'.$search.'%')
                    ->orWhereHas('farm', fn ($farm) => $farm->where('farm_name', 'like', '%'.$search.'%'))
                    ->orWhereHas('product', function ($product) use ($search): void {
                        $product->where('product_name', 'like', '%'.$search.'%')
                            ->orWhere('product_code', 'like', '%'.$search.'%');
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
