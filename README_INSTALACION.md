# Gestión interna de reservas

La creación de reservas está disponible únicamente para el personal autenticado
con permiso de gestión de reservas. El cliente y la mascota se seleccionan desde
los registros existentes.

- Formulario interno: `GET /reserva/create`
- Guardado interno: `POST /reserva`
- Consulta de horarios disponibles: `GET /reserva/disponibilidad`

La reserva pública, la búsqueda de mascotas por correo y la verificación por
correo ya no están habilitadas. Estos cambios no modifican la estructura de la
base de datos ni requieren migraciones.

Para validar la aplicación, ejecuta `php artisan test --compact` y
`php artisan route:list --path=reserva`.
