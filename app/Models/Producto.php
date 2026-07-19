<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id_producto';
    public $timestamps = false;

    protected $fillable = [
        'id_categoria',
        'codigo_barras',
        'descripcion',
        'stock_actual',
        'stock_minimo',
        'precio_compra',
        'precio_venta',
        'estado'
    ];

    protected $casts = [
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'stock_actual' => 'integer',
        'stock_minimo' => 'integer',
        'estado' => 'boolean'
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

    public function detallesCompra()
    {
        return $this->hasMany(DetalleCompra::class, 'id_producto', 'id_producto');
    }

    public function detallesVenta()
    {
        return $this->hasMany(DetalleVenta::class, 'id_producto', 'id_producto');
    }
}
