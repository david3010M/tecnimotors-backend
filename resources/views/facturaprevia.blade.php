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
            height: 30px;
            text-align: center;
        }

        .titlePresupuesto {
            font-size: 14px;
            font-weight: bolder;
            text-align: center;
            /*margin-top: 20px;*/
            /*margin-bottom: 20px;*/
            color: #ffffff;
        }

        .numberPresupuesto {
            font-size: 17px;
            font-weight: bolder;
            text-align: right;
            /*margin-top: 20px;*/
            /*margin-bottom: 20px;*/
            color: #007AC2;
        }

        .bordered {
            border: 1px solid black;
            border-collapse: collapse;
            width: 100%;
        }

        .bordered td {
            border: 1px solid black;
        }

        .innerTable {
            width: 100%;
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

        .font-7 {
            font-size: 7px;
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

    <table class="tableInfo bordered">
        <tr>
            <td class="center" style="width:20%;">
                <img class="logoImage" src="{{ asset('img/logoTecnimotors.png') }}" alt="logoTecnimotors">
            </td>

            <td class="center" style="width:50%;background-color:rgb(22 0 104);">
                <div class="titlePresupuesto">REGISTRO DE MANTENIMIENTO VEHICULAR</div>
            </td>

            <td class="center font-13" style="width:25%;">
                <table class="bordered innerTable">
                    <tr>
                        <td class="font-7" style="width: 60%">CÓDGIO DEL FORMATO</td>
                        <td class="font-7" style="width: 40%">AD9-SG-027</td>
                    </tr>
                    <tr>
                        <td class="font-7" style="width: 60%">REVISIÓN DEL FORMATO</td>
                        <td class="font-7" style="width: 40%">00</td>
                    </tr>
                    <tr>
                        <td class="font-7" style="width: 60%">FORMATO VIGENTE DESDE</td>
                        <td class="font-7" style="width: 40%">12/ABR/2017</td>
                    </tr>
                    <tr>
                        <td class="font-7" style="width: 60%">PÁGINA DEL FORMATO</td>
                        <td class="font-7" style="width: 40%">1 DE 1</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br>
    <table class="bordered">
        <tr>

            <td class="left font-9" colspan="2">
                <div class="">CONCESIÓN: Operación y Mantenimiento de las Obras de Irrigación del Proyecto
                    Olmos
                </div>
            </td>
            <td class="left font-9">
                <div class="">FECHA DEL REGISTRO</div>
            </td>

        </tr>
        <tr>

            <td class="left font-9">
                <div class="">CONCESIONARIA: H2Olmos S.A.</div>
            </td>
            <td class="left font-9">
                <div class="">CLIENTE: Gobierno Regional de Lambayeque</div>
            </td>
            <td class="left font-9">
                <div class=""></div>
            </td>

        </tr>
    </table>
    <br>
    <table class="bordered">
        <tr>
            <th colspan="3" class="blueBackground"
                style="background:#007AC2;color:white;border: 1px solid #bfbfbf; text-align: center;font-size: 10px">
                DATOS GENERALES
            </th>
        </tr>
        <tr>

            <td class="left font-9" style="width:30%">
                <div class="">UNIDAD: {{ $budgetsheet->attention?->vehicle?->typeVehicle?->name ?? '-' }}</div>
            </td>
            <td class="left font-9" style="width:30%">
                <div class="">MARCA: {{ $budgetsheet->attention?->vehicle?->vehicleModel?->brand?->name ?? '-' }}</div>
            </td>
            <td class="left font-9" style="width:40%">
                <div class="">TIPO MANTENIMIENTO</div>
            </td>

        </tr>
        <tr>

            <td class="left font-9" style="width:30%">
                <div class="">PLACA: {{ $budgetsheet->attention->vehicle->plate }}</div>
            </td>
            <td class="left font-9" style="width:30%">
                <div class="">MODELO: {{ $budgetsheet->attention->vehicle->model }}</div>
            </td>
            <td class="left font-9" style="width:40%">
                <div class="">PREVENTIVO:</div>
            </td>

        </tr>
        <tr>

            <td class="left font-9" style="width:30%">
                <div class="">UA: 67062H11</div>
            </td>
            <td class="left font-9" style="width:30%">
                <div class="">KM DE MANTENIMIENTO:</div>
            </td>
            <td class="left font-9" style="width:40%">
                <div class="">CORRECTIVO: X</div>
            </td>

        </tr>
    </table>

    <br>
    <table class="bordered">
        <tr>
            <th colspan="3" class="blueBackground"
                style="background:#007AC2;color:white;border: 1px solid #bfbfbf; text-align: center;font-size: 10px">
                TALLER
            </th>
        </tr>
        <tr>

            <td class="left font-9" colspan="2" style="width:30%">
                <div class="">EMPRESA: TECNI MOTORS DEL PERÚ E.I.R.L</div>
            </td>

            <td class="left font-9" style="width:40%">
                <div class="">TIPO MANTENIMIENTO</div>
            </td>

        </tr>
        <tr>

            <td class="left font-9" style="width:30%">
                <div class="">KM DE INGRESO: {{ $budgetsheet->attention?->km ?? '-' }}</div>
            </td>
            <td class="left font-9" style="width:30%">
                <div class="">KM DE SALIDA: {{ $budgetsheet->attention?->km ?? '-' }}</div>
            </td>
            <td class="left font-9" style="width:40%">
                <div class="">TELEFONO: {{ $budgetsheet->attention?->worker?->person?->phone  ?? '-' }}</div>
            </td>

        </tr>
        <tr>

            <td class="left font-9" style="width:30%">
                <div class="">FECHA
                    INGRESO: {{ \Carbon\Carbon::parse($budgetsheet->attention?->arrivalDate)->format('d/m/Y') ?? '-' }}</div>
            </td>
            <td class="left font-9" style="width:30%">
                <div class="">FECHA
                    SALIDA:{{ \Carbon\Carbon::parse($budgetsheet->attention?->deliveryDate)->format('d/m/Y') ?? '-' }}</div>
            </td>
            <td class="left font-9" style="width:40%">
                <div class="">
                    ASESOR:{{ $budgetsheet->attention?->worker?->person?->getFullNameAttribute()  ?? '-' }}</div>
            </td>

        </tr>
    </table>


    <!-- Tabla Repuestos e Insumos -->
    <table class="tableDetail font-9" style="border-collapse: collapse; width: 100%; border: 1px solid #bfbfbf;">
        <thead>
        <tr>
            <th colspan="5" class="blueBackground"
                style="background:#007AC2;color:white;border: 1px solid #bfbfbf; text-align: center;font-size: 10px">
                OBSERVACIONES
            </th>
        </tr>
        <tr>
            <th class="id" style="border: 1px solid #bfbfbf; padding: 5px;">ITEM</th>
            <th class="quantity" style="border: 1px solid #bfbfbf; padding: 5px;">CANT</th>
            <th class="unit" style="border: 1px solid #bfbfbf; padding: 5px;">UND</th>
            <th class="description" style="border: 1px solid #bfbfbf; padding: 5px;">DESCRIPCIÓN DE REPUESTOS</th>
            <th class="unitPrice" style="border: 1px solid #bfbfbf; padding: 5px;">COSTO</th>
        </tr>
        </thead>
        <tbody>
        @php
            $idIncremental = 1;
            $totalRows = 20;
            $filledRows = 0;
        @endphp

        @if (!empty($budgetsheet->attention?->details))
            @foreach ($budgetsheet->attention->details as $detail)
                @if ($detail->type == 'Product' || $detail->type == 'Service')
                    <tr>
                        <td class="id" style="border: 1px solid #bfbfbf; padding: 5px;">{{ $idIncremental }}</td>
                        <td class="quantity"
                            style="border: 1px solid #bfbfbf; padding: 5px;">{{ $detail->quantity ?? '-' }}</td>
                        <td class="unit"
                            style="border: 1px solid #bfbfbf; padding: 5px;">{{ $detail->product?->unit?->code ?? 'und.' }}</td>
                        <td class="description"
                            style="border: 1px solid #bfbfbf; padding: 5px;">{{ $detail->product?->name ?? $detail->service?->name }}</td>
                        <td class="sailTotal" style="border: 1px solid #bfbfbf; padding: 5px;">
                            S/{{ number_format($detail->saleprice * $detail->quantity ?? 0, 2) }}</td>
                    </tr>
                    @php
                        $idIncremental++;
                        $filledRows++;
                    @endphp
                @endif
            @endforeach
        @endif

        @for ($i = $filledRows; $i < $totalRows; $i++)
            <tr>
                <td class="id" style="border: 1px solid #bfbfbf; padding: 5px;">{{ $idIncremental }}</td>
                <td class="quantity" style="border: 1px solid #bfbfbf; padding: 5px;"></td>
                <td class="unit" style="border: 1px solid #bfbfbf; padding: 5px;"></td>
                <td class="description" style="border: 1px solid #bfbfbf; padding: 5px;"></td>
                <td class="sailTotal" style="border: 1px solid #bfbfbf; padding: 5px;"></td>
            </tr>
            @php
                $idIncremental++;
            @endphp
        @endfor
        </tbody>
    </table>


    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <!-- Tabla de Observaciones (Izquierda) -->
            <td style="width: 60%; vertical-align: top;">


            </td>

            <!-- Tabla de Totales (Derecha) -->
            <td style="width: 40%; vertical-align: top;">
                <table class="tableTotal" style="width: 100%; font-size: 12px;">
                    <tr>
                        <td class="right totalInfo" style="padding: 5px;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="text-align: right;">
                                        <p><strong>SUBTOTAL</strong></p>
                                        <p><strong>IGV 18%</strong></p>

                                        <p><strong>Total</strong></p>
                                    </td>
                                    <td style="text-align: right; padding-left: 10px;">
                                        <p>{{ $budgetsheet->subtotal }}</p>
                                        <p>{{ $budgetsheet->igv }}</p>

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
    <br>
    <table style="border-collapse: collapse; width: 100%; border: 1px solid #000;">
        <thead>
        <tr>
            <th style="border: 1px solid #000; padding: 5px; text-align: center;">CONDUCTOR</th>
            <th style="border: 1px solid #000; padding: 5px; text-align: center;">AUTORIZA</th>
            <th style="border: 1px solid #000; padding: 5px; text-align: center;">JEFE DE TALLER</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td style="border: 1px solid #000; padding: 5px; width:30%">FIRMA:</td>
            <td style="border: 1px solid #000; padding: 5px; width:30%">FIRMA:</td>
            <td style="border: 1px solid #000; padding: 5px; width:30%">FIRMA:</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px; width:30%">NOMBRE:</td>
            <td style="border: 1px solid #000; padding: 5px; width:30%">NOMBRE:</td>
            <td style="border: 1px solid #000; padding: 5px; width:30%">NOMBRE:</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px; width:30%">TELEFONO:</td>
            <td style="border: 1px solid #000; padding: 5px; width:30%">TELEFONO:</td>
            <td style="border: 1px solid #000; padding: 5px; width:30%">TELEFONO:</td>
        </tr>
        </tbody>
    </table>


</div>


<img class="footerImage" src="{{ asset('img/degraded.png') }}" alt="degraded">
</body>

</html>
