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
            font-size: 48px;
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
                    <p>N° {{ $budgetsheet->number }}</p>
                    <p><strong>{{ Carbon::parse($budgetsheet->created_at)->format('d-m-Y') }}</strong></p>
                </td>
                <td>
                    <div class="titlePresupuesto right">PRESUPUESTO</div>
                </td>
            </tr>
        </table>

        <table class="tablePeople font-14">
            <tr>
                <td class="left w50 font-12 gris">
                    <strong>EMPRESA</strong>
                </td>
                <td class="right w50 font-12 gris">
                    <strong>CLIENTE</strong>
                </td>
            </tr>

            <tr>
                <td class="left w50 blue bolder"><strong>TECNIMOTORS DEL PERÚ</strong></td>
                <td class="right w50 blue bolder"><strong>NOMBRE DEL CLIENTE</strong></td>
            </tr>

            <tr>
                <td class="left w50">{{ $budgetsheet->attention->vehicle->person->address }}</td>
                <td class="right w50">
                    {{ $budgetsheet->attention->vehicle->person->documentNumber }}
                </td>
            </tr>

            <tr>
                <td class="left w50">{{ $budgetsheet->attention->vehicle->person->phone }}</td>
                <td class="right w50">Vehículo</td>
            </tr>

            <tr>
                <td class="left w50">Fecha de entrega</td>
                <td class="right w50">{{ $budgetsheet->attention->vehicle->plate }}</td>
            </tr>

            <tr>
                <td class="left w50">
                    {{ Carbon::parse($budgetsheet->attention->deliveryDate)->format('d/m/Y') ?? 'Sin fecha' }}</td>
                <td class="right w50">{{ $budgetsheet->attention->vehicle->brand->name }}
                    {{ $budgetsheet->attention->vehicle->model }}</td>
            </tr>
        </table>

        <table class="tableDetail">
            <tr>
                <th class="description">Descripción</th>
                <th class="quantity">Cantidad</th>
                <th class="price">Precio</th>
            </tr>

            @foreach ($budgetsheet->attention->details as $detail)
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

        <table class="tableTotal">
            <tr>
                <td class="w50 left"></td>
                <td class="w50 right totalInfo">
                    <table>
                        <tr>
                            <td>
                                <p class="p10 right"><strong>Subtotal</strong></p>
                                <p class="p10 right"><strong>IGV (18%)</strong></p>
                                <p class="p10 right"><strong>Total</strong></p>
                            </td>
                            <td>
                                <p class="p10 right">{{ $budgetsheet->subtotal }}</p>
                                <p class="p10 right">{{ $budgetsheet->igv }}</p>
                                <p class="p10 right">{{ $budgetsheet->total }}</p>

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
