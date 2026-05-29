<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Habilitación CUP</title>
</head>
<body style="margin:0;padding:0;background:#f0f4f8;font-family:'Segoe UI',Roboto,Arial,sans-serif;color:#1f2937;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f0f4f8;padding:40px 20px;">
    <tr>
      <td align="center">
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" style="max-width:600px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 8px 32px rgba(13,44,94,0.10);">

          <tr>
            <td style="background:linear-gradient(135deg,#0d2c5e 0%,#1e5fa8 100%);padding:40px 30px;text-align:center;">
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center">
                <tr>
                  <td style="background:linear-gradient(135deg,#ffd54f 0%,#ffb300 100%);width:64px;height:64px;border-radius:14px;text-align:center;vertical-align:middle;">
                    <span style="color:#0d2c5e;font-size:22px;font-weight:800;">CUP</span>
                  </td>
                </tr>
              </table>
              <h1 style="color:#fff;font-size:24px;font-weight:700;margin:18px 0 4px;">¡Estás habilitado!</h1>
              <p style="color:rgba(255,255,255,0.85);font-size:12px;letter-spacing:1.5px;text-transform:uppercase;margin:0;">Sistema CUP · UAGRM · FICCT</p>
            </td>
          </tr>

          <tr>
            <td style="padding:40px 40px 30px;">
              <h2 style="color:#0d2c5e;font-size:22px;font-weight:700;margin:0 0 16px;">Hola, {{ $nombreUsuario }} 🎉</h2>

              <p style="color:#374151;font-size:15px;line-height:1.6;margin:0 0 16px;">
                Validamos toda tu documentación para el <strong>Curso Preuniversitario CUP</strong>
                de la FICCT — UAGRM. Estás <strong>habilitado para realizar el pago</strong> y
                asegurar tu cupo en la carrera de <strong>{{ $carrera1 }}</strong>.
              </p>

              <div style="background:#f8f9fb;border-radius:12px;padding:24px;margin:24px 0;border-left:4px solid #0d2c5e;">
                <p style="color:#6b7280;font-size:12px;letter-spacing:1px;text-transform:uppercase;margin:0 0 12px;">Tus credenciales de acceso</p>
                <p style="color:#1f2937;font-size:14px;margin:0 0 8px;"><strong>Usuario (email):</strong> {{ $email }}</p>
                <p style="color:#1f2937;font-size:14px;margin:0;"><strong>Contraseña temporal:</strong> <code style="background:#fff3cd;padding:4px 10px;border-radius:6px;font-family:monospace;font-size:16px;color:#856404;">{{ $passwordTemporal }}</code></p>
              </div>

              <div style="background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.25);border-radius:10px;padding:14px 18px;margin:0 0 24px;">
                <p style="color:#92400e;font-size:13px;line-height:1.5;margin:0;">
                  <strong>⚠ Importante:</strong> Esta es una contraseña temporal. Al iniciar sesión
                  por primera vez, el sistema te pedirá cambiarla por una nueva.
                </p>
              </div>

              <p style="color:#374151;font-size:15px;line-height:1.6;margin:0 0 16px;"><strong>Próximos pasos:</strong></p>
              <ol style="color:#374151;font-size:14px;line-height:1.8;margin:0 0 24px;padding-left:20px;">
                <li>Iniciá sesión con las credenciales de arriba</li>
                <li>Cambiá tu contraseña por una nueva (8+ caracteres, mayúscula, minúscula y número)</li>
                <li>Realizá el pago de inscripción</li>
                <li>Elegí tu turno: Mañana, Tarde o Noche</li>
                <li>Descargá tu horario en PDF</li>
              </ol>

              <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" style="margin:0 auto 24px;">
                <tr>
                  <td style="background:linear-gradient(135deg,#1e5fa8 0%,#0d2c5e 100%);border-radius:10px;padding:14px 36px;">
                    <a href="{{ url('/login') }}" style="color:#fff;text-decoration:none;font-size:15px;font-weight:600;">
                      🚀 Iniciar Sesión
                    </a>
                  </td>
                </tr>
              </table>

              <p style="color:#6b7280;font-size:12px;line-height:1.5;text-align:center;margin:0;">
                Si tenés problemas, comunicate con el Coordinador del CUP.
              </p>
            </td>
          </tr>

          <tr>
            <td style="background:#f8f9fb;padding:24px 40px;border-top:1px solid #e5e7eb;text-align:center;">
              <p style="color:#6b7280;font-size:12px;margin:0 0 6px;">
                © {{ date('Y') }} <strong>UAGRM · FICCT</strong>
              </p>
              <p style="color:#9ca3af;font-size:11px;margin:0;letter-spacing:0.3px;text-transform:uppercase;">
                Sistema CUP — Curso Preuniversitario
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
