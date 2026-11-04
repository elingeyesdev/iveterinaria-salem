# Atención sin reserva previa

## Flujo implementado
1. Desde Consulta se elige `Atención sin reserva`.
2. Se puede registrar un cliente nuevo y después una mascota, o elegir un cliente y una mascota ya registrados.
3. La pantalla de confirmación muestra cliente y mascota.
4. Al pulsar `Continuar a consulta`, el servidor valida que la mascota pertenezca al cliente.
5. Se busca el primer horario disponible dentro de los próximos 14 días usando `ReservaService`.
6. Se crea una reserva interna sin enviar el correo de confirmación pública.
7. Se redirige al formulario de consulta con la reserva creada, conservando el modelo de datos actual.

## Migraciones
No se modificaron las migraciones ni se agregaron tablas o columnas.

## Archivos modificados
- `app/Http/Controllers/ConsultaController.php`
- `app/Http/Controllers/ClienteController.php`
- `app/Http/Controllers/MascotumController.php`
- `app/Services/ReservaService.php`
- `resources/views/consultum/create.blade.php`
- `resources/views/mascotum/create.blade.php`
- `resources/views/mascotum/form.blade.php`
- `routes/web.php`

## Verificación
Se ejecutó `php -l` en los controladores, servicio y rutas modificados; no se detectaron errores de sintaxis PHP. No se ejecutaron pruebas funcionales contra una base de datos.

## Nota importante
Como se solicitó no modificar la base de datos, la reserva interna utiliza la misma tabla y estructura de las reservas normales. Por ello no existe un campo persistente que permita distinguirlas en reportes; se distinguen durante el flujo por la pantalla y por el mensaje de éxito. El sistema selecciona el primer horario libre futuro. Si la veterinaria necesita una cola de espera separada o una etiqueta permanente para atenciones sin cita, eso requeriría una modificación de esquema posterior.
