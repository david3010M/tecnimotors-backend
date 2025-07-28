@php
    use Carbon\Carbon;

    function fuelLevelToFractionText($fuelLevel)
    {
        $fractions = [
            0 => 'Tanque Vacío',
            2 => '20%',
            4 => '40%',
            6 => '60%',
            8 => '80%',
            10 => 'Tanque Lleno',
        ];
        return $fractions[$fuelLevel] ?? 'N/A';
    }

    function fuelColor($level)
    {
        return match (true) {
            $level <= 2 => '#e74c3c', // rojo
            $level <= 4 => '#e67e22', // naranja
            $level <= 6 => '#f1c40f', // amarillo
            $level <= 8 => '#2ecc71', // verde claro
            default => '#27ae60',     // verde
        };
    }

    $fuelLevel = $order->fuelLevel;
    $fuelHeight = ($fuelLevel / 10) * 100; // en porcentaje
    $fuelText = fuelLevelToFractionText($fuelLevel);
    $fuelColor = fuelColor($fuelLevel);
@endphp


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden de Trabajo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 5px;
        }

        .title,
        .number {
            display: block;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px;
        }

        .no-border {
            border: none;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .header-table td {
            border: none;
        }

        .logo {
            height: 50px;
        }


    .firmas-container {
        text-align: center;
        margin-top: 25px;
    }

    .firmas-container .firma {
        display: inline-block;
        width: 40%;
        margin: 30px 20px 0 20px;
        border-top: 1px solid #000;
        padding-top: 6px;
        font-size: 14px;
    }


        .footer p {
            margin: 20ox;
            text-align: justify;
        }

        .checkboxes label {
            width: 24%;
            display: inline-block;
            margin-bottom: 3px;
        }

        .tank-container {
    width: 70px;
    height: 40px;
    border: 2px solid #333;
    border-radius: 10px;
    position: relative;
    background-color: #f0f0f0;
    overflow: hidden;
    margin: auto;
    margin-top: 5px;
}
.tank-fill {
    position: absolute;
    bottom: 0;
    width: 100%;
    background-color: {{ $fuelColor }};
    transition: height 0.3s ease-in-out;
    height: {{ $fuelHeight }}%;
}
.tank-label {
    text-align: center;
    font-weight: bold;
    margin-top: 5px;
}
    </style>
</head>

<body>
    {{-- Cabecera --}}
    <table class="header-table">
        <tr>
            <td style="width: 10%">
                <img src="{{ asset('img/logoTecnimotors.png') }}" width="130" class="logo" alt="Logo">
            </td>
            <td class="center">
                <div class="bold">TECNI MOTORS DEL PERÚ</div>
                <div>División Mantenimiento</div>
                <div class="bold">Líderes en Gestión del Mantenimiento Automotriz</div>
                <div>AV. FRANCISCO CUNEO N° 1150 - URB. PATACZA - CHICLAYO</div>
                <div>Cel: 952085190 - RPC: 979392964 - Telf: 237348</div>
                <div>Email: cesargutierrez@tecnimotorsdelperu.com</div>
            </td>
            <td class="right">
                <div class="title">ORDEN DE TRABAJO</div>
                <div class="number" >N° {{ $order->number }}</div>
                <div>Fecha: {{ Carbon::parse($order->created_at)->format('d/m/Y') }}</div>
            </td>
        </tr>
    </table>

    {{-- Información del Cliente --}}
    <table style="width: 100%;">
        <tr>
            <th style="text-align: left;">Cliente</th>
            <td colspan="5">
                @if ($order->vehicle->person->typeofDocument == 'DNI')
                    {{ $order->vehicle->person->names }} {{ $order->vehicle->person->fatherSurname }}
                    {{ $order->vehicle->person->motherSurname }}
                @else
                    {{ $order->vehicle->person->businessName }}
                @endif
            </td>
        </tr>
        <tr>
            <th style="text-align: left;">Dirección</th>
            <td colspan="5">{{ $order->vehicle->person->address }}</td>
        </tr>
<!-- 
        <tr>
            <th style="text-align: left;">Responsable</th>
            <td colspan="5">{{ $order->vehicle->person->representativeNames }}</td>
        </tr> -->
        <tr>
            <th style="text-align: left;">RUC / DNI</th>
            <td>{{ $order->vehicle->person->documentNumber }}</td>
            <th style="text-align: left;">Teléfono</th>
            <td colspan="3">{{ $order->vehicle->person->phone }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">Email</th>
            <td colspan="5">{{ $order->vehicle->person->email }}</td>
        </tr>
    </table>


    {{-- Información del Vehículo --}}
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <th style="text-align: left;">Tipo</th>
            <td>{{ $order->vehicle->typeVehicle?->name ?? '' }}</td>

            <th style="text-align: left;">Marca</th>
            <td>{{ $order->vehicle->vehicleModel?->brand?->name ?? '' }}</td>

            <th style="text-align: left;">Modelo</th>
            <td>{{ $order->vehicle->vehicleModel?->name ?? '' }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">Placa</th>
            <td>{{ $order->vehicle?->plate ?? '' }}</td>

            <th style="text-align: left;">Chasis</th>
            <td>{{ $order->vehicle?->chasis ?? '' }}</td>

            <th style="text-align: left;">Motor</th>
            <td>{{ $order->vehicle?->motor ?? '' }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">Kilometraje</th>
            <td>{{ intval($order->km) }}</td>

            <th style="text-align: left;">Año</th>
            <td>{{ $order->vehicle?->year ?? '' }}</td>

            <th style="text-align: left;">Bin</th>
            <td>{{ $order->vehicle?->codeBin ?? '' }}</td>
        </tr>
    </table>

{{-- Detalles de servicios --}}
<table style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th style="width: 5%; text-align: center;">N°</th>
            <th style="text-align: left;">Descripción</th>
            <th style="width: 20%; text-align: center;">Monto</th>
        </tr>
    </thead>
    <tbody>
        @php
            $minRows = 12;
            $totalRows = max($order->details->count(), $minRows);
        @endphp

        @for ($i = 0; $i < $totalRows; $i++)
            <tr>
                <td style="text-align: center;">
                    {{ $i + 1 }}
                </td>
                <td>
                    {{ $order->details[$i]->service->name ?? $order->details[$i]->product->name ?? '' }}
                </td>
                <td style="text-align: center;">
                    @if (isset($order->details[$i]))
                        S/ {{ number_format($order->details[$i]->amount ?? 0, 2) }}
                    @endif
                </td>
            </tr>
        @endfor
    </tbody>
</table>

<br>
{{-- ELEMENTOS --}}
<table class="table table-bordered">
    <thead>
        <tr>
            <th colspan="7" class="text-center">Elementos Verificados</th>
        </tr>
    </thead>
    <tbody>
        @php
            $items = \App\Models\Element::get();
            $selectedElements = $order->elements->pluck('element_id')->toArray();
            $chunks = $items->chunk(7);
        @endphp

        @foreach ($chunks as $row)
            <tr>
                @foreach ($row as $item)
                    @php
                        $isChecked = in_array($item->id, $selectedElements);
                    @endphp
                    <td>
                        <label style="color: black; {{ $isChecked ? 'font-weight: bold;' : '' }}">
                            <input type="checkbox" name="elements[]" value="{{ $item->id }}"
                                {{ $isChecked ? 'checked' : '' }}
                                style="{{ $isChecked ? 'accent-color: green;' : '' }}">
                            {{ $item->name }}
                        </label>
                    </td>
                @endforeach
                @for ($i = count($row); $i < 7; $i++)
                    <td></td>
                @endfor
            </tr>
        @endforeach
    </tbody>
</table>

<br>
{{-- Observaciones --}}
<table style="width: 100%; border: none; border-collapse: collapse;">
    <tr>
        <!-- Columna izquierda: Fechas y Asesor -->
        <td style="vertical-align: top; width: 70%;">
            <table class="left-data" style="width: 100%; border: 1px solid #ccc; border-collapse: collapse;">
                <tr>
                    <th style="border: 1px solid #ccc;">Fecha Ingreso</th>
                    <td style="border: 1px solid #ccc;">{{ Carbon::parse($order->arrivalDate)->format('d/m/Y') }}</td>
                    <th style="border: 1px solid #ccc;">Hora Ingreso</th>
                    <td style="border: 1px solid #ccc;">{{ Carbon::parse($order->arrivalDate)->format('H:i') }}</td>
                </tr>
                <tr>
                    <th style="border: 1px solid #ccc;">Fecha Entrega</th>
                    <td style="border: 1px solid #ccc;">{{ Carbon::parse($order->deliveryDate)->format('d/m/Y') }}</td>
                    <th style="border: 1px solid #ccc;">Hora Entrega</th>
                    <td style="border: 1px solid #ccc;">{{ Carbon::parse($order->deliveryDate)->format('H:i') }}</td>
                </tr>
                <tr>
                    <th style="border: 1px solid #ccc;">Asesor del Servicio</th>
                    <td style="border: 1px solid #ccc;" colspan="3">
                        {{ $order->worker->person->names }}
                        {{ $order->worker->person->fatherSurname }}
                        {{ $order->worker->person->motherSurname }}
                    </td>
                </tr>
            </table>
        </td>

        <!-- Columna derecha: Combustible -->
       <td style="vertical-align: top; text-align: center;">
    <div style="width: 30px; height: 60px; border: 2px solid #999; border-radius: 8px; margin: 0 auto; position: relative; background: #f5f5f5; display: flex; align-items: flex-end; justify-content: center; overflow: hidden;">
        <div style="
            width: 100%; 
            height: {{ ($order->fuelLevel / 10) * 100 }}%; 
            background-color:
                {{ 
                    match (true) {
                        $order->fuelLevel <= 2 => '#e74c3c',    // rojo
                        $order->fuelLevel <= 4 => '#e67e22',    // naranja
                        $order->fuelLevel <= 6 => '#f1c40f',    // amarillo
                        $order->fuelLevel <= 8 => '#2ecc71',    // verde claro
                        default => '#27ae60'                    // verde lleno
                    } 
                }};
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: bold;
            font-size: 14px;
        ">
            {{ $fuelText }}
        </div>
    </div>
</td>

    </tr>
</table>



<br>
    {{-- Footer y firmas --}}
    <div class="footer">
        <p><strong>POR LA PRESENTE AUTORIZO</strong> LAS REPARACIONES AQUÍ DESCRITAS CONJUNTAMENTE CON EL MATERIAL
            QUE SEA NECESARIO USAR EN ELLAS. TAMBIÉN AUTORIZO A USTEDES Y A SUS EMPLEADOS PARA QUE AFIEREN ESTE
            VEHÍCULO POR LAS CALLES, CARRETERAS U OTROS SITIOS A FIN DE ASEGURAR LAS PRUEBAS O INSPECCIONES
            PERTINENTES QUE GARANTICEN EL TRABAJO.</p>
        <p><strong>NOTA:</strong> SE COBRARÁ DERECHO DE GUARDANÍA SI EL VEHÍCULO NO ES RETIRADO EN LAS 48
            HORAS DESPUÉS DE TERMINADO EL TRABAJO.</p>

        <div class="firmas-container">
            <div class="firma">FIRMA ASESOR DE SERVICIO</div>
            <div class="firma">FIRMA AUTORIZADA DEL CLIENTE</div>
        </div>

        </div>

   
</body>

</html>