<div class="card shadow">
    <div class="card-header">
        <h4>Dashboard</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Informações do Usuário</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Nome:</strong> {{ Auth::user()->name }}</li>
                            <li class="list-group-item"><strong>E-mail:</strong> {{ Auth::user()->email }}</li>
                            <li class="list-group-item"><strong>Conta criada em:</strong> {{ Auth::user()->created_at->format('d/m/Y H:i') }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Seu Plano</h5>
                        @if(Auth::user()->hasActivePlan())
                            @php
                                $currentPlan = Auth::user()->currentPlan;
                            @endphp
                            <div class="alert alert-success">
                                <h6>{{ $currentPlan->plan->name }}</h6>
                                <p class="mb-1">{{ $currentPlan->plan->description }}</p>
                                <small>
                                    <strong>Status:</strong>
                                    <span class="badge bg-success">{{ ucfirst($currentPlan->status) }}</span><br>
                                    <strong>Telas:</strong> {{ $currentPlan->plan->screens }}<br>
                                    <strong>Período:</strong> {{ ucfirst($currentPlan->billing_period) }}<br>
                                    <strong>Valor pago:</strong> R$ {{ number_format($currentPlan->amount_paid, 2, ',', '.') }}<br>
                                    @if($currentPlan->expires_at)
                                        <strong>Expira em:</strong> {{ $currentPlan->expires_at->format('d/m/Y H:i') }}
                                    @else
                                        <strong>Plano permanente</strong>
                                    @endif
                                </small>
                            </div>
                            <h6>Recursos do seu plano:</h6>
                            <ul class="list-group list-group-flush">
                                @foreach($currentPlan->plan->features as $feature)
                                    <li class="list-group-item"><i class="text-success">✓</i> {{ $feature }}</li>
                                @endforeach
                            </ul>
                        @else
                            <div class="alert alert-warning">
                                <p>Você ainda não possui um plano ativo.</p>
                                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm">Ver Planos</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const copyToken = () => {
        const tokenInput = document.getElementById('accessToken');
        tokenInput.select();
        document.execCommand('copy');
        alert('Token copiado para a área de transferência!');
    }
</script>
