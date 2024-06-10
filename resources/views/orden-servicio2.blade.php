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
            padding-top: 60px;
            padding-bottom: 30px;
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
            padding-left: 90px;
            padding-right: 90px;
        }

        .contentImage {
            width: 100%;
            text-align: right;
        }

        .logoImage {
            width: 150px;
        }

        .titlePresupuesto {
            font-size: 33px;
            font-weight: bolder;
            text-align: center;
            margin-top: 20px;
            margin-bottom: 20px;
            color: #007AC2;
        }

        .blue {
            color: #007AC2;
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
            font-size: 16px;
            font-weight: bolder;
        }

        .tableDetail td {
            padding: 12px;
            border-bottom: 1px solid #3b3b3b;
        }

        .description {
            width: 60%;
        }

        .quantity {
            width: 15%;
            text-align: center;
        }

        .price {
            width: 25%;
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
    </style>
</head>

<body>

    <img class="headerImage" src="{{ asset('img/degraded.png') }}" alt="degraded">

    <div class="content">
        <div class="contentImage">
            <img class="logoImage" src="{{ asset('img/logoTecnimotors.png') }}" alt="logoTecnimotors">
        </div>

        <table class="tableInfo">
            <tr>
                <td class="left">
                    <p>N° {{ $attention->number }}</p>
                    <p><strong>{{ Carbon::parse($attention->created_at)->format('d-m-Y') }}</strong></p>
                </td>
                <td>
                    <div class="titlePresupuesto right">ORDEN DE TRABAJO</div>
                </td>
            </tr>
        </table>

        <table class="tablePeople font-14">
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
                    <strong>
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
                <td class="right w50 blue bolder"><strong>{{ $attention->vehicle->brand->name }}</strong></td>
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
                <<td class="left w50">{{ $attention->arrivalDate }}</td>
                    <td class="right w50">{{ $attention->vehicle->km }}</td>
            </tr>
            <tr>
                <td class="left w50">{{ $attention->deliveryDate }}</td>
                <td class="right w50">{{ $attention->vehicle->year }}</td>
            </tr>
        </table>

        <table class="tableDetail">
            <tr>
                <th class="description">Descripción</th>
                <th class="quantity">Cantidad</th>
                <th class="price">Precio</th>
            </tr>

            @foreach ($attention->details as $detail)
                <tr>
                    @if ($detail->type == 'Service')
                        <td class="description">{{ $detail->service->name }}</td>
                    @elseif ($detail->type == 'Product')
                        <td class="description">{{ $detail->product->name }}</td>
                    @endif
                    <td class="quantity">{{ $detail->quantity }}</td>
                    <td class="price">S/ {{ $detail->saleprice }}</td>
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
