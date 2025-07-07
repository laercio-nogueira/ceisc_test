<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>TechFlix - Planos</title>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite([
                'resources/css/app.css',
                'resources/css/plans.css',
                'resources/js/app.js'
            ])
        @endif
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Conheça os planos TechFlix</h1>
                <p>Encontre o plano perfeito para suas necessidades</p>
            </div>

            <div class="billing-toggle">
                <button class="active" data-period="monthly">Mensal</button>
                <button data-period="semiannual">Semestral</button>
                <button data-period="annual">Anual</button>
            </div>

            <div class="plans-container">
                @foreach(\App\Models\Plan::active()->get() as $plan)
                    <div class="plan-card {{ $plan->is_popular ? 'featured' : '' }}">
                        @if($plan->is_popular)
                            <div class="plan-badge">Mais Popular</div>
                        @endif

                        <div class="plan-name">{{ $plan->name }}</div>
                        <div class="plan-price">
                            <span class="currency">R$</span>
                            <span class="amount"
                                  data-monthly="{{ $plan->price_monthly }}"
                                  data-semiannual="{{ $plan->price_semiannual }}"
                                  data-annual="{{ $plan->price_annual }}">
                                {{ $plan->price_monthly }}
                            </span>
                            <span class="period">/mês</span>
                        </div>
                        <div class="plan-description {{ $plan->is_popular ? 'plan-description--white' : '' }}">
                            {{ $plan->description }}
                        </div>
                        <ul class="plan-features">
                            @foreach($plan->features as $feature)
                                <li>{{ $feature }}</li>
                            @endforeach
                        </ul>
                        <button class="plan-button" data-plan-id="{{ $plan->id }}">
                            {{ $plan->is_popular ? 'Escolher Plano' : 'Começar Agora' }}
                        </button>
                        <div class="savings-badge" id="plan-{{ $plan->id }}-savings" style="display: none;">
                            Economia de {{ $plan->id == 2 ? '15%' : '17%' }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const billingButtons = document.querySelectorAll('.billing-toggle button');
                const amountElements = document.querySelectorAll('.amount');
                const periodElements = document.querySelectorAll('.period');
                const savingsBadges = document.querySelectorAll('.savings-badge');

                function updatePricing(period) {
                    amountElements.forEach(element => {
                        const amount = element.getAttribute(`data-${period}`);
                        element.textContent = amount;
                    });

                    periodElements.forEach(element => {
                        if (period === 'monthly') {
                            element.textContent = '/mês';
                        } else if (period === 'semiannual') {
                            element.textContent = '/semestre';
                        } else if (period === 'annual') {
                            element.textContent = '/ano';
                        }
                    });

                    savingsBadges.forEach(badge => {
                        if (period === 'semiannual' || period === 'annual') {
                            badge.style.display = 'inline-block';
                        } else {
                            badge.style.display = 'none';
                        }
                    });
                }

                billingButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        billingButtons.forEach(btn => btn.classList.remove('active'));

                        this.classList.add('active');

                        const period = this.getAttribute('data-period');
                        updatePricing(period);
                    });
                });

                const planCards = document.querySelectorAll('.plan-card');
                planCards.forEach(card => {
                    card.addEventListener('mouseenter', function() {
                        this.style.transform = this.classList.contains('featured')
                            ? 'scale(1.05) translateY(-10px)'
                            : 'translateY(-10px)';
                    });

                    card.addEventListener('mouseleave', function() {
                        this.style.transform = this.classList.contains('featured')
                            ? 'scale(1.05)'
                            : 'translateY(0)';
                    });
                });

                const planButtons = document.querySelectorAll('.plan-button');
                planButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const planCard = this.closest('.plan-card');
                        const planId = this.getAttribute('data-plan-id');
                        const activePeriod = document.querySelector('.billing-toggle button.active').getAttribute('data-period');

                        window.location.href = '{{ route('payment.form') }}'
                            + '?plan_id=' + encodeURIComponent(planId)
                            + '&period=' + encodeURIComponent(activePeriod);
                    });
                });
            });
        </script>
    </body>
</html>
