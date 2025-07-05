<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\UserPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'period' => 'required|in:monthly,semiannual,annual',
            'card_number' => 'required|string|min:13|max:19',
            'card_holder' => 'required|string|max:255',
            'card_expiry' => 'required|string|regex:/^\d{2}\/\d{2}$/',
            'card_cvv' => 'required|string|min:3|max:4',
        ]);

        $user = Auth::user();
        $plan = Plan::findOrFail($request->plan_id);

        $paymentSuccess = $this->simulatePayment($request);

        if ($paymentSuccess) {
            $user->userPlans()
                 ->where('status', 'active')
                 ->update(['status' => 'inactive']);

            $expiresAt = null;
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

            return response()->json([
                'success' => true,
                'message' => "Pagamento aprovado! Plano {$plan->name} ativado com sucesso.",
                'redirect_url' => route('dashboard')
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Pagamento recusado. Verifique os dados do cartão e tente novamente.'
            ], 400);
        }
    }

    private function simulatePayment(Request $request)
    {
        // Simulação simples de pagamento
        // Em produção, aqui você faria a integração real com gateway de pagamento

        // Validar formato do cartão (Luhn algorithm básico)
        $cardNumber = preg_replace('/\D/', '', $request->card_number);
        if (strlen($cardNumber) < 13 || strlen($cardNumber) > 19) {
            return false;
        }

        // Validar data de expiração
        $expiry = explode('/', $request->card_expiry);
        $month = (int)$expiry[0];
        $year = (int)$expiry[1];

        if ($month < 1 || $month > 12) {
            return false;
        }

        $currentYear = (int)date('y');
        $currentMonth = (int)date('m');

        if ($year < $currentYear || ($year == $currentYear && $month < $currentMonth)) {
            return false;
        }

        // Simular 95% de sucesso (para teste)
        return rand(1, 100) <= 95;
    }
}
