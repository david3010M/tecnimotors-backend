@php use Carbon\Carbon; @endphp
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

        .titlePresupuesto {
            font-size: 25px;
            font-weight: bolder;
            text-align: right;
            /*margin-top: 20px;*/
            /*margin-bottom: 20px;*/
            color: #007AC2;
        }

        .numberPresupuesto {
            font-size: 17px;
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
            font-size: 10px;
        }

        .tableInfo {
            margin-top: 30px;
        }

        .tablePeople {
            margin-top: 30px;
            font-size: 10px;
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
            margin-top: 25px;
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

        .font-10 {
            font-size: 10px;
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
            padding: 5px;
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

        .sailTotal {
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
                <div class="titlePresupuesto">PRESUPUESTO</div>
                <div class="numberPresupuesto">N° {{ $budgetsheet->number }}</div>
            </td>
        </tr>
        <tr>
            <td class="center gray w40">
                <div class="text-sm">RUC: 20546989656</div>
                <div class="text-sm">Dir: Mz. A Lt. 7 Urb. San Manuel - Prolongación Bolognesi</div>
                <div class="text-sm">Telfono: 986202388 - 941515301</div>
            </td>
            <td class="right">
                <div><strong>{{ Carbon::parse($budgetsheet->created_at)->format('d-m-Y') }}</strong></div>
            </td>
        </tr>
    </table>

    <table class="tablePeople font-12">
        <tr>
            <th class="w10 blue">
                Cliente
            </th>
            <td class="w50">
                @if ($budgetsheet->attention->vehicle->person->typeofDocument == 'DNI')
                    {{ $budgetsheet->attention->vehicle->person->names .
                        ' ' .
                        $budgetsheet->attention->vehicle->person->fatherSurname .
                        ' ' .
                        $budgetsheet->attention->vehicle->person->motherSurname }}
                @elseif($budgetsheet->attention->vehicle->person->typeofDocument == 'RUC')
                    {{ $budgetsheet->attention->vehicle->person->businessName }}
                @endif
            </td>
            <th class="w20 blue">
                Fecha de Entrada
            </th>
            <td class="w20">
                {{ Carbon::parse($budgetsheet->attention->entryDate)->format('d/m/Y') }}
            </td>
        </tr>

        <tr>
            <th class="w10 blue">
                Placa
            </th>
            <td class="w50">
                {{ $budgetsheet->attention->vehicle->plate }}
            </td>
            <th class="w20 blue">
                Fecha de Entrega
            </th>
            <td class="w20">
                {{ Carbon::parse($budgetsheet->attention->deliveryDate)->format('d/m/Y') }}
            </td>
        </tr>

        <tr>
            <th class="w10 blue">
                Marca
            </th>
            <td class="w50">
                {{ $budgetsheet->attention->vehicle->vehicleModel->brand->name }}
            </td>
            <th class="w20 blue">
                Km
            </th>
            <td class="w20">
                {{ intval($budgetsheet->attention->km) }}
            </td>
        </tr>

        <tr>
            <th class="w10 blue">
                Modelo
            </th>
            <td class="w50">
                {{ $budgetsheet->attention->vehicle->model }}
            </td>
            <th class="w20 blue">
                Año
            </th>
            <td class="w20">
                {{ $budgetsheet->attention->vehicle->year }}
            </td>
        </tr>

    </table>

    <table class="tableDetail font-10">
        <tr>
            <th class="id">ITEM</th>
            <th class="description">DESCRIPCIÓN DE SERVICIOS Y REPUESTOS</th>
            <th class="unit">UND</th>
            <th class="quantity">CANT</th>
            <th class="unitPrice">V. UNIT</th>
            <th class="sailPrice">V. VENTA</th>
        </tr>

        <tr>
            <td colspan="6" class="blue strong center">MANO DE OBRA Y FACTORÍA</td>
        </tr>

        @php
            $idIncremental = 1;
        @endphp

        @foreach ($budgetsheet->attention->details as $detail)
            @if ($detail->type == 'Service')
                <tr>
                    <td class="id">{{ $idIncremental }}</td>
                    <td class="description" colspan="2">{{ $detail->service->name }}</td>
                    <td class="quantity">{{ $detail->quantity }}</td>
                    <td class="sailPrice">S/ {{ $detail->service->saleprice }}</td>
                    <td class="sailTotal">S/ {{ number_format($detail->saleprice, 2) }}</td>
                </tr>
                @php
                    $idIncremental++;
                @endphp
            @endif
        @endforeach

        <tr>
            <td colspan="6" class="blue strong center">REPUESTOS E INSUMOS</td>
        </tr>

        @php
            $idIncremental = 1;
        @endphp

        @foreach ($budgetsheet->attention->details as $detail)
            @if ($detail->type == 'Product')
                <tr>
                    <td class="id">{{ $idIncremental }}</td>
                    <td class="description">{{ $detail->product->name }}</td>
                    <td class="unit">{{ $detail->product->unit->code }}</td>
                    <td class="quantity">{{ $detail->quantity }}</td>
                    <td class="unitPrice">S/ {{ $detail->product->sale_price }}</td>
                    <td class="sailTotal">S/ {{ number_format($detail->saleprice, 2) }}</td>

                </tr>
            @endif
        @endforeach
    </table>

    <table class="tableTotal">
        <tr>
            <td class="w100 left"></td>
            <td class="w30 right totalInfo">
                <table class="font-12">
                    <tr>
                        <td>
                            <p class="right"><strong>Subtotal</strong></p>
                            <p class="right"><strong>IGV (18%)</strong></p>
                            <p class="right"><strong>Descuento</strong></p>
                            <p class="right"><strong>Total</strong></p>
                        </td>
                        <td>
                            <p class="right">{{ $budgetsheet->subtotal }}</p>
                            <p class="right">{{ $budgetsheet->igv }}</p>
                            <p class="right">{{ $budgetsheet->discount }}</p>
                            <p class="right">{{ $budgetsheet->total }}</p>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="observaciones">
        <p class="p10 bolder gris font-14">OBSERVACIONES</p>
        <ul class="listaObservaciones font-12">
            <li>El presente presupuesto tiene una validez de 2 días.</li>
            <li>Cualquier servicio adicional será notificado y cotizado por separado.</li>
            <li>El tiempo estimado de entrega está sujeto a disponibilidad de repuestos y aprobación del cliente.
            </li>
        </ul>
    </div>

    {{--    FIRMAS --}}
    <table class="tableFirmas">
        <tr>
            <td class="center borderTop w40">
                Firma del Cliente
            </td>
            <td class="w20"></td>
            <td class="center borderTop w40">
                Firma del Mecánico
            </td>
        </tr>
    </table>

</div>


<img class="footerImage" src="{{ asset('img/degraded.png') }}" alt="degraded">
</body>

</html>
