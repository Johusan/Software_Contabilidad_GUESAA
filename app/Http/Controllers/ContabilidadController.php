<?php

namespace App\Http\Controllers;

use App\Models\AsientoContable;
use App\Models\DetalleAsiento;
use App\Models\CuentaPcge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ContabilidadController extends Controller
{
    public function index(Request $request)
    {
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');

        // 1. Libro Diario
        $queryDiario = AsientoContable::with(['usuario', 'detalles.cuenta'])
            ->where('estado', 'ACTIVO');

        if ($fechaDesde) {
            $queryDiario->whereDate('fecha_asiento', '>=', $fechaDesde);
        }
        if ($fechaHasta) {
            $queryDiario->whereDate('fecha_asiento', '<=', $fechaHasta);
        }

        $asientosDiario = $queryDiario->orderBy('fecha_asiento', 'desc')
            ->orderBy('id_asiento', 'desc')
            ->get();

        // 2. Libro Mayor
        $queryDetalles = DB::table('detalle_asientos')
            ->join('asientos_contables', 'detalle_asientos.id_asiento', '=', 'asientos_contables.id_asiento')
            ->join('cuentas_pcge', 'detalle_asientos.codigo_cuenta', '=', 'cuentas_pcge.codigo_cuenta')
            ->where('asientos_contables.estado', 'ACTIVO');

        if ($fechaDesde) {
            $queryDetalles->whereDate('asientos_contables.fecha_asiento', '>=', $fechaDesde);
        }
        if ($fechaHasta) {
            $queryDetalles->whereDate('asientos_contables.fecha_asiento', '<=', $fechaHasta);
        }

        $movimientos = $queryDetalles->select(
            'detalle_asientos.id_detalle_asiento',
            'detalle_asientos.id_asiento',
            'asientos_contables.fecha_asiento',
            'asientos_contables.glosa',
            'asientos_contables.tipo_operacion',
            'detalle_asientos.codigo_cuenta',
            'cuentas_pcge.denominacion as nombre_cuenta',
            'detalle_asientos.debe',
            'detalle_asientos.haber'
        )
        ->orderBy('detalle_asientos.codigo_cuenta')
        ->orderBy('asientos_contables.fecha_asiento', 'asc')
        ->orderBy('asientos_contables.id_asiento', 'asc')
        ->get();

        // 3. Balance de Comprobación (Balanza)
        $queryBal = DB::table('detalle_asientos')
            ->join('asientos_contables', 'detalle_asientos.id_asiento', '=', 'asientos_contables.id_asiento')
            ->join('cuentas_pcge', 'detalle_asientos.codigo_cuenta', '=', 'cuentas_pcge.codigo_cuenta')
            ->where('asientos_contables.estado', 'ACTIVO');

        if ($fechaDesde) {
            $queryBal->whereDate('asientos_contables.fecha_asiento', '>=', $fechaDesde);
        }
        if ($fechaHasta) {
            $queryBal->whereDate('asientos_contables.fecha_asiento', '<=', $fechaHasta);
        }

        $balanza = $queryBal->select(
            'detalle_asientos.codigo_cuenta',
            'cuentas_pcge.denominacion as nombre_cuenta',
            DB::raw('SUM(detalle_asientos.debe) as suma_debe'),
            DB::raw('SUM(detalle_asientos.haber) as suma_haber')
        )
        ->groupBy('detalle_asientos.codigo_cuenta', 'cuentas_pcge.denominacion')
        ->orderBy('detalle_asientos.codigo_cuenta')
        ->get()
        ->map(function ($row) {
            $debe = (float)$row->suma_debe;
            $haber = (float)$row->suma_haber;
            $saldoDeudor = max(0, $debe - $haber);
            $saldoAcreedor = max(0, $haber - $debe);

            return [
                'codigo_cuenta' => (string)$row->codigo_cuenta,
                'nombre_cuenta' => $row->nombre_cuenta,
                'suma_debe' => $debe,
                'suma_haber' => $haber,
                'saldo_deudor' => $saldoDeudor,
                'saldo_acreedor' => $saldoAcreedor
            ];
        });

        // 4. Estados Financieros y Ratios
        $estadoResultados = $this->calcularEstadoResultados($balanza);
        $estadoPatrimonio = $this->calcularEstadoPatrimonio($balanza, $estadoResultados['utilidad_neta']);
        $balanceGeneral = $this->calcularBalanceGeneral($balanza, $estadoPatrimonio['total_patrimonio']);
        $ratios = $this->calcularRatiosFinancieros($balanceGeneral, $estadoResultados, $estadoPatrimonio);

        // Cuentas del catálogo para el selector de asientos manuales
        $cuentasCatalogo = CuentaPcge::where('estado', true)
            ->orderBy('codigo_cuenta')
            ->get();

        return Inertia::render('Contabilidad/Index', [
            'asientosDiario' => $asientosDiario,
            'movimientosMayor' => $movimientos,
            'balanza' => $balanza,
            'estadoResultados' => $estadoResultados,
            'estadoPatrimonio' => $estadoPatrimonio,
            'balanceGeneral' => $balanceGeneral,
            'ratios' => $ratios,
            'cuentasCatalogo' => $cuentasCatalogo,
            'filters' => [
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta
            ]
        ]);
    }

    /**
     * Calcula el Estado de Resultados (Ganancias y Pérdidas)
     */
    private function calcularEstadoResultados($balanza)
    {
        $cuentas = [];
        $totalDebe = 0;
        $totalHaber = 0;

        foreach ($balanza as $item) {
            $codigo = (string)$item['codigo_cuenta'];
            $clase = substr($codigo, 0, 1);

            // Elemento 6 (Gastos y Costos) y Elemento 7 (Ingresos)
            if (in_array($clase, ['6', '7'])) {
                $debe = (float)$item['saldo_deudor'];
                $haber = (float)$item['saldo_acreedor'];

                if ($debe > 0 || $haber > 0) {
                    $cuentas[] = [
                        'codigo' => $codigo,
                        'cuenta' => $item['nombre_cuenta'],
                        'debe' => $debe,
                        'haber' => $haber
                    ];
                    $totalDebe += $debe;
                    $totalHaber += $haber;
                }
            }
        }

        // Ordenar correlativamente por código de cuenta PCGE
        usort($cuentas, fn($a, $b) => strcmp($a['codigo'], $b['codigo']));

        $utilidadNeta = $totalHaber - $totalDebe;

        return [
            'cuentas' => $cuentas,
            'total_debe' => $totalDebe,
            'total_haber' => $totalHaber,
            'utilidad_neta' => $utilidadNeta
        ];
    }

    /**
     * Calcula el Estado de Cambios en el Patrimonio Neto
     */
    private function calcularEstadoPatrimonio($balanza, $utilidadNeta)
    {
        $capitalSocial = 0;
        $donaciones = 0;
        $utilidadesRetenidasInicial = 0;
        $dividendos = 0;

        foreach ($balanza as $item) {
            $codigo = $item['codigo_cuenta'];
            $dosDigitos = substr($codigo, 0, 2);

            // Cuenta 50 (Capital)
            if ($dosDigitos === '50') {
                $capitalSocial += ($item['saldo_acreedor'] > 0 ? $item['saldo_acreedor'] : ($item['suma_haber'] - $item['suma_debe']));
            }
            // Cuentas 52, 58 (Capital Adicional, Reservas, Donaciones)
            elseif (in_array($dosDigitos, ['52', '58'])) {
                $donaciones += ($item['saldo_acreedor'] > 0 ? $item['saldo_acreedor'] : ($item['suma_haber'] - $item['suma_debe']));
            }
            // Cuenta 59 (Resultados Acumulados / Utilidades Retenidas anteriores)
            elseif ($dosDigitos === '59') {
                $utilidadesRetenidasInicial += ($item['saldo_acreedor'] > 0 ? $item['saldo_acreedor'] : ($item['suma_haber'] - $item['suma_debe']));
                if ($item['suma_debe'] > 0) {
                    $dividendos += $item['suma_debe'];
                }
            }
        }

        $saldosIniciales = [
            'capital_social' => $capitalSocial,
            'donaciones' => $donaciones,
            'utilidades_retenidas' => $utilidadesRetenidasInicial,
            'exceso_insuficiencia' => 0,
            'total' => $capitalSocial + $donaciones + $utilidadesRetenidasInicial
        ];

        $utilidadNetaFila = [
            'capital_social' => 0,
            'donaciones' => 0,
            'utilidades_retenidas' => $utilidadNeta,
            'exceso_insuficiencia' => 0,
            'total' => $utilidadNeta
        ];

        $dividendosFila = [
            'capital_social' => 0,
            'donaciones' => 0,
            'utilidades_retenidas' => -$dividendos,
            'exceso_insuficiencia' => 0,
            'total' => -$dividendos
        ];

        $utilidadesRetenidasFinal = $utilidadesRetenidasInicial + $utilidadNeta - $dividendos;
        $totalPatrimonio = $capitalSocial + $donaciones + $utilidadesRetenidasFinal;

        $saldosFinales = [
            'capital_social' => $capitalSocial,
            'donaciones' => $donaciones,
            'utilidades_retenidas' => $utilidadesRetenidasFinal,
            'exceso_insuficiencia' => 0,
            'total' => $totalPatrimonio
        ];

        return [
            'saldos_iniciales' => $saldosIniciales,
            'utilidad_neta_fila' => $utilidadNetaFila,
            'dividendos_fila' => $dividendosFila,
            'saldos_finales' => $saldosFinales,
            'total_patrimonio' => $totalPatrimonio,
            'capital_social' => $capitalSocial,
            'utilidades_retenidas' => $utilidadesRetenidasFinal
        ];
    }

    /**
     * Calcula el Balance General (Estado de Situación Financiera)
     */
    private function calcularBalanceGeneral($balanza, $totalPatrimonioCalculado)
    {
        $activoCirculante = [];
        $activoNoCirculante = [];
        $pasivoCortoPlazo = [];
        $pasivoLargoPlazo = [];

        $totalActivoCirculante = 0;
        $totalActivoNoCirculante = 0;
        $totalPasivoCP = 0;
        $totalPasivoLP = 0;
        $totalInventarios = 0;

        foreach ($balanza as $item) {
            $codigo = $item['codigo_cuenta'];
            $dosDigitos = substr($codigo, 0, 2);
            $clase = substr($codigo, 0, 1);

            // ACTIVO (Clases 1, 2, 3)
            if (in_array($clase, ['1', '2', '3'])) {
                // Activo Circulante (Clase 1 excepto 19, Clase 2)
                if (in_array($dosDigitos, ['10', '11', '12', '13', '14', '16', '17', '18', '20', '21', '22', '23', '24', '25', '26', '28'])) {
                    $saldo = $item['saldo_deudor'] > 0 ? $item['saldo_deudor'] : ($item['suma_debe'] - $item['suma_haber']);
                    if ($saldo != 0) {
                        $activoCirculante[] = [
                            'codigo' => $codigo,
                            'cuenta' => $item['nombre_cuenta'],
                            'monto' => $saldo
                        ];
                        $totalActivoCirculante += $saldo;

                        // Suma de inventarios para prueba ácida
                        if (in_array($dosDigitos, ['20', '21', '22', '23', '24', '25', '26', '28'])) {
                            $totalInventarios += $saldo;
                        }
                    }
                }
                // Activo No Circulante (Clase 3: Inmuebles, Maquinaria, Equipo, Intangibles)
                elseif (in_array($dosDigitos, ['30', '31', '32', '33', '34', '35', '36', '37', '38'])) {
                    $saldo = $item['saldo_deudor'] > 0 ? $item['saldo_deudor'] : ($item['suma_debe'] - $item['suma_haber']);
                    if ($saldo != 0) {
                        $activoNoCirculante[] = [
                            'codigo' => $codigo,
                            'cuenta' => $item['nombre_cuenta'],
                            'monto' => $saldo
                        ];
                        $totalActivoNoCirculante += $saldo;
                    }
                }
                // Depreciación y Amortización Acumulada (Cuenta 39 - Saldo Acreedor que resta al Activo)
                elseif ($dosDigitos === '39') {
                    $saldoAcreedor = $item['saldo_acreedor'] > 0 ? $item['saldo_acreedor'] : ($item['suma_haber'] - $item['suma_debe']);
                    if ($saldoAcreedor != 0) {
                        $activoNoCirculante[] = [
                            'codigo' => $codigo,
                            'cuenta' => $item['nombre_cuenta'],
                            'monto' => -$saldoAcreedor // Se presenta restando
                        ];
                        $totalActivoNoCirculante -= $saldoAcreedor;
                    }
                }
            }

            // PASIVO (Clase 4)
            elseif ($clase === '4') {
                $saldo = $item['saldo_acreedor'] > 0 ? $item['saldo_acreedor'] : ($item['suma_haber'] - $item['suma_debe']);
                if ($saldo != 0) {
                    // Pasivo Largo Plazo (Cuentas 45 o deudas a LP)
                    if ($dosDigitos === '45') {
                        $pasivoLargoPlazo[] = [
                            'codigo' => $codigo,
                            'cuenta' => $item['nombre_cuenta'],
                            'monto' => $saldo
                        ];
                        $totalPasivoLP += $saldo;
                    } else {
                        // Pasivo Corto Plazo (40, 41, 42, 43, 46, 47, etc.)
                        $pasivoCortoPlazo[] = [
                            'codigo' => $codigo,
                            'cuenta' => $item['nombre_cuenta'],
                            'monto' => $saldo
                        ];
                        $totalPasivoCP += $saldo;
                    }
                }
            }
        }

        $totalActivo = $totalActivoCirculante + $totalActivoNoCirculante;
        $totalPasivo = $totalPasivoCP + $totalPasivoLP;
        $totalPasivoMasPatrimonio = $totalPasivo + $totalPatrimonioCalculado;

        return [
            'activo_circulante' => $activoCirculante,
            'total_activo_circulante' => $totalActivoCirculante,
            'activo_no_circulante' => $activoNoCirculante,
            'total_activo_no_circulante' => $totalActivoNoCirculante,
            'total_activo' => $totalActivo,
            'total_inventarios' => $totalInventarios,

            'pasivo_corto_plazo' => $pasivoCortoPlazo,
            'total_pasivo_cp' => $totalPasivoCP,
            'pasivo_largo_plazo' => $pasivoLargoPlazo,
            'total_pasivo_lp' => $totalPasivoLP,
            'total_pasivo' => $totalPasivo,

            'total_patrimonio' => $totalPatrimonioCalculado,
            'total_pasivo_patrimonio' => $totalPasivoMasPatrimonio,
            'cuadrado' => abs($totalActivo - $totalPasivoMasPatrimonio) < 0.01
        ];
    }

    /**
     * Calcula los Ratios Financieros Clave
     */
    private function calcularRatiosFinancieros($balance, $resultados, $patrimonio)
    {
        $activoCirculante = $balance['total_activo_circulante'];
        $pasivoCP = $balance['total_pasivo_cp'];
        $totalActivo = $balance['total_activo'];
        $totalPasivo = $balance['total_pasivo'];
        $totalPatrimonio = $patrimonio['total_patrimonio'];
        $inventarios = $balance['total_inventarios'];
        $totalIngresos = $resultados['total_haber'];
        $utilidadNeta = $resultados['utilidad_neta'];

        // Liquidez
        $razonCorriente = $pasivoCP > 0 ? round($activoCirculante / $pasivoCP, 2) : ($activoCirculante > 0 ? 99.9 : 0);
        $pruebaAcida = $pasivoCP > 0 ? round(($activoCirculante - $inventarios) / $pasivoCP, 2) : ($activoCirculante > 0 ? 99.9 : 0);
        $capitalTrabajo = $activoCirculante - $pasivoCP;

        // Solvencia / Endeudamiento
        $endeudamientoPatrimonial = $totalPatrimonio > 0 ? round($totalPasivo / $totalPatrimonio, 2) : 0;
        $gradoEndeudamiento = $totalActivo > 0 ? round(($totalPasivo / $totalActivo) * 100, 2) : 0;

        // Rentabilidad
        $margenNeto = $totalIngresos > 0 ? round(($utilidadNeta / $totalIngresos) * 100, 2) : 0;
        $roa = $totalActivo > 0 ? round(($utilidadNeta / $totalActivo) * 100, 2) : 0;
        $roe = $totalPatrimonio > 0 ? round(($utilidadNeta / $totalPatrimonio) * 100, 2) : 0;

        return [
            'liquidez' => [
                'razon_corriente' => [
                    'nombre' => 'Razón Corriente (Liquidez General)',
                    'valor' => $razonCorriente,
                    'formula' => 'Activo Circulante / Pasivo Corto Plazo',
                    'estado' => $razonCorriente >= 1.5 ? 'EXCELENTE' : ($razonCorriente >= 1.0 ? 'ACEPTABLE' : 'ALERTA'),
                    'descripcion' => 'Capacidad de la empresa para cubrir sus deudas a corto plazo con activos circulantes.'
                ],
                'prueba_acida' => [
                    'nombre' => 'Prueba Ácida',
                    'valor' => $pruebaAcida,
                    'formula' => '(Activo Circulante - Inventarios) / Pasivo C.P.',
                    'estado' => $pruebaAcida >= 1.0 ? 'EXCELENTE' : ($pruebaAcida >= 0.8 ? 'ACEPTABLE' : 'ALERTA'),
                    'descripcion' => 'Capacidad de pago inmediata sin depender de la venta de inventarios.'
                ],
                'capital_trabajo' => [
                    'nombre' => 'Capital de Trabajo',
                    'valor' => $capitalTrabajo,
                    'formula' => 'Activo Circulante - Pasivo Corto Plazo',
                    'estado' => $capitalTrabajo > 0 ? 'POSITIVO' : 'NEGATIVO',
                    'descripcion' => 'Recursos monetarios netos disponibles para operar en el día a día.'
                ]
            ],
            'solvencia' => [
                'endeudamiento_patrimonial' => [
                    'nombre' => 'Endeudamiento Patrimonial',
                    'valor' => $endeudamientoPatrimonial,
                    'formula' => 'Total Pasivo / Total Patrimonio',
                    'estado' => $endeudamientoPatrimonial <= 1.0 ? 'SALUDABLE' : ($endeudamientoPatrimonial <= 1.5 ? 'MODERADO' : 'ALTO'),
                    'descripcion' => 'Proporción de financiamiento ajeno respecto a los fondos propios.'
                ],
                'grado_endeudamiento' => [
                    'nombre' => 'Grado de Endeudamiento',
                    'valor' => $gradoEndeudamiento,
                    'formula' => '(Total Pasivo / Total Activo) * 100',
                    'unidad' => '%',
                    'estado' => $gradoEndeudamiento <= 50 ? 'SALUDABLE' : ($gradoEndeudamiento <= 70 ? 'MODERADO' : 'ALTO'),
                    'descripcion' => 'Porcentaje total de los activos de la empresa que está financiado por acreedores.'
                ]
            ],
            'rentabilidad' => [
                'margen_neto' => [
                    'nombre' => 'Margen Neto de Utilidad (ROS)',
                    'valor' => $margenNeto,
                    'formula' => '(Utilidad Neta / Total Ingresos) * 100',
                    'unidad' => '%',
                    'estado' => $margenNeto >= 20 ? 'ALTO' : ($margenNeto >= 10 ? 'MODERADO' : ($margenNeto > 0 ? 'BAJO' : 'NEGATIVO')),
                    'descripcion' => 'Porcentaje de ganancia limpia que queda por cada sol generado en ventas.'
                ],
                'roa' => [
                    'nombre' => 'Rentabilidad sobre Activos (ROA)',
                    'valor' => $roa,
                    'formula' => '(Utilidad Neta / Total Activo) * 100',
                    'unidad' => '%',
                    'estado' => $roa >= 10 ? 'ALTO' : ($roa >= 5 ? 'MODERADO' : ($roa > 0 ? 'BAJO' : 'NEGATIVO')),
                    'descripcion' => 'Capacidad de los activos totales de la empresa para generar utilidades netas.'
                ],
                'roe' => [
                    'nombre' => 'Rentabilidad sobre Patrimonio (ROE)',
                    'valor' => $roe,
                    'formula' => '(Utilidad Neta / Total Patrimonio) * 100',
                    'unidad' => '%',
                    'estado' => $roe >= 15 ? 'ALTO' : ($roe >= 8 ? 'MODERADO' : ($roe > 0 ? 'BAJO' : 'NEGATIVO')),
                    'descripcion' => 'Rendimiento porcentual obtenido sobre los recursos propios invertidos por los socios.'
                ]
            ]
        ];
    }

    public function storeAsientoManual(Request $request)
    {
        $request->validate([
            'glosa' => 'required|string|max:255',
            'fecha_asiento' => 'nullable|date',
            'detalles' => 'required|array|min:2',
            'detalles.*.codigo_cuenta' => 'required|exists:cuentas_pcge,codigo_cuenta',
            'detalles.*.debe' => 'required|numeric|min:0',
            'detalles.*.haber' => 'required|numeric|min:0'
        ]);

        $detalles = $request->input('detalles');
        $totalDebe = 0;
        $totalHaber = 0;

        foreach ($detalles as $det) {
            $totalDebe += (float)$det['debe'];
            $totalHaber += (float)$det['haber'];
        }

        if (abs($totalDebe - $totalHaber) > 0.01) {
            return back()->withErrors([
                'error' => 'El asiento contable no está cuadrado. Total Debe: S/. ' . number_format($totalDebe, 2) . ' | Total Haber: S/. ' . number_format($totalHaber, 2)
            ]);
        }

        DB::beginTransaction();
        try {
            $asiento = AsientoContable::create([
                'id_usuario' => Auth::user()->id_usuario,
                'fecha_asiento' => $request->input('fecha_asiento') ? $request->input('fecha_asiento') : now(),
                'glosa' => 'MANUAL: ' . strtoupper($request->input('glosa')),
                'tipo_operacion' => 'MANUAL',
                'estado' => 'ACTIVO'
            ]);

            foreach ($detalles as $det) {
                if ((float)$det['debe'] > 0 || (float)$det['haber'] > 0) {
                    DetalleAsiento::create([
                        'id_asiento' => $asiento->id_asiento,
                        'codigo_cuenta' => $det['codigo_cuenta'],
                        'debe' => $det['debe'],
                        'haber' => $det['haber']
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Asiento contable manual registrado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al registrar asiento: ' . $e->getMessage()]);
        }
    }
}
