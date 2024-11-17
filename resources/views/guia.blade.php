@php
    use Carbon\Carbon;
    header('Access-Control-Allow-Origin: https://transportes-hernandez-mrsoft.vercel.app');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Allow-Credentials: true');

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        exit();
    }

    // Aqu� contin�a tu l�gica para procesar la solicitud y generar la respuesta
    // por ejemplo:
    $data1 = ['mensaje' => 'Solicitud permitida por CORS'];
    echo json_encode($data1);

    function personNames($person)
    {
        if ($person == null) {
            return '-'; // Si $person es nulo, retornamos un valor predeterminado
        }

        $typeD = $person->typeofDocument ?? 'dni';
        $cadena = '';

        if (strtolower($typeD) === 'ruc') {
            $cadena = $person->businessName;
        } else {
            $cadena = $person->names . ' ' . $person->fatherSurname . ' ' . $person->motherSurname;
        }

        // Corregido el operador ternario y la concatenación
        return $cadena;
    }

    function getArchivosDocument($idventa, $typeDocument)
    {
        $funcion = 'buscarNumeroSolicitud';
        $url =
            'https://develop.garzasoft.com:81/tecnimotors-facturador/controlador/contComprobante.php?funcion=' .
            $funcion .
            '&typeDocument=' .
            $typeDocument;

        // Parámetros para la solicitud
        $params = http_build_query(['idventa' => $idventa]);

        // Inicializamos cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '&' . $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Ejecutamos la solicitud y obtenemos la respuesta
        $response = curl_exec($ch);

        // Cerramos cURL
        curl_close($ch);

        // Verificamos si la respuesta es válida
        if ($response !== false) {
            // Decodificamos la respuesta JSON
            $data = json_decode($response, true);

            // Verificamos si la respuesta contiene la información del archivo PNG
            if (isset($data['png'])) {
                $pngFile = $data['png'];

                // Aquí podrías agregar el código para mostrar la imagen en una etiqueta <img>
                echo '<img width="130px" src="https://develop.garzasoft.com:81/tecnimotors-facturador/ficheros/' .
                    $pngFile .
                    '" alt="Imagen PNG">';
            } else {
                echo '';
            }
        } else {
            echo 'Error en la solicitud.';
        }
    }

    $receptionDetails = $object->details;
    $descriptions = $receptionDetails->pluck('description')->toArray();
    $descriptionString = implode(', ', $descriptions);

@endphp

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GUIA</title>
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
            padding-top: 5px;
            padding-bottom: 30px;
        }

        td,
        th {
            padding: 2px;
        }

        .headerImage {
            position: absolute;
            top: -20;
            left: 0;
            width: 100%;
        }


        .footerImage {
            position: absolute;
            bottom: -20;
            left: 0;
            width: 100%;
        }

        .content {
            margin-top: 10px;
            padding-left: 30px;

            padding-right: 30px;
        }

        .contentImage {
            width: 100%;
            text-align: left;

        }

        .logoImage {
            width: 70%;
            height: 70px;
            text-align: left
        }

        .logoImageQr {
            width: auto;
            height: 100px;
            text-align: right
        }

        .containerQr {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .titlePresupuesto {

            font-size: 20px;
            font-weight: bolder;
            text-align: center;
            /*margin-top: 20px;*/
            /*margin-bottom: 20px;*/
            color: rgb(126, 0, 0);


        }

        .numberPresupuesto {
            font-size: 15px;

            text-align: center;
            /*margin-top: 20px;*/
            /*margin-bottom: 20px;*/
            color: black;
        }

        .blue {
            color: black;
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
            font-size: 12px;
        }

        .tableInfo {
            margin-top: 20px;
        }

        .tablePeople {
            margin-top: 10px;
            font-size: 12px;
            /* border: 1px solid rgb(0, 0, 0); */

        }

        .tablePeople td,
        .tablePeople th {
            padding: 3px 7px
        }

        .tablePeople th {
            background-color: rgb(255, 255, 255);
            color: rgb(0, 0, 0);
            text-align: left;

        }

        .tableDetail {
            margin-top: 30px;
        }

        .detTabla {
            float: right;
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
            padding: 1px;
        }

        .border {
            border: 1px solid black;
            padding: 5px;
            text-align: center
        }

        .tablePeople td.right {
            padding: 1px;
        }

        .tableDetail th {
            background-color: rgb(126, 0, 0);
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
            font-size: 12px;
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
            width: 14%;
        }

        .detTabla tr:last-child {
            border-top: 1px solid #000;
        }
    </style>
</head>

<body>

    {{-- <img class="headerImage" src="{{ asset('storage/img/curvTop.png') }}" alt="degraded"> --}}
    <div class="content">
        <table class="tableInfo">
            <tr>
                <div class="contentImage">
                    <img class="logoImage" src="{{ asset('storage/img/logoTecnimotors.png') }}" alt="logoTransporte">

                </div>


                <td class="right">
                    <div
                        style="border-radius:8px;border: 1px solid black; display: inline-block; text-align: center; width: 210px;">
                        <div class="numberPresupuesto" style="padding:4px;">RUC: 20487467139</div>
                        <div class="titlePresupuesto"
                            style="background-color: black;padding:2px 7px;font-size:12px; color: white; font-weight: bold;">
                            GUIA REMISIÓN ELECTRÓNICA
                        </div>

                        <div class="numberPresupuesto" style="padding:3px; margin-top: 5px;">N°
                            {{ $object->full_number }}
                        </div>
                    </div>
                </td>



            </tr>
        </table>

        {{-- <table>
            <tr>
                <th class="blue left" style="font-size: 10px; width: 41%;">
                    
                </th>
                <th class="blue left" style="font-size: 10px; width: 39%;">
                    <b> Número de Registro o MTC: 1596501CNG</b>
                </th>
                <th class="blue left" style="font-size: 10px; width: 20%;">
                    
                </th>
            </tr>
        </table> --}}
        <br>

        <table>
            <tr>
                <td class="w10 blue left">
                    <b> TECNI MOTORS DEL PERÚ E.I.R.L.</b>
                </td>
            </tr>
            <tr>
                <td class="w10 blue left" style="font-size: 11px">
                    PRO. AVENIDA BOLOGNESI - URB. SAN MANUEL MZA. A LOTE. 7 LAMBAYEQUE - CHICLAYO - CHICLAYO
                </td>
            </tr>
        </table>

        <table class="tablePeople font-10">

            <tr>
                <th class="w20 blue">
                    Fecha de Emisión:
                </th>
                <td class="w40">
                    {{ $object->date_emision ? \Carbon\Carbon::parse($object->date_emision)->format('d/m/Y') : 'N/A' }}
                </td>
                <th class="w20 blue">
                    Fecha Traslado:
                </th>
                <td class="w40">
                    {{ $object->date_traslado ? \Carbon\Carbon::parse($object->date_traslado)->format('d/m/Y') : 'N/A' }}
                </td>
            </tr>



            <tr>


                <th class="w20 blue">
                    Destinatario:
                </th>
                <td>
                    {{ $object->recipient_names ?? '' }}
                </td>

                {{-- <td style="w50 text-align: justify">
                    {{ strtoupper($object->addressStart ?? '') }}
                    - {{ strtoupper($object->districtStart?->province?->department?->name ?? '') }}
                    - {{ strtoupper($object->districtStart?->province?->name ?? '') }}
                    - {{ strtoupper($object->districtStart?->name ?? '') }}
                </td> --}}


                {{-- 
                <th class="w20 blue">
                    Punto de llegada:
                </th> --}}

                {{-- <td style="w50 text-align: justify">
                    {{ strtoupper($object->addressEnd ?? '') }}
                    - {{ strtoupper($object->districtEnd?->province?->department?->name ?? '') }}
                    - {{ strtoupper($object->districtEnd?->province?->name ?? '') }}
                    - {{ strtoupper($object->districtEnd?->name ?? '') }}
                </td> --}}

            </tr>
            <tr>


                <th class="w20 blue">
                    DNI/RUC:
                </th>
                <td>
                    {{ $object->recipient_document ?? '' }}
                </td>

            </tr>

            {{-- <tr>


                <th class="w20 blue">
                    Dirección:
                </th>
                <td>
                    {{-- {{ personNames($object->sender ?? '') }} 


                </td>
         
            </tr> --}}
            <tr>
                <th class="w20 blue">
                    Modalidad:
                </th>
                <td class="w20">

                    {{ $object->modality ?? '' }}

                </td>
                <th class="w20 blue">
                    Transbordo:
                </th>
                <td class="w20">
                    {{ $object->transbordo ? 'SI' : 'NO' }}
                </td>

            </tr>


            <tr>
                <th class="w20 blue">
                    Motivo:
                </th>
                <td class="w20">
                    {{ $object->cod_motive . ' - ' . $object->motive_name ?? '0' }}
                </td>


            </tr>

            <tr>
                <th class="w20 blue">
                    Cant. Paquetes:
                </th>
                <td class="w20">
                    {{ $object->nro_paquetes ?? '0' }}
                </td>


            </tr>



        </table>

        {{-- <table class="tablePeople font-10">
            <tr>
                <th class="w20 blue">
                    PUNTO PARTIDA:
                </th>
                @php
                    // dd($object->origin?->name );
                @endphp
                <td style="text-align: left">
                    {{ strtoupper($object->addressStart ?? '') }}
                    - {{ strtoupper($object->districtStart?->province?->department?->name ?? '') }}
                    - {{ strtoupper($object->districtStart?->province?->name ?? '') }}
                    - {{ strtoupper($object->districtStart?->name ?? '') }}
                </td>


            </tr>
            <tr>
                <th class="w20 blue">
                    PUNTO LLEGADA:
                </th>
                @php
                    // dd($object);
                @endphp
                <td style="text-align: left">
                    {{ strtoupper($object->addressEnd ?? '') }}
                    - {{ strtoupper($object->districtEnd?->province?->department?->name ?? '') }}
                    - {{ strtoupper($object->districtEnd?->province?->name ?? '') }}
                    - {{ strtoupper($object->districtEnd?->name ?? '') }}
                </td>

            </tr>
        </table> --}}

        <table class="tablePeople font-10" style="border: 1px solid black; border-collapse: collapse; width: 100%;">
            <thead>
                <tr>
                    <th
                        style="text-align: center; background:    rgb(126, 0, 0);; color: white; padding: 8px; border: 1px solid black;">
                        Código</th>
                    <th
                        style="text-align: center; background:    rgb(126, 0, 0);; color: white; padding: 8px; border: 1px solid black;">
                        Descripción</th>
                    <th
                        style="text-align: center; background:    rgb(126, 0, 0);; color: white; padding: 8px; border: 1px solid black;">
                        UM</th>
                    <th
                        style="text-align: center; background:    rgb(126, 0, 0);; color: white; padding: 8px; border: 1px solid black;">
                        Cantidad</th>
                    <th
                        style="text-align: center; background:    rgb(126, 0, 0);; color: white; padding: 8px; border: 1px solid black;">
                        Peso Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalWeight = 0;
                    $item = 1;
                @endphp

                @foreach ($receptionDetails as $detail)
                    <tr>
                        <td style="text-align: center; padding: 8px; border: 1px solid black;">{{ $detail->code }}</td>
                        <td style="text-align: center; padding: 8px; border: 1px solid black;">
                            {{ $detail->description }}</td>

                        <td style="text-align: center; padding: 8px; border: 1px solid black;">
                            {{ $detail->unit ?? 'NIU' }}</td>
                        <td style="text-align: center; padding: 8px; border: 1px solid black;">{{ $detail->cant ?? 1 }}
                        </td>
                        <td style="text-align: center; padding: 8px; border: 1px solid black;">
                            {{ number_format($detail->weight ?? 0, 2) }}</td>
                    </tr>
                    @php
                        $totalWeight += $detail->weight ?? 0;
                    @endphp
                @endforeach

                <tr>
                    <td colspan="3" style="border: 1px solid black;"></td>
                    <td style="text-align: center; font-weight: bold; border: 1px solid black;">Total</td>
                    <td style="text-align: center; border: 1px solid black;">{{ number_format($totalWeight, 2) }}</td>
                </tr>
            </tbody>
        </table>




        <table class="tablePeople font-10" style="margin-top: 15px">
            <tr>
                <th class="w20 blue">
                    Observaciones:
                </th>
                <td class="w50">
                </td>
            </tr>

            <tr class="extra-space" width="50%">
                <td colspan="2">
                    {{ $object->observation ?? '' }}
                </td>
            </tr>

        </table>

        <table class="tablePeople font-10" style="border: 1px solid black; border-collapse: collapse; width: 100%;">
            <tr>
                <th class="w20 blue" colspan="2">
                    Conductor y unidad:
                </th>

                <td colspan="2"></td>
            </tr>
            <tr>
                <th class="w20 blue" colspan="2"></th>
                <td colspan="2"></td>
            </tr>
            <tr>
                <th class="w20 blue">
                    Conductor:
                </th>
                <td class="w20">
                    {{ $object->driver_names . ' ' . $object->driver_surnames ?? '' }}
                </td>

                <th class="w20 blue">

                    DNI/RUC

                </th>
                <td class="w20">
                    {{ $object->driver_document }}


                </td>
            </tr>

            <tr>



                <th class="w20 blue">
                    @if ($object->copilot?->person)
                        Licencia
                    @else
                    @endif
                </th>
                <td class="w20">
                    @if ($object->copilot?->person)
                        {{ $object->copilot?->licencia }}
                    @else
                    @endif
                </td>
            </tr>

            <tr>
                <th class="w20 blue" colspan="2"></th>
                <td colspan="2"></td>
            </tr>
            <tr>
                <th class="w20 blue">
                    Placa:
                </th>
                <td class="w40">
                    {{ $object->vehicle_placa }}
                </td>

                <th class="w20 blue">
                    Nro. Licencia
                </th>
                <td class="w40">{{ $object->driver_licencia ?? '' }}
                </td>


            </tr>
            <tr>
                <th class="w20 blue" colspan="2"></th>
                <td colspan="2"></td>
            </tr>

            <tr>
                <th class="w20 blue">
                    Factura:
                </th>
                <td class="w40">
                    {{ $object->factura }}
                </td>

                <th class="w20 blue">

                </th>
                <td class="w40">
                </td>
            </tr>
            <tr>
                <th class="w20 blue" colspan="2"></th>
                <td colspan="2"></td>
            </tr>
        </table>

        <table class="tablePeople font-10" style="border: 1px solid black; border-collapse: collapse; width: 100%;">
            <tr>
                <th class="blue" style="width: 14%; text-align: left;">
                    Ubigeo Partida:
                </th>
                <td style="width: 70%; text-align: left;">
                    {{ $object?->districtStart?->cadena ?? '' }}
                </td>
            </tr>
            <tr>
                <th class="blue" style="width: 14%; text-align: left;">
                    Dirección Partida:
                </th>
                <td style="width: 70%; text-align: left;">
                    {{ $object->address_start }}
                </td>
            </tr>
            <tr>
                <th class="blue" style="width: 14%; text-align: left;">
                    Ubigeo Llegada:
                </th>
                <td style="width: 70%; text-align: left;">
                    {{ $object?->districtEnd?->cadena ?? '' }}
                </td>
            </tr>
            <tr>
                <th class="blue" style="width: 14%; text-align: left;">
                    Dirección Llegada:
                </th>
                <td style="width: 70%; text-align: left;">
                    {{ $object->address_end }}
                </td>
            </tr>
        </table>


        <br>

        <p style="font-size:11px">Representación impresa de la GUIA DE
            REMISIÓN ELECTRÓNICA. Consulte en
            <a href="https://facturae-garzasoft.com" style="text-decoration: none; color: inherit;" target="_blank">
                https://facturae-garzasoft.com
            </a>
        </p>


        <table class="footer"
            style="width: 100%; border-collapse: collapse; background-color: white; 
        margin-top:150px;text-align: right;">
            <tr>
                <th style="width: 50%; text-align: center; font-weight: normal;">
                    {{ getArchivosDocument($object->id, 'guia') }}
                </th>
                {{-- <th style="width: 50%; text-align: left; font-weight: normal;">Representación impresa de la GUIA DE
                    REMISIÓN REMITENTE ELECTRÓNICA. Consulte en
                    <a href="https://facturae-garzasoft.com" style="text-decoration: none; color: inherit;"
                        target="_blank">
                        https://facturae-garzasoft.com
                    </a>
                </th> --}}
                {{-- <th style="width: 45%;  text-align: center; font-weight: normal;">
                  
                        style="border: 1px solid black;text-align: center; width: 230px; height: 80px; 
                    margin: 10px auto; border-radius: 10px;">
                    </div> <!-- Cuadrado para firma -->
                    <div><b>Recibí Conforme</b></div> 
                </th> --}}
            </tr>
        </table>


    </div>


    {{-- <img class="footerImage" src="{{ asset('storage/img/curvBotton.png') }}" alt="degraded"> --}}
</body>

</html>
