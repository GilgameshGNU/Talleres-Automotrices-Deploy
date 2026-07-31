<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reporte;

class ReporteController extends Controller
{
    public function index()
    {
        return response()->json(Reporte::all());
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['idUsuario'] = auth()->id() ?? 1; // Default to user 1 if not logged in
        $data['fechaGeneracion'] = now();
        $item = Reporte::create($data);
        return response()->json($item, 201);
    }

    public function show($id)
    {
        $item = Reporte::findOrFail($id);
        return response()->json($item);
    }

    public function update(Request $request, $id)
    {
        $item = Reporte::findOrFail($id);
        $item->update($request->all());
        return response()->json($item);
    }

    public function destroy($id)
    {
        $item = Reporte::findOrFail($id);
        $item->delete();
        return response()->json(null, 204);
    }

    public function data($id)
    {
        $reporte = Reporte::findOrFail($id);
        $result = [
            'tipo'       => $reporte->tipo,
            'parametros' => $reporte->parametros,
            'data'       => []
        ];
        
        try {
            if ($reporte->tipo === 'Ingresos y Ventas') {
                $result['data'] = \App\Models\Factura::select('nroFactura', 'fechaEmision', 'montoTotal', 'nitCliente')
                    ->get()->toArray();
            } elseif ($reporte->tipo === 'Rendimiento de Órdenes') {
                $result['data'] = \App\Models\OrdenTrabajo::select('idOrden', 'estado', 'total', 'fechaIngreso', 'fechaEntrega')
                    ->get()->toArray();
            } elseif ($reporte->tipo === 'Estado de Inventario') {
                $result['data'] = \App\Models\Repuesto::select('codigo', 'nombre', 'marca', 'precioVenta')
                    ->get()->toArray();
            } elseif ($reporte->tipo === 'Historial de Clientes') {
                $result['data'] = \App\Models\Cliente::select('nombreCompleto', 'ci_nit', 'telefono', 'direccion')
                    ->get()->toArray();
            } elseif ($reporte->tipo === 'Citas Programadas') {
                $result['data'] = \App\Models\Cita::select('idCita', 'estado', 'fechaCita')
                    ->get()->toArray();
            } else {
                $result['data'] = \App\Models\Cliente::select('nombreCompleto', 'ci_nit', 'telefono')
                    ->get()->toArray();
            }
        } catch (\Exception $e) {
            $result['data'] = [];
            $result['error'] = 'Error al obtener datos: ' . $e->getMessage();
        }
        
        return response()->json($result);
    }

    public function download($id)
    {
        $reporte = Reporte::findOrFail($id);
        $p = $reporte->parametros ?? '';
        $formato = 'PDF';
        if (str_contains($p, 'Formato: EXCEL')) $formato = 'EXCEL';
        if (str_contains($p, 'Formato: CSV')) $formato = 'CSV';

        $data = [];
        try {
            if ($reporte->tipo === 'Ingresos y Ventas') {
                $data = \App\Models\Factura::select('nroFactura', 'fechaEmision', 'montoTotal', 'nitCliente')
                    ->get()->toArray();
            } elseif ($reporte->tipo === 'Rendimiento de Órdenes') {
                $data = \App\Models\OrdenTrabajo::select('idOrden', 'estado', 'total', 'fechaIngreso', 'fechaEntrega')
                    ->get()->toArray();
            } elseif ($reporte->tipo === 'Estado de Inventario') {
                $data = \App\Models\Repuesto::select('codigo', 'nombre', 'marca', 'precioVenta')
                    ->get()->toArray();
            } elseif ($reporte->tipo === 'Historial de Clientes') {
                $data = \App\Models\Cliente::select('nombreCompleto', 'ci_nit', 'telefono', 'direccion')
                    ->get()->toArray();
            } elseif ($reporte->tipo === 'Citas Programadas') {
                $data = \App\Models\Cita::select('idCita', 'estado', 'fechaCita')
                    ->get()->toArray();
            } else {
                $data = \App\Models\Cliente::select('nombreCompleto', 'ci_nit', 'telefono')
                    ->get()->toArray();
            }
        } catch (\Exception $e) {
            $data = [];
        }
        
        if ($formato === 'CSV' || $formato === 'EXCEL') {
            $filename = "reporte_{$id}_" . date('Ymd_His') . ".csv";
            $responseHeaders = [
                "Content-type"        => "text/csv; charset=UTF-8",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function() use($data) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                if (!empty($data)) {
                    fputcsv($file, array_keys($data[0]));
                    foreach ($data as $row) {
                        fputcsv($file, array_values($row));
                    }
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $responseHeaders);
        }

        // PDF using dompdf
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.reporte', [
            'reporte' => $reporte,
            'data'    => $data
        ]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download("reporte_{$reporte->tipo}_{$id}.pdf");
    }
}
