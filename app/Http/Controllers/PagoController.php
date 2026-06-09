<?php
namespace App\Http\Controllers;

use App\Domain\Pagos\UseCases\AprobarPagoUseCase;
use App\Domain\Pagos\UseCases\ProcesarPayPalUseCase;
use App\Domain\Pagos\UseCases\ProcesarStripeUseCase;
use App\Domain\Pagos\UseCases\RechazarPagoUseCase;
use App\Models\Inscripcion;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PagoController extends Controller
{
    public function __construct(
        private AprobarPagoUseCase    $aprobarUseCase,
        private RechazarPagoUseCase   $rechazarUseCase,
        private ProcesarStripeUseCase $stripeUseCase,
        private ProcesarPayPalUseCase $paypalUseCase,
    ) {}

    public function seleccionarMetodo(Inscripcion $inscripcion)
    {
        $pago = $inscripcion->pago;
        if ($pago && $pago->estado === 'aprobado') {
            return redirect()->route('pagos.estado', $inscripcion)
                ->with('info', 'Tu pago ya fue aprobado.');
        }
        return view('pagos.seleccionar', compact('inscripcion'));
    }

    public function stripeForm(Inscripcion $inscripcion)
    {
        $datos = $this->stripeUseCase->crearIntento($inscripcion);
        return view('pagos.stripe', compact('inscripcion', 'datos'));
    }

    public function stripeConfirmar(Request $request, Inscripcion $inscripcion)
    {
        $paymentIntentId = $request->input('payment_intent');
        $exito = $this->stripeUseCase->confirmar($paymentIntentId, $inscripcion);
        if ($exito) {
            return redirect()->route('pagos.estado', $inscripcion)
                ->with('success', 'Pago recibido. El administrador lo confirmara en breve.');
        }
        return redirect()->route('pagos.stripe', $inscripcion)
            ->with('error', 'El pago no se completo. Intenta nuevamente.');
    }

    public function paypalCrear(Inscripcion $inscripcion)
    {
        $datos = $this->paypalUseCase->crearOrden($inscripcion);
        if (!$datos['approve_url']) {
            $error = $datos['error'] ?? 'No se pudo conectar con PayPal.';
            return redirect()->route('pagos.seleccionar', $inscripcion)
                ->with('error', $error);
        }
        session(['paypal_order_id' => $datos['order_id']]);
        session(['pago_inscripcion_id' => $inscripcion->id]);
        return redirect($datos['approve_url']);
    }

    public function paypalExito(Request $request, Inscripcion $inscripcion)
    {
        $orderId = $request->input('token') ?? session('paypal_token');
        if (!$orderId) {
            return redirect()->route('pagos.seleccionar', $inscripcion)
                ->with('error', 'No se encontro informacion del pago PayPal.');
        }
        $exito = $this->paypalUseCase->capturarOrden($orderId, $inscripcion);
        if ($exito) {
            return redirect()->route('pagos.estado', $inscripcion)
                ->with('success', 'Pago PayPal recibido. El administrador lo confirmara en breve.');
        }
        return redirect()->route('pagos.seleccionar', $inscripcion)
            ->with('error', 'No se pudo capturar el pago PayPal.');
    }

    public function paypalCancelar(Inscripcion $inscripcion)
    {
        return redirect()->route('pagos.seleccionar', $inscripcion)
            ->with('error', 'Pago PayPal cancelado.');
    }

    public function paypalCancelarGeneral(Request $request)
    {
        return redirect()->route('login')
            ->with('error', 'Pago PayPal cancelado por el usuario.');
    }

    public function paypalRetorno(Request $request)
    {
        $token = $request->query('token');
        $inscripcionId = session('pago_inscripcion_id');
        if (!$inscripcionId) {
            return redirect()->route('login')
                ->with('error', 'No se pudo identificar la inscripcion para este pago.');
        }
        $inscripcion = Inscripcion::find($inscripcionId);
        if (!$inscripcion) {
            return redirect()->route('login')
                ->with('error', 'Inscripcion no encontrada.');
        }
        $exito = $this->paypalUseCase->capturarOrden($token, $inscripcion);
        if ($exito) {
            return redirect()->route('pagos.estado', $inscripcion)
                ->with('success', 'Pago PayPal recibido correctamente.');
        }
        return redirect()->route('pagos.seleccionar', $inscripcion)
            ->with('error', 'No se pudo capturar el pago PayPal.');
    }

    public function estadoPago(Inscripcion $inscripcion)
    {
        $pago = $inscripcion->pago;
        return view('pagos.estado', compact('inscripcion', 'pago'));
    }

    public function panelAdmin()
    {
        $pagos = Pago::with([
                'inscripcion.postulante.persona',
                'inscripcion.postulacionCarreras.carrera',
            ])
            ->where('estado', 'pendiente')
            ->orderBy('created_at', 'asc')
            ->get();

        $pagosResueltos = Pago::with([
                'inscripcion.postulante.persona',
                'revisor',
            ])
            ->whereIn('estado', ['aprobado', 'rechazado'])
            ->orderBy('updated_at', 'desc')
            ->take(30)
            ->get();

        return view('pagos.admin', compact('pagos', 'pagosResueltos'));
    }

    public function aprobar(Pago $pago)
    {
        $resultado = $this->aprobarUseCase->ejecutar($pago, Auth::id());
        return back()->with('success',
            "Pago aprobado. Postulante {$resultado['correo']} habilitado."
        );
    }

    public function rechazar(Request $request, Pago $pago)
    {
        $request->validate(['observacion' => 'required|string|max:255']);
        $this->rechazarUseCase->ejecutar($pago, Auth::id(), $request->observacion);
        return back()->with('error', 'Pago rechazado.');
    }
}