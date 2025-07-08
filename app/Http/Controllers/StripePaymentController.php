<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripePaymentController extends Controller
{
    public function payWeb(Request $request, $amount)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $paymentMethodId = $request->payment_method_id;

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => intval($amount * 100),
                'currency' => 'brl',
                'payment_method' => $paymentMethodId,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never'
                ],
                'confirm' => true,
            ]);

            \Log::info('Stripe PaymentIntent:', [
                'amount' => $amount,
                'payment_method_id' => $paymentMethodId,
                'status' => $paymentIntent->status,
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'status' => $paymentIntent->status,
            ]);

        } catch (\Exception $e) {
            \Log::error('Stripe Payment Error:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erro ao processar pagamento'], 500);
        }
    }

    public function pay(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $paymentMethodId = $request->payment_method_id;
        $amount = $request->amount;

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => intval($amount * 100),
                'currency' => 'brl',
                'payment_method' => $paymentMethodId,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never'
                ],
                'confirm' => true,
            ]);

            \Log::info('Stripe PaymentIntent:', [
                'amount' => $amount,
                'payment_method_id' => $paymentMethodId,
                'status' => $paymentIntent->status,
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'status' => $paymentIntent->status,
            ]);

        } catch (\Exception $e) {
            \Log::error('Stripe Payment Error:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erro ao processar pagamento'], 500);
        }
    }
}
