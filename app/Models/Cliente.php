<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Cliente extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'clientes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'apellido',
        'tipo_documento',
        'numero_documento',
        'email',
        'password',
        'telefono',
        'fecha_nacimiento',
        'direccion',
        'ciudad',
        'departamento',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'fecha_nacimiento' => 'date',
        'password' => 'hashed',
    ];

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'email';
    }

    /**
     * Get the full name attribute.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    /**
     * Get the formatted document attribute.
     *
     * @return string
     */
    public function getFormattedDocumentAttribute()
    {
        return $this->tipo_documento . ' ' . $this->numero_documento;
    }
}