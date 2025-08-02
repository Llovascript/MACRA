<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserDatabaseController extends Controller
{
    /**
     * Usar la conexión por defecto configurada en .env
     */
    private function getConnection()
    {
        return DB::connection();
    }

    /**
     * Crear usuario de prueba para testing
     * 
     * Crea un usuario beneficiario con datos predefinidos
     * útil para desarrollo y pruebas
     */
    public function createTestUser()
    {
        try {
            $db = $this->getConnection();

            // Datos del usuario de prueba
            $userData = [
                'tipo' => 'Persona Física',
                'nombre' => 'María Elena',
                'aP' => 'González',
                'aM' => 'Rodríguez',
                'edad' => 35,
                'telefono' => '5551234567',
                'correo' => 'maria.gonzalez@ejemplo.com',
                'contraseña' => $this->hashPassword('password123'),
                'rfc' => 'GORM850615ABC',
                'paginaWeb' => 'https://maria-beneficiaria.com',
                'fundacion' => '2024-01-15',
                'aprobacion' => 1,
                'del' => 0,
                'rol_id' => 3, // Beneficiario
                'estatus_id' => 1, // Activo
                'direccion_id' => 1
            ];

            // Verificar que no exista el usuario
            $existingUser = $db->table('usuarios')
                ->where('correo', $userData['correo'])
                ->first();

            if ($existingUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario de prueba ya existe',
                    'existing_user_id' => $existingUser->id
                ]);
            }

            // Verificar que existan los IDs de referencia
            $validations = $this->validateReferenceIds($userData);
            if (!$validations['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $validations['message']
                ]);
            }

            $userId = $db->table('usuarios')->insertGetId($userData);

            Log::info('Usuario de prueba creado exitosamente', [
                'user_id' => $userId,
                'correo' => $userData['correo']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Usuario de prueba creado exitosamente',
                'user_id' => $userId,
                'credentials' => [
                    'correo' => $userData['correo'],
                    'contraseña' => 'password123'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error creando usuario de prueba: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear usuario de prueba: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Verificar estructura de la base de datos
     */
    public function checkDatabase()
    {
        try {
            $db = $this->getConnection();
            
            $tables = $db->select("SELECT name FROM sqlite_master WHERE type='table'");
            
            $tableNames = array_map(function($table) {
                return $table->name;
            }, $tables);

            $usuariosStructure = $db->select("PRAGMA table_info(usuarios)");

            return response()->json([
                'success' => true,
                'database_info' => [
                    'tables_count' => count($tableNames),
                    'tables' => $tableNames,
                    'usuarios_structure' => $usuariosStructure
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Verificar datos de referencia disponibles
     */
    public function checkReferenceData()
    {
        try {
            $db = $this->getConnection();
            
            $roles = $db->table('roles')->get();
            $estatus = $db->table('estatusG')->get();
            $direcciones = $db->table('direcciones')->get();

            return response()->json([
                'success' => true,
                'reference_data' => [
                    'roles' => $roles,
                    'estatus' => $estatus,
                    'direcciones' => $direcciones
                ],
                'summary' => [
                    'roles_count' => count($roles),
                    'estatus_count' => count($estatus),
                    'direcciones_count' => count($direcciones)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Validar que existan los IDs de referencia
     */
    private function validateReferenceIds($userData)
    {
        $db = $this->getConnection();

        $rolExists = $db->table('roles')->where('id', $userData['rol_id'])->exists();
        if (!$rolExists) {
            return [
                'valid' => false,
                'message' => 'El rol_id ' . $userData['rol_id'] . ' no existe en la tabla roles'
            ];
        }

        $estatusExists = $db->table('estatusG')->where('id', $userData['estatus_id'])->exists();
        if (!$estatusExists) {
            return [
                'valid' => false,
                'message' => 'El estatus_id ' . $userData['estatus_id'] . ' no existe en la tabla estatusG'
            ];
        }

        $direccionExists = $db->table('direcciones')->where('id', $userData['direccion_id'])->exists();
        if (!$direccionExists) {
            return [
                'valid' => false,
                'message' => 'El direccion_id ' . $userData['direccion_id'] . ' no existe en la tabla direcciones'
            ];
        }

        return ['valid' => true];
    }

    /**
     * Hash de contraseña compatible con FastAPI
     */
    private function hashPassword($password)
    {
        return Hash::make($password);
    }
}