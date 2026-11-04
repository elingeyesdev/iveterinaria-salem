<?php

use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\EnsureUserIsActive;
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
        $middleware->append(PreventHttpParameterPollution::class);
        $middleware->web(append: [EnsureUserIsActive::class]);

        $middleware->alias([
            'role' => EnsureUserHasRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // --- MANEJO OWASP DE EXCEPCIONES 5XX (DESACTIVADO TEMPORALMENTE) ---
        // Bloque comentado para desarrollo: se mostrarán los stack traces completos.
        // Para reactivar en producción, descomenta el bloque siguiente.
        //
        // /*
        //  * OWASP ASVS V7.4.3 - Controlador de errores de último recurso.
        //  * OWASP ASVS V7.4.1 - Mensaje genérico + identificador de soporte.
        //  *
        //  * Las excepciones inesperadas no exponen SQL, rutas, clases, stack trace
        //  * ni mensajes de excepción al usuario final. El UUID queda únicamente
        //  * como referencia para soporte y se registra junto con la excepción.
        //  */
        // $exceptions->render(function (\Throwable $e, Request $request) {
        //     // Los errores HTTP controlados (404, 403, 419, etc.) conservan
        //     // su comportamiento normal. Solo interceptamos fallos 5xx.
        //     if ($e instanceof HttpExceptionInterface && $e->getStatusCode() < 500) {
        //         return null;
        //     }
        //
        //     $errorId = (string) Str::uuid();
        //     $status = $e instanceof HttpExceptionInterface
        //         ? $e->getStatusCode()
        //         : 500;
        //
        //     Log::error('Excepción no controlada', [
        //         'support_id' => $errorId,
        //         'exception' => $e,
        //         'method' => $request->method(),
        //         'path' => $request->path(),
        //         'user_id' => $request->user()?->getAuthIdentifier(),
        //         'ip' => $request->ip(),
        //     ]);
        //
        //     if ($request->expectsJson() || $request->is('api/*')) {
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'No fue posible completar la operación.',
        //             'support_id' => $errorId,
        //         ], $status >= 500 ? 500 : $status);
        //     }
        //
        //     return response()->view('errors.generic', [
        //         'errorId' => $errorId,
        //     ], $status >= 500 ? 500 : $status);
        // });
    })->create();
