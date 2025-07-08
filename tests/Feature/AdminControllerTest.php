<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Plan;
use App\Models\UserPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_dashboard()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin'
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.admin.admin');
    }

    public function test_regular_user_cannot_access_admin_dashboard()
    {
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user'
        ]);

        $this->actingAs($user);

        $response = $this->get('/admin');

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_admin_dashboard()
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/');
    }

    public function test_admin_dashboard_shows_correct_statistics()
    {
        \Carbon\Carbon::setTestNow('2023-01-01 00:00:00');
        $admin = User::factory()->create(['role' => 'admin']);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $plan = Plan::factory()->create([
            'slug' => 'premium-plan',
            'screens' => 1,
            'features' => ['feature1', 'feature2'],
        ]);
        UserPlan::create([
            'user_id' => $user1->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'started_at' => '2023-01-01 00:00:00',
            'expires_at' => '2023-02-01 00:00:00',
            'billing_period' => 'monthly',
            'amount_paid' => 29.99,
        ]);
        UserPlan::create([
            'user_id' => $user2->id,
            'plan_id' => $plan->id,
            'status' => 'inactive',
            'started_at' => '2023-01-01 00:00:00',
            'expires_at' => '2022-12-01 00:00:00',
            'billing_period' => 'monthly',
            'amount_paid' => 29.99,
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin');

        $response->assertStatus(200);
        $response->assertViewHas('stats');
        
        $stats = $response->viewData('stats');
        dump(UserPlan::all()->toArray());
        dump(Plan::all()->toArray());
        $this->assertEquals(2, collect($stats)->sum('total'));
        $this->assertEquals(1, collect($stats)->sum('active'));
        $this->assertEquals(1, collect($stats)->sum('inactive'));
    }

    public function test_admin_dashboard_shows_recent_user_plans()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin'
        ]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user'
        ]);

        $plan = Plan::create([
            'name' => 'Premium Plan',
            'slug' => 'premium-plan',
            'description' => 'Premium features',
            'price_monthly' => 29.99,
            'price_semiannual' => 159.99,
            'price_annual' => 299.99,
            'screens' => 1,
            'features' => ['feature1', 'feature2']
        ]);

        $userPlan = UserPlan::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'started_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addMonth(),
            'billing_period' => 'monthly',
            'amount_paid' => 29.99
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin');

        $response->assertStatus(200);
        $response->assertViewHas('recentUserPlans');
        
        $recentUserPlans = $response->viewData('recentUserPlans');
        $this->assertCount(1, $recentUserPlans);
        $this->assertEquals($userPlan->id, $recentUserPlans->first()->id);
    }

    public function test_admin_dashboard_shows_plan_statistics()
    {
        \Carbon\Carbon::setTestNow('2023-01-01 00:00:00');
        $admin = User::factory()->create(['role' => 'admin']);
        $plan1 = Plan::factory()->create([
            'name' => 'Premium Plan',
            'slug' => 'premium-plan',
            'screens' => 1,
            'features' => ['feature1', 'feature2'],
        ]);
        $plan2 = Plan::factory()->create([
            'name' => 'Basic Plan',
            'slug' => 'basic-plan',
            'screens' => 1,
            'features' => ['feature1', 'feature2'],
        ]);
        $user1 = User::factory()->create();
        UserPlan::create([
            'user_id' => $user1->id,
            'plan_id' => $plan1->id,
            'status' => 'active',
            'started_at' => '2023-01-01 00:00:00',
            'expires_at' => '2024-01-01 00:00:00',
            'billing_period' => 'annual',
            'amount_paid' => 299.99,
        ]);
        $this->actingAs($admin);
        $response = $this->get('/admin');
        $response->assertStatus(200);
        $response->assertViewHas('planStats');
        $planStats = $response->viewData('planStats');
        dump(UserPlan::all()->toArray());
        dump(Plan::all()->toArray());
        $this->assertGreaterThanOrEqual(2, count($planStats));
        $this->assertEquals(1, collect($planStats)->sum('user_count'));
    }

    public function test_admin_dashboard_requires_authentication()
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/');
    }

    public function test_admin_dashboard_requires_admin_role()
    {
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user'
        ]);

        $this->actingAs($user);

        $response = $this->get('/admin');

        $response->assertStatus(403);
    }
} 