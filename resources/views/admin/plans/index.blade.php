@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Gerenciamento de Planos dos Usuários</h4>
                    <a href="{{ route('admin') }}" class="btn btn-secondary">Voltar ao Admin</a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['total'] }}</h5>
                                    <small>Total de Planos</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['active'] }}</h5>
                                    <small>Planos Ativos</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['expired'] }}</h5>
                                    <small>Planos Expirados</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-secondary text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['inactive'] }}</h5>
                                    <small>Planos Inativos</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Usuário</th>
                                    <th>Plano</th>
                                    <th>Status</th>
                                    <th>Período</th>
                                    <th>Valor Pago</th>
                                    <th>Início</th>
                                    <th>Expiração</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userPlans as $userPlan)
                                <tr>
                                    <td>
                                        <strong>{{ $userPlan->user->name }}</strong><br>
                                        <small class="text-muted">{{ $userPlan->user->email }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $userPlan->plan->name }}</strong><br>
                                        <small class="text-muted">{{ $userPlan->plan->description }}</small>
                                    </td>
                                    <td>
                                        @if($userPlan->status === 'active')
                                            <span class="badge bg-success">Ativo</span>
                                        @elseif($userPlan->status === 'expired')
                                            <span class="badge bg-warning">Expirado</span>
                                        @else
                                            <span class="badge bg-secondary">Inativo</span>
                                        @endif
                                    </td>
                                    <td>{{ ucfirst($userPlan->billing_period) }}</td>
                                    <td>R$ {{ number_format($userPlan->amount_paid, 2, ',', '.') }}</td>
                                    <td>{{ $userPlan->started_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($userPlan->expires_at)
                                            {{ $userPlan->expires_at->format('d/m/Y H:i') }}
                                        @else
                                            <span class="text-muted">Permanente</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.plans.show', $userPlan) }}"
                                               class="btn btn-sm btn-info">Ver</a>
                                            <button type="button"
                                                    class="btn btn-sm btn-warning dropdown-toggle"
                                                    data-bs-toggle="dropdown">
                                                Status
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#"
                                                       onclick="updateStatus({{ $userPlan->id }}, 'active')">Ativo</a></li>
                                                <li><a class="dropdown-item" href="#"
                                                       onclick="updateStatus({{ $userPlan->id }}, 'inactive')">Inativo</a></li>
                                                <li><a class="dropdown-item" href="#"
                                                       onclick="updateStatus({{ $userPlan->id }}, 'expired')">Expirado</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $userPlans->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateStatus(userPlanId, status) {
    if (!confirm('Tem certeza que deseja alterar o status para ' + status + '?')) {
        return;
    }

    fetch(`/admin/plans/${userPlanId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Erro ao atualizar status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao atualizar status');
    });
}
</script>
@endsection
