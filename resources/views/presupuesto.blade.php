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
            padding-top: 20px;
            padding-bottom: 20px;
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
            height: 50px;
        }

        .titlePresupuesto {
            font-size: 20px;
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
            margin-top: 15px;
            font-size: 10px;
            border: 1px solid #007AC2;
        }

        .tablePeople td,
        .tablePeople th {
            border: 1px solid #007AC2;
        }

        .tablePeople th {
            background-color: #ffffff;
            color: rgb(0, 0, 0);
            text-align: left;
        }

        .tableDetail {
            margin-top: 15px;
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

        .font-9 {
            font-size: 9px;
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
            background-color: #ffffff;
            color: rgb(0, 0, 0);
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

        .tableMessages {
            margin-top: 30px;
        }

        .tableMessages tr {
            text-align: center;
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

        .text-blue {
            color: #007AC2;
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

    {{-- <table class="tablePeople font-12">
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

</table> --}}


    <table class="tablePeople font-9" style="border-collapse: collapse; width: 100%; border: 1px solid #bfbfbf;">
        <thead>
        <tr>
            <th colspan="4" class="blueBackground"
                style="background:#007AC2;color:white;border: 1px solid #bfbfbf; text-align: center;font-size: 10px">
                CLIENTE
            </th>
        </tr>
        </thead>
        <tbody>
        @if ($budgetsheet->attention?->vehicle?->person)
            <tr>
                <th class="w20 blue" style="border: 1px solid #bfbfbf; padding: 5px;">Nombre</th>
                <td class="w30" style="border: 1px solid #bfbfbf; padding: 5px;">
                    @if ($budgetsheet->attention?->vehicle?->person?->typeofDocument == 'DNI')
                        {{ $budgetsheet->attention?->vehicle?->person?->names ?? '-' }} {{ $budgetsheet->attention?->vehicle?->person?->fatherSurname ?? '-' }} {{ $budgetsheet->attention?->vehicle?->person?->motherSurname ?? '-' }}
                    @elseif($budgetsheet->attention?->vehicle?->person?->typeofDocument == 'RUC')
                        {{ $budgetsheet->attention?->vehicle?->person?->businessName ?? '-' }}
                    @endif
                </td>
                <th class="w20 blue" style="border: 1px solid #bfbfbf; padding: 5px;">Celular</th>
                <td class="w30"
                    style="border: 1px solid #bfbfbf; padding: 5px;">{{ $budgetsheet->attention?->vehicle?->person?->phone ?? '-' }}</td>
            </tr>

            <tr>
                <th class="w20 blue" style="border: 1px solid #bfbfbf; padding: 5px;">Dirección</th>
                <td class="w30"
                    style="border: 1px solid #bfbfbf; padding: 5px;">{{ $budgetsheet->attention?->vehicle?->person?->address ?? '-' }}</td>
                <th class="w20 blue" style="border: 1px solid #bfbfbf; padding: 5px;">Tipo Documento</th>
                <td class="w30"
                    style="border: 1px solid #bfbfbf; padding: 5px;">{{ $budgetsheet->attention?->vehicle?->person?->typeofDocument ?? '-' }}</td>
            </tr>

            <tr>
                <th class="w20 blue" style="border: 1px solid #bfbfbf; padding: 5px;">Email</th>
                <td class="w30"
                    style="border: 1px solid #bfbfbf; padding: 5px;">{{ $budgetsheet->attention?->vehicle?->person?->email ?? '-' }}</td>
                <th class="w20 blue" style="border: 1px solid #bfbfbf; padding: 5px;">DNI/RUC</th>
                <td class="w30"
                    style="border: 1px solid #bfbfbf; padding: 5px;">{{ $budgetsheet->attention?->vehicle?->person?->documentNumber ?? '-' }}</td>
            </tr>
        @else
            <tr>
                <td colspan="4" style="border: 1px solid #bfbfbf; padding: 5px; text-align: center;">No hubo registros
                </td>
            </tr>
        @endif
        </tbody>
    </table>

    <table class="tablePeople font-9" style="border-collapse: collapse; width: 100%; border: 1px solid #bfbfbf;">
        <thead>
        <tr>
            <th colspan="4" class="blueBackground"
                style="background:#007AC2;color:white;border: 1px solid #bfbfbf; text-align: center;font-size: 10px">
                VEHÍCULO
            </th>
        </tr>
        </thead>
        <tbody>
        @if ($budgetsheet->attention?->vehicle)
            <tr>
                <th class="w20 blue" style="border: 1px solid #bfbfbf; padding: 5px;">Placa</th>
                <td class="w30"
                    style="border: 1px solid #bfbfbf; padding: 5px;">{{ $budgetsheet->attention?->vehicle?->plate ?? '-' }}</td>
                <th class="w20 blue" style="border: 1px solid #bfbfbf; padding: 5px;">Motor</th>
                <td class="w30"
                    style="border: 1px solid #bfbfbf; padding: 5px;">{{ $budgetsheet->attention?->vehicle?->motor ?? '-' }}</td>
            </tr>

            <tr>
                <th class="w20 blue" style="border: 1px solid #bfbfbf; padding: 5px;">Marca</th>
                <td class="w30"
                    style="border: 1px solid #bfbfbf; padding: 5px;">{{ $budgetsheet->attention?->vehicle?->vehicleModel?->brand?->name ?? '-' }}</td>
                <th class="w20 blue" style="border: 1px solid #bfbfbf; padding: 5px;">Año de Fab</th>
                <td class="w30"
                    style="border: 1px solid #bfbfbf; padding: 5px;">{{ $budgetsheet->attention?->vehicle?->year ?? '-' }}</td>
            </tr>

            <tr>
                <th class="w20 blue" style="border: 1px solid #bfbfbf; padding: 5px;">Modelo</th>
                <td class="w30"
                    style="border: 1px solid #bfbfbf; padding: 5px;">{{ $budgetsheet->attention?->vehicle?->model ?? '-' }}</td>
                <th class="w20 blue" style="border: 1px solid #bfbfbf; padding: 5px;">VIN</th>
                <td class="w30"
                    style="border: 1px solid #bfbfbf; padding: 5px;">{{ $budgetsheet->attention?->vehicle?->codeBin ?? '-' }}</td>
            </tr>

            <tr>
                <th class="w20 blue" style="border: 1px solid #bfbfbf; padding: 5px;">Km. Actual</th>
                <td class="w30"
                    style="border: 1px solid #bfbfbf; padding: 5px;">{{ $budgetsheet->attention?->km ?? '-' }}</td>
                <th class="w20 blue" style="border: 1px solid #bfbfbf; padding: 5px;">Tipo Vehículo</th>
                <td class="w30"
                    style="border: 1px solid #bfbfbf; padding: 5px;">{{ $budgetsheet->attention?->vehicle?->typeVehicle?->name ?? '-' }}</td>
            </tr>

            <tr>
                <th class="w20 blue" style="border: 1px solid #bfbfbf; padding: 5px;">Fecha de Ingreso</th>
                <td class="w30"
                    style="border: 1px solid #bfbfbf; padding: 5px;">{{ \Carbon\Carbon::parse($budgetsheet->attention?->arrivalDate)->format('d/m/Y') ?? '-' }}</td>
                <th class="w20 blue" style="border: 1px solid #bfbfbf; padding: 5px;">Fecha de Salida</th>
                <td class="w30"
                    style="border: 1px solid #bfbfbf; padding: 5px;">{{ \Carbon\Carbon::parse($budgetsheet->attention?->deliveryDate)->format('d/m/Y') ?? '-' }}</td>
            </tr>

            <tr>
                <th class="w10 blue" style="border: 1px solid #bfbfbf; padding: 5px;">Hora</th>
                <td class="w30"
                    style="border: 1px solid #bfbfbf; padding: 5px;">{{ \Carbon\Carbon::parse($budgetsheet->attention?->arrivalDate)->format('g:i a') ?? '-' }}</td>
                <th class="w20 blue" style="border: 1px solid #bfbfbf; padding: 5px;">Hora</th>
                <td class="w20"
                    style="border: 1px solid #bfbfbf; padding: 5px;">{{ \Carbon\Carbon::parse($budgetsheet->attention?->deliveryDate)->format('g:i a') ?? '-' }}</td>
            </tr>
        @else
            <tr>
                <td colspan="4" style="border: 1px solid #bfbfbf; padding: 5px; text-align: center;">No hubo registros
                </td>
            </tr>
        @endif
        </tbody>
    </table>


    <!-- Tabla Mano de Obra y Factoría -->
    <table class="tableDetail font-9" style="border-collapse: collapse; width: 100%; border: 1px solid #bfbfbf;">
        <thead>
        <tr>
            <th colspan="5" class="blueBackground"
                style="background:#007AC2;color:white;border: 1px solid #bfbfbf; text-align: center;font-size: 10px">
                MANO DE OBRA Y FACTORÍA
            </th>
        </tr>
        <tr>
            <th class="id" style="border: 1px solid #bfbfbf; padding: 5px;">ITEM</th>
            <th class="description" style="border: 1px solid #bfbfbf; padding: 5px;">DESCRIPCIÓN DE SERVICIOS</th>
            <th class="quantity" style="border: 1px solid #bfbfbf; padding: 5px;">CANT</th>
            <th class="sailPrice" style="border: 1px solid #bfbfbf; padding: 5px;">V. UNIT</th>
            <th class="sailTotal" style="border: 1px solid #bfbfbf; padding: 5px;">V. VENTA</th>
        </tr>
        </thead>
        <tbody>
        @php
            $idIncremental = 1;
            $hasDetails = false; // Variable para verificar si hay detalles
        @endphp

        @if (!empty($budgetsheet->attention?->details))
            @foreach ($budgetsheet->attention->details as $detail)
                @if ($detail->type == 'Service')
                    <tr>
                        <td class="id" style="border: 1px solid #bfbfbf; padding: 5px;">{{ $idIncremental }}</td>
                        <td class="description" style="border: 1px solid #bfbfbf; padding: 5px;">
                            {{ $detail->service?->name ?? '-' }}</td>
                        <td class="quantity" style="border: 1px solid #bfbfbf; padding: 5px;">
                            {{ $detail->quantity ?? '-' }}</td>
                        <td class="quantity" style="border: 1px solid #bfbfbf; padding: 5px;">
                            {{ number_format($detail->saleprice ?? 0, 2) }}</td>
                        <td class="sailTotal" style="border: 1px solid #bfbfbf; padding: 5px;">S/
                            {{ number_format(($detail->saleprice ?? 0) * ($detail->quantity ?? 0), 2) }}</td>
                    </tr>
                    @php
                        $idIncremental++;
                        $hasDetails = true; // Marcamos que hay detalles
                    @endphp
                @endif
            @endforeach
        @else
            <tr>
                <td colspan="5" style="border: 1px solid #bfbfbf; padding: 5px; text-align: center;">No hubo registros
                </td>
            </tr>
        @endif

        @if (!$hasDetails)
            <tr>
                <td colspan="5" style="border: 1px solid #bfbfbf; padding: 5px; text-align: center;">No hubo registros
                </td>
            </tr>
        @endif
        </tbody>
    </table>


    <br>

    <!-- Tabla Repuestos e Insumos -->
    <table class="tableDetail font-9" style="border-collapse: collapse; width: 100%; border: 1px solid #bfbfbf;">
        <thead>
        <tr>
            <th colspan="6" class="blueBackground"
                style="background:#007AC2;color:white;border: 1px solid #bfbfbf; text-align: center;font-size: 10px">
                REPUESTOS E INSUMOS
            </th>
        </tr>
        <tr>
            <th class="id" style="border: 1px solid #bfbfbf; padding: 5px;">ITEM</th>
            <th class="description" style="border: 1px solid #bfbfbf; padding: 5px;">DESCRIPCIÓN DE REPUESTOS</th>
            <th class="unit" style="border: 1px solid #bfbfbf; padding: 5px;">UND</th>
            <th class="quantity" style="border: 1px solid #bfbfbf; padding: 5px;">CANT</th>
            <th class="unitPrice" style="border: 1px solid #bfbfbf; padding: 5px;">V. UNIT</th>
            <th class="sailTotal" style="border: 1px solid #bfbfbf; padding: 5px;">V. VENTA</th>
        </tr>
        </thead>
        <tbody>
        @php
            $idIncremental = 1;
            $hasDetails = false; // Variable para verificar si hay detalles
        @endphp

        @if (!empty($budgetsheet->attention?->details))
            @foreach ($budgetsheet->attention->details as $detail)
                @if ($detail->type == 'Product')
                    <tr>
                        <td class="id" style="border: 1px solid #bfbfbf; padding: 5px;">{{ $idIncremental }}</td>
                        <td class="description" style="border: 1px solid #bfbfbf; padding: 5px;">
                            {{ $detail->product?->name ?? '-' }}</td>
                        <td class="unit" style="border: 1px solid #bfbfbf; padding: 5px;">
                            {{ $detail->product?->unit?->code ?? '-' }}</td>
                        <td class="quantity" style="border: 1px solid #bfbfbf; padding: 5px;">
                            {{ $detail->quantity ?? '-' }}</td>
                        <td class="unitPrice" style="border: 1px solid #bfbfbf; padding: 5px;">S/
                            {{ number_format($detail->product?->sale_price ?? 0, 2) }}</td>
                        <td class="sailTotal" style="border: 1px solid #bfbfbf; padding: 5px;">S/
                            {{ number_format($detail->saleprice*$detail->quantity ?? 0, 2) }}</td>
                    </tr>
                    @php
                        $idIncremental++;
                        $hasDetails = true; // Marcamos que hay detalles
                    @endphp
                @endif
            @endforeach
        @else
            <tr>
                <td colspan="6" style="border: 1px solid #bfbfbf; padding: 5px; text-align: center;">No hubo registros
                </td>
            </tr>
        @endif

        @if (!$hasDetails)
            <tr>
                <td colspan="6" style="border: 1px solid #bfbfbf; padding: 5px; text-align: center;">No hubo registros
                </td>
            </tr>
        @endif
        </tbody>
    </table>


    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <!-- Tabla de Observaciones (Izquierda) -->
            <td style="width: 60%; vertical-align: top;">
                <table class="observaciones font-9" style="border-collapse: collapse; width: 100%; font-size: 10px;">
                    <tr>
                        <th colspan="2"
                            style="border: 1px solid #bfbfbf; padding: 5px; text-align: left; font-weight: bold; background:#007AC2;color:white;font-size: 10px">
                            OBSERVACIONES
                        </th>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #bfbfbf; padding: 5px; width: 5%; text-align: center;">1</td>
                        <td style="border: 1px solid #bfbfbf; padding: 5px; width: 95%;">El presente presupuesto
                            tiene una validez de 2 días.
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #bfbfbf; padding: 5px; text-align: center;">2</td>
                        <td style="border: 1px solid #bfbfbf; padding: 5px;">Cualquier servicio adicional será
                            notificado y cotizado por separado.
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #bfbfbf; padding: 5px; text-align: center;">3</td>
                        <td style="border: 1px solid #bfbfbf; padding: 5px;">El tiempo estimado de entrega está
                            sujeto a disponibilidad de repuestos y aprobación del cliente.
                        </td>
                    </tr>
                </table>
            </td>

            <!-- Tabla de Totales (Derecha) -->
            <td style="width: 40%; vertical-align: top;">
                <table class="tableTotal" style="width: 100%; font-size: 12px;">
                    <tr>
                        <td class="right totalInfo" style="padding: 5px;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="text-align: right;">
                                        <p><strong>Subtotal</strong></p>
                                        <p><strong>IGV (18%)</strong></p>
                                        <p><strong>Descuento</strong></p>
                                        <p><strong>Total</strong></p>
                                    </td>
                                    <td style="text-align: right; padding-left: 10px;">
                                        <p>{{ $budgetsheet->subtotal }}</p>
                                        <p>{{ $budgetsheet->igv }}</p>
                                        <p>{{ $budgetsheet->discount }}</p>
                                        <p>{{ $budgetsheet->total }}</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="tableMessages">
        <tr>
            <p> Estoy de acuerdo en que todo el trabajo se ha realizado satisfactoriamente. </p>
        </tr>
        <tr>
            <p>
                <strong>
                    <em class="text-blue">
                        ¡Gracias por hacer negocios!</em>
                </strong>
            </p>
        </tr>
    </table>


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
