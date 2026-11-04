# Cambios: Emergencia / Walk-in sin modificar tablas

Se modificaron únicamente estos archivos:

- `app/Http/Controllers/ConsultaController.php`
- `app/Services/ConsultaService.php`
- `resources/views/consultum/form.blade.php`

No se modificaron migraciones ni `script/script.sql`.

## Flujo implementado

1. Si llega un cliente existente, se reutiliza.
2. Si llega un cliente nuevo, se registra.
3. Si la mascota existe, se verifica que pertenezca al cliente seleccionado.
4. Si la mascota es nueva, se registra.
5. Se crea una reserva técnica interna para la emergencia.
6. Se crea la consulta con `tipo = Emergencia` y `reserva_id` apuntando a esa reserva técnica.
7. Se abre directamente la edición de la consulta.
8. Al editar una emergencia, se conserva `tipo = Emergencia` y no se permite cambiar la reserva/origen desde el formulario.

## Importante

La relación usada es:

`consulta.reserva_id -> reserva.mascota_id -> mascota.cliente_id`

No se usa ni se agrega `consulta.mascota_id`.
