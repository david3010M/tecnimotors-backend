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
            font-size: 32px;
            font-weight: bolder;
            text-align: right;
            /*margin-top: 20px;*/
            /*margin-bottom: 20px;*/
            color: #007AC2;
        }

        .numberPresupuesto {
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

            text-align: center;
        }

        .description {
            width: 50%;
        }

        .unit {
            width: 10%;
            text-align: left;
        }

        .quantity {
            width: 10%;
            text-align: left;
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

    <img class="headerImage" src="{{ asset('storage/img/degraded.png') }}" alt="degraded">

    <div class="content">


        <table class="tableInfo">
            <tr>
                <td class="center">
                    <img class="logoImage" src="{{ asset('storage/img/logoTecnimotors.png') }}" alt="logoTecnimotors">
                </td>
                <td class="right">
                    <div class="titlePresupuesto">REPORTE DE CAJA</div>
                    <div class="numberPresupuesto">{{ $data['MovCajaApertura']->sequentialNumber }}</div>
                </td>
            </tr>
            <tr>
                <td class="center gray w40">
                    <div class="text-sm">RUC: 20546989656</div>
                    <div class="text-sm">Dir: Mz. A Lt. 7 Urb. San Manuel - Prolongación Bolognesi</div>
                    <div class="text-sm">Telfono: 986202388 - 941515301</div>
                </td>
                <td class="right">
                    <div><strong>{{ Carbon::parse($data['MovCajaApertura']->created_at)->format('d-m-Y') }}</strong>
                    </div>
                </td>
            </tr>
        </table>

        <table class="tablePeople font-14">
            <tr>
                <th class="w10 blue">
                    Usuario
                </th>

                <td class="w50">
                    @if ($data['MovCajaApertura']->user->worker->person->typeofDocument == 'DNI')
                        {{ $data['MovCajaApertura']->user->worker->person->names .
                            ' ' .
                            $data['MovCajaApertura']->user->worker->person->fatherSurname .
                            ' ' .
                            $data['MovCajaApertura']->user->worker->person->motherSurname }}
                    @elseif($data['MovCajaApertura']->user->worker->person->typeofDocument == 'RUC')
                        {{ $data['MovCajaApertura']->user->worker->person->businessName }}
                    @endif
                </td>
                <th class="w20 blue">
                    Fecha de Impresión
                </th>
                <td class="w20">
                    {{ Carbon::parse(now()) }}
                </td>
            </tr>

            <tr>
                <th class="w20 blue">
                    T. Apertura
                </th>
                <td class="w20">
                    {{ $data['MovCajaApertura']->total }}
                </td>
                <th class="w10 blue">
                    Fecha Apertura
                </th>
                <td class="w50">
                    {{ $data['MovCajaApertura']->created_at }}
                </td>

            </tr>

            <tr>

                <th class="w20 blue">
                    T. Cierre
                </th>
                <td class="w20">
                    {{ $data['MovCajaCierre'] ? $data['MovCajaCierre']->total : '-' }}
                </td>
                <th class="w10 blue">
                    Fecha Cierre
                </th>
                <td class="w50">
                    {{ $data['MovCajaCierre'] ? \Carbon\Carbon::parse($data['MovCajaCierre']->created_at)->format('d-m-Y') : '-' }}
                </td>
            </tr>



        </table>


        <table class="tableDetail font-12">
            <tr>

                <th class="description">Fecha</th>
                <th class="quantity">Tipo</th>
                <th class="unitPrice">Concepto</th>
                <th class="sailPrice">Persona</th>
                <th class="sailPrice">Efect.</th>
                <th class="sailPrice">Yape</th>
                <th class="sailPrice">Plin</th>
                <th class="sailPrice">Tarj.</th>
                <th class="sailPrice">Depós.</th>
                <th class="sailPrice">Total</th>
                <th class="sailPrice">Comentario</th>

            </tr>


            @if (isset($data['MovCajaInternos']) && is_array($data['MovCajaInternos']))

                @foreach ($data['MovCajaInternos']['data'] as $movCajaInterno)
                    <tr>
                        <td class="id">
                            {{ $movCajaInterno->paymentDate ? \Carbon\Carbon::parse($movCajaInterno->paymentDate)->format('d-m-Y') : '-' }}
                        </td>
                        <td class="id">{{ $movCajaInterno->typeDocument ?? '-' }}</td>

                        <td class="id">{{ $movCajaInterno->paymentConcept->name ?? '-' }}</td>
                        <td class="w50">
                            @if ($movCajaInterno->person->typeofDocument == 'DNI')
                                {{ $movCajaInterno->person->names .
                                    ' ' .
                                    $movCajaInterno->person->fatherSurname .
                                    ' ' .
                                    $movCajaInterno->person->motherSurname }}
                            @elseif($movCajaInterno->person->typeofDocument == 'RUC')
                                {{ $movCajaInterno->person->businessName }}
                            @endif
                        </td>
                        <td class="id">{{ $movCajaInterno->cash ?? '0.00' }}</td>

                        <td class="id">{{ $movCajaInterno->yape ?? '0.00' }}</td>
                        <td class="id">{{ $movCajaInterno->plin ?? '0.00' }}</td>
                        <td class="id">{{ $movCajaInterno->card ?? '0.00' }}</td>
                        <td class="id">{{ $movCajaInterno->deposit ?? '0.00' }}</td>

                        <td class="id">{{ $movCajaInterno->total ?? '-' }}</td>
                        <td class="id">{{ $movCajaInterno->comment ?? '-' }}</td>

                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="1">No data available</td>
                </tr>
            @endif


        </table>


        <div style="page-break-before: always;" class="tableDetail font-12">
            <h2>Resumen de Caja</h2>
            <br>
            @php
                $resumenCaja = $data['resumenCaja'];
                $totalIngresos =
                    $resumenCaja['efectivo_ingresos'] +
                    $resumenCaja['yape_ingresos'] +
                    $resumenCaja['plin_ingresos'] +
                    $resumenCaja['tarjeta_ingresos'] +
                    $resumenCaja['deposito_ingresos'];
                $totalEgresos =
                    $resumenCaja['efectivo_egresos'] +
                    $resumenCaja['yape_egresos'] +
                    $resumenCaja['plin_egresos'] +
                    $resumenCaja['tarjeta_egresos'] +
                    $resumenCaja['deposito_egresos'];

            @endphp
            <table class="fondoNegro">
                <tr>
                    <th>Método de Pago</th>
                    <th>Total Ingresos</th>
                    <th>Total Egresos</th>
                    <th>Saldo Final</th>
                </tr>
                <tr>
                    <td>Efectivo</td>
                    <td>S/. {{ number_format($resumenCaja['efectivo_ingresos'], 2) ?? '0.00' }}</td>
                    <td>S/. {{ number_format($resumenCaja['efectivo_egresos'], 2) ?? '0.00' }}</td>
                    <td><b>S/.
                            {{ number_format($resumenCaja['efectivo_ingresos'] - $resumenCaja['efectivo_egresos'], 2) ?? '0.00' }}
                        </b></td>
                </tr>
                <tr>
                    <td>Yape</td>
                    <td>S/. {{ number_format($resumenCaja['yape_ingresos'], 2) ?? '0.00' }}</td>
                    <td>S/. {{ number_format($resumenCaja['yape_egresos'], 2) ?? '0.00' }}</td>
                    <td><b>S/.
                            {{ number_format($resumenCaja['yape_ingresos'] - $resumenCaja['yape_egresos'], 2) ?? '0.00' }}
                        </b></td>
                </tr>
                <tr>
                    <td>Plin</td>
                    <td>S/. {{ number_format($resumenCaja['plin_ingresos'], 2) ?? '0.00' }}</td>
                    <td>S/. {{ number_format($resumenCaja['plin_egresos'], 2) ?? '0.00' }}</td>
                    <td><b>S/.
                            {{ number_format($resumenCaja['plin_ingresos'] - $resumenCaja['plin_egresos'], 2) ?? '0.00' }}
                        </b></td>
                </tr>
                <tr>
                    <td>Tarjeta</td>
                    <td>S/. {{ number_format($resumenCaja['tarjeta_ingresos'], 2) ?? '0.00' }}</td>
                    <td>S/. {{ number_format($resumenCaja['tarjeta_egresos'], 2) ?? '0.00' }}</td>
                    <td><b>S/.
                            {{ number_format($resumenCaja['tarjeta_ingresos'] - $resumenCaja['tarjeta_egresos'], 2) ?? '0.00' }}
                        </b></td>
                </tr>
                <tr>
                    <td>Depósito</td>
                    <td>S/. {{ number_format($resumenCaja['deposito_ingresos'], 2) ?? '0.00' }}</td>
                    <td>S/. {{ number_format($resumenCaja['deposito_egresos'], 2) ?? '0.00' }}</td>
                    <td><b>S/.
                            {{ number_format($resumenCaja['deposito_ingresos'] - $resumenCaja['deposito_egresos'], 2) ?? '0.00' }}
                        </b></td>
                </tr>
                <!-- Otros métodos de pago aquí -->

                <!-- Fila de sumatoria -->
                <tr>
                    <td><b>Total General</b></td>
                    <td><b>S/. {{ number_format($totalIngresos, 2) }}</b></td>
                    <td><b>S/. {{ number_format($totalEgresos, 2) }}</b></td>
                    <td><b>S/. {{ number_format($totalIngresos - $totalEgresos, 2) }}</b></td>
                </tr>
            </table>
        </div>

    </div>


    <img class="footerImage" src="{{ asset('storage/img/degraded.png') }}" alt="degraded">
</body>

</html>
