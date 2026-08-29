<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Models\Usuario;
use App\Models\CuentaPcge;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Roles
        $roles = [
            ['id_rol' => 1, 'nombre_rol' => 'Administrador', 'descripcion' => 'Acceso total al sistema y configuración'],
            ['id_rol' => 2, 'nombre_rol' => 'Cajero', 'descripcion' => 'Gestión de caja y registro de ventas'],
            ['id_rol' => 3, 'nombre_rol' => 'Almacenero', 'descripcion' => 'Gestión de productos, categorías y compras'],
        ];

        foreach ($roles as $rol) {
            Rol::updateOrCreate(['id_rol' => $rol['id_rol']], $rol);
        }
        echo "Roles sembrados con éxito.\n";

        // 2. Seed Usuarios
        $usuarios = [
            [
                'id_usuario' => 1,
                'id_rol' => 1,
                'nombres' => 'Jhon Wilhelm',
                'apellidos' => 'Huaman Sánchez',
                'email' => 'admin@guesaa.com',
                'password' => Hash::make('password'),
                'estado' => true
            ],
            [
                'id_usuario' => 2,
                'id_rol' => 2,
                'nombres' => 'Abima Galilei',
                'apellidos' => 'Lozano Paz',
                'email' => 'cajero@guesaa.com',
                'password' => Hash::make('password'),
                'estado' => true
            ],
            [
                'id_usuario' => 3,
                'id_rol' => 3,
                'nombres' => 'Anggie Fiorella',
                'apellidos' => 'Tarrillo Rojas',
                'email' => 'almacenero@guesaa.com',
                'password' => Hash::make('password'),
                'estado' => true
            ],
        ];

        foreach ($usuarios as $u) {
            Usuario::updateOrCreate(['id_usuario' => $u['id_usuario']], $u);
        }
        echo "Usuarios de prueba sembrados con éxito.\n";

        // 3. Seed Plan de Cuentas Oficial (PCGE 2026 desde archivo JSON incluido)
        $jsonPath = database_path('seeders/data/pcge_2026.json');
        
        if (file_exists($jsonPath)) {
            $accounts = json_decode(file_get_contents($jsonPath), true);
            
            if (is_array($accounts)) {
                DB::beginTransaction();
                try {
                    foreach ($accounts as $acc) {
                        CuentaPcge::updateOrCreate(
                            ['codigo_cuenta' => (string)$acc['codigo_cuenta']],
                            [
                                'denominacion' => substr(trim($acc['denominacion']), 0, 150),
                                'elemento' => intval($acc['elemento']),
                                'estado' => true
                            ]
                        );
                    }
                    DB::commit();
                    echo "Plan de Cuentas Oficial (PCGE 2026) sembrado con éxito (" . count($accounts) . " cuentas/subcuentas).\n";
                } catch (\Exception $e) {
                    DB::rollBack();
                    echo "Error al sembrar el plan de cuentas: " . $e->getMessage() . "\n";
                }
            }
        } else {
            echo "No se encontró el archivo del plan de cuentas en: $jsonPath\n";
        }

        // 4. Sembrar Categorías
        $cat1 = \App\Models\Categoria::updateOrCreate(['nombre' => 'Medicamentos'], ['descripcion' => 'Productos farmacéuticos y fármacos']);
        $cat2 = \App\Models\Categoria::updateOrCreate(['nombre' => 'Cuidado Personal'], ['descripcion' => 'Artículos de higiene y aseo']);
        $cat3 = \App\Models\Categoria::updateOrCreate(['nombre' => 'Perfumería'], ['descripcion' => 'Fragancias y cosméticos']);

        // 5. Sembrar Productos
        \App\Models\Producto::updateOrCreate(
            ['codigo_barras' => '7750102030405'],
            [
                'id_categoria' => $cat1->id_categoria,
                'descripcion' => 'Paracetamol 500mg (Caja x 100 tab)',
                'stock_actual' => 50,
                'stock_minimo' => 10,
                'precio_compra' => 8.50,
                'precio_venta' => 15.00,
                'estado' => true
            ]
        );

        \App\Models\Producto::updateOrCreate(
            ['codigo_barras' => '7750102030504'],
            [
                'id_categoria' => $cat1->id_categoria,
                'descripcion' => 'Ibuprofeno 400mg (Caja x 100 tab)',
                'stock_actual' => 45,
                'stock_minimo' => 10,
                'precio_compra' => 9.20,
                'precio_venta' => 18.00,
                'estado' => true
            ]
        );

        \App\Models\Producto::updateOrCreate(
            ['codigo_barras' => '7750201040608'],
            [
                'id_categoria' => $cat2->id_categoria,
                'descripcion' => 'Jabón Antibacteriano Líquido 400ml',
                'stock_actual' => 12,
                'stock_minimo' => 5,
                'precio_compra' => 4.50,
                'precio_venta' => 9.00,
                'estado' => true
            ]
        );

        // 6. Sembrar Clientes
        \App\Models\Cliente::updateOrCreate(
            ['num_documento' => '00000000'],
            [
                'tipo_documento' => 'DNI',
                'nombre_razon_social' => 'PÚBLICO GENERAL',
                'direccion' => 'Chiclayo',
                'telefono' => '',
                'estado' => true
            ]
        );

        \App\Models\Cliente::updateOrCreate(
            ['num_documento' => '75315984'],
            [
                'tipo_documento' => 'DNI',
                'nombre_razon_social' => 'Jhon Wilhelm Huaman',
                'direccion' => 'Lambayeque',
                'telefono' => '951753852',
                'estado' => true
            ]
        );

        // 7. Sembrar Proveedores
        \App\Models\Proveedor::updateOrCreate(
            ['ruc' => '20123456789'],
            [
                'razon_social' => 'DIFARMA S.A.C.',
                'direccion' => 'Av. Salaverry 456, Lima',
                'telefono' => '01-4567890',
                'estado' => true
            ]
        );

        \App\Models\Proveedor::updateOrCreate(
            ['ruc' => '20503020109'],
            [
                'razon_social' => 'ALBIS S.A.C.',
                'direccion' => 'Jr. Carabaya 789, Lima',
                'telefono' => '01-3151515',
                'estado' => true
            ]
        );

        echo "Categorías, productos, clientes y proveedores de base sembrados.\n";
    }
}
