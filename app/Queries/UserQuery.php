<?php

namespace App\Queries;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class UserQuery
{
    /**
     * @var list<string>
     */
    private const ALLOWED_SORTS = ['id', 'name', 'email', 'contact_no', 'created_at'];

    /**
     * @var list<int>
     */
    private const ALLOWED_PER_PAGE = [10, 25, 50];

    public function __construct(private Request $request)
    {
    }

    public function paginate(): LengthAwarePaginator
    {
        $query = User::query()->with('userRole:id,name,slug');

        $search = trim((string) $this->request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('contact_no', 'like', '%'.$search.'%');
            });
        }

        $roleId = $this->request->input('user_role_id');
        if ($roleId !== null && $roleId !== '') {
            $query->where('user_role_id', (int) $roleId);
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
     * @return array{search: string, sort: string, direction: string, user_role_id: string}
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
            'user_role_id' => (string) $this->request->input('user_role_id', ''),
        ];
    }
}
