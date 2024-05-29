<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoja de Servicio</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .header,
        .footer {
            width: 100%;
            text-align: center;
            position: fixed;
        }

        .content {
            margin-top: 10px;
            margin-bottom: 50px;
        }

        .content table {
            width: 100%;
            border-collapse: collapse;
        }

        .content th,
        .content td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .content th {
            background-color: #f2f2f2;
        }

        .content {
            font-size: 12px;
        }

        .content .observaciones {
            margin-top: 20px;
        }

        .content .footer-section {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>

<body>
<h1 style="text-align: center">TECNI MOTOR'S DEL PERÚ</h1>
<p style="text-align: center"><b>Orden de Trabajo:</b> {{$order->number}}</p>

<div class="content">
    <table style="margin-bottom: 8px;">
        <tr>
            <th>Cliente</th>
            <td colspan="3">{{$order->vehicle->person->names . ' ' . $order->vehicle->person->fatherSurname . ' ' . $order->vehicle->person->motherSurname}}</td>
            <th>DNI</th>
            <td>{{$order->vehicle->person->typeofDocument == 'DNI' ? $order->vehicle->person->documentNumber : ''}}</td>
        </tr>
        <tr>
            <th>Dirección</th>
            <td colspan="3">{{$order->vehicle->person->address}}</td>
            <th>Teléfono</th>
            <td>{{$order->vehicle->person->phone}}</td>
        </tr>
        <tr>
            <th>RUC</th>
            <td>{{$order->vehicle->person->typeofDocument == 'RUC' ? $order->vehicle->person->documentNumber : ''}}</td>
            <th>Email</th>
            <td colspan="3">{{$order->vehicle->person->email}}</td>
        </tr>
    </table>

    <table style="margin-bottom: 8px;">
        <tr>
            <th>Marca</th>
            <td>{{$order->vehicle->brand->name}}</td>

            <th>Modelo</th>
            <td>{{$order->vehicle->model}}</td>

            <th>Chasis</th>
            <td colspan="3">{{$order->vehicle->chasis}}</td>

        </tr>

        <tr>

            <th>Placa</th>
            <td>{{$order->vehicle->plate}}</td>

            <th>Motor</th>
            <td>{{$order->vehicle->motor}}</td>

            <th>Km.</th>
            <td>{{$order->vehicle->km}}</td>

            <th>Año Fab.</th>
            <td>{{$order->vehicle->year}}</td>

        </tr>
    </table>
    <table>
        <thead>
        <tr>
            <th>Nro.</th>
            <th>Servicio / Producto o Repuesto</th>
            <th>Cantidad</th>
        </tr>
        </thead>
        <tbody>
        @if($order->details)
            @foreach($order->details as $detail)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    @if($detail->service && $detail->product == null)
                        <td>{{$detail->service->name}}</td>
                        <td>{{ round($detail->service->quantity) }}</td>
                    @elseif($detail->service == null && $detail->product)
                        <td>{{$detail->product->name}}</td>
                        <td>{{ round($detail->product->quantity) }}</td>
                    @endif
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>


    <div class="footer-section">
        <div>
            <h1></h1>
            <table>
                <tr>
                    <th>Observaciones</th>
                    <td>
                        {{$order->observations}}
                    </td>
                    <th>Elementos</th>
                    <td>
                        {{$order->elements}}
                    </td>
                </tr>
            </table>
        </div>


    </div>
    <h1></h1>
    <div>
        <table>
            <tr>
                <th>Fecha Ingreso</th>
                <td>2024-05-28</td>
                <th>Hora</th>
                <td>09:00 AM</td>
                <th>Fecha Entrega</th>
                <td>2024-05-29</td>
                <th>Hora</th>
                <td>10:30 AM</td>
            </tr>
            <tr>
                <th>Nivel De Combustible</th>
                <td colspan="3">3/4 Tanque</td>
                <th>Asesor de Servicio</th>
                <td colspan="3">Juan Pérez</td>

            </tr>
        </table>
    </div>


</div>
<div class="footer">
    <p>POR LA PRESENTE AUTORIZO LAS REPARACIONES AQUI DESCRITAS CONJUNTAMENTE CON EL MATERIAL QUE SEA NECESARIO
        USAR EN ELLAS, TAMBIEN AUTORIZO A USTEDES Y A SUS EMPLEADOS PARA QUE AFIEREN ESTE VEHICULO POR LAS CALLES.
        CARRETERAS U OTROS SITIOS A FIN DE ASEGURAR LAS PRUEBAS O INSPECCIONES PERTINENTES QUE GARANTIZEN EL
        TRABAJO.
        NOTA: SE COBRARA DERECHO DE GUARDANIA SI EL VEHICULO NO ES RETIRADO EN LAS 48 HORAS DESPUES DE
        TERMINADO EL TRABAJO.
    </p>
</div>
</body>

</html>
