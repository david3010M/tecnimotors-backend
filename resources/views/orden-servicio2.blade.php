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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
            letter-spacing: 0.5px;
        }

        html,
        body {
            width: 100%;
            height: 100%;
        }

        body {
            padding-top: 30px;
            padding-bottom: 30px;
        }

        td,
        th {
            padding: 2px;
        }

        .headerImage {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
        }

        .footerImage {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
        }

        .content {
            padding-left: 30px;
            padding-right: 30px;
        }

        .contentImage {
            width: 100%;
            text-align: right;
        }

        .logoImage {
            width: auto;
            height: 60px;
        }

        .titleAtencion {
            font-size: 32px;
            font-weight: bolder;
            text-align: right;
            /*margin-top: 20px;*/
            /*margin-bottom: 20px;*/
            color: #007AC2;
        }

        .numberAtencion {
            font-size: 20px;
            font-weight: bolder;
            text-align: right;
            /*margin-top: 20px;*/
            /*margin-bottom: 20px;*/
            color: #007AC2;
        }

        .blue {
            color: #007AC2;
        }

        .strong {
            font-weight: bolder;
        }

        .gris {
            color: #3b3b3b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .tableInfo {
            margin-top: 30px;
        }

        .tablePeople {
            margin-top: 30px;
            font-size: 16px;
            border: 1px solid #007AC2;
        }

        .tablePeople td,
        .tablePeople th {
            border: 1px solid #007AC2;
        }

        .tablePeople th {
            background-color: #007AC2;
            color: white;
            text-align: left;
        }

        .tableDetail {
            margin-top: 30px;
        }

        .p10 {
            padding: 10px;
        }

        .right {
            text-align: right;
        }

        .left {
            text-align: left;
        }

        .center {
            text-align: center;
        }

        .font-12 {
            font-size: 12px;
        }

        .font-14 {
            font-size: 14px;
        }

        .font-16 {
            font-size: 16px;
        }

        .margin20 {
            margin-top: 20px;
        }

        .bolder {
            font-weight: bolder;
        }

        .tablePeople td.left {
            padding: 2px;
        }

        .tablePeople td.right {
            padding: 2px;
        }

        .tableDetail th {
            background-color: #007AC2;
            color: white;
            padding: 10px;
            font-weight: bolder;
        }

        .tableDetail td {
            border-bottom: 1px solid #3b3b3b;
        }

        .id {
            width: 5%;
            text-align: center;
        }

        .description {
            width: 50%;
        }

        .unit {
            width: 10%;
            text-align: center;
        }

        .quantity {
            width: 10%;
            text-align: center;
        }

        .unitPrice {
            width: 10%;
            text-align: right;
        }

        .sailPrice {
            width: 15%;
            text-align: right;
        }

        .tableTotal {
            margin-top: 30px;
        }

        .w50 {
            width: 50%;
        }

        .w40 {
            width: 40%;
        }

        .w20 {
            width: 20%;
        }

        .totalInfo {
            border-collapse: collapse;
            font-size: 16px;
            background-color: #f2f2f2;
        }

        .observaciones {
            margin-top: 30px;
        }

        .listaObservaciones {
            padding-left: 20px;
            color: #3b3b3b;
        }

        .listaObservaciones li {
            margin-top: 2px;
            text-align: justify;
        }

        .tableFirmas {
            margin-top: 100px;
        }

        .borderTop {
            border-top: 1px solid #3b3b3b;
        }

        .text-sm {
            font-size: 9px;
        }

        .w40 {
            width: 40%;
        }

        .w25 {
            width: 25%;
        }

        .w10 {
            width: 10%;
        }

        .w30 {
            width: 30%;
        }
    </style>
</head>

<body>

    <img class="headerImage" src="{{ asset('img/degraded.png') }}" alt="degraded">

    <div class="content">

        <table class="tableInfo">
            <tr>
                <td class="center">
                    <img class="logoImage" src="{{ asset('img/logoTecnimotors.png') }}" alt="logoTecnimotors">
                </td>
                <td class="right">
                    <div class="titleAtencion">ORDEN ATENCIÓN</div>
                    <div class="numberAtencion">N° {{ $attention->correlativo }}</div>
                </td>
            </tr>
            <tr>
                <td class="center gray w40">
                    <div class="text-sm">RUC: 20546989656</div>
                    <div class="text-sm">Dir: Mz. A Lt. 7 Urb. San Manuel - Prolongación Bolognesi</div>
                    <div class="text-sm">Telfono: 986202388 - 941515301</div>
                </td>
                <td class="right">
                    <div><strong>{{ Carbon::parse($attention->created_at)->format('d-m-Y') }}</strong></div>
                </td>
            </tr>
        </table>

        {{-- <table class="tablePeople font-14">
        <tr>
            <td class="left w50 font-12 gris">
                <strong>CLIENTE</strong>
            </td>
            <td class="right w50 font-12 gris">
                <strong>AUTOMOVIL</strong>
            </td>
        </tr>

        <tr>
            <td class="left w50 blue bolder">
                <strong style="text-transform: uppercase;">
                    @if ($attention->vehicle->person->typeofDocument == 'DNI')
                        {{ $attention->vehicle->person->names .
                            ' ' .
                            $attention->vehicle->person->fatherSurname .
                            ' ' .
                            $attention->vehicle->person->motherSurname }}
                    @elseif($attention->vehicle->person->typeofDocument == 'RUC')
                        {{ $attention->vehicle->person->businessName }}
                    @endif
                </strong>
            </td>
            <td style="text-transform: uppercase;" class="right w50 blue bolder">
                <strong>{{ $attention->vehicle->brand->name }}</strong></td>
        </tr>
        <br>
        <tr>
            <td class="left w50">{{ $attention->vehicle->person->documentNumber }}</td>
            <td class="right w50">
                {{ $attention->vehicle->plate }}
            </td>
        </tr>

        <tr>
            <td class="left w50">{{ $attention->vehicle->person->address }}</td>
            <td class="right w50">{{ $attention->vehicle->model }}</td>
        </tr>

        <tr>
            <td class="left w50">{{ $attention->vehicle->person->phone }}</td>
            <td class="right w50">{{ $attention->vehicle->chasis }}</td>
        </tr>

        <tr>
            <td class="left w50">
                @if ($attention->vehicle->person->typeofDocument == 'DNI')
                    {{ $attention->vehicle->person->email }}
                @elseif($attention->vehicle->person->typeofDocument == 'RUC')
                    {{ $attention->vehicle->person->representativeNames .' '.$attention->vehicle->person->representativeDni }}
                @endif
            </td>
            <td class="right w50">{{ $attention->vehicle->motor }}</td>
        </tr>
        <tr>
            <
            <td class="left w50">{{ $attention->arrivalDate }}</td>
            <td class="right w50">{{ $attention->vehicle->km }}</td>
        </tr>
        <tr>
            <td class="left w50">{{ $attention->deliveryDate }}</td>
            <td class="right w50">{{ $attention->vehicle->year }}</td>
        </tr>
    </table> --}}

        <table class="tablePeople font-14">
            <tr>
                <th class="w10 blue">
                    Cliente
                </th>
                <td class="w50">
                    @if ($attention->vehicle->person->typeofDocument == 'DNI')
                        {{ $attention->vehicle->person->names .
                            ' ' .
                            $attention->vehicle->person->fatherSurname .
                            ' ' .
                            $attention->vehicle->person->motherSurname }}
                    @elseif($attention->vehicle->person->typeofDocument == 'RUC')
                        {{ $attention->vehicle->person->businessName }}
                    @endif
                </td>
                <th class="w20 blue">
                    Fecha de Entrada
                </th>
                <td class="w20">
                    {{ Carbon::parse($attention->entryDate)->format('d/m/Y') }}
                </td>
            </tr>

            <tr>
                <th class="w10 blue">
                    Placa
                </th>
                <td class="w50">
                    {{ $attention->vehicle->plate }}
                </td>
                <th class="w20 blue">
                    Fecha de Entrega
                </th>
                <td class="w20">
                    {{ Carbon::parse($attention->deliveryDate)->format('d/m/Y') }}
                </td>
            </tr>

            <tr>
                <th class="w10 blue">
                    Marca
                </th>
                <td class="w50">
                    {{ $attention->vehicle->brand->name }}
                </td>
                <th class="w20 blue">
                    Km
                </th>
                <td class="w20">
                    {{ $attention->vehicle->km }}
                </td>
            </tr>

            <tr>
                <th class="w10 blue">
                    Modelo
                </th>
                <td class="w50">
                    {{ $attention->vehicle->model }}
                </td>
                <th class="w20 blue">
                    Año
                </th>
                <td class="w20">
                    {{ $attention->vehicle->year }}
                </td>
            </tr>

        </table>


        <table class="tableDetail">
            <tr>
                <th class="description">Descripción</th>
                <th class="quantity">Cantidad</th>
            </tr>

            @foreach ($attention->details as $detail)
                <tr>
                    @if ($detail->type == 'Service')
                        <td class="description">{{ $detail->service->name }}</td>
                    @elseif ($detail->type == 'Product')
                        <td class="description">{{ $detail->product->name }}</td>
                    @endif
                    <td class="quantity">{{ $detail->quantity }}</td>
                </tr>
            @endforeach
        </table>


        <div class="observaciones">
            <p class="p10 bolder gris font-14">OBSERVACIONES</p>
            <ul class="listaObservaciones font-12">
                <li> {{ $attention->observations }}</li>

            </ul>
        </div>
        <div class="observaciones">
            <p class="p10 bolder gris font-14">ELEMENTOS</p>
            <ul class="listaObservaciones font-12">
                @foreach ($attention->elements as $element)
                    <li>{{ $element->element->name }}</li>
                @endforeach
            </ul>
        </div>

        <div class="observaciones">
            <p class="p10 gris font-14"> <span style="font-weight:bold;">NIVEL DE COMBUSTIBLE:
                </span> {{ fuelLevelToFraction($attention->fuelLevel) }}</p>
            <p class="p10 gris font-14"> <span style="font-weight:bold;">ASESOR DE SERVICIO:
                </span> {{ $attention->worker->person->names . ' ' . $attention->worker->person->fatherSurname }}</p>
        </div>


    </div>


    <img class="footerImage" src="{{ asset('img/degraded.png') }}" alt="degraded">
</body>

</html>
