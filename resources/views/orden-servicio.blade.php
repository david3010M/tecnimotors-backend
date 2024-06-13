@php use Carbon\Carbon; @endphp
@php
    function fuelLevelToFraction($fuelLevel)
    {
        $fractions = [
            0 => 'Tanque Vacio',
            2 => '20% Tanque',
            4 => '40% Tanque',
            6 => '60% Tanque',
            8 => '80% Tanque',
            10 => 'Tanque Lleno',
        ];

        return $fractions[$fuelLevel] ?? 'N/A';
    }
@endphp
    <!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoja de Servicio</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.5;
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
            /*padding: 8px;*/
            text-align: left;
            padding-left: 3px;
        }

        .content th {
            background-color: #e2e2e2;
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

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .left {
            text-align: left;
        }

        .text-sm {
            font-size: 10px;
        }

        .w100 {
            width: 100%;
        }

        .gray {
            color: #4d4d4d;
        }

        .w30 {
            width: 30%;
        }

        h1, h2, h3 {
            margin: 0;
        }

        .mb-2 {
            margin-bottom: 16px;
        }
    </style>
</head>

<body>

<table class="w100 mb-2">
    <tr>
        <td class="center gray w30">
            <img height="66px" width="auto" class="logoImage" src="{{ asset('img/logoTecnimotors.png') }}"
                 alt="logoTecnimotors">

        </td>
        <td class="w100 center">
            <h2>HOJA DE SERVICIO</h2>
            <div>N° {{ $order->number }}</div>

        </td>
    </tr>
    <tr>
        <td class="center gray w30">
            <div class="text-sm">RUC: 20546989656</div>
            <div class="text-sm">Dir: Mz. A Lt. 7 Urb. San Manuel - Prolongación Bolognesi</div>

        </td>
        <td class="w100 center">
            <div class="text-sm gray right">&nbsp;</div>
            <div class="text-sm gray right">Telfono: 986202388 - 941515301</div>

        </td>
    </tr>
</table>

<div class="content">

    <table style="margin-bottom: 8px;">
        <tr>
            <th>Cliente</th>
            <td colspan="2">
                @if ($order->vehicle->person->typeofDocument == 'DNI')
                    {{ $order->vehicle->person->names .
                        ' ' .
                        $order->vehicle->person->fatherSurname .
                        ' ' .
                        $order->vehicle->person->motherSurname }}
                @elseif($order->vehicle->person->typeofDocument == 'RUC')
                    {{ $order->vehicle->person->businessName }}
                @endif
            </td>
            <th>
                @if ($order->vehicle->person->typeofDocument == 'DNI')
                    DNI
                @elseif($order->vehicle->person->typeofDocument == 'RUC')
                    RUC
                @endif
            </th>
            <td>
                @if ($order->vehicle->person->typeofDocument == 'DNI')
                    {{ $order->vehicle->person->documentNumber }}
                @elseif($order->vehicle->person->typeofDocument == 'RUC')
                    {{ $order->vehicle->person->documentNumber }}
                @endif
            </td>
        </tr>
        <tr>
            <th>Dirección</th>
            <td colspan="2">{{ $order->vehicle->person->address }}</td>
            <th>Teléfono</th>
            <td>{{ $order->vehicle->person->phone }}</td>
        </tr>

        @if ($order->vehicle->person->typeofDocument == 'RUC')
            <tr>
                <th>Responsable</th>
                <td colspan="2">
                    {{ $order->vehicle->person->representativeNames }}
                </td>

                <th>DNI</th>
                <td>
                    {{ $order->vehicle->person->representativeDni }}
                </td>
            </tr>
        @endif

        <tr>
            <th>Email</th>
            <td colspan="4">{{ $order->vehicle->person->email }}</td>
        </tr>
    </table>
    <h1></h1>
    <table style="margin-bottom: 8px;">
        <tr>
            <th>Marca</th>
            <td>{{ $order->vehicle->brand->name }}</td>

            <th>Modelo</th>
            <td>{{ $order->vehicle->model }}</td>

            <th>Chasis</th>
            <td colspan="3">{{ $order->vehicle->chasis }}</td>

        </tr>

        <tr>

            <th>Placa</th>
            <td>{{ $order->vehicle->plate }}</td>

            <th>Motor</th>
            <td>{{ $order->vehicle->motor }}</td>

            <th>Km.</th>
            <td>{{ $order->km }}</td>

            <th>Año Fab.</th>
            <td>{{ $order->vehicle->year }}</td>

        </tr>
    </table>
    <h1></h1>
    <table style="margin-bottom: 8px;">
        <thead>
        <tr>
            <th style="text-align: center">Nro.</th>
            <th style="text-align: center">Servicio / Producto o Repuesto</th>
            <th style="text-align: center">Cantidad</th>
        </tr>
        </thead>
        <tbody>
        @if ($order->details)
            @foreach ($order->details as $detail)
                <tr>
                    <td style="text-align: center">{{ $loop->iteration }}</td>
                    @if ($detail->service && $detail->product == null)
                        <td>{{ $detail->service->name }}</td>
                        {{--                        <td style="text-align: center">{{ round($detail->service->quantity) }}</td> --}}
                        <td style="text-align: center">1</td>
                    @elseif($detail->service == null && $detail->product)
                        <td>{{ $detail->product->name }}</td>
                        {{--                        <td style="text-align: center">{{ round($detail->product->quantity) }}</td> --}}
                        <td style="text-align: center">1</td>
                    @endif
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>


    <div class="footer-section">
        <div>
            <h1></h1>
            <table style="margin-bottom: 8px;">
                <tr>
                    <th>Observaciones</th>
                    <td>
                        {{ $order->observations }}
                    </td>
                </tr>
                <tr>
                    <th>Elementos</th>
                    <td>
                        {{ $order->elements->map(function ($element) {
                                return $element->element->name;
                            })->implode(', ') }}
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
                <td>{{ Carbon::parse($order->arrivalDate)->format('Y-m-d') }}</td>

                <th>Hora</th>
                <td>{{ Carbon::parse($order->arrivalDate)->format('h:i A') }}</td>

                <th>Fecha Entrega</th>
                <td>{{ Carbon::parse($order->deliveryDate)->format('Y-m-d') }}</td>

                <th>Hora</th>
                <td>{{ Carbon::parse($order->deliveryDate)->format('h:i A') }}</td>
            </tr>
            <tr>
                <th>Nivel De Combustible</th>
                <td colspan="3">{{ fuelLevelToFraction($order->fuelLevel) }} </td>
                <th>Asesor de Servicio</th>
                <td colspan="3">
                    {{ $order->worker->person->names . ' ' . $order->worker->person->fatherSurname . ' ' . $order->worker->person->motherSurname }}
                </td>

            </tr>
        </table>
    </div>


</div>
{{-- <div class="footer"> --}}
{{--    <p>POR LA PRESENTE AUTORIZO LAS REPARACIONES AQUI DESCRITAS CONJUNTAMENTE CON EL MATERIAL QUE SEA NECESARIO --}}
{{--        USAR EN ELLAS, TAMBIEN AUTORIZO A USTEDES Y A SUS EMPLEADOS PARA QUE AFIEREN ESTE VEHICULO POR LAS CALLES. --}}
{{--        CARRETERAS U OTROS SITIOS A FIN DE ASEGURAR LAS PRUEBAS O INSPECCIONES PERTINENTES QUE GARANTIZEN EL --}}
{{--        TRABAJO. --}}
{{--        NOTA: SE COBRARA DERECHO DE GUARDANIA SI EL VEHICULO NO ES RETIRADO EN LAS 48 HORAS DESPUES DE --}}
{{--        TERMINADO EL TRABAJO. --}}
{{--    </p> --}}
{{-- </div> --}}
</body>

</html>
