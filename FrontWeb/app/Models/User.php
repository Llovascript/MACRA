<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Usar tu tabla usuarios existente
    protected $table = 'usuarios';

    // Sin timestamps automáticos de Laravel (tu tabla no tiene created_at/updated_at)
    public $timestamps = false;

    protected $fillable = [
        'tipo',
        'nombre',
        'aP',
        'aM',
        'edad',
        'telefono',
        'correo',
        'contraseña',
        'rfc',
        'paginaWeb',
        'fundacion',
        'aprobacion',
        'del_flag',
        'rol_id',
        'estatus_id',
        'direccion_id',
    ];

    protected $hidden = [
        'contraseña',
        'remember_token',
    ];

    protected $casts = [
        'fundacion' => 'date',
        'aprobacion' => 'boolean',
        'del_flag' => 'boolean',
    ];

    // Laravel busca por defecto 'email' y 'password', pero tu tabla usa 'correo' y 'contraseña'
    public function getAuthIdentifierName()
    {
        return 'correo';
    }

    public function getAuthPassword()
    {
        return $this->contraseña;
    }

    // Accessor para que Laravel pueda usar 'email' internamente si es necesario
    public function getEmailAttribute()
    {
        return $this->correo;
    }

    public function getPasswordAttribute()
    {
        return $this->contraseña;
    }

    // Si necesitas las relaciones (opcional por ahora)
    /*
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }
    */
}