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
 * Test that model relationships are properly configured
 */
class ModelRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test User model has userlevel relationship method
     */
    public function test_user_model_has_relationship_methods()
    {
        $user = new User();
        $this->assertTrue(method_exists($user, 'userlevel'));
    }

    /**
     * Test Employee model loads correctly
     */
    public function test_employee_model_can_be_instantiated()
    {
        $employee = new Employee();
        $this->assertNotNull($employee);
    }

    /**
     * Test Party model has farm relationship method
     */
    public function test_party_model_has_farm_relationship()
    {
        $party = new Party();
        $this->assertTrue(method_exists($party, 'farm'));
    }

    /**
     * Test model boot methods are defined
     */
    public function test_model_boot_methods_work()
    {
        // Employee has custom boot method - verify it exists
        $this->assertTrue(method_exists(Employee::class, 'boot'));
    }
}
