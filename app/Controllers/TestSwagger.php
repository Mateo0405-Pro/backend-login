<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use OpenApi\Annotations as OA;

class TestSwagger extends ResourceController
{
    /**
     * @OA\Get(
     *     path="/api/test",
     *     summary="Endpoint de prueba",
     *     tags={"Test"},
     *     @OA\Response(response=200, description="Respuesta exitosa")
     * )
     */
    public function test()
    {
        return $this->respond(['message' => 'Funciona Swagger']);
    }
}