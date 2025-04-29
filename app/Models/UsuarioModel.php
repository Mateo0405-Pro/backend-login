<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table      = 'tbl_usuarios';  // Nombre real de la tabla
    protected $primaryKey = 'id';             // Clave primaria

    protected $allowedFields = [
        'nombre',
        'apellido',
        'fecha_nacimiento',
        'genero',
        'correo',
        'telefono',
        'contrasena',
        'rol_id',
        'fecha_registro'
    ];

    protected $useTimestamps = false; 
    protected $returnType    = 'array';
}