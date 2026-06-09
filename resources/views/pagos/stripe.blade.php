@extends('layouts.base')
@section('titulo', 'Pago con tarjeta — Stripe')
@section('contenido')

<div class="container mt-4" style="max-width:520px">
    <h2 class="mb-1"><i class="bi bi-credit-card me-2"></i>Pago con Tarjeta</h2>
    <p class="text-muted">Monto a pagar: <strong class="text-success">Bs. 50.00</strong></p>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="panel-cup">
        <div class="panel-cup-header">
            <strong>Datos de la tarjeta</strong>
            <img src="https://upload.wikimedia.org/wikipedia/commons/b/ba/Stripe_Logo%2C_revised_2016.svg"
                 alt="Stripe" style="height:20px;float:right">
        </div>
        <div class="panel-cup-body">
            <form id="payment-form">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Número de tarjeta</label>
                    <div id="card-element" class="form-control" style="padding:10px;height:42px"></div>
                    <div id="card-errors" class="text-danger small mt-1"></div>
                </div>

                <div class="alert alert-info small">
                    <strong>Tarjeta de prueba:</strong> 4242 4242 4242 4242 | Exp: cualquier fecha futura | CVV: cualquier 3 dígitos
                </div>

                <button id="submit-btn" type="submit" class="btn btn-primary w-100">
                    <span id="btn-text">💳 Pagar Bs. 50.00</span>
                    <span id="btn-spinner" class="d-none">
                        <span class="spinner-border spinner-border-sm me-2"></span>Procesando...
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
const stripe = Stripe('{{ config("pagos.stripe.key") }}');
const elements = stripe.elements();
const cardElement = elements.create('card', {
    style: {
        base: { fontSize: '16px', color: '#424770' }
    }
});
cardElement.mount('#card-element');

cardElement.on('change', ({error}) => {
    document.getElementById('card-errors').textContent = error ? error.message : '';
});

const form = document.getElementById('payment-form');
form.addEventListener('submit', async (e) => {
    e.preventDefault();

    document.getElementById('btn-text').classList.add('d-none');
    document.getElementById('btn-spinner').classList.remove('d-none');
    document.getElementById('submit-btn').disabled = true;

    const {paymentIntent, error} = await stripe.confirmCardPayment(
        '{{ $datos["client_secret"] }}',
        { payment_method: { card: cardElement } }
    );

    if (error) {
        document.getElementById('card-errors').textContent = error.message;
        document.getElementById('btn-text').classList.remove('d-none');
        document.getElementById('btn-spinner').classList.add('d-none');
        document.getElementById('submit-btn').disabled = false;
    } else if (paymentIntent.status === 'succeeded') {
        // Enviar confirmación al servidor
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("pagos.stripe.confirmar", $inscripcion) }}';

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';

        const pi = document.createElement('input');
        pi.type = 'hidden';
        pi.name = 'payment_intent';
        pi.value = paymentIntent.id;

        form.appendChild(csrf);
        form.appendChild(pi);
        document.body.appendChild(form);
        form.submit();
    }
});
</script>

@endsection