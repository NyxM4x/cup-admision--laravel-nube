@extends('layouts.base')
@section('titulo', 'Seleccionar método de pago — CUP')
@section('contenido')

<div class="page-header mb-4">
    <h1><i class="bi bi-credit-card me-2"></i>Pago de Inscripción</h1>
    <p class="page-subtitle">Curso Preuniversitario — FICCT UAGRM</p>
</div>

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="panel-cup mb-4" style="max-width:600px">
    <div class="panel-cup-header">
        <strong>Detalle del pago</strong>
    </div>
    <div class="panel-cup-body">
        <table class="table table-sm mb-0">
            <tr>
                <td class="text-muted">Postulante:</td>
                <td><strong>{{ $inscripcion->postulante->persona->nombre }}</strong></td>
            </tr>
            <tr>
                <td class="text-muted">Concepto:</td>
                <td>Inscripción Curso Preuniversitario</td>
            </tr>
            <tr>
                <td class="text-muted">Monto:</td>
                <td><strong class="text-success fs-5">Bs. {{ number_format($monto, 2) }}</strong></td>
            </tr>
        </table>
    </div>
</div>

<h5 class="mb-3">Selecciona tu método de pago:</h5>

<div class="row g-3" style="max-width:600px">
    <div class="col-md-6">
        <a href="{{ route('pagos.stripe', $inscripcion) }}"
           class="d-flex flex-column align-items-center justify-content-center p-4 text-decoration-none rounded border h-100"
           style="background:#f8f9fb;border-color:#635bff!important;min-height:140px">
            <img src="https://upload.wikimedia.org/wikipedia/commons/b/ba/Stripe_Logo%2C_revised_2016.svg"
                 alt="Stripe" style="height:35px;margin-bottom:12px">
            <span class="text-dark fw-semibold">Tarjeta débito/crédito</span>
            <small class="text-muted mt-1">Visa, Mastercard, Amex</small>
        </a>
    </div>
    <div class="col-md-6">
        <a href="{{ route('pagos.paypal.crear', $inscripcion) }}"
           class="d-flex flex-column align-items-center justify-content-center p-4 text-decoration-none rounded border h-100"
           style="background:#f8f9fb;border-color:#0070ba!important;min-height:140px">
            <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg"
                 alt="PayPal" style="height:35px;margin-bottom:12px">
            <span class="text-dark fw-semibold">PayPal</span>
            <small class="text-muted mt-1">Cuenta PayPal o tarjeta</small>
        </a>
    </div>
</div>

@endsection