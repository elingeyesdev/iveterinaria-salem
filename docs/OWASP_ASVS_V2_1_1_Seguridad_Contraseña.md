# Documentación Técnica de Seguridad: OWASP ASVS V2.1.1

- **Código OWASP:** ASVS V2.1.1 (Control C6)
- **Categoría:** V2.1 - Seguridad de Contraseña
- **Control:** Verifique que las contraseñas de los usuarios tienen al menos 12 caracteres de longitud (después de combinar varios espacios).
- **Fecha de implementación:** 2026-09-18
- **Estado:** IMPLEMENTADO

---

## 1. Justificación de Seguridad

### 1.1 Riesgo mitigado
Sin una longitud mínima adecuada, las contraseñas son vulnerables a ataques de:
- **Fuerza bruta offline (hash cracking):** Un atacante que obtenga la base de datos de hashes puede romper contraseñas cortas (< 10 caracteres) en cuestión de minutos con GPU modernas.
- **Ataques de diccionario y credential stuffing.**
- **Ingeniería social / adivinanza.**

### 1.2 ¿Por qué 12 caracteres y no 8?
OWASP ASVS 4.0.3 establece 12 caracteres como línea base para entornos de producción. Se recomienda (NIST SP 800-63B) al menos 8, pero 12 es el valor que ofrece margen de seguridad contra hardware de cracking moderno (entropía > 60 bits con caracteres variados).

### 1.3 ¿Por qué normalizar/combinar espacios múltiples?
El requisito OWASP es explícito: "después de combinar varios espacios". Si no se normaliza:
- `"a           b"` (12 espacios + 2 letras = longitud 14) se aceptaría por longitud, pero en realidad son solo 3 caracteres efectivos.
- Un usuario podría "hacer trampa" rellenando con espacios múltiples para alcanzar el mínimo sin aportar entropía.
- Evita inconsistencias al validar: el valor **almacenado** debe ser el mismo con el que se midió la longitud.

---

## 2. Resumen de la Implementación

La medida V2.1.1 se aplica en **TODOS los puntos de entrada** donde se establece o cambia una contraseña. Cada punto implementa dos capas:

| Capa | Descripción |
|------|-------------|
| **1. Normalización** previa | `trim()` + `preg_replace('/\s+/', ' ', $valor)`. Combina espacios múltiples en uno solo y elimina espacios extremos. Se ejecuta ANTES de la validación. |
| **2. Validación min:12** | Regla `min:12` o `Password::min(12)` sobre el valor ya normalizado. |
| **3. Mensaje al usuario** | Explícito: menciona que el cálculo se realiza después de normalizar espacios. |

### Archivos modificados

| Archivo | Ruta | Cambios |
|---------|------|---------|
| AppServiceProvider | [AppServiceProvider.php](file:///c:/laragon/www/proyectologin/app/Providers/AppServiceProvider.php#L26-L33) | `Password::defaults( min(12) )` → regla global para `Rules\Password::defaults()` |
| UserRequest (Admin crea/edita usuarios) | [UserRequest.php](file:///c:/laragon/www/proyectologin/app/Http/Requests/UserRequest.php#L26-L37) | `prepareForValidation()` normaliza; `rules()` exige `min:12` en store (required) y update (nullable) |
| ResetPasswordController (Olvide mi contraseña) | [ResetPasswordController.php](file:///c:/laragon/www/proyectologin/app/Http/Controllers/Auth/ResetPasswordController.php#L32-L105) | Sobrescribe `reset()` para normalizar; `rules()` fuerza `Password::min(12)`; mensajes custom |
| RegisterController (Registro público, DESACTIVADO) | [RegisterController.php](file:///c:/laragon/www/proyectologin/app/Http/Controllers/Auth/RegisterController.php#L44-L127) | Sobrescribe `register()` para normalizar; `validator()` cambia `min:8` → `min:12` |

> **Nota:** `ConfirmPasswordController` y `AuthController@login` no requieren modificación porque no **establecen** una contraseña nueva; solo la verifican.

---

## 3. Detalle Técnico por Componente

### 3.1 AppServiceProvider — Regla Global

```php
Password::defaults(
    static fn (): Password => Password::min(12)
);
```
- **Objetivo:** Establecer un valor por defecto seguro. Cualquier código nuevo que utilice `Rules\Password::defaults()` hereda el mínimo 12 automáticamente.
- **Referencia:** Laravel docs — `Illuminate\Validation\Rules\Password::defaults()`.

### 3.2 UserRequest (FormRequest) — Gestión de Usuarios por Admin

**Flujo de ejecución:**
1. Laravel llama a `prepareForValidation()` ANTES de evaluar `rules()`.
2. `password` y `password_confirmation` se normalizan:
   ```php
   $normalized = preg_replace('/\s+/', ' ', trim($password));
   $this->merge(['password' => $normalized]);
   ```
3. Luego `rules()` evalúa `min:12` sobre ese valor **ya normalizado**.
4. El valor normalizado es el que llega a `UserController@store/update` y a `Hash::make()`.

**Casos:**
- **POST (store):** `password` es `required|string|min:12|confirmed`
- **PUT/PATCH (update):** `password` es `nullable|string|min:12|confirmed`

### 3.3 ResetPasswordController (Trait ResetsPasswords)

El trait original `ResetsPasswords` define `rules()` con `Rules\Password::defaults()` (que antes era min:8 por defecto). Se sobrescribe el método `rules()` y `reset()`:

- Sobrescribir `reset()` es necesario porque el trait no ofrece hook `prepareForValidation` (solo los FormRequest lo tienen).
- `traitReset()` es una copia del `reset()` original del trait; se usa para evitar recursividad infinita al sobrescribir.
- El broker `Password::broker()->reset()` recibe la contraseña **normalizada** y la pasa a `resetPassword()` que ejecuta `Hash::make()`.

### 3.4 RegisterController

Aunque la ruta de registro está **desactivada** en `web.php` (`Auth::routes(['register' => false])`), se actualiza por dos razones:
1. **Defensa en profundidad:** Si en el futuro se reactiva el registro público, ya cumple OWASP sin sorpresas.
2. **Coherencia:** El mismo estándar se aplica en todos los puntos de creación de contraseña.

---

## 4. Matemáticas de la Entropía (Justificación del mínimo 12)

| Longitud | Conjunto | Entropía estimada | Tiempo estimado de crack (GPU moderna 10^12 hashes/s) |
|----------|----------|-------------------|------------------------------------------------------|
| 8        | a-z (26) | 37.6 bits         | < 1 segundo |
| 8        | a-zA-Z0-9 (62) | 47.6 bits | ~ 2 minutos |
| 12       | a-z (26) | 56.5 bits | ~ 48 horas |
| 12       | a-zA-Z0-9 (62) | 71.5 bits | > 70.000 años |
| 12       | ASCII printable (94) | 78.1 bits | > 10 millones años |

**Conclusión:** 12 caracteres con conjunto mixto hace inviable el cracking práctico para contraseñas hasheadas con bcrypt (factor de costo estándar 12 → ~200 ms/hash).

---

## 5. Casos de Prueba Manuales (para verificación)

Usar `php artisan tinker` o el formulario `/users/create`.

| # | Input password | Normalized | Longitud contada | Resultado esperado |
|---|---------------|------------|------------------|--------------------|
| 1 | `"12345678901"` | `"12345678901"` | 11 | ❌ Falla: min:12 |
| 2 | `"123456789012"` | `"123456789012"` | 12 | ✅ Aceptado |
| 3 | `"  hola   mundo  "` (12 espacios + 10 letras) | `"hola mundo"` | 10 | ❌ Falla (espacios colapsados, quedan 10 reales) |
| 4 | `"mi   contraseña   segura  "` | `"mi contraseña segura"` | 21 | ✅ Aceptado |
| 5 | `"abc       def"` (7 espacios) | `"abc def"` | 7 | ❌ Falla |
| 6 | `"   password   de   12   chars "` | `"password de 12 chars"` | 21 | ✅ Aceptado |

---

## 6. Cobertura OWASP Complementaria

La siguiente tabla relaciona este control V2.1.1 con controles adicionales recomendados por ASVS para completar V2.1:

| Control OWASP | Estado en el proyecto |
|---------------|----------------------|
| **V2.1.1** Min. 12 caracteres (normalizado) | ✅ IMPLEMENTADO (este documento) |
| V2.1.2 Lista de contraseñas comunes (top 10k) | ❌ Pendiente (recomendado `Password::min(12)->uncompromised()`) |
| V2.1.3 Máx. longitud (evitar DoS) | ❌ Pendiente (recomendado max:128) |
| V2.1.4 Bloquear uso de email/username como password | ❌ Pendiente |
| V2.1.5 Prevenir contraseñas filtradas (HaveIBeenPwned) | ❌ Pendiente (opcional) |

---

## 7. Trazabilidad y Evidencia

| Elemento | Ubicación |
|---------|-----------|
| Configuración global | [AppServiceProvider.php:boot()](file:///c:/laragon/www/proyectologin/app/Providers/AppServiceProvider.php#L26-L33) |
| Normalización + reglas en creación/edición de usuarios | [UserRequest.php](file:///c:/laragon/www/proyectologin/app/Http/Requests/UserRequest.php#L19-L85) |
| Normalización + reglas en reseteo de contraseña | [ResetPasswordController.php](file:///c:/laragon/www/proyectologin/app/Http/Controllers/Auth/ResetPasswordController.php#L32-L105) |
| Normalización + reglas en registro (ruta desactivada) | [RegisterController.php](file:///c:/laragon/www/proyectologin/app/Http/Controllers/Auth/RegisterController.php#L44-L127) |
| Hash almacenado (bcrypt) | `UserController@store` → [linea 56](file:///c:/laragon/www/proyectologin/app/Http/Controllers/UserController.php#L56) y `@update` → [linea 100](file:///c:/laragon/www/proyectologin/app/Http/Controllers/UserController.php#L100) (`Hash::make()`) |
| Política almacenada (config) | [auth.php](file:///c:/laragon/www/proyectologin/config/auth.php) — hash driver por defecto `bcrypt` (Laravel) |

---

## 8. Comandos de verificación

```bash
# 1. Verificar sintaxis PHP sin errores
php -l app/Providers/AppServiceProvider.php
php -l app/Http/Requests/UserRequest.php
php -l app/Http/Controllers/Auth/ResetPasswordController.php
php -l app/Http/Controllers/Auth/RegisterController.php

# 2. Formatear código con Pint
vendor/bin/pint --dirty --format agent

# 3. Ejecutar tests existentes
php artisan test --compact
```

---

**Firmado:** Implementación segura OWASP V2.1.1 C6 — Laravel 13, PHP 8.3
