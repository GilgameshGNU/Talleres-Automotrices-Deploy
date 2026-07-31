<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte - {{ $reporte->tipo }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #b6f24a; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #22d3c5; font-size: 24px; }
        .header p { margin: 5px 0; color: #666; }
        .info-box { background-color: #f8f9fa; border: 1px solid #e9ecef; padding: 15px; border-radius: 8px; margin-bottom: 30px; }
        .info-box p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #04100e; color: #b6f24a; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .footer { position: fixed; bottom: -10px; width: 100%; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SisGest Pro - Taller Automotriz</h1>
        <p>Reporte Oficial del Sistema</p>
    </div>

    <div class="info-box">
        <p><strong>Tipo de Reporte:</strong> {{ $reporte->tipo }}</p>
        <p><strong>Generado el:</strong> {{ $reporte->fechaGeneracion }}</p>
        <p><strong>Parámetros:</strong> {{ $reporte->parametros }}</p>
    </div>

    <h3>Resultados del Reporte</h3>
    @if(count($data) > 0)
    <table>
        <thead>
            <tr>
                @foreach(array_keys((array)$data[0]) as $key)
                    <th>{{ ucfirst(preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $key)) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                @foreach((array)$row as $val)
                    <td>{{ $val }}</td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <p>No se encontraron datos para este reporte con los parámetros especificados.</p>
    @endif

    <div class="footer">
        Generado por SisGest Pro &copy; {{ date('Y') }} - Todos los derechos reservados.
    </div>
</body>
</html>
