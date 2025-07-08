@extends('layouts.app')

@section('title', 'Área Admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Usuários</h5>
                    <h2>{{ $totalUsers }}</h2>
                    <small>Total</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Ativos</h5>
                    <h2>{{ $activeUsers }}</h2>
                    <small>Usuários Ativos</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-secondary mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Inativos</h5>
                    <h2>{{ $inactiveUsers }}</h2>
                    <small>Usuários Inativos</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Admins</h5>
                    <h2>{{ $adminCount }}</h2>
                    <small>Administradores</small>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Planos Ativos</h5>
                    <h2>{{ $activePlans }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-secondary mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Planos Inativos</h5>
                    <h2>{{ $inactivePlans }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Planos Expirados</h5>
                    <h2>{{ $expiredPlans }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Receita Total</h5>
                    <h2>R$ {{ number_format($totalRevenue, 2, ',', '.') }}</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Planos Ativos por Mês (últimos 12 meses)</h5>
                    <canvas id="plansByMonthChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Usuários por Plano</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Plano</th>
                                    <th>Usuários Ativos</th>
                                    <th>Receita Total</th>
                                    <th>Receita Média</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($usersByPlan->count() > 0)
                                    @foreach($usersByPlan as $planData)
                                    <tr>
                                        <td>
                                            <strong>{{ $planData->name }}</strong><br>
                                            <small class="text-muted">{{ $planData->description }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $planData->user_count }}</span>
                                        </td>
                                        <td>
                                            <strong>R$ {{ number_format($planData->total_amount, 2, ',', '.') }}</strong>
                                        </td>
                                        <td>
                                            @if($planData->user_count > 0)
                                                R$ {{ number_format($planData->total_amount / $planData->user_count, 2, ',', '.') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Nenhum plano ativo encontrado
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Usuários Recentes</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Status</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentUsers as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->active ?? true)
                                        <span class="badge bg-success">Ativo</span>
                                    @else
                                        <span class="badge bg-secondary">Inativo</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->role !== 'admin')
                                        @if($user->active ?? true)
                                            <button class="btn btn-sm btn-danger">Inativar</button>
                                        @else
                                            <button class="btn btn-sm btn-success">Ativar</button>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        Planos de Usuários Recentes
                        <a href="/admin/plans/" class="btn-link float-end">Gerenciar</a>
                    </h5>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Usuário</th>
                                <th>Plano</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(\App\Models\UserPlan::with('user', 'plan')->latest()->take(5)->get() as $userPlan)
                            <tr>
                                <td>{{ $userPlan->user->name ?? '-' }}</td>
                                <td>{{ $userPlan->plan->name ?? '-' }}</td>
                                <td>
                                    @if($userPlan->status === 'active')
                                        <span class="badge bg-success">Ativo</span>
                                    @elseif($userPlan->status === 'inactive')
                                        <span class="badge bg-secondary">Inativo</span>
                                    @else
                                        <span class="badge bg-warning">Expirado</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('plansByMonthChart').getContext('2d');
        const data = {
            labels: [
                @foreach($plansByMonth as $item)
                    '{{ str_pad($item->month, 2, '0', STR_PAD_LEFT) }}/{{ $item->year }}',
                @endforeach
            ],
            datasets: [{
                label: 'Planos Ativos',
                data: [
                    @foreach($plansByMonth as $item)
                        {{ $item->total }},
                    @endforeach
                ],
                backgroundColor: 'rgba(102, 126, 234, 0.5)',
                borderColor: 'rgba(102, 126, 234, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        };
        new Chart(ctx, {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    });
</script>
@endsection
