<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\UserPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Controllers\StripePaymentController;

class PaymentController extends Controller
{
    public function showPaymentForm(Request $request)
    {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin');
        }
        $planId = $request->plan_id;
        $period = $request->period;

        $plan = Plan::findOrFail($planId);

        $amount = 0;
        switch ($period) {
            case 'monthly':
                $amount = $plan->price_monthly;
                break;
            case 'semiannual':
                $amount = $plan->price_semiannual;
                break;
            case 'annual':
                $amount = $plan->price_annual;
                break;
        }

        return view('payment.form', compact('plan', 'period', 'amount'));
    }

    public function processPayment(Request $request)
    {
        if (auth()->user()->role === 'admin') {
            return response()->json(['success' => false, 'message' => 'Admins não podem contratar planos.'], 403);
        }

        $user = Auth::user();
        $plan = Plan::findOrFail($request->plan_id);
        $amountPaid = 0;

        switch ($request->period) {
            case 'monthly':
                $expiresAt = Carbon::now()->addMonth();
                $amountPaid = $plan->price_monthly;
                break;
            case 'semiannual':
                $expiresAt = Carbon::now()->addMonths(6);
                $amountPaid = $plan->price_semiannual;
                break;
            case 'annual':
                $expiresAt = Carbon::now()->addYear();
                $amountPaid = $plan->price_annual;
                break;
        }

        $paymentSuccess = $this->simulatePayment($request, $amountPaid);

        if ($paymentSuccess) {
            $expiresAt = null;
            $user->userPlans()
                 ->where('status', 'active')
                 ->update(['status' => 'inactive']);

            UserPlan::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => 'active',
                'started_at' => Carbon::now(),
                'expires_at' => $expiresAt,
                'billing_period' => $request->period,
                'amount_paid' => $amountPaid,
                'notes' => "Plano {$plan->name} - Período {$request->period} - Pagamento aprovado"
            ]);

            return $paymentSuccess;
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Pagamento recusado. Verifique os dados do cartão e tente novamente.'
            ], 400);
        }
    }

    private function simulatePayment(Request $request, $amountPaid)
    {
        try {
            $stripePaymentController = new StripePaymentController();
            $response = $stripePaymentController->payWeb($request, $amountPaid);
            $responseData = json_decode($response->getContent(), true);
            return $responseData;
        } catch (\Exception $e) {
            return false;
        }
    }
}
