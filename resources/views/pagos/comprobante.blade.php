<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Comprobante de pago #{{ $pago->id }}</title>
    @vite('resources/js/app.js')
</head>
<body class="comprobante-page">
    <nav class="no-print" aria-label="Acciones del comprobante">
        <button type="button" data-imprimir-comprobante>Imprimir / Guardar como PDF</button>
        <a href="{{ route('pagos.show', $pago) }}">Volver a la cuenta</a>
    </nav>
    <main>
        <header class="comprobante-encabezado">
            <div class="comprobante-marca">
                <img src="{{ asset('salem-logo.jpg') }}" alt="Salem Centro Veterinario" width="96" height="96">
                <div><p class="marca-nombre">SALEM</p><p class="marca-subtitulo">Centro Veterinario</p></div>
            </div>
            <div class="comprobante-referencia">
                <span class="etiqueta">Comprobante de pago</span>
                <strong>#{{ str_pad((string) $pago->id, 6, '0', STR_PAD_LEFT) }}</strong>
                <span class="estado-pago">{{ $pago->estado }}</span>
            </div>
        </header>
        <section class="comprobante-titulo">
            <p class="etiqueta">Constancia de cobro</p>
            <h1>Comprobante de pago #{{ $pago->id }}</h1>
            <p>Fecha del cobro: <strong>{{ $pago->updated_at?->format('d/m/Y H:i') ?? 'Sin registrar' }}</strong></p>
        </section>
        <h2 class="seccion-titulo">Datos de la atención</h2>
        @include('pagos._resumen-cuenta')
        <section class="comprobante-cierre" aria-label="Resumen del pago">
            <div><h2>Gracias por confiar en Salem</h2><p>Conserve este comprobante como respaldo de su pago.</p></div>
            <p class="importe"><span>Importe recibido:</span> <strong>Bs {{ number_format((float) $pago->monto, 2) }}</strong></p>
        </section>
        <footer class="comprobante-pie">
            <span>Salem Centro Veterinario</span>
            <span>Comprobante interno de pago. No es una factura fiscal.</span>
        </footer>
    </main>
</body>
</html>
