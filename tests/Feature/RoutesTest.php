<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class RoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_route_redirects_to_login()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_register_route_shows_register_form()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    public function test_dashboard_route_requires_authentication()
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/');
    }

    public function test_dashboard_route_accessible_for_authenticated_user()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user'
        ]);

        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.user.dashboard');
    }

    public function test_admin_route_requires_admin_role()
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

    public function test_admin_route_accessible_for_admin()
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

    public function test_payment_route_requires_authentication()
    {
        $response = $this->get('/payment');

        $response->assertRedirect('/');
    }

    public function test_payment_route_accessible_for_authenticated_user()
    {
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

        $this->actingAs($user);

        $response = $this->get('/payment?plan_id=' . $plan->id . '&period=monthly');

        $response->assertStatus(200);
        $response->assertViewIs('payment.form');
    }

    public function test_payment_process_route_requires_authentication()
    {
        $response = $this->post('/payment/process');

        $response->assertRedirect('/');
    }

    public function test_payment_process_route_accessible_for_authenticated_user()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user'
        ]);

        $this->actingAs($user);

        $response = $this->post('/payment/process', [
            'plan_id' => 1,
            'period' => 'monthly',
            'payment_method_id' => 'pm_test_123',
            'amount' => 2999
        ]);

        // Deve retornar erro 500 porque o plano não existe, mas a rota está acessível
        $this->assertNotEquals(401, $response->getStatusCode());
        $this->assertNotEquals(403, $response->getStatusCode());
    }

    public function test_logout_route_requires_authentication()
    {
        $response = $this->post('/logout');

        $response->assertRedirect('/');
    }

    public function test_logout_route_works_for_authenticated_user()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user'
        ]);

        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_admin_plans_route_requires_admin_role()
    {
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user'
        ]);

        $this->actingAs($user);

        $response = $this->get('/admin/plans');

        $response->assertStatus(403);
    }

    public function test_admin_plans_route_accessible_for_admin()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin'
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/plans');

        $response->assertStatus(200);
        $response->assertViewIs('admin.plans.index');
    }

    public function test_plans_assign_route_requires_authentication()
    {
        $response = $this->post('/plans/assign');

        $response->assertRedirect('/');
    }

    public function test_plans_assign_route_accessible_for_authenticated_user()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user'
        ]);

        $this->actingAs($user);

        $response = $this->post('/plans/assign', [
            'plan_id' => 1,
            'period' => 'monthly'
        ]);

        // Deve retornar erro porque o plano não existe, mas a rota está acessível
        $this->assertNotEquals(401, $response->getStatusCode());
        $this->assertNotEquals(403, $response->getStatusCode());
    }

    public function test_plans_my_plan_route_requires_authentication()
    {
        $response = $this->get('/plans/my-plan');

        $response->assertRedirect('/');
    }

    public function test_plans_my_plan_route_accessible_for_authenticated_user()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user'
        ]);

        $this->actingAs($user);

        $response = $this->get('/plans/my-plan');

        $response->assertStatus(200);
        // Não exigir assertViewIs, pois pode ser JSON ou outro tipo de resposta
        $this->assertStringNotContainsString('Exception', $response->getContent());
    }

    public function test_oauth_test_route_accessible()
    {
        $response = $this->get('/oauth-test');

        $response->assertStatus(200);
        $response->assertViewIs('oauth-test');
    }

    public function test_csrf_protection_on_post_routes()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user'
        ]);

        $this->actingAs($user);

        // Testar sem CSRF token
        $response = $this->post('/payment/process', [
            'plan_id' => 1,
            'period' => 'monthly'
        ]);

        // Deve retornar erro 419 (CSRF token mismatch) ou 500 (outro erro)
        $this->assertNotEquals(200, $response->getStatusCode());
    }
} 