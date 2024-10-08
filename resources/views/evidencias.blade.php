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

        h1 {
            font-size: 20px;
            font-weight: bolder;
            color: #007AC2;
            text-align: center;
            padding-bottom: 10px;
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

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .font-12 {
            font-size: 12px;
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

        .w50 {
            width: 50%;
        }

        .w40 {
            width: 40%;
        }

        .w20 {
            width: 20%;
        }

        .listaObservaciones li {
            margin-top: 2px;
            text-align: justify;
        }

        .text-sm {
            font-size: 9px;
        }

        .w40 {
            width: 40%;
        }

        .w10 {
            width: 10%;
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
                <div class="titlePresupuesto">EVIDENCIAS DE ATTENCION</div>
                <div class="numberPresupuesto">N° {{ $attention->number }}</div>
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

    <table class="tablePeople font-12">
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
                {{ $attention->vehicle->vehicleModel->brand->name }}
            </td>
            <th class="w20 blue">
                Km
            </th>
            <td class="w20">
                {{ intval($attention->km) }}
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

    <table>
        {{--        @foreach($attention->routeImages->chunk(2) as $imageChunk)--}}
        {{--            <tr>--}}
        {{--                @foreach($imageChunk as $image)--}}
        {{--                    <td>--}}
        {{--                        <img--}}
        {{--                            src="https://imagenes.20minutos.es/files/image_1920_1080/uploads/imagenes/2023/09/27/25-anos-google.jpeg"--}}
        {{--                            alt="imagen"--}}
        {{--                            style="max-width: 300px;"--}}
        {{--                        >--}}
        {{--                    </td>--}}
        {{--                @endforeach--}}
        {{--            </tr>--}}
        {{--        @endforeach--}}

        <br> <br>
        <table style="width: 100%; border-collapse: collapse; text-align: center;">
            @if($attention->routeImagesAttention->isNotEmpty())
                <tr>
                    <td colspan="2">
                        <h1>Evidencias de Llegada del Vehiculo</h1>
                    </td>
                </tr>
                <tr>
                    @foreach($attention->routeImagesAttention as $index => $image)
                        @if($index % 2 == 0 && $index != 0)
                </tr>
                <tr>
                    @endif
                    <td style="width: 50%; padding: 10px; border: none;">
                        <img
                            src="{{ $image->route }}"
                            alt="imagen"
                            style="width: 100%; height: 200px; object-fit: cover;"
                        >
                    </td>
                    @endforeach
                </tr>
            @endif

            @if($attention->routeImagesTask->isNotEmpty())
                <tr>
                    <td colspan="2">
                        <h1>Evidencias de Tareas Realizadas</h1>
                    </td>
                </tr>
                <tr>
                    @foreach($attention->routeImagesTask as $index => $image)
                        @if($index % 2 == 0 && $index != 0)
                </tr>
                <tr>
                    @endif
                    <td style="width: 50%; padding: 10px; border: none;">
                        <img
                            src="{{ $image->route }}"
                            alt="imagen"
                            style="width: 100%; height: 200px; object-fit: cover;"
                        >
                    </td>
                    @endforeach
                </tr>
            @endif
        </table>


    </table>


</div>


<img class="footerImage" src="{{ asset('img/degraded.png') }}" alt="degraded">
</body>

</html>
