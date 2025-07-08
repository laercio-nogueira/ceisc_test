<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Pagamento - TechFlix</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .payment-container {
            max-width: 500px;
            margin: 50px auto;
        }

        .payment-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .payment-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .payment-body {
            padding: 40px;
        }

        .plan-summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .card-input-group {
            position: relative;
        }

        .card-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 20px;
        }

        .btn-pay {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 15px 30px;
            font-size: 18px;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-pay:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-pay:disabled {
            opacity: 0.7;
            transform: none;
        }

        .security-badge {
            text-align: center;
            margin-top: 20px;
            color: #6c757d;
            font-size: 14px;
        }

        .security-badge i {
            color: #28a745;
            margin-right: 5px;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: white;
            text-decoration: none;
            font-weight: 500;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-container">
            <div class="payment-card">
                <div class="payment-header">
                    <h2><i class="fas fa-credit-card me-2"></i>Pagamento Seguro</h2>
                    <p class="mb-0">Complete sua assinatura</p>
                </div>

                <div class="payment-body">
                    <div class="plan-summary">
                        <h5><i class="fas fa-check-circle text-success me-2"></i>Resumo da Assinatura</h5>
                        <div class="row">
                            <div class="col-6">
                                <strong>Plano:</strong><br>
                                <span class="text-primary">{{ $plan->name }}</span>
                            </div>
                            <div class="col-6">
                                <strong>Período:</strong><br>
                                <span class="text-primary">{{ ucfirst($period) }}</span>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Total a pagar:</h4>
                            <h3 class="text-success mb-0">R$ {{ number_format($amount, 2, ',', '.') }}</h3>
                        </div>
                    </div>

                    <form id="paymentForm">
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                        <input type="hidden" name="period" value="{{ $period }}">
                        <input type="hidden" id="amount" value="{{ intval($amount * 100) }}">
                        <div class="form-group">
                            <label class="form-label">Cartão de Crédito</label>
                            <div id="card-element" class="form-control"></div>
                        </div>
                        <button type="submit" class="btn btn-pay" id="payButton">
                            <i class="fas fa-lock me-2"></i>Pagar R$ {{ number_format($amount, 2, ',', '.') }}
                        </button>
                    </form>
                    <div id="payment-message" class="mt-3 text-center"></div>

                    <div class="security-badge">
                        <i class="fas fa-shield-alt"></i>
                        Pagamento seguro com criptografia SSL
                    </div>
                </div>
            </div>

            <div class="back-link">
                <a href="{{ route('dashboard') }}">
                    <i class="fas fa-arrow-left me-2"></i>Voltar ao Dashboard
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://js.stripe.com/v3/"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stripe = Stripe('{{ env('STRIPE_KEY') }}');
            const elements = stripe.elements();
            const card = elements.create('card');
            card.mount('#card-element');

            const form = document.getElementById('paymentForm');
            const payButton = document.getElementById('payButton');
            const paymentMessage = document.getElementById('payment-message');
            const amount = document.getElementById('amount').value;

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                payButton.disabled = true;
                payButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processando...';
                paymentMessage.textContent = '';

                const {paymentMethod, error} = await stripe.createPaymentMethod({
                    type: 'card',
                    card: card,
                });

                if (error) {
                    paymentMessage.textContent = error.message;
                    payButton.disabled = false;
                    payButton.innerHTML = '<i class="fas fa-lock me-2"></i>Pagar R$ {{ number_format($amount, 2, ",", ".") }}';
                    return;
                }

                fetch('/payment/process', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        payment_method_id: paymentMethod.id,
                        plan_id: '{{ $plan->id }}',
                        period: '{{ $period }}',
                        amount: amount
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'succeeded') {
                        paymentMessage.textContent = 'Pagamento aprovado!';
                        setTimeout(() => window.location.href = '{{ route('dashboard') }}', 1500);
                    } else {
                        paymentMessage.textContent = data.error || 'Pagamento recusado!';
                        payButton.disabled = false;
                        payButton.innerHTML = '<i class="fas fa-lock me-2"></i>Pagar R$ {{ number_format($amount, 2, ",", ".") }}';
                    }
                })
                .catch(error => {
                    paymentMessage.textContent = 'Erro ao processar pagamento. Tente novamente.';
                    payButton.disabled = false;
                    payButton.innerHTML = '<i class="fas fa-lock me-2"></i>Pagar R$ {{ number_format($amount, 2, ",", ".") }}';
                });
            });
        });
    </script>
</body>
</html>
