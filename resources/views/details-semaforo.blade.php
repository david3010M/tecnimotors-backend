<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Detalle de Atención</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 13px;
            background-color: #f8f9fa;
            margin: 20px;
            color: #2c3e50;
        }

        /* Título principal */
        .titulo {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            color: #1a252f;
            margin-bottom: 10px;
            border-bottom: 3px solid #3498db;
            padding-bottom: 5px;
        }

        /* Información del periodo */
        .periodo {
            text-align: left;
            background-color: #dfe6e9;
            padding: 12px;
            margin: 15px auto;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            border: 1px solid #b2bec3;
            border-radius: 8px;
            width: 60%;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th,
        td {
            padding: 10px 8px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        thead {
            background-color: #2c3e50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #eaf2f8;
        }

        .semaforo {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 4px;
        }

        .light {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            display: inline-block;
            border: 1px solid #999;
        }

        .verde.on {
            background-color: #2ecc71;
        }

        .amarillo.on {
            background-color: #f1c40f;
        }

        .rojo.on {
            background-color: #e74c3c;
        }

        .gris.on {
            background-color: #bdc3c7;
        }
    </style>
</head>

<body>

    <div class="titulo">REPORTE DE DETALLES DE ATENCIÓN</div>

    <div class="periodo">
        Fecha: : {{ now() }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Nro° Atención</th>
                <th>Cliente</th>
                <th>Placa</th>
                <th>Servicio</th>
                <th>Fecha Atención</th>
                <th>Periodo</th>
                <th>Semáforo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $detail)
                @php
                    $fecha = \Carbon\Carbon::parse($detail['date_register']);
                    $dias = $detail['period_detail']['days_diference'] ?? '-';
                    $luces = $detail['period_detail']['lights'] ?? ['gris', 'gris', 'gris'];
                @endphp

                <tr>
                    <td>ATN-{{ str_pad($detail['id'], 3, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $detail['client_name'] ?? 'No disponible' }}</td>
                    <td>{{ $detail['plate'] ?? '-' }}</td>
                    <td>{{ $detail['service_name'] ?? 'N/A' }}</td>
                    <td>{{ $fecha->format('Y-m-d') }}</td>
                    <td>{{ $dias }} de {{ $detail['period'] }}</td>

                    <td>
                        <div class="semaforo">
                            @foreach($luces as $color)
                                <span class="light {{ $color }} on"></span>
                            @endforeach
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>