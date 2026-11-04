# ETAPA 2 — Flujo clínico profesional

## Objetivo

Profesionalizar el flujo clínico sin modificar ninguna migración ni agregar columnas a las tablas existentes.

## Flujo implementado

Cliente → Mascota → Reserva → Consulta → Emergencia → Servicios → Pagos → Historial clínico

## Cambios realizados

### 1. ConsultaService
- Se mantiene como capa central de creación y actualización de consultas.
- Se agregó `eliminar()` para centralizar la eliminación segura.
- Impide eliminar una consulta con movimientos de pago.
- Desvincula `consulta_servicio` antes de eliminar.
- Libera la reserva cuando ya no tiene consultas asociadas.

### 2. ReservaService
- `ReservaController` delega creación, actualización y eliminación.
- Centraliza cambio de estado.
- Centraliza disponibilidad y generación de slots.
- Centraliza eventos para calendario.
- Centraliza búsqueda pública de mascotas por correo.
- Mantiene confirmación y recordatorio mediante `NotificacionService`.

### 3. EmergenciaService
- `ConsultaController::atencionRapidaStore()` ahora delega el flujo completo.
- Resuelve cliente existente o registra uno nuevo.
- Resuelve mascota existente verificando pertenencia al cliente o registra una nueva.
- Actualiza el peso del paciente.
- Crea la reserva técnica necesaria para conservar la estructura relacional actual.
- Crea la consulta como tipo `Emergencia` mediante `ConsultaService`.

### 4. PagoService
- `PagoController` delega registro y anulación.
- Bloquea la consulta durante la operación financiera.
- Calcula total, pagado y saldo en servidor.
- Rechaza pagos superiores al saldo.
- Conserva pagos anulados para historial.

### 5. NotificacionService
- Se utiliza desde `ReservaService`.
- Centraliza correo de confirmación.
- Programa y cancela recordatorios.
- Reutiliza `RecordatorioCorreoService` existente.

### 6. Ficha clínica de mascota

Se agregó la relación `Mascotum::consultas()` mediante `hasManyThrough`, utilizando la relación existente:

`mascota → reserva → consulta`

La vista `resources/views/mascotum/show.blade.php` ahora presenta:
- datos del paciente;
- propietario;
- historial de consultas;
- diagnóstico y tratamiento;
- veterinario;
- consultas de emergencia;
- historial de reservas;
- acceso directo a cada consulta.

## Archivos principales modificados

- `app/Services/ConsultaService.php`
- `app/Services/ReservaService.php`
- `app/Services/EmergenciaService.php`
- `app/Services/PagoService.php`
- `app/Services/NotificacionService.php`
- `app/Http/Controllers/ConsultaController.php`
- `app/Http/Controllers/ReservaController.php`
- `app/Http/Controllers/PagoController.php`
- `app/Http/Controllers/MascotumController.php`
- `app/Models/Mascotum.php`
- `resources/views/mascotum/show.blade.php`

## Base de datos

No se modificaron migraciones.

No se agregaron columnas.
No se eliminaron columnas.
No se cambiaron tablas.
No se agregaron migraciones nuevas.

## Compatibilidad

La creación y gestión de reservas requieren autenticación y permiso del submódulo. No existe un flujo público de reservas ni verificación de correo.

## Validaciones técnicas

Los archivos PHP modificados fueron comprobados con `php -l` y no presentan errores de sintaxis.

El entorno de generación no contiene `vendor/autoload.php`, por lo que no fue posible ejecutar `php artisan route:list` ni la suite de pruebas Laravel desde este entorno.
