<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error del sistema</title>
    <style>body{font-family:Arial,sans-serif;background:#f5f6f8;margin:0;display:flex;align-items:center;justify-content:center;min-height:100vh}.box{background:#fff;padding:32px;max-width:560px;border-radius:10px;box-shadow:0 4px 18px rgba(0,0,0,.08);text-align:center}h1{margin-top:0}code{background:#f0f1f3;padding:4px 7px;border-radius:4px}</style>
</head>
<body>
    <main class="box" role="alert">
        <h1>Ocurrió un problema</h1>
        <p>La operación no pudo completarse. Por seguridad, no se muestran detalles internos del sistema.</p>
        <p>Si necesita asistencia, proporcione este código al soporte técnico:</p>
        <p><code>{{ $errorId }}</code></p>
        <a href="{{ url()->previous() }}" style="display:inline-block;margin-top:12px;text-decoration:none">Volver</a>
    </main>
</body>
</html>
