<?php

use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\PreventHttpParameterPollution;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(HandleCors::class);

        // OWASP ASVS V5.1.1 - Protección contra HTTP Parameter Pollution (HPP).
        // Se ejecuta antes de los controladores para rechazar parámetros duplicados.
        $middleware->prepend(PreventHttpParameterPollution::class);

        $middleware->alias([
            'role' => EnsureUserHasRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        /*
         * OWASP ASVS V7.4.3 - Controlador de errores de último recurso.
         * OWASP ASVS V7.4.1 - Mensaje genérico + identificador de soporte.
         *
         * Las excepciones no controladas nunca deben mostrar detalles internos
         * (SQL, rutas, clases, stack trace, etc.) al usuario final.
         */
        $exceptions->render(function (Throwable $e, Request $request) {
            $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;

            // Los errores 5xx y las excepciones inesperadas reciben respuesta genérica.
            if ($status < 500) {
                return null;
            }

            $errorId = (string) Str::uuid();
            $request->attributes->set('security_error_id', $errorId);

            // OWASP V7.4: registrar el evento con un identificador correlacionable.
            Log::error('security.unhandled_exception', [
                'event' => 'security.unhandled_exception',
                'error_id' => $errorId,
                'exception' => $e::class,
                'route' => $request->route()?->getName(),
                'method' => $request->method(),
                'path' => $request->path(),
                'user_id' => auth()->id(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Ocurrió un error inesperado. Contacte al soporte técnico.',
                    'error_id' => $errorId,
                ], 500);
            }

            return response()->view('errors.generic', [
                'errorId' => $errorId,
            ], 500);
        });
    })->create();
