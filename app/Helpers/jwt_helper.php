<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Clave secreta para firmar el token (escoge una segura)
const JWT_SECRET_KEY = 'Mateo123';

// Función para crear un JWT
function createJWT(array $data): string
{
    $issuedAt = time();
    $expirationTime = $issuedAt + 3600; // 1 hora de duración

    $payload = array_merge($data, [
        'iat' => $issuedAt,
        'exp' => $expirationTime,
    ]);

    return JWT::encode($payload, JWT_SECRET_KEY, 'HS256');
}

// Función para verificar un JWT
function verifyJWT(string $token)
{
    try {
        return JWT::decode($token, new Key(JWT_SECRET_KEY, 'HS256'));
    } catch (\Exception $e) {
        return null;
    }
}