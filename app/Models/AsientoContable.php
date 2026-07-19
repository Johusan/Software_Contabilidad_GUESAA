<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsientoContable extends Model
{
    protected $table = 'asientos_contables';
    protected $primaryKey = 'id_asiento';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'fecha_asiento',
        'glosa',
        'tipo_operacion',
        'referencia_id',
        'estado'
    ];

    protected $casts = [
        'fecha_asiento' => 'datetime',
        'referencia_id' => 'integer'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleAsiento::class, 'id_asiento', 'id_asiento');
    }
}
