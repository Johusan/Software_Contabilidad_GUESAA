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
                'password' => Hash::make('admin123'),
                'estado' => true
            ],
            [
                'id_usuario' => 2,
                'id_rol' => 2,
                'nombres' => 'Abima Galilei',
                'apellidos' => 'Lozano Paz',
                'email' => 'cajero@guesaa.com',
                'password' => Hash::make('cajero123'),
                'estado' => true
            ],
            [
                'id_usuario' => 3,
                'id_rol' => 3,
                'nombres' => 'Anggie Fiorella',
                'apellidos' => 'Tarrillo Rojas',
                'email' => 'almacenero@guesaa.com',
                'password' => Hash::make('almacenero123'),
                'estado' => true
            ],
        ];

        foreach ($usuarios as $u) {
            Usuario::updateOrCreate(['id_usuario' => $u['id_usuario']], $u);
        }
        echo "Usuarios de prueba sembrados con éxito.\n";

        // 3. Seed Plan de Cuentas (PCGE)
        $filePath = 'j:\\Documentos\\Proyectos\\Software Contabilidad\\Plan de Cuentas PCGE 2026 (1)_extracted.txt';
        
        if (file_exists($filePath)) {
            $lines = file($filePath);
            $accounts = [];
            $currentCode = null;

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                
                // Ignorar cabeceras y elementos
                if (str_starts_with(strtoupper($line), 'ELEMENTO') || str_starts_with(strtoupper($line), '--- PAGE')) {
                    continue;
                }
                
                if (preg_match('/^([0-9]+)\s+(.*)$/', $line, $matches)) {
                    $currentCode = $matches[1];
                    // Filtrar para conservar únicamente cuentas de 2 y 3 dígitos
                    if (strlen($currentCode) <= 3) {
                        $denom = trim($matches[2]);
                        $accounts[$currentCode] = [
                            'codigo_cuenta' => $currentCode,
                            'denominacion' => $denom,
                            'elemento' => intval(substr($currentCode, 0, 1)),
                            'estado' => true
                        ];
                    } else {
                        $currentCode = null;
                    }
                } else {
                    if ($currentCode && isset($accounts[$currentCode]) && !str_contains($line, 'Curso:') && !str_contains($line, 'CUADRO DE CLASIFICACIÓN') && !str_contains($line, 'Elaborado por:')) {
                        $accounts[$currentCode]['denominacion'] .= ' ' . $line;
                    }
                }
            }

            // Cuentas contables específicas de 3 dígitos para compras, ventas, caja e IGV
            $requiredAccounts = [
                '101' => ['denominacion' => 'Caja', 'elemento' => 1],
                '104' => ['denominacion' => 'Cuentas corrientes en instituciones financieras', 'elemento' => 1],
                '105' => ['denominacion' => 'Otros equivalentes de efectivo', 'elemento' => 1],
                '121' => ['denominacion' => 'Facturas, boletas y otros comprobantes por cobrar', 'elemento' => 1],
                '201' => ['denominacion' => 'Mercaderías', 'elemento' => 2],
                '401' => ['denominacion' => 'Gobierno central', 'elemento' => 4],
                '421' => ['denominacion' => 'Facturas, boletas y otros comprobantes por pagar', 'elemento' => 4],
                '501' => ['denominacion' => 'Capital social', 'elemento' => 5],
                '601' => ['denominacion' => 'Mercaderías', 'elemento' => 6],
                '611' => ['denominacion' => 'Mercaderías', 'elemento' => 6],
                '659' => ['denominacion' => 'Otros gastos de gestión', 'elemento' => 6],
                '701' => ['denominacion' => 'Mercaderías', 'elemento' => 7],
                '704' => ['denominacion' => 'Prestación de servicios', 'elemento' => 7],
            ];

            foreach ($requiredAccounts as $code => $data) {
                $accounts[$code] = [
                    'codigo_cuenta' => $code,
                    'denominacion' => $data['denominacion'],
                    'elemento' => $data['elemento'],
                    'estado' => true
                ];
            }

            // Insertar/actualizar en la base de datos
            DB::beginTransaction();
            try {
                foreach ($accounts as $code => $acc) {
                    CuentaPcge::updateOrCreate(
                        ['codigo_cuenta' => $code],
                        [
                            'denominacion' => substr(trim($acc['denominacion']), 0, 150),
                            'elemento' => $acc['elemento'],
                            'estado' => true
                        ]
                    );
                }
                DB::commit();
                echo "Plan de cuentas (PCGE) sembrado con éxito (" . count($accounts) . " cuentas a 3 dígitos).\n";
            } catch (\Exception $e) {
                DB::rollBack();
                echo "Error al sembrar el plan de cuentas: " . $e->getMessage() . "\n";
            }
        } else {
            echo "No se encontró el archivo del plan de cuentas en la ruta indicada: $filePath\n";
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
