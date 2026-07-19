<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passkeys\Contracts\PasskeyUser;
use Laravel\Passkeys\PasskeyAuthenticatable;

class Usuario extends Authenticatable implements PasskeyUser
{
    use Notifiable, PasskeyAuthenticatable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    protected $fillable = [
        'id_rol',
        'nombres',
        'apellidos',
        'email',
        'password',
        'estado',
        'name'
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Accesor virtual para name (mapeado a nombres).
     */
    public function getNameAttribute(): ?string
    {
        return $this->nombres;
    }

    /**
     * Mutador virtual para name (mapeado a nombres).
     */
    public function setNameAttribute(?string $value): void
    {
        $this->attributes['nombres'] = $value;
    }

    /**
     * Obtener las llaves de acceso asociadas con el usuario contable.
     */
    public function passkeys(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\Laravel\Passkeys\Passkey::class, 'user_id', 'id_usuario');
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    public function compras()
    {
        return $this->hasMany(Compra::class, 'id_usuario', 'id_usuario');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_usuario', 'id_usuario');
    }

    public function cajas()
    {
        return $this->hasMany(CajaDiaria::class, 'id_usuario', 'id_usuario');
    }

    public function asientos()
    {
        return $this->hasMany(AsientoContable::class, 'id_usuario', 'id_usuario');
    }
}
