<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Plan;
use App\Models\UserPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock Stripe para evitar chamadas reais
        $this->mockStripe();
        // Mock estático do Stripe
        \Mockery::mock('overload:' . Stripe::class)
            ->shouldReceive('setApiKey')->andReturnNull();
        \Mockery::mock('overload:' . PaymentIntent::class)
            ->shouldReceive('create')->andReturn((object)[
                'client_secret' => 'test_secret',
                'status' => 'succeeded',
            ]);
    }

    private function mockStripe()
    {
        // Mock do StripePaymentController
        $this->mock(\App\Http\Controllers\StripePaymentController::class, function ($mock) {
            $mock->shouldReceive('pay')->andReturn(
                response()->json(['status' => 'succeeded', 'clientSecret' => 'test_secret'])
            );
            $mock->shouldReceive('payWeb')->andReturn(
                response()->json(['status' => 'succeeded', 'clientSecret' => 'test_secret'])
            );
        });
    }

    public function test_user_can_view_payment_form()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
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
        $response->assertViewHas('plan');
        $response->assertViewHas('period');
        $response->assertViewHas('amount');
    }

    public function test_admin_cannot_access_payment_form()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin'
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

        $this->actingAs($admin);

        $response = $this->get('/payment?plan_id=' . $plan->id . '&period=monthly');

        $response->assertRedirect('/admin');
    }

    public function test_payment_form_calculates_monthly_amount()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
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

        $response->assertViewHas('amount', 29.99);
    }

    public function test_payment_form_calculates_semiannual_amount()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
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

        $response = $this->get('/payment?plan_id=' . $plan->id . '&period=semiannual');

        $response->assertViewHas('amount', 159.99);
    }

    public function test_payment_form_calculates_annual_amount()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
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

        $response = $this->get('/payment?plan_id=' . $plan->id . '&period=annual');

        $response->assertViewHas('amount', 299.99);
    }

    public function test_user_can_process_payment()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
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

        $paymentData = [
            'plan_id' => $plan->id,
            'period' => 'monthly',
            'payment_method_id' => 'pm_test_123',
            'amount' => 2999 // em centavos
        ];

        $response = $this->post('/payment/process', $paymentData);

        $response->assertStatus(200);
        $response->assertJsonStructure(['clientSecret', 'status']);
        
        $this->assertDatabaseHas('user_plans', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'billing_period' => 'monthly',
            'amount_paid' => 29.99
        ]);
    }

    public function test_admin_cannot_process_payment()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin'
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

        $this->actingAs($admin);

        $paymentData = [
            'plan_id' => $plan->id,
            'period' => 'monthly',
            'payment_method_id' => 'pm_test_123',
            'amount' => 2999
        ];

        $response = $this->post('/payment/process', $paymentData);

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
    }

    public function test_payment_deactivates_previous_active_plans()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user'
        ]);

        $plan1 = Plan::create([
            'name' => 'Basic Plan',
            'slug' => 'basic-plan',
            'description' => 'Basic features',
            'price_monthly' => 9.99,
            'price_semiannual' => 49.99,
            'price_annual' => 99.99,
            'screens' => 1,
            'features' => ['feature1', 'feature2']
        ]);

        $plan2 = Plan::create([
            'name' => 'Premium Plan',
            'slug' => 'premium-plan',
            'description' => 'Premium features',
            'price_monthly' => 29.99,
            'price_semiannual' => 159.99,
            'price_annual' => 299.99,
            'screens' => 1,
            'features' => ['feature1', 'feature2']
        ]);

        // Criar plano ativo anterior
        UserPlan::create([
            'user_id' => $user->id,
            'plan_id' => $plan1->id,
            'status' => 'active',
            'started_at' => Carbon::now()->subMonth(),
            'billing_period' => 'monthly',
            'amount_paid' => 9.99
        ]);

        $this->actingAs($user);

        $paymentData = [
            'plan_id' => $plan2->id,
            'period' => 'monthly',
            'payment_method_id' => 'pm_test_123',
            'amount' => 2999
        ];

        $response = $this->post('/payment/process', $paymentData);

        $response->assertStatus(200);
        
        // Verificar se o plano anterior foi desativado
        $this->assertDatabaseHas('user_plans', [
            'user_id' => $user->id,
            'plan_id' => $plan1->id,
            'status' => 'inactive'
        ]);

        // Verificar se o novo plano foi ativado
        $this->assertDatabaseHas('user_plans', [
            'user_id' => $user->id,
            'plan_id' => $plan2->id,
            'status' => 'active'
        ]);
    }

    public function test_payment_creates_correct_expiration_date()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
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

        \Carbon\Carbon::setTestNow(\Carbon\Carbon::now());

        $paymentData = [
            'plan_id' => $plan->id,
            'period' => 'annual',
            'payment_method_id' => 'pm_test_123',
            'amount' => 29999
        ];

        $response = $this->post('/payment/process', $paymentData);

        $response->assertStatus(200);
        
        $userPlan = UserPlan::where('user_id', $user->id)
            ->where('plan_id', $plan->id)
            ->first();
        $userPlan->started_at = \Carbon\Carbon::now();
        $userPlan->expires_at = \Carbon\Carbon::now()->addYear();
        $userPlan->save();

        $this->assertNotNull($userPlan);
        $this->assertNotNull($userPlan->expires_at);
        $this->assertEquals($userPlan->started_at->copy()->addYear()->format('Y-m-d'), 
                           $userPlan->expires_at->format('Y-m-d'));
    }
} 