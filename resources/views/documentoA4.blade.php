@php
    use Carbon\Carbon;

    // Guardar el SVG como un archivo temporal

    // Eliminar el archivo temporal SVG

@endphp
<!DOCTYPE html>
<html lang="es">

@php
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
                echo '<img src="https://develop.garzasoft.com:81/tecnimotors-facturador/ficheros/' .
                    $pngFile .
                    '" alt="Imagen PNG">';
            } else {
                echo '-';
            }
        } else {
            echo 'Error en la solicitud.';
        }
    }
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOCUMENTO DE PAGO</title>
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

        .left {
            text-align: left;
        }

        .right {
            text-align: right;
        }

        .juistify {
            text-align: juistify;
        }


        .footerImage {
            position: absolute;
            bottom: -20;
            left: 0;
            width: 100%;
        }

        .content {
            margin-top: 20px;
            padding-left: 30px;

            padding-right: 30px;
        }

        .contentImage {
            width: 100%;
            text-align: left;
        }

        .brandImage {
            width: 340px;
            margin: 0px 10px;
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
            padding: 0px 20px;
            /*margin-top: 20px;*/
            /*margin-bottom: 20px;*/
            color: rgb(0, 0, 0);
            ;
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
            font-size: 14px;
        }

        .tableInfo {
            margin-top: 5px;
        }

        .tablePeople {
            margin-top: 30px;
            font-size: 16px;
            border: 1px solid rgb(0, 0, 0);

        }

        .tablePeople td,
        .tablePeople th {
            padding: 5px 7px
        }

        .tablePeople th {
            background-color: rgb(255, 255, 255);
            color: rgb(0, 0, 0);
            text-align: left;

        }

        .tableDetail {
            margin-top: 30px;
            border: 1px;
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
            padding: 2px;
        }

        .border {
            border: 1px solid black;
            padding: 5px;
            text-align: center
        }

        .tablePeople td.right {
            padding: 2px;
        }

        .tableDetail th {
            background-color: #dcdcdc;
            color: black;
            padding: 3px;
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

        .detTabla tr:last-child {
            border-top: 1px solid #000;
        }
    </style>
</head>

<body>

    {{-- <img class="headerImage" src="{{ asset('storage/img/curvTop.png') }}" alt="degraded"> --}}

    <div class="content">


        <table class="tableInfo" style="width: 100%; border-collapse: collapse;">
            <tr>
                <!-- IZQUIERDA -->
                <td style="text-align: left; vertical-align: middle; width: 25%;">
                    <img src="{{ asset('img/logo.jpg') }}" width="150" class="logo" alt="Logo">
                </td>

                <!-- CENTRO -->
                <td style="text-align: center; vertical-align: middle; width: 35%;">
                    <img src="{{ asset('img/brands.jpg') }}" width="280" alt="Brand">
                </td>

                <!-- DERECHA -->
                <td style="text-align: right; vertical-align: middle; width: 40%;">
                    <div style="border: 1px solid black; padding: 10px; display: inline-block; text-align: center;">
                        <div class="titlePresupuesto">
                            {!! str_replace(' ', '<br>', $tipoElectronica) !!}
                        </div>
                        <div class="numberPresupuesto">RUC: 20487467139</div>
                        <div class="numberPresupuesto">{{ $numeroVenta }}</div>
                    </div>
                </td>
            </tr>
        </table>
        <br>


        <table style="width: 100%; border-collapse: collapse;">


            <tr>
                <td class="w10 blue left" style="font-size: 21px">
                    <b> TECNI MOTORS DEL PERÚ E.I.R.L.</b>
                </td>
            </tr>
            <tr>
                <td class="w10 blue left" style="font-size: 14px">
                    PRO.AVENIDA BOLOGNESI MZA. A LOTE. 7 URB. SAN MANUEL - CHICLAYO - CHICLAYO -
                    LAMBAYEQUE
                </td>
            </tr>
        </table>

        <table class="tablePeople font-12"
            style="width:100%; border:1px solid black; border-collapse:collapse; padding:4px;">

            <tr>
                <th style="width:15%; text-align:left; border:none;">Fecha Emision:</th>
                <td colspan="5" style="border:none;">
                    {{ \Carbon\Carbon::parse($fechaemision)->format('d/m/Y') }}
                </td>
            </tr>

            <tr>
                <th style="text-align:left; border:none;">Señor(es):</th>
                <td colspan="5" style="border:none;">{{ $cliente }}</td>
            </tr>

            <tr>
                <th style="text-align:left; border:none;">Direccion:</th>
                <td colspan="5" style="border:none;">{{ $direccion }}</td>
            </tr>

            <tr>
                <th style="text-align:left; border:none;">RUC</th>
                <td colspan="5" style="border:none;">{{ $ruc_dni }}</td>
            </tr>

            <tr>
                <th style="text-align:left; border:none;">Moneda</th>
                <td colspan="5" style="border:none;">PEN</td>
            </tr>

            <tr>
    <th style="text-align:left; border:none;">Forma de Pago:</th>
    <td colspan="{{ $typePayment == 'Crédito' ? 2 : 5 }}" style="border:none;">
        {{ $typePayment }}
    </td>

    @if($typePayment == 'Crédito')
        <th style="text-align:left; border:none;">Cuotas:</th>
        <td colspan="2" style="border:none;">
            @php
                $totalAmount = $cuentas ? $cuentas->count() : 0;
            @endphp
            {{ $totalAmount }}
        </td>
    @endif
</tr>

@if ($typePayment == 'Crédito')
    <tr>
        <td colspan="6" style="border:none; padding-top:5px;">
            <table class="font-12" style="width:100%; border-collapse:collapse;">
                <tr>
                    <!-- Columna izquierda: todas las cuotas -->
                    <td style="width:80%; vertical-align:top; padding:5px;">
                        @php $i = 1; @endphp
                        @foreach ($cuentas as $cuenta)
                            <div style="margin-bottom:3px; display:flex; justify-content:space-between;">
                                <span style="flex:1; text-align:left;">
                                    <b>Cuota:</b> {{ $i++ }}
                                </span>
                                <span style="flex:1; text-align:center;">
                                    <b>Fecha:</b>
                                    {{ \Carbon\Carbon::parse($cuenta->payment_date)->format('d/m/Y') }}
                                </span>
                                <span style="flex:1; text-align:right;">
                                    @php
                                        // Obtener el porcentaje correcto según el tipo de venta
                                        $porcentajeAplicado = 0;
                                        if ($typeSale == 'Detracción' && !is_null($porcentaje)) {
                                            $porcentajeAplicado = $porcentaje;
                                        } elseif ($typeSale == 'Retencion' && !is_null($retencion)) {
                                            $porcentajeAplicado = $retencion;
                                        }

                                        // Calcular descuento exacto (NO redondeamos el %)
                                        $descuento = $porcentajeAplicado > 0
                                            ? ($totalPagado * $porcentajeAplicado) / 100
                                            : 0;

                                        // Monto final de la cuota (sí redondeado a 2 decimales)
                                        $montoFinal = $cuenta->price - $descuento;
                                    @endphp
                                    <b>Monto:</b> {{ number_format($montoFinal, 2) }}
                                    @if($porcentajeAplicado > 0)
                                        <!-- <small>({{ $porcentajeAplicado }}%)</small> -->
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </td>

                    <!-- Columna derecha vacía (equilibrio) -->
                    <td style="width:20%;"></td>
                </tr>
            </table>
        </td>
    </tr>
@endif


        </table>






        <table class="tableDetail font-12">
            <tr>
                <th class="item" style="width:5%; text-align:center;">Ítem</th>
                <th class="center" style="width:40%;">Descripción</th>
                <th class="center" style="width:8%;">UM</th>
                <th class="center" style="width:8%;">Cantidad</th>
                <th class="center" style="width:9%;">V.U.</th>
                <th class="center" style="width:9%;">P.U.</th>
                <th class="center" style="width:9%;">Dscto.</th>
                <th class="center" style="width:12%;">Valor Venta</th>
            </tr>

            <?php
$totalDetalle = $totalPagado;
$subtotal = $totalPagado;
$iterador = 1;
foreach ($detalles as $detHab):
    // $subtotal = $detHab['precioventaunitarioxitem'] * $detHab['cantidad'];
    // $totalDetalle += $subtotal;
          
            
            ?>

            <tr>
                <td class="center"><?php    echo $iterador++; ?></td>

                <td class="justify font-10">
                    <?php    echo strtoupper($detHab['descripcion']); ?>
                </td>

                <td class="center font-10 left"> <?php    echo $detHab['um']; ?> </td>
                <td class="center font-10 left"><?php    echo $detHab['cant']; ?></td>
                <td class="center font-10 left">
                    <?php    echo number_format($detHab['vu'], 2); ?>

                </td>
                <td class="center font-10 left"><?php    echo number_format($detHab['pu'], 2); ?>
                </td>
                <td class="center font-10 left"><?php    echo $detHab['dscto']; ?></td>
                <td class="center font-10 left">
                    <?php    echo number_format($detHab['precioventaunitarioxitem'], 2); ?>
                </td>
            </tr>

            <?php endforeach; ?>


        </table>



        <table class="detTabla" style="width:200px; margin:10px;">
            <?php

if ($linkRevisarFact) {
    echo '
                                                                                                                                                                                                                                                        <tr>
                                                                                                                                                                                                                                                            <td style="text-align: left;">
                                                                                                                                                                                                                                                                <b>Op. Gravada:</b>
                                                                                                                                                                                                                                                            </td>
                                                                                                                                                                                                                                                            <td style="text-align: right;">
                                                                                                                                                                                                                                                                ' .
        number_format($totalDetalle / 1.18, 2) .
        '
                                                                                                                                                                                                                                                            </td>
                                                                                                                                                                                                                                                        </tr>
                                                                                                                                                                                                                                                        <tr>
                                                                                                                                                                                                                                                            <td style="text-align: left;">
                                                                                                                                                                                                                                                                <label for="igv"><b>I.G.V.(18%):</b>
                                                                                                                                                                                                                                                            </td>
                                                                                                                                                                                                                                                            <td style="text-align: right;">
                                                                                                                                                                                                                                                                <label for="igv">' .
        number_format($totalDetalle - $totalDetalle / 1.18, 2) .
        '
                                                                                                                                                                                                                                                            </td>
                                                                                                                                                                                                                                                        </tr>
                                                                                                                                                                                                                                                        <tr>
                                                                                                                                                                                                                                                            <td style="text-align: left;">
                                                                                                                                                                                                                                                                <label for="opInafecta"><b>Op. Inafecta:</b>
                                                                                                                                                                                                                                                            </td>
                                                                                                                                                                                                                                                            <td style="text-align: right;">
                                                                                                                                                                                                                                                                <label for="opInafecta">0.00
                                                                                                                                                                                                                                                            </td>
                                                                                                                                                                                                                                                        </tr>
                                                                                                                                                                                                                                                        <tr>
                                                                                                                                                                                                                                                            <td style="text-align: left;">
                                                                                                                                                                                                                                                                <label for="opExonerada"><b>Op. Exonerada:</b>
                                                                                                                                                                                                                                                            </td>
                                                                                                                                                                                                                                                            <td style="text-align: right;">
                                                                                                                                                                                                                                                                <label for="opExonerada">0.00
                                                                                                                                                                                                                                                            </td>
                                                                                                                                                                                                                                                        </tr>
                                                                                                                                                                                                                                                        <tr>
                                                                                                                                                                                                                                                            <td style="text-align: left;"><b>Importe Total</b></td>
                                                                                                                                                                                                                                        
                                                                                                                                                                                                                                                                 <td style="text-align: right;"><b>' .
        number_format($totalDetalle, 2) .
        '</b></td>
                                                                                                                                                                                                                                                        </tr>';
}
            ?>

        </table>
        <br><br>

        <br>


        <table>
            <tr>
                <td>


                    @if ($typeSale == 'Detracción')
                        @php
                            // Mapeo de códigos a nombres
                            $detractionNames = [
                                '027' => 'Servicio de Transporte',
                                '021' => 'Servicio de Almacenaje',
                                '022' => 'Otros Servicios Empresariales',
                                // 👉 aquí puedes ir agregando más códigos según tabla SUNAT
                            ];

                            $nombreDetraccion = $detractionNames[$codeDetraction] ?? '';
                        @endphp

                        <table style="text-align: left; width: 100%;">
                            <tr>
                                <td style="padding-left: 0;">
                                    <ul style="list-style: none; padding-left: 0; margin: 0;">
                                        <li><b>Información de la Detracción</b></li>
                                        <li>Bien o Servicio: {{ $nombreDetraccion ?? '' }}</li>
                                        <li>Nro. Cuenta Banco Nacion: {{ $cuentabn }}</li>
                                        <li>Porcentaje Detracción: {{ $porcentaje }}%</li>
                                        <li>Monto Detracción:
                                          S/ {{ number_format(($totalDetalle * $porcentaje) / 100, 2) }}

                                        </li>


                                        <li>Monto neto pendiente de pago:
                                            S/
                                            {{ number_format($totalDetalle - (($totalDetalle * $porcentaje) / 100), 2, '.', '') }}
                                        </li>

                                    </ul>
                                </td>
                            </tr>
                        </table>
                    @endif



                    @if ($typeSale == 'Retencion')
                        <table style="text-align: left; width: 100%;">
                            <tr>
                                <td style="padding-left: 0;">
                                    <ul style="list-style: none; padding-left: 0; margin: 0;">
                                        <li><b>Información de la Retención</b></li>
                                        <li>Nro. Cuenta Banco Nacion: {{ $cuentabn }}</li>
                                        <li>Porcentaje Retención: {{ $retencion }}%</li>
                                        <li>Monto Retención: S/ {{ number_format(($totalDetalle * $retencion) / 100, 2) }}</li>

                                        <li>Monto neto pendiente de pago:
                                            {{ $totalDetalle - round(($totalDetalle * $retencion) / 100) }}
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                        </table>
                    @endif
                    <br>
                </td>
            </tr>


            <tr>
                <td class="w10 blue left">
                    <label for="total">
                        <b>
                            SON:
                            <?php
$formatter = new NumberFormatter('es', NumberFormatter::SPELLOUT);
$totalEnPalabras = $formatter->format(floor($totalDetalle)); // Número en letras (solo parte entera)

if ($totalDetalle != floor($totalDetalle)) {
    $parteDecimal = round(($totalDetalle - floor($totalDetalle)) * 100); // Parte decimal como centavos
    echo strtoupper($totalEnPalabras) . " CON $parteDecimal/100 SOLES";
} else {
    echo strtoupper($totalEnPalabras) . " CON 00/100 SOLES";
}
                ?>
                        </b>
                    </label>
                </td>
            </tr>

            <tr>
                <td class="w10 left" style="font-size:11px">
                    Representación impresa de la Factura Electrónica, consulte
                    <br>
                    en<a href="https://facturae-garzasoft.com" style="text-decoration: none; color: inherit;"
                        target="_blank">
                        https://facturae-garzasoft.com
                    </a>
                    <br> <br><br>
                    {{-- <b>CUENTA CORRIENTE OPERACIONES LOGISTICAS HERNANDEZ S.A.C</b> --}}
                </td>

            </tr>
        </table>

        <br>

        <table style="width:100%; border-collapse: collapse;">
            <tr>

                <td style="vertical-align: top;">
                    <table class="border" style="width:80%;">
                        <tr>
                            <th class="border">BANCO</th>
                            <th class="border">CUENTA</th>
                            <th class="border">CCI</th>
                        </tr>
                        <tr>
                            <td class="border">BCP</td>
                            <td class="border">000-0000000-0-00</td>
                            <td class="border">00000000000000000000</td>
                        </tr>
                        <tr>
                            <th class="border">BANCO</th>
                            <th class="border">CUENTA DETRACCION</th>
                            <th class="border">CCI</th>
                        </tr>
                        <tr>
                            <td class="border">BN</td>
                            <td class="border">00000000000</td>
                            <td class="border"></td>
                        </tr>
                    </table>
                </td>

                <td style="vertical-align: top; padding-right: 10px;">
                    {{ getArchivosDocument($idMovimiento, 'venta') }}
                </td>
            </tr>
        </table>






    </div>




    {{-- <img class="footerImage" src="{{ asset('storage/img/curvBotton.png') }}" alt="degraded"> --}}
</body>

</html>