@component('mail::message')
# ✅ ¡Tu pago fue aprobado!

Hola **{{ $usuario->name }}**,

Tu pago de inscripción al **Curso Preuniversitario FICCT - UAGRM** ha sido confirmado.

---

## 🔑 Tus credenciales de acceso

@component('mail::panel')
**CI / Usuario:** {{ $usuario->ci }}

**Contraseña temporal:** `{{ $passwordTemporal }}`
@endcomponent

> ⚠️ Deberás cambiar tu contraseña en tu primer inicio de sesión.

---

## 📋 Tu inscripción

| Campo | Detalle |
|---|---|
| **Nombre** | {{ $inscripcion->postulante->persona->nombre ?? '' }} |
| **CI** | {{ $inscripcion->postulante->persona->ci ?? '' }} |
| **Periodo** | {{ $inscripcion->periodo->fecha_ini_curso->format('d/m/Y') }} — {{ $inscripcion->periodo->fecha_fin_curso->format('d/m/Y') }} |

**Carrera(s) postulada(s):**
@foreach($inscripcion->postulacionCarreras as $pc)
- {{ $pc->prioridad }}. {{ $pc->carrera->nombre }}
@endforeach

---

## 📅 Próximos pasos

1. Ingresa al sistema con tu CI y contraseña temporal
2. Cambia tu contraseña en el primer ingreso
3. Espera la asignación de tu grupo y horario

@component('mail::button', ['url' => $urlLogin, 'color' => 'success'])
Ingresar al Sistema
@endcomponent

**Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones**
UAGRM — Santa Cruz de la Sierra, Bolivia
@endcomponent