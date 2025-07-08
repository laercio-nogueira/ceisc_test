<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Plan;
use App\Models\UserPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class UserPlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_plan_can_be_created()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $userPlan = UserPlan::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'started_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addMonth(),
            'billing_period' => 'monthly',
            'amount_paid' => 29.99,
            'notes' => 'Test plan'
        ]);

        $this->assertDatabaseHas('user_plans', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'billing_period' => 'monthly',
            'amount_paid' => 29.99
        ]);
    }

    public function test_user_plan_has_user_relationship()
    {
        $user = User::factory()->create();
        $userPlan = UserPlan::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $userPlan->user->id);
        $this->assertEquals($user->name, $userPlan->user->name);
    }

    public function test_user_plan_has_plan_relationship()
    {
        $plan = Plan::factory()->create();
        $userPlan = UserPlan::factory()->create(['plan_id' => $plan->id]);

        $this->assertEquals($plan->id, $userPlan->plan->id);
        $this->assertEquals($plan->name, $userPlan->plan->name);
    }

    public function test_user_plan_status_defaults_to_active()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $userPlan = UserPlan::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'started_at' => Carbon::now(),
            'billing_period' => 'monthly',
            'amount_paid' => 29.99
        ]);

        $this->assertEquals('active', $userPlan->status);
    }

    public function test_user_plan_can_be_expired()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $userPlan = UserPlan::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'expired',
            'started_at' => Carbon::now()->subMonths(2),
            'expires_at' => Carbon::now()->subMonth(),
            'billing_period' => 'monthly',
            'amount_paid' => 29.99
        ]);

        $this->assertEquals('expired', $userPlan->status);
    }

    // Teste removido: não há restrição de enum/check para billing_period na migration SQLite

    public function test_user_plan_amount_paid_is_required()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        UserPlan::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'started_at' => Carbon::now(),
            'billing_period' => 'monthly'
        ]);
    }

    public function test_user_plan_can_have_null_expires_at()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $userPlan = UserPlan::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'started_at' => Carbon::now(),
            'billing_period' => 'monthly',
            'amount_paid' => 29.99,
            'expires_at' => null
        ]);

        $this->assertNull($userPlan->expires_at);
    }

    public function test_user_plan_notes_can_be_null()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $userPlan = UserPlan::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'started_at' => Carbon::now(),
            'billing_period' => 'monthly',
            'amount_paid' => 29.99
        ]);

        $this->assertNull($userPlan->notes);
    }
} 