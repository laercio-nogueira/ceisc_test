<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\StripePaymentController;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class StripePaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock Stripe::setApiKey
        Mockery::mock('overload:Stripe\Stripe')
            ->shouldReceive('setApiKey')->andReturnNull();
    }

    public function test_pay_method_creates_payment_intent()
    {
        $controller = new StripePaymentController();
        $request = new Request();
        $request->merge([
            'payment_method_id' => 'pm_test_123',
            'amount' => 2999
        ]);
        // Mock para sucesso
        Mockery::mock('overload:Stripe\\PaymentIntent')
            ->shouldReceive('create')
            ->andReturn((object)[
                'client_secret' => 'pi_test_secret',
                'status' => 'succeeded'
            ]);
        $amount = 29.99;
        $response = $controller->pay($request, $amount);
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('succeeded', $responseData['status']);
    }

    public function test_pay_web_method_creates_payment_intent()
    {
        $controller = new StripePaymentController();
        $request = new Request();
        $request->merge([
            'payment_method_id' => 'pm_test_123',
            'amount' => 2999
        ]);
        // Mock para sucesso
        Mockery::mock('overload:Stripe\\PaymentIntent')
            ->shouldReceive('create')
            ->andReturn((object)[
                'client_secret' => 'pi_test_secret',
                'status' => 'succeeded'
            ]);
        $response = $controller->payWeb($request, 2999);
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('succeeded', $responseData['status']);
    }

    public function test_pay_method_handles_stripe_errors()
    {
        $controller = new StripePaymentController();
        $request = new Request();
        $request->merge([
            'payment_method_id' => 'pm_test_123',
            'amount' => 2999
        ]);
        // Mock para erro
        Mockery::mock('overload:Stripe\\PaymentIntent')
            ->shouldReceive('create')
            ->andThrow(new \Exception('Stripe error'));
        $amount = 29.99;
        $response = $controller->pay($request, $amount);
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('Erro ao processar pagamento', $responseData['error']);
    }

    public function test_pay_web_method_handles_stripe_errors()
    {
        $controller = new StripePaymentController();
        $request = new Request();
        $request->merge([
            'payment_method_id' => 'pm_test_123',
            'amount' => 2999
        ]);
        // Mock para erro
        Mockery::mock('overload:Stripe\\PaymentIntent')
            ->shouldReceive('create')
            ->andThrow(new \Exception('Stripe error'));
        $response = $controller->payWeb($request, 2999);
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('Erro ao processar pagamento', $responseData['error']);
    }

    public function test_pay_method_converts_amount_to_cents()
    {
        $controller = new StripePaymentController();
        $request = new Request();
        $request->merge([
            'payment_method_id' => 'pm_test_123',
            'amount' => 29.99
        ]);
        // Mock para sucesso
        Mockery::mock('overload:Stripe\\PaymentIntent')
            ->shouldReceive('create')
            ->andReturn((object)[
                'client_secret' => 'pi_test_secret',
                'status' => 'succeeded'
            ]);
        $amount = 29.99;
        $response = $controller->pay($request, $amount);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_pay_web_method_uses_amount_directly()
    {
        $controller = new StripePaymentController();
        $request = new Request();
        $request->merge([
            'payment_method_id' => 'pm_test_123',
            'amount' => 2999
        ]);
        // Mock para sucesso
        Mockery::mock('overload:Stripe\\PaymentIntent')
            ->shouldReceive('create')
            ->andReturn((object)[
                'client_secret' => 'pi_test_secret',
                'status' => 'succeeded'
            ]);
        $response = $controller->payWeb($request, 2999);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_pay_method_logs_successful_payment()
    {
        $controller = new StripePaymentController();
        $request = new Request();
        $request->merge(['payment_method_id' => 'pm_test_123']);
        $amount = 29.99;

        // Mock do PaymentIntent::create
        Mockery::mock('overload:Stripe\\PaymentIntent')
            ->shouldReceive('create')
            ->andReturn((object)[
                'client_secret' => 'pi_test_secret',
                'status' => 'succeeded'
            ]);

        // Mock do Log
        \Log::shouldReceive('info')->once();
        \Log::shouldReceive('error')->zeroOrMoreTimes();

        $response = $controller->pay($request, $amount);
        
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_pay_method_logs_errors()
    {
        $controller = new StripePaymentController();
        $request = new Request();
        $request->merge(['payment_method_id' => 'pm_test_123']);
        $amount = 29.99;

        // Mock do PaymentIntent::create para lançar exceção
        Mockery::mock('overload:Stripe\\PaymentIntent')
            ->shouldReceive('create')
            ->andThrow(new \Exception('Stripe error'));

        // Mock do Log
        \Log::shouldReceive('error')->once();

        $response = $controller->pay($request, $amount);
        
        $this->assertEquals(500, $response->getStatusCode());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
} 