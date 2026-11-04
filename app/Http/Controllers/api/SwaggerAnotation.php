<?php

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="API de Proyecto Veterinaria",
 *     description="Documentación de las APIs de Clientes, Veterinarios, Servicios y Autenticación",
 *
 *     @OA\Contact(
 *         email="admin@tudominio.com"
 *     ),
 *
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Servidor Local"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Token Bearer de Sanctum (obtenido de /api/login)"
 * )
 */
