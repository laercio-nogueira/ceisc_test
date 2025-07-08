<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'role' => 'user'
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'user'
        ]);
    }

    public function test_user_has_user_plans_relationship()
    {
        $user = User::factory()->create();
        $userPlan = UserPlan::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->userPlans->contains($userPlan));
        $this->assertEquals(1, $user->userPlans->count());
    }

    public function test_user_password_is_hashed()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user'
        ]);

        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertNotEquals('password123', $user->password);
    }

    public function test_user_email_is_unique()
    {
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'role' => 'user'
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        User::create([
            'name' => 'Jane Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'role' => 'user'
        ]);
    }

    public function test_user_role_defaults_to_user()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password')
        ]);

        $this->assertEquals('user', $user->role);
    }

    public function test_user_can_have_admin_role()
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        $this->assertEquals('admin', $user->role);
        $this->assertTrue($user->isAdmin());
    }

    public function test_user_has_active_plan_relationship()
    {
        $user = User::factory()->create();
        $activePlan = UserPlan::factory()->create([
            'user_id' => $user->id,
            'status' => 'active'
        ]);
        $inactivePlan = UserPlan::factory()->create([
            'user_id' => $user->id,
            'status' => 'inactive'
        ]);

        $this->assertEquals($activePlan->id, $user->activePlan->id);
        $this->assertNotEquals($inactivePlan->id, $user->activePlan->id);
    }
} 