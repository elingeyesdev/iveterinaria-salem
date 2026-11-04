<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

/**
 * OWASP ASVS V6.1.2 - Datos de salud cifrados en reposo.
 *
 * Usa AES-256-GCM con una clave derivada de APP_KEY y un nonce aleatorio por
 * valor. Se almacena como texto Base64 compacto para no cambiar la estructura
 * existente de la tabla (los campos actuales son VARCHAR(255)).
 */
class CompactEncryptedText implements CastsAttributes
{
    private const PREFIX = 'v1:';

    private const CIPHER = 'aes-256-gcm';

    private const NONCE_BYTES = 12;

    private const TAG_BYTES = 16;

    private const MAX_PLAINTEXT_LENGTH = 160;

    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        // Compatibilidad temporal con registros anteriores al despliegue.
        // La orden de cifrado convierte estos valores a formato v1:.
        if (! str_starts_with($value, self::PREFIX)) {
            return $value;
        }

        $payload = base64_decode(substr($value, strlen(self::PREFIX)), true);
        if ($payload === false || strlen($payload) <= self::NONCE_BYTES + self::TAG_BYTES) {
            throw new RuntimeException('No se puede descifrar el dato de salud: formato inválido.');
        }

        $nonce = substr($payload, 0, self::NONCE_BYTES);
        $tag = substr($payload, -self::TAG_BYTES);
        $ciphertext = substr($payload, self::NONCE_BYTES, -self::TAG_BYTES);

        $plaintext = openssl_decrypt(
            $ciphertext,
            self::CIPHER,
            $this->key(),
            OPENSSL_RAW_DATA,
            $nonce,
            $tag
        );

        // Fallo seguro: no devolvemos datos alterados o indescifrables.
        if ($plaintext === false) {
            throw new RuntimeException('No se puede descifrar el dato de salud: autenticación fallida.');
        }

        return $plaintext;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        $value = (string) $value;

        if (mb_strlen($value) > self::MAX_PLAINTEXT_LENGTH) {
            throw new RuntimeException(
                "El campo {$key} supera el máximo de ".self::MAX_PLAINTEXT_LENGTH.' caracteres permitido para el almacenamiento cifrado.'
            );
        }

        $nonce = random_bytes(self::NONCE_BYTES);
        $tag = '';

        $ciphertext = openssl_encrypt(
            $value,
            self::CIPHER,
            $this->key(),
            OPENSSL_RAW_DATA,
            $nonce,
            $tag,
            '',
            self::TAG_BYTES
        );

        if ($ciphertext === false || strlen($tag) !== self::TAG_BYTES) {
            throw new RuntimeException('No se pudo cifrar el dato de salud.');
        }

        $encoded = self::PREFIX.base64_encode($nonce.$ciphertext.$tag);

        if (strlen($encoded) > 255) {
            throw new RuntimeException('El dato cifrado no cabe en el campo VARCHAR(255).');
        }

        return $encoded;
    }

    private function key(): string
    {
        $key = config('app.key');

        if (! is_string($key) || $key === '') {
            throw new RuntimeException('APP_KEY no está configurada. No se permite cifrar ni descifrar datos de salud.');
        }

        if (str_starts_with($key, 'base64:')) {
            $decoded = base64_decode(substr($key, 7), true);
            if ($decoded === false) {
                throw new RuntimeException('APP_KEY no contiene una clave Base64 válida.');
            }
            $key = $decoded;
        }

        if (strlen($key) !== 32) {
            throw new RuntimeException('APP_KEY debe proporcionar una clave de 32 bytes para AES-256-GCM.');
        }

        return $key;
    }
}
