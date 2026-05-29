<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Recuperación de Contraseña</title>
</head>
<body style="margin:0;padding:0;background:#f0f4f8;font-family:'Segoe UI',Roboto,Arial,sans-serif;color:#1f2937;">

  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f0f4f8;padding:40px 20px;">
    <tr>
      <td align="center">

        <!-- Card central -->
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" style="max-width:600px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 8px 32px rgba(13,44,94,0.10);">

          <!-- Header azul UAGRM con logo -->
          <tr>
            <td style="background:linear-gradient(135deg,#0d2c5e 0%,#1e5fa8 100%);padding:40px 30px;text-align:center;">
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center">
                <tr>
                  <td style="background:linear-gradient(135deg,#ffd54f 0%,#ffb300 100%);width:64px;height:64px;border-radius:14px;text-align:center;vertical-align:middle;">
                    <span style="color:#0d2c5e;font-size:22px;font-weight:800;">CUP</span>
                  </td>
                </tr>
              </table>
              <h1 style="color:#fff;font-size:22px;font-weight:700;margin:18px 0 4px;">Sistema CUP</h1>
              <p style="color:rgba(255,255,255,0.85);font-size:12px;letter-spacing:1.5px;text-transform:uppercase;margin:0;">UAGRM · FICCT</p>
            </td>
          </tr>

          <!-- Cuerpo -->
          <tr>
            <td style="padding:40px 40px 30px;">

              <h2 style="color:#0d2c5e;font-size:24px;font-weight:700;margin:0 0 16px;">Hola, {{ $nombreUsuario }} 👋</h2>

              <p style="color:#374151;font-size:15px;line-height:1.6;margin:0 0 16px;">
                Recibimos una solicitud para restablecer la contraseña de tu cuenta
                asociada al correo <strong>{{ $emailUsuario }}</strong> en el
                <strong>Sistema de Gestión del Curso Preuniversitario</strong> de la
                FICCT — UAGRM.
              </p>

              <p style="color:#374151;font-size:15px;line-height:1.6;margin:0 0 24px;">
                Para crear una contraseña nueva, hacé click en el botón:
              </p>

              <!-- Botón CTA -->
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" style="margin:0 auto 24px;">
                <tr>
                  <td style="background:linear-gradient(135deg,#1e5fa8 0%,#0d2c5e 100%);border-radius:10px;padding:14px 36px;text-align:center;box-shadow:0 4px 12px rgba(13,44,94,0.25);">
                    <a href="{{ $resetUrl }}" style="color:#fff;text-decoration:none;font-size:15px;font-weight:600;letter-spacing:0.3px;display:inline-block;">
                      🔐 Restablecer mi Contraseña
                    </a>
                  </td>
                </tr>
              </table>

              <p style="color:#6b7280;font-size:13px;line-height:1.5;text-align:center;margin:0 0 24px;">
                O copiá y pegá este enlace en tu navegador:<br>
                <a href="{{ $resetUrl }}" style="color:#2c7be5;word-break:break-all;font-size:12px;">{{ $resetUrl }}</a>
              </p>

              <!-- Aviso de tiempo -->
              <div style="background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.25);border-radius:10px;padding:14px 18px;margin:0 0 24px;">
                <p style="color:#92400e;font-size:13px;line-height:1.5;margin:0;">
                  <strong>⏱ Importante:</strong> Este enlace expirará en {{ $minutos }} minutos por seguridad.
                </p>
              </div>

              <p style="color:#6b7280;font-size:13px;line-height:1.5;margin:0;">
                <strong>¿No solicitaste este cambio?</strong><br>
                Si no fuiste vos quien pidió restablecer la contraseña, podés ignorar
                este correo. Tu cuenta seguirá segura.
              </p>

            </td>
          </tr>

          <!-- Footer -->
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

        <!-- Pie auxiliar -->
        <p style="color:#9ca3af;font-size:11px;margin:20px 0 0;text-align:center;">
          Este es un correo automático. Por favor no respondas a este mensaje.<br>
          Para soporte, contactá al Coordinador del CUP.
        </p>

      </td>
    </tr>
  </table>

</body>
</html>
