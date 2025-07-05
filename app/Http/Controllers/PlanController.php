<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use App\Models\UserPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::active()->get();
        return view('dashboard.user.plans', compact('plans'));
    }

    public function assignPlan(Request $request)
    {
        if (auth()->user()->role === 'admin') {
            return response()->json(['success' => false, 'message' => 'Admins não podem contratar planos.'], 403);
        }
        try {

            if (!Auth::check()) {
                \Log::error('PlanController::assignPlan - User not authenticated');
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado'
                ], 401);
            }

            $request->validate([
                'plan_id' => 'required|exists:plans,id',
                'period' => 'required|in:monthly,semiannual,annual'
            ]);

            $user = Auth::user();
            $plan = Plan::findOrFail($request->plan_id);

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
                'notes' => "Plano {$plan->name} - Período {$request->period}"
            ]);

            return response()->json([
                'success' => true,
                'message' => "Plano {$plan->name} atribuído com sucesso!",
                'expires_at' => $expiresAt ? $expiresAt->format('d/m/Y H:i') : null,
                'amount_paid' => $amountPaid
            ]);
        } catch (\Exception $e) {
            \Log::error('PlanController::assignPlan - Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atribuir plano: ' . $e->getMessage()
            ], 500);
        }
    }

    public function myPlan()
    {
        if (auth()->user()->role === 'admin') {
            return response()->json(['success' => false, 'message' => 'Admins não possuem planos.'], 403);
        }
        $user = Auth::user();
        $currentPlan = $user->currentPlan;

        return response()->json([
            'has_plan' => $user->hasActivePlan(),
            'plan' => $currentPlan ? $currentPlan->plan : null,
            'user_plan' => $currentPlan,
            'expires_at' => $currentPlan ? $currentPlan->expires_at->format('d/m/Y H:i') : null
        ]);
    }


}
