@php
    use Carbon\Carbon;

    function fuelLevelToFractionText($fuelLevel)
    {
        $fractions = [
            0 => '0%',
            2 => '20%',
            4 => '40%',
            6 => '60%',
            8 => '80%',
            10 => '100%',
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
        .sombra{
            background-color: #dcdcdc;
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
            height: 90px;
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
                <img src="{{ asset('img/logo.jpg') }}" width="150" class="logo" alt="Logo">
            </td>
            
           <td class="center">
                <div class="bold" style="font-size:16">TECNI MOTORS DEL PERÚ</div>
                <div>Dir: Mz. A Lt. 7 Urb. San Manuel - Prolongación Bolognesi</div>
                <div>Cel: 941515301 - 986202388</div>
                <div>Email: cynthiab.tecnimotorsdelperu@gmail.com</div>
            </td>
            <td class="right" style="text-align: right; vertical-align: top; padding: 10px;">
    <div class="title" style="margin-bottom: 60px;"></div> {{-- margen para separar bloque superior --}}
    <div class="title" style="font-weight: bold; font-size: 14px; margin-bottom: 5px;">
        ORDEN DE TRABAJO
    </div>
    <div class="number" style="font-size: 14px; margin-bottom: 5px;">
        N° {{ $order->number }}
    </div>
    <div style="font-size: 12px;">
        Fecha: {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}
    </div>
</td>

        </tr>
    </table>

    {{-- Información del Cliente --}}
    <table style="width: 100%;">
        <tr>
            <th style="text-align: left;" class="sombra">CLIENTE</th>
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
            <th style="text-align: left;" class="sombra">DIRECCIÓN</th>
            <td colspan="5">{{ $order->vehicle->person->address }}</td>
        </tr>
        <tr>
            <th style="text-align: left;" class="sombra">CONDUCTOR RESPONSABLE</th>
            <td colspan="5">{{ $order->driver }}</td>
        </tr>
<!-- 
        <tr>
            <th style="text-align: left;">Responsable</th>
            <td colspan="5">{{ $order->vehicle->person->representativeNames }}</td>
        </tr> -->
        <tr>
            <th style="text-align: left;" class="sombra">RUC / DNI</th>
            <td>{{ $order->vehicle->person->documentNumber }}</td>
            <th style="text-align: left;" class="sombra">TELEFONO</th>
            <td colspan="3">{{ $order->vehicle->person->phone }}</td>
        </tr>
        <tr>
            <th style="text-align: left;" class="sombra">EMAIL</th>
            <td colspan="5">{{ $order->vehicle->person->email }}</td>
        </tr>
    </table>


    {{-- Información del Vehículo --}}
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <th style="text-align: left;" class="sombra">TIPO</th>
            <td>{{ $order->vehicle->typeVehicle?->name ?? '' }}</td>

            <th style="text-align: left;" class="sombra">MARCA</th>
            <td>{{ $order->vehicle->vehicleModel?->brand?->name ?? '' }}</td>

            <th style="text-align: left;" class="sombra">MODELO</th>
            <td>{{ $order->vehicle->vehicleModel?->name ?? '' }}</td>
        </tr>
        <tr>
            <th style="text-align: left;" class="sombra">PLACA</th>
            <td>{{ $order->vehicle?->plate ?? '' }}</td>

            <th style="text-align: left;" class="sombra">CHASIS</th>
            <td>{{ $order->vehicle?->chasis ?? '' }}</td>

            <th style="text-align: left;" class="sombra">MOTOR</th>
            <td>{{ $order->vehicle?->motor ?? '' }}</td>
        </tr>
        <tr>
            <th style="text-align: left;" class="sombra">KILOMETRAJE</th>
            <td>{{ intval($order->km) }}</td>

            <th style="text-align: left;" class="sombra">AÑO</th>
            <td>{{ $order->vehicle?->year ?? '' }}</td>

            <th style="text-align: left;" class="sombra">VIN</th>
            <td>{{ $order->vehicle?->codeBin ?? '' }}</td>
        </tr>
    </table>

{{-- Detalles de servicios --}}
<table style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th style="width: 5%; text-align: center;" class="sombra">N°</th>
            <th style="text-align: left;" class="sombra">Descripción</th>
            <th style="width: 20%; text-align: center;" class="sombra">Monto</th>
        </tr>
    </thead>
    <tbody>
        
    @foreach ($order->details as $i => $detail)
        <tr>
            <td style="text-align: center;">
                {{ $i + 1 }}
            </td>
            <td>
                {{ $detail->service->name ?? $detail->product->name ?? '' }}
            </td>
            <td style="text-align: center;">
                S/ {{ number_format($detail->saleprice ?? 0, 2) }}
            </td>
        </tr>
    @endforeach
</tbody>

</table>

<br>
<table class="table table-bordered" 
       style="font-size: 10px; border-collapse: collapse; width: 100%; table-layout: fixed;">
    <thead>
        <tr>
            <th colspan="11" class="text-center sombra" style="font-size: 11px; padding: 3px;">
                ELEMENTOS VERIFICADOS
            </th>
        </tr>
    </thead>
    <tbody>
        @php
            $items = \App\Models\Element::get();
            $selectedElements = $order->elements->pluck('element_id')->toArray();
            $chunks = $items->chunk(11);
        @endphp

        @foreach ($chunks as $row)
            <tr>
                @foreach ($row as $index => $item)
                    @php
                        $isChecked = in_array($item->id, $selectedElements);
                        $remaining = 11 - count($row);
                        $colspan = ($loop->last && $remaining > 0) ? 'colspan=' . (1 + $remaining) : '';
                    @endphp

                    <td {{ $colspan }} style="padding: 3px; text-align: center; vertical-align: top;">
                        <div>
                            <input type="checkbox" name="elements[]" value="{{ $item->id }}"
                                {{ $isChecked ? 'checked' : '' }}
                                style="transform: scale(0.9); margin-bottom: 2px; {{ $isChecked ? 'accent-color: green;' : '' }}">
                        </div>
                        <div style="font-size: 9px; color: black; {{ $isChecked ? 'font-weight: bold;' : '' }}">
                            {{ $item->name }}
                        </div>
                    </td>
                @endforeach
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
                    <th style="border: 1px solid #ccc;"  class="sombra">FECHA INGRESO</th>
                    <td style="border: 1px solid #ccc;">{{ Carbon::parse($order->arrivalDate)->format('d/m/Y') }}</td>
                    <th style="border: 1px solid #ccc;"  class="sombra">HORA INGRESO</th>
                    <td style="border: 1px solid #ccc;">{{ Carbon::parse($order->arrivalDate)->format('H:i') }}</td>
                </tr>
                <tr>
                    <th style="border: 1px solid #ccc;" class="sombra" >FECHA ENTREGA</th>
                    <td style="border: 1px solid #ccc;">{{ Carbon::parse($order->deliveryDate)->format('d/m/Y') }}</td>
                    <th style="border: 1px solid #ccc;" class="sombra" >HORA ENTREGA</th>
                    <td style="border: 1px solid #ccc;">{{ Carbon::parse($order->deliveryDate)->format('H:i') }}</td>
                </tr>
                <tr>
                    <th style="border: 1px solid #ccc;" class="sombra">ASESOR DEL SERVICIO</th>
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
            font-size: 10px;
        ">
            {{ $fuelText }}
        </div>
    </div>
</td>

    </tr>
</table>



{{-- Observaciones --}}
@if (!empty($order->observations))
    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
        <tr>
            <th  class="sombra" style="width: 20%; text-align: center; padding: 5px; border-top: 1px solid #000; border-bottom: 1px solid #000;">
                OBSERVACIONES
            </th>
            <td style="padding: 3px; border-top: 1px solid #000; border-bottom: 1px solid #000; white-space: pre-wrap;">
                {{ $order->observations }}
            </td>
        </tr>
    </table>
@endif




<br>
    {{-- Footer y firmas --}}
   <div class="footer" style="text-transform: none;">
    <p>
        <strong>Por la presente autorizo</strong> las reparaciones aquí descritas conjuntamente con el material
        que sea necesario usar en ellas. También autorizo a ustedes y a sus empleados para que afierren este
        vehículo por las calles, carreteras u otros sitios a fin de asegurar las pruebas o inspecciones
        pertinentes que garanticen el trabajo.
    </p>
    <p>
        <strong>Nota:</strong> Se cobrará derecho de guardanía si el vehículo no es retirado en las 48
        horas después de terminado el trabajo.
    </p>
    <br>

    <div class="firmas-container" style="text-transform: none;">
        <div class="firma">Firma asesor de servicio</div>
        <div class="firma">Firma autorizada del cliente</div>
    </div>
</div>


   
</body>

</html>