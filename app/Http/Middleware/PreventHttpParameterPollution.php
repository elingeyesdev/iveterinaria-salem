<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * OWASP ASVS 4.0.3 - V5.1.1
 *
 * Bloquea solicitudes que contienen parámetros HTTP duplicados con el mismo
 * nombre cuando el parámetro es escalar. Las colecciones PHP (por ejemplo
 * servicios[]=1&servicios[]=2) se permiten porque forman parte de los
 * formularios legítimos de la aplicación.
 */
class PreventHttpParameterPollution
{
    public function handle(Request $request, Closure $next): Response
    {
        $duplicate = $this->findDuplicateParameter((string) $request->server('QUERY_STRING', ''));

        // Para JSON, el contenido bruto permite detectar claves repetidas antes
        // de que json_decode/Laravel descarte una de ellas.
        if ($duplicate === null && $this->isJsonRequest($request)) {
            $duplicate = $this->findDuplicateJsonKey($request->getContent());
        }

        if ($duplicate !== null) {
            Log::warning('security.http_parameter_pollution_blocked', [
                'event' => 'security.http_parameter_pollution_blocked',
                'parameter' => $duplicate,
                'source' => (string) $request->server('QUERY_STRING', '') !== null && $this->findDuplicateParameter((string) $request->server('QUERY_STRING', '')) !== null
                    ? 'query'
                    : 'json',
                'method' => $request->method(),
                'path' => $request->path(),
                'route' => $request->route()?->getName(),
                'user_id' => $request->user()?->getAuthIdentifier(),
            ]);

            return response()->json([
                'message' => 'Solicitud inválida: se detectaron parámetros HTTP duplicados.',
            ], 400);
        }

        return $next($request);
    }

    private function findDuplicateParameter(?string $queryString): ?string
    {
        if ($queryString === null || $queryString === '') {
            return null;
        }

        $seen = [];

        foreach (preg_split('/[&;]/', $queryString) as $pair) {
            if ($pair === '') {
                continue;
            }

            [$rawName] = array_pad(explode('=', $pair, 2), 2, '');
            $name = urldecode(str_replace('+', ' ', $rawName));

            // PHP/Laravel usa [] para representar colecciones legítimas.
            if ($this->isArrayParameter($name)) {
                continue;
            }

            if (isset($seen[$name])) {
                return $name;
            }

            $seen[$name] = true;
        }

        return null;
    }

    private function isArrayParameter(string $name): bool
    {
        return str_ends_with($name, '[]')
            || preg_match('/\[[^\]]*\]/', $name) === 1;
    }

    private function isJsonRequest(Request $request): bool
    {
        return str_contains(strtolower((string) $request->headers->get('Content-Type')), 'application/json');
    }

    /**
     * Detecta claves repetidas dentro de cada objeto JSON sin depender del
     * resultado de json_decode(), que normalmente conserva solo una ocurrencia.
     */
    private function findDuplicateJsonKey(string $json): ?string
    {
        if ($json === '') {
            return null;
        }

        json_decode($json);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        $length = strlen($json);
        $objects = [];

        for ($i = 0; $i < $length; $i++) {
            if ($json[$i] === '{') {
                $objects[] = [];
            } elseif ($json[$i] === '}') {
                array_pop($objects);
            } elseif ($json[$i] === '"') {
                [$value, $end] = $this->readJsonString($json, $i);
                $next = $end + 1;
                while ($next < $length && ctype_space($json[$next])) {
                    $next++;
                }

                if ($next < $length && $json[$next] === ':' && $objects !== []) {
                    $index = count($objects) - 1;
                    if (isset($objects[$index][$value])) {
                        return $value;
                    }
                    $objects[$index][$value] = true;
                }

                $i = $end;
            }
        }

        return null;
    }

    /**
     * Devuelve el valor de una cadena JSON y la posición de su comilla final.
     * No intenta interpretar el contenido: solo necesita identificar claves.
     */
    private function readJsonString(string $json, int $start): array
    {
        $length = strlen($json);
        $value = '';

        for ($i = $start + 1; $i < $length; $i++) {
            $char = $json[$i];

            if ($char === '\\') {
                if ($i + 1 >= $length) {
                    return [null, $i];
                }
                $value .= $json[$i].$json[$i + 1];
                $i++;

                continue;
            }

            if ($char === '"') {
                $decoded = json_decode('"'.$value.'"');

                return [$decoded, $i];
            }

            $value .= $char;
        }

        return [null, $length - 1];
    }
}
