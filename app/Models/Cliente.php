<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';
    public $timestamps = false;

    protected $fillable = [
        'tipo_documento',
        'num_documento',
        'nombre_razon_social',
        'direccion',
        'telefono',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean'
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_cliente', 'id_cliente');
    }
}
