<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UsuarioModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use OpenApi\Annotations as OA;
use Firebase\JWT\JWT; // ahora usarás JWT aquí también
use Firebase\JWT\Key; // para verificar luego



class AuthController extends ResourceController
{
    protected $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

/**
 * @OA\Post(
 *     path="/api/register",
 *     summary="Registrar nuevo usuario",
 *     tags={"Auth"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"nombre", "apellido", "fecha_nacimiento", "genero", "correo", "telefono", "contrasena", "rol_id"},
 *             @OA\Property(property="nombre", type="string"),
 *             @OA\Property(property="apellido", type="string"),
 *             @OA\Property(property="fecha_nacimiento", type="string", format="date"),
 *             @OA\Property(property="genero", type="string"),
 *             @OA\Property(property="correo", type="string", format="email"),
 *             @OA\Property(property="telefono", type="string"),
 *             @OA\Property(property="contrasena", type="string"),
 *             @OA\Property(property="rol_id", type="integer")
 *         )
 *     ),
 *     @OA\Response(response=201, description="Usuario registrado correctamente"),
 *     @OA\Response(response=400, description="Error de validación"),
 *     @OA\Response(response=500, description="Error de servidor")
 * )
 */
    
    public function register()
{
    $data = $this->request->getJSON(true);

    // Validaciones básicas
    if (
        empty($data['nombre']) ||
        empty($data['apellido']) ||
        empty($data['fecha_nacimiento']) ||
        empty($data['genero']) ||
        empty($data['correo']) ||
        empty($data['telefono']) ||
        empty($data['contrasena']) ||
        empty($data['rol_id'])
    ) {
        return $this->failValidationErrors('Todos los campos son obligatorios.');
    }

    if (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
        return $this->failValidationErrors('Correo electrónico inválido.');
    }

    if (!ctype_digit($data['telefono'])) {
        return $this->failValidationErrors('El teléfono debe contener solo números.');
    }

    if (!is_numeric($data['rol_id'])) {
        return $this->failValidationErrors('El rol debe ser un número válido.');
    }

    // Validar la seguridad de la contraseña
    $password = $data['contrasena'];
    if (strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) || // Al menos una mayúscula
        !preg_match('/[a-z]/', $password) || // Al menos una minúscula
        !preg_match('/\d/', $password) ||    // Al menos un número
        !preg_match('/[^a-zA-Z\d]/', $password) // Al menos un carácter especial
    ) {
        return $this->failValidationErrors('La contraseña debe tener mínimo 8 caracteres, incluyendo una mayúscula, una minúscula, un número y un carácter especial.');
    }

    // Validar fecha de nacimiento
    $fecha = date_create_from_format('Y-m-d', $data['fecha_nacimiento']);
    if (!$fecha || $fecha->format('Y-m-d') !== $data['fecha_nacimiento']) {
        return $this->failValidationErrors('La fecha de nacimiento no tiene un formato válido (Y-m-d).');
    }
    if ($fecha > new \DateTime()) {
        return $this->failValidationErrors('La fecha de nacimiento no puede ser una fecha futura.');
    }

    // Preparar datos para insertar
    $usuario = [
        'nombre' => $data['nombre'],
        'apellido' => $data['apellido'],
        'fecha_nacimiento' => $data['fecha_nacimiento'],
        'genero' => $data['genero'],
        'correo' => $data['correo'],
        'telefono' => $data['telefono'],
        'contrasena' => password_hash($data['contrasena'], PASSWORD_DEFAULT),
        'rol_id' => (int) $data['rol_id'],
        'fecha_registro' => date('Y-m-d H:i:s')
    ];

    try {
        $this->usuarioModel->insert($usuario);
    
        return $this->respondCreated([
            'status' => 201,
            'message' => 'Usuario registrado correctamente'
        ]);
    } catch (DatabaseException $e) {
        return $this->failServerError('Error BD: ' . $e->getMessage());
    } catch (\Throwable $e) {
        return $this->failServerError('Error General: ' . $e->getMessage());
    }
}

/**
 * @OA\Post(
 *     path="/api/login",
 *     summary="Inicio de sesión de usuarios",
 *     tags={"Auth"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"correo", "contrasena"},
 *             @OA\Property(property="correo", type="string", example="usuario@correo.com"),
 *             @OA\Property(property="contrasena", type="string", example="12345678")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Inicio de sesión exitoso."
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Credenciales inválidas."
 *     )
 * )
 */


public function login()
{
    $data = $this->request->getJSON(true);

    $correo = $data['correo'] ?? null;
    $contrasena = $data['contrasena'] ?? null;

    if (empty($correo) || empty($contrasena)) {
        return $this->failValidationError('Correo y contraseña son obligatorios.');
    }

    $usuario = $this->usuarioModel->where('correo', $correo)->first();

    if (!$usuario) {
        return $this->failNotFound('El correo no se encuentra registrado.');
    }

    if (!password_verify($contrasena, $usuario['contrasena'])) {
        return $this->failUnauthorized('Contraseña incorrecta.');
    }

    // ✅ Generar el token JWT
    helper('jwt'); // Cargar nuestro helper
    $token = createJWT([
        'id' => $usuario['id'],
        'nombre' => $usuario['nombre'],
        'correo' => $usuario['correo'],
        'rol_id' => $usuario['rol_id']
    ]);

    return $this->respond([
        'status' => 200,
        'message' => 'Login exitoso',
        'token' => $token,
    ]);
}

    // Función privada para validar la seguridad de la contraseña
    private function isPasswordSecure(string $password): bool
    {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password);
    }

    /**
 * @OA\Get(
 *     path="/api/protected",
 *     summary="Acceso protegido mediante token JWT",
 *     tags={"Auth"},
 *     @OA\Parameter(
 *         name="Authorization",
 *         in="header",
 *         required=true,
 *         description="Token JWT en formato Bearer",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response=200, description="Acceso concedido a zona protegida"),
 *     @OA\Response(response=401, description="Token inválido o no encontrado")
 * )
 */


    public function protected()
{
    helper('jwt');

    $authHeader = $this->request->getHeaderLine('Authorization');

    if (!$authHeader) {
        return $this->failUnauthorized('Token de autorización no encontrado.');
    }

    $token = str_replace('Bearer ', '', $authHeader);

    $decoded = verifyJWT($token);

    if (!$decoded) {
        return $this->failUnauthorized('Token inválido o expirado.');
    }

    return $this->respond([
        'status' => 200,
        'message' => 'Acceso concedido a zona protegida.',
        'user' => $decoded
    ]);
}
}