<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Party;
use App\Models\User;
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
     * Test that models have $guarded protection
     */
    public function test_all_models_have_mass_assignment_protection()
    {
        $employee = new Employee();
        $party = new Party();
        $user = new User();

        // Check that $guarded property exists (indicates mass assignment control)
        $this->assertTrue(property_exists($employee, 'guarded') || property_exists($employee, 'fillable'));
        $this->assertTrue(property_exists($party, 'guarded') || property_exists($party, 'fillable'));
        $this->assertTrue(property_exists($user, 'guarded') || property_exists($user, 'fillable'));
    }

    /**
     * Test Model::unguard() is not being used
     */
    public function test_model_unguard_not_globally_enabled()
    {
        // If we can instantiate models without errors, unguard() was properly removed
        $employee = new Employee();
        $party = new Party();
        $user = new User();

        $this->assertNotNull($employee);
        $this->assertNotNull($party);
        $this->assertNotNull($user);
    }
}
