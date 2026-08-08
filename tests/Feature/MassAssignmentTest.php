<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Party;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test that mass assignment protection is properly configured
 * after removing Model::unguard()
 */
class MassAssignmentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that models can be filled with mass assignment ($guarded = [])
     */
    public function test_employee_mass_assignment_works()
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'designation' => 'Manager',
        ];

        // This should work with $guarded = []
        $employee = Employee::create($data);

        $this->assertDatabaseHas('employees', [
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
    }

    /**
     * Test that Party model can be mass assigned
     */
    public function test_party_mass_assignment_works()
    {
        $data = [
            'name' => 'Test Farm',
            'phone' => '123456789',
            'address' => 'Test Address',
        ];

        $party = Party::create($data);

        $this->assertDatabaseHas('parties', [
            'name' => 'Test Farm',
        ]);
    }

    /**
     * Test that User model can be mass assigned
     */
    public function test_user_mass_assignment_works()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
        ];

        $user = User::create($data);

        $this->assertDatabaseHas('users', [
            'email' => 'user@example.com',
        ]);
    }

    /**
     * Test that mass assignment via fillable attribute works
     */
    public function test_mass_assignment_via_fill()
    {
        $employee = new Employee();
        $employee->fill([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ]);

        $this->assertEquals('Jane', $employee->first_name);
        $this->assertEquals('Smith', $employee->last_name);
    }

    /**
     * Test model can be updated with mass assignment
     */
    public function test_model_update_with_mass_assignment()
    {
        $employee = Employee::factory()->create();

        $employee->update([
            'first_name' => 'Updated Name',
        ]);

        $this->assertEquals('Updated Name', $employee->fresh()->first_name);
    }
}
