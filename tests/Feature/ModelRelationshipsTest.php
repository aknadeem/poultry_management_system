<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserLevel;
use App\Models\Employee;
use App\Models\EmployeeLevel;
use App\Models\Party;
use App\Models\PartyFarm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test that model relationships work correctly
 */
class ModelRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test User belongsTo UserLevel relationship
     */
    public function test_user_belongs_to_user_level()
    {
        $userLevel = UserLevel::factory()->create();
        $user = User::factory()->create([
            'user_level_id' => $userLevel->id,
        ]);

        $this->assertNotNull($user->userlevel);
        $this->assertEquals($userLevel->id, $user->userlevel->id);
    }

    /**
     * Test Employee model loads correctly
     */
    public function test_employee_model_can_be_created()
    {
        $employee = Employee::factory()->create();

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
        ]);
    }

    /**
     * Test Party hasOne PartyFarm relationship
     */
    public function test_party_has_farm()
    {
        $party = Party::factory()->create();
        $farm = PartyFarm::factory()->create([
            'party_id' => $party->id,
        ]);

        $this->assertNotNull($party->farm);
        $this->assertEquals($farm->id, $party->farm->id);
    }

    /**
     * Test model soft deletes work
     */
    public function test_employee_soft_delete()
    {
        $employee = Employee::factory()->create();
        $employeeId = $employee->id;

        $employee->delete();

        // Should not exist in normal query
        $this->assertNull(Employee::find($employeeId));

        // Should exist in withTrashed query
        $this->assertNotNull(Employee::withTrashed()->find($employeeId));
    }

    /**
     * Test model timestamps are saved
     */
    public function test_model_timestamps_are_saved()
    {
        $employee = Employee::factory()->create();

        $this->assertNotNull($employee->created_at);
        $this->assertNotNull($employee->updated_at);
    }
}
