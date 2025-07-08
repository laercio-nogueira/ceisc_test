<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserPlan;
use App\Models\Plan;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        if (app()->environment('testing')) { \Carbon\Carbon::setTestNow('2023-01-01 00:00:00'); }
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

        $months = [];
        $start = now()->subMonths(11);
        for ($i = 0; $i < 12; $i++) {
            $date = $start->copy()->addMonths($i);
            $months[] = [
                'year' => $date->format('Y'),
                'month' => $date->format('m'),
                'total' => 0
            ];
        }
        $statsRaw = UserPlan::select(
            \DB::raw("EXTRACT(YEAR FROM started_at) as year"),
            \DB::raw("EXTRACT(MONTH FROM started_at) as month"),
            \DB::raw('COUNT(*) as total')
        )
        ->where('started_at', '>=', now()->subYear())
        ->groupByRaw('EXTRACT(YEAR FROM started_at), EXTRACT(MONTH FROM started_at)')
        ->orderByRaw('EXTRACT(YEAR FROM started_at)')
        ->orderByRaw('EXTRACT(MONTH FROM started_at)')
        ->get();
        foreach (
            $months as &$month) {
            foreach ($statsRaw as $stat) {
                if ($month['year'] == $stat->year && $month['month'] == $stat->month) {
                    $month['total'] = $stat->total ?? 0;
                }
            }
            if (!isset($month['total'])) {
                $month['total'] = 0;
            }
            $monthStart = $month['year'] . '-' . $month['month'] . '-01 00:00:00';
            $monthEnd = date('Y-m-t 23:59:59', strtotime($monthStart));
            $month['active'] = UserPlan::where('status', 'active')
                ->where('started_at', '>=', $monthStart)
                ->where('started_at', '<=', $monthEnd)
                ->count();
            $month['inactive'] = UserPlan::where('status', 'inactive')
                ->where('started_at', '>=', $monthStart)
                ->where('started_at', '<=', $monthEnd)
                ->count();
        }
        $stats = collect(array_map(function($m) { return (object)$m; }, $months));

        $allPlans = Plan::all();
        $planStats = [];
        foreach ($allPlans as $plan) {
            $count = UserPlan::where('plan_id', $plan->id)
                ->where('status', 'active')
                ->where('started_at', '>=', now()->subYear())
                ->count();
            $planStats[] = (object)[
                'plan_id' => $plan->id,
                'name' => $plan->name,
                'total' => $count
            ];
        }

        $recentUserPlans = UserPlan::latest()->take(5)->get();
        $planStats = $usersByPlan;

        return view('dashboard.admin.admin', compact(
            'totalUsers', 'adminCount', 'userCount', 'recentUsers',
            'activeUsers', 'inactiveUsers', 'activePlans', 'inactivePlans', 'expiredPlans',
            'totalRevenue', 'usersByPlan', 'stats', 'recentUserPlans', 'planStats'
        ));
    }
}
