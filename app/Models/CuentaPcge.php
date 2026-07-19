<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaPcge extends Model
{
    protected $table = 'cuentas_pcge';
    protected $primaryKey = 'codigo_cuenta';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'codigo_cuenta',
        'denominacion',
        'elemento',
        'estado'
    ];

    protected $casts = [
        'elemento' => 'integer',
        'estado' => 'boolean'
    ];

    public function detallesAsiento()
    {
        return $this->hasMany(DetalleAsiento::class, 'codigo_cuenta', 'codigo_cuenta');
    }
}
