<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Plan;
use App\Models\UserPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_plan_can_be_created()
    {
        $features = ['feature1', 'feature2'];
        $plan = Plan::create([
            'name' => 'Premium Plan',
            'slug' => 'premium-plan',
            'description' => 'Premium features',
            'price_monthly' => 29.99,
            'price_semiannual' => 159.99,
            'price_annual' => 299.99,
            'screens' => 1,
            'features' => $features
        ]);

        $this->assertDatabaseHas('plans', [
            'name' => 'Premium Plan',
            'description' => 'Premium features',
            'price_monthly' => 29.99,
            'price_semiannual' => 159.99,
            'price_annual' => 299.99
        ]);
    }

    public function test_plan_has_user_plans_relationship()
    {
        $plan = Plan::factory()->create();
        $userPlan = UserPlan::factory()->create(['plan_id' => $plan->id]);

        $this->assertTrue($plan->userPlans->contains($userPlan));
        $this->assertEquals(1, $plan->userPlans->count());
    }

    public function test_plan_features_are_json_encoded()
    {
        $features = ['feature1', 'feature2', 'feature3'];
        $plan = Plan::create([
            'name' => 'Test Plan',
            'slug' => 'test-plan',
            'description' => 'Test Description',
            'price_monthly' => 10,
            'price_semiannual' => 50,
            'price_annual' => 100,
            'features' => $features,
            'screens' => 1
        ]);

        $this->assertIsArray($plan->features);
        $this->assertEquals($features, $plan->features);
    }

    public function test_plan_prices_are_numeric()
    {
        $features = ['feature1', 'feature2'];
        $plan = Plan::create([
            'name' => 'Test Plan',
            'slug' => 'test-plan',
            'description' => 'Test Description',
            'price_monthly' => 10.5,
            'price_semiannual' => 55.25,
            'price_annual' => 100.75,
            'features' => $features,
            'screens' => 1
        ]);

        $this->assertIsNumeric($plan->price_monthly);
        $this->assertIsNumeric($plan->price_semiannual);
        $this->assertIsNumeric($plan->price_annual);
    }

    public function test_plan_name_is_required()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Plan::create([
            'description' => 'Test Description',
            'slug' => 'test-plan',
            'price_monthly' => 10,
            'price_semiannual' => 50,
            'price_annual' => 100,
            'screens' => 1
        ]);
    }

    public function test_plan_prices_cannot_be_negative()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Plan::create([
            'name' => 'Test Plan',
            'slug' => 'test-plan',
            'description' => 'Test Description',
            'price_monthly' => -10,
            'price_semiannual' => 50,
            'price_annual' => 100,
            'screens' => 1
        ]);
    }
} 