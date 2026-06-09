@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Seleccionar método de pago</h1>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h3>Stripe</h3>
                    <p>Paga con tarjeta de crédito/débito</p>
                    <a href="{{ route('pagos.stripe', $inscripcion) }}" class="btn btn-primary">
                        Pagar con Stripe
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h3>PayPal</h3>
                    <p>Paga con tu cuenta PayPal</p>
                    <a href="{{ route('pagos.paypal.crear', $inscripcion) }}" class="btn btn-info">
                        Pagar con PayPal
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection