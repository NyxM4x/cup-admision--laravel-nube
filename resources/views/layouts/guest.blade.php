<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistema CUP') }}</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            --cup-primary:        #0d2c5e;
            --cup-primary-light:  #1e5fa8;
            --cup-accent:         #2c7be5;
            --cup-success:        #198754;
            --cup-danger:         #dc2626;
            --cup-bg:             #f8f9fb;
            --cup-text:           #1f2937;
            --cup-muted:          #6b7280;
            --cup-border:         #e5e7eb;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--cup-text);
            background: linear-gradient(135deg, #0d2c5e 0%, #1e5fa8 50%, #2c7be5 100%);
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .auth-container {
            width: 100%;
            max-width: 1080px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.25);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 580px;
        }

        .auth-branding {
            background: linear-gradient(160deg, #0d2c5e 0%, #1e5fa8 100%);
            color: #fff;
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .auth-branding::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 300px;
            height: 300px;
            background: rgba(255,213,79,0.10);
            border-radius: 50%;
        }
        .auth-branding::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 250px;
            height: 250px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }

        .auth-brand-top {
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
            z-index: 2;
        }
        .auth-brand-mark {
            width: 64px;
            height: 64px;
            border-radius: 14px;
            background: linear-gradient(135deg, #ffd54f 0%, #ffb300 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--cup-primary);
            font-weight: 800;
            font-size: 22px;
            box-shadow: 0 6px 20px rgba(255,179,0,0.35);
        }
        .auth-brand-text .main {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.3px;
            line-height: 1.1;
        }
        .auth-brand-text .sub {
            font-size: 12px;
            opacity: 0.85;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .auth-branding-content {
            position: relative;
            z-index: 2;
        }
        .auth-branding-content h2 {
            font-size: 1.9rem;
            font-weight: 700;
            line-height: 1.25;
            margin: 0 0 1rem 0;
        }
        .auth-branding-content p {
            opacity: 0.85;
            font-size: 0.95rem;
            line-height: 1.6;
            margin: 0;
        }

        .auth-branding-footer {
            position: relative;
            z-index: 2;
            font-size: 0.75rem;
            opacity: 0.7;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .auth-features {
            position: relative;
            z-index: 2;
            margin-top: 2rem;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }
        .auth-feature {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            font-size: 0.92rem;
        }
        .auth-feature i {
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.12);
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            color: #ffd54f;
        }

        .auth-form {
            padding: 3rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-form h1 {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--cup-primary);
            margin: 0 0 0.4rem 0;
        }
        .auth-form-subtitle {
            color: var(--cup-muted);
            font-size: 0.93rem;
            margin-bottom: 2rem;
        }

        .auth-form .form-label {
            font-weight: 600;
            color: var(--cup-text);
            font-size: 0.88rem;
            margin-bottom: 0.4rem;
        }
        .auth-form .form-control {
            border: 1px solid var(--cup-border);
            border-radius: 10px;
            padding: 0.7rem 0.9rem;
            font-size: 0.95rem;
            transition: all 0.15s ease;
        }
        .auth-form .form-control:focus {
            border-color: var(--cup-primary-light);
            box-shadow: 0 0 0 4px rgba(30,95,168,0.10);
            outline: none;
        }

        .auth-input-wrap {
            position: relative;
        }
        .auth-input-wrap .form-control {
            padding-left: 2.6rem;
        }
        .auth-input-wrap .input-icon {
            position: absolute;
            left: 0.95rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--cup-muted);
            font-size: 1rem;
        }

        .auth-form .btn-cup {
            background: linear-gradient(135deg, var(--cup-primary-light) 0%, var(--cup-primary) 100%);
            color: #fff;
            padding: 0.8rem 1rem;
            border-radius: 10px;
            font-weight: 600;
            border: none;
            width: 100%;
            font-size: 0.98rem;
            letter-spacing: 0.3px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(13,44,94,0.25);
        }
        .auth-form .btn-cup:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(13,44,94,0.35);
        }

        .auth-error {
            background: rgba(220,38,38,0.06);
            border: 1px solid rgba(220,38,38,0.20);
            color: #842029;
            padding: 0.7rem 0.9rem;
            border-radius: 10px;
            font-size: 0.88rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }
        .auth-error i {
            flex-shrink: 0;
            margin-top: 2px;
        }

        .auth-success {
            background: rgba(25,135,84,0.06);
            border: 1px solid rgba(25,135,84,0.22);
            color: #0f5132;
            padding: 0.7rem 0.9rem;
            border-radius: 10px;
            font-size: 0.88rem;
            margin-bottom: 1.25rem;
        }

        .auth-meta {
            margin-top: 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
        }
        .auth-meta a {
            color: var(--cup-primary-light);
            text-decoration: none;
            font-weight: 500;
        }
        .auth-meta a:hover {
            text-decoration: underline;
        }

        .auth-form-footer {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--cup-border);
            font-size: 0.82rem;
            color: var(--cup-muted);
            text-align: center;
        }

        /* Responsive: en mobile colapsa a una sola columna */
        @media (max-width: 860px) {
            body { padding: 0; }
            .auth-container {
                grid-template-columns: 1fr;
                border-radius: 0;
                min-height: 100vh;
            }
            .auth-branding {
                padding: 2rem 1.5rem;
                min-height: auto;
            }
            .auth-branding-content h2 {
                font-size: 1.4rem;
            }
            .auth-features {
                display: none;
            }
            .auth-form {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">

        <!-- COLUMNA IZQUIERDA: Branding -->
        <div class="auth-branding">
            <div class="auth-brand-top">
                <div class="auth-brand-mark">CUP</div>
                <div class="auth-brand-text">
                    <div class="main">Sistema CUP</div>
                    <div class="sub">UAGRM · FICCT</div>
                </div>
            </div>

            <div class="auth-branding-content">
                <h2>Sistema de Gestión del Curso Preuniversitario</h2>
                <p>
                    Plataforma institucional de la Facultad de Ingeniería en Ciencias
                    de la Computación y Telecomunicaciones para la administración
                    del proceso de admisión.
                </p>

                <div class="auth-features">
                    <div class="auth-feature">
                        <i class="bi bi-shield-check"></i>
                        <span>Acceso seguro con autenticación por rol</span>
                    </div>
                    <div class="auth-feature">
                        <i class="bi bi-clipboard-data"></i>
                        <span>Gestión académica y de postulantes</span>
                    </div>
                    <div class="auth-feature">
                        <i class="bi bi-graph-up"></i>
                        <span>Auditoría completa en bitácora</span>
                    </div>
                </div>
            </div>

            <div class="auth-branding-footer">
                © {{ date('Y') }} UAGRM · FICCT · Curso Preuniversitario
            </div>
        </div>

        <!-- COLUMNA DERECHA: Slot del formulario -->
        <div class="auth-form">
            {{ $slot }}
        </div>

    </div>
</body>
</html>
