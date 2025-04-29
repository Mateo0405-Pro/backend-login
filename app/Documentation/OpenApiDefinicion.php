<?php

namespace App\Documentation;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         version="1.0.0",
 *         title="API de Gestión de Usuarios",
 *         description="Esta API permite registrar, autenticar y acceder a zonas protegidas mediante JWT en CodeIgniter 4.",
 *         @OA\Contact(
 *             email="emperugachil@pucesi.edu.ec"
 *         )
 *     ),
 *     @OA\Server(
 *         url="http://localhost:8081",
 *         description="Servidor local"
 *     )
 * )
 *
 * @OA\Tag(
 *     name="Auth",
 *     description="Operaciones de autenticación"
 * )
 *
 * @OA\PathItem(
 *     path="/api/register"
 * )
 */
class OpenApiDefinicion {}