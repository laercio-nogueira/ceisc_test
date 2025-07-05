<?php

namespace App\Http\Controllers;

use App\Models\UserPlan;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminPlanController extends Controller
{
    public function index()
    {
        $userPlans = UserPlan::with(['user', 'plan'])
                             ->orderBy('created_at', 'desc')
                             ->paginate(20);

        $stats = [
            'total' => UserPlan::count(),
            'active' => UserPlan::where('status', 'active')->count(),
            'expired' => UserPlan::where('status', 'expired')->count(),
            'inactive' => UserPlan::where('status', 'inactive')->count(),
        ];

        return view('admin.plans.index', compact('userPlans', 'stats'));
    }

    public function show(UserPlan $userPlan)
    {
        $userPlan->load(['user', 'plan']);
        return view('admin.plans.show', compact('userPlan'));
    }

    public function updateStatus(Request $request, UserPlan $userPlan)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,expired'
        ]);

        $userPlan->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status atualizado com sucesso!'
        ]);
    }

    public function userPlans(User $user)
    {
        $userPlans = $user->userPlans()->with('plan')->orderBy('created_at', 'desc')->get();
        return view('admin.plans.user-plans', compact('user', 'userPlans'));
    }
}
