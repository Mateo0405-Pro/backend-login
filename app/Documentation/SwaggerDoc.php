<?php

namespace App\Documentation;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *   @OA\Info(
 *       title="API de Gestión de Usuarios",
 *       version="1.0.0",
 *       description="Esta API permite registrar, autenticar y acceder a zonas protegidas mediante JWT en CodeIgniter 4.",
 *       @OA\Contact(
 *           email="emperugachil@pucesi.edu.ec"
 *       )
 *   ),
 *   @OA\Server(
 *       url="http://localhost:8081",
 *       description="Servidor local de desarrollo"
 *   )
 * )
 */
class SwaggerDoc
{
    // Clase vacía solo para definir la documentación de Swagger
}
