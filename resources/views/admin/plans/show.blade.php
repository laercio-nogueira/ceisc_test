@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Detalhes do Plano</h4>
                    <a href="{{ route('admin.plans.index') }}" class="btn btn-secondary">Voltar</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Informações do Usuário</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Nome:</strong></td>
                                    <td>{{ $userPlan->user->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $userPlan->user->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Registrado em:</strong></td>
                                    <td>{{ $userPlan->user->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Informações do Plano</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Plano:</strong></td>
                                    <td>{{ $userPlan->plan->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Descrição:</strong></td>
                                    <td>{{ $userPlan->plan->description }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Telas:</strong></td>
                                    <td>{{ $userPlan->plan->screens }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h5>Detalhes da Assinatura</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($userPlan->status === 'active')
                                            <span class="badge bg-success">Ativo</span>
                                        @elseif($userPlan->status === 'expired')
                                            <span class="badge bg-warning">Expirado</span>
                                        @else
                                            <span class="badge bg-secondary">Inativo</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Período:</strong></td>
                                    <td>{{ ucfirst($userPlan->billing_period) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Valor Pago:</strong></td>
                                    <td>R$ {{ number_format($userPlan->amount_paid, 2, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Início:</strong></td>
                                    <td>{{ $userPlan->started_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Expiração:</strong></td>
                                    <td>
                                        @if($userPlan->expires_at)
                                            {{ $userPlan->expires_at->format('d/m/Y H:i') }}
                                            @if($userPlan->expires_at->isPast())
                                                <span class="badge bg-danger ms-2">Expirado</span>
                                            @else
                                                <span class="badge bg-success ms-2">Válido</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Permanente</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Recursos do Plano</h5>
                            <ul class="list-group list-group-flush">
                                @foreach($userPlan->plan->features as $feature)
                                    <li class="list-group-item">
                                        <i class="text-success">✓</i> {{ $feature }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    @if($userPlan->notes)
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h5>Observações</h5>
                            <p class="text-muted">{{ $userPlan->notes }}</p>
                        </div>
                    </div>
                    @endif

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <h5>Alterar Status</h5>
                            <div class="btn-group" role="group">
                                <button type="button"
                                        class="btn btn-success"
                                        onclick="updateStatus('{{ $userPlan->id }}', 'active')"
                                        {{ $userPlan->status === 'active' ? 'disabled' : '' }}>
                                    Ativar
                                </button>
                                <button type="button"
                                        class="btn btn-secondary"
                                        onclick="updateStatus('{{ $userPlan->id }}', 'inactive')"
                                        {{ $userPlan->status === 'inactive' ? 'disabled' : '' }}>
                                    Desativar
                                </button>
                                <button type="button"
                                        class="btn btn-warning"
                                        onclick="updateStatus('{{ $userPlan->id }}', 'expired')"
                                        {{ $userPlan->status === 'expired' ? 'disabled' : '' }}>
                                    Marcar como Expirado
                                </button>
                            </div>
                        </div>
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
