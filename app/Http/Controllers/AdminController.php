<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserPlan;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $totalUsers = User::count();
        $adminCount = User::where('role', 'admin')->count();
        $userCount = User::where('role', 'user')->count();
        $recentUsers = User::where('role', 'user')->latest()->take(5)->get();
        $activeUsers = User::where('role', 'user')->where('active', true)->count();
        $inactiveUsers = User::where('role', 'user')->where('active', false)->count();
        $activePlans = UserPlan::where('status', 'active')->count();
        $inactivePlans = UserPlan::where('status', 'inactive')->count();
        $expiredPlans = UserPlan::where('status', 'expired')->count();

        $totalRevenue = UserPlan::where('status', 'active')->sum('amount_paid');

        $usersByPlan = \App\Models\Plan::leftJoin('user_plans', function($join) {
                $join->on('plans.id', '=', 'user_plans.plan_id')
                     ->where('user_plans.status', 'active');
            })
            ->selectRaw('plans.id as plan_id, plans.name, plans.description,
                        COALESCE(COUNT(user_plans.id), 0) as user_count,
                        COALESCE(SUM(user_plans.amount_paid), 0) as total_amount')
            ->groupBy('plans.id', 'plans.name', 'plans.description')
            ->get();

        $plansByMonth = UserPlan::selectRaw('EXTRACT(YEAR FROM started_at) as year, EXTRACT(MONTH FROM started_at) as month, COUNT(*) as total')
            ->where('status', 'active')
            ->where('started_at', '>=', now()->subMonths(12))
            ->groupByRaw('EXTRACT(YEAR FROM started_at), EXTRACT(MONTH FROM started_at)')
            ->orderByRaw('EXTRACT(YEAR FROM started_at), EXTRACT(MONTH FROM started_at)')
            ->get();

        return view('dashboard.admin.admin', compact(
            'totalUsers', 'adminCount', 'userCount', 'recentUsers',
            'activeUsers', 'inactiveUsers', 'activePlans', 'inactivePlans', 'expiredPlans',
            'totalRevenue', 'usersByPlan', 'plansByMonth'
        ));
    }
}
