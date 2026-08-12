<?php

namespace App\Actions\PartyManagement;

use App\Http\Requests\PartyManagement\StoreLookupTypeRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StoreLookupTypeAction
{
    /**
     * @return array{id: int, name: string}
     */
    public function execute(array $data): array
    {
        $table = $data['tag_name'];

        if (! in_array($table, StoreLookupTypeRequest::ALLOWED_TABLES, true) || ! Schema::hasTable($table)) {
            throw ValidationException::withMessages([
                'tag_name' => 'Invalid lookup table',
            ]);
        }

        $id = DB::table($table)->insertGetId([
            'name' => $data['name'],
            'slug' => Str::of($data['name'])->slug('-'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'id' => $id,
            'name' => $data['name'],
        ];
    }
}
