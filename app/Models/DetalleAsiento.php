<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleAsiento extends Model
{
    protected $table = 'detalle_asientos';
    protected $primaryKey = 'id_detalle_asiento';
    public $timestamps = false;

    protected $fillable = [
        'id_asiento',
        'codigo_cuenta',
        'debe',
        'haber'
    ];

    protected $casts = [
        'debe' => 'decimal:2',
        'haber' => 'decimal:2'
    ];

    public function asiento()
    {
        return $this->belongsTo(AsientoContable::class, 'id_asiento', 'id_asiento');
    }

    public function cuenta()
    {
        return $this->belongsTo(CuentaPcge::class, 'codigo_cuenta', 'codigo_cuenta');
    }
}
