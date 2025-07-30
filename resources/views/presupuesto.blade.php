@php
    use Carbon\Carbon;

    $total = 0;
@endphp

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>PRESUPUESTO</title>
    <style>
         body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 5px;
        }

        .title,
        .number {
            display: block;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .header-table,
        .info-table,
        .items-table,
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .header-table td {
            border: none;
        }

        .info-table td,
        .info-table th,
        .items-table td,
        .items-table th {
            border: 1px solid #000;
            padding: 2px;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .items-table th {
            background-color: #dcdcdc;
        }

        .footer {
            margin-top: 15px;
            font-size: 9px;
        }

        .totales td {
            font-weight: bold;
        }
        .logo {
            height: 50px;
        }
    </style>
</head>

<body>
  {{-- Cabecera --}}
    <table class="header-table">
        <tr>
            <td style="width: 10%">
                <img src="{{ asset('img/logoTecnimotors.png') }}" width="130" class="logo" alt="Logo">
            </td>
            <td class="center">
                <div class="bold">TECNI MOTORS DEL PERÚ</div>
                <div>División Mantenimiento</div>
                <div class="bold">Líderes en Gestión del Mantenimiento Automotriz</div>
                <div>AV. FRANCISCO CUNEO N° 1150 - URB. PATACZA - CHICLAYO</div>
                <div>Cel: 952085190 - RPC: 979392964 - Telf: 237348</div>
                <div>Email: cesargutierrez@tecnimotorsdelperu.com</div>
            </td>
            <td class="right">
                <div class="title">PRESUPUESTO</div>
                <div class="number" >N° {{ $budgetsheet->number }}</div>
                <div>Fecha: {{ Carbon::parse($budgetsheet->created_at)->format('d/m/Y') }}</div>
            </td>
        </tr>
    </table>

    {{-- DATOS DEL CLIENTE Y VEHÍCULO --}}
    <table class="info-table">
        <tr>
            <th>Cliente</th>
            <td>{{ $budgetsheet?->attention?->vehicle->person->businessName ?? $budgetsheet?->attention?->vehicle->person->full_name }}
            </td>
            <th>Fecha de Entrada</th>
            <td>{{ Carbon::parse($budgetsheet?->attention?->arrivalDate)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th>Placa</th>
            <td>{{ $budgetsheet?->attention?->vehicle->plate }}</td>
            <th>KM</th>
            <td>{{ $budgetsheet?->attention?->km }}</td>
        </tr>
        <tr>
            <th>Marca</th>
            <td>{{ $budgetsheet?->attention?->vehicle->vehicleModel->brand->name ?? '' }}</td>
            <th>Fecha de Entrega</th>
            <td>{{ Carbon::parse($budgetsheet?->attention?->deliveryDate)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th>Modelo</th>
            <td>{{ $budgetsheet?->attention?->vehicle->vehicleModel->name ?? '' }}</td>
            <th>Año</th>
            <td>{{ $budgetsheet?->attention?->vehicle->year ?? '' }}</td>
        </tr>
    </table>
    {{-- DESCRIPCIÓN DE SERVICIOS Y REPUESTOS --}}
    {{-- DESCRIPCIÓN DE SERVICIOS Y REPUESTOS --}}
<table class="items-table">
    <thead>
        <tr>
            <th>ITEM</th>
            <th>DESCRIPCIÓN DE SERVICIOS Y REPUESTOS</th>
            <th>UND</th>
            <th>CANT</th>
            <th>V. UNIT</th>
            <th>V. VENTA</th>
        </tr>
    </thead>
    <tbody>
        @php
            $i = 1;
            $total = 0;
            $servicios = $details->whereNotNull('service_id');
            $productos = $details->whereNotNull('product_id');
        @endphp

        {{-- MANO DE OBRA Y FACTORÍA --}}
        @if ($servicios->count())
            <tr style="background-color: #f0f0f0;">
                <td colspan="6" class="bold center">MANO DE OBRA Y FACTORÍA</td>
            </tr>
            @foreach ($servicios as $item)
                @php
                    $desc = $item?->service?->name ?? 'Servicio';
                    $unit = '---';
                    $quantity = $item?->quantity;
                    $price = $item?->saleprice;
                    $subtotal = $quantity * $price;
                    $total += $subtotal;
                @endphp
                <tr>
                    <td class="center">{{ $i++ }}</td>
                    <td colspan="4">{{ $desc }}</td>
                    <td class="right">S/ {{ number_format($subtotal, 2) }}</td>
                </tr>
            @endforeach
        @endif

        {{-- REPUESTOS E INSUMOS --}}
        @if ($productos->count())
            <tr style="background-color: #f0f0f0;">
                <td colspan="6" class="bold center">REPUESTOS E INSUMOS</td>
            </tr>
            @foreach ($productos as $item)
                @php
                    $desc = $item?->product?->name ?? 'Producto';
                    $unit = $item?->product?->unit?->code ?? 'und';
                    $quantity = $item?->quantity;
                    $price = $item?->saleprice;
                    $subtotal = $quantity * $price;
                    $total += $subtotal;
                @endphp
                <tr>
                    <td class="center">{{ $i++ }}</td>
                    <td>{{ $desc }}</td>
                    <td class="center">{{ $unit }}</td>
                    <td class="center">{{ $quantity }}</td>
                    <td class="right">S/ {{ number_format($price, 2) }}</td>
                    <td class="right">S/ {{ number_format($subtotal, 2) }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>

    <tfoot class="totales">
        <tr>
            <td colspan="5" class="right">SUBTOTAL</td>
            <td class="right">S/ {{ number_format($total, 2) }}</td>
        </tr>
        <tr>
            <td colspan="5" class="right">I.G.V. (18%)</td>
            <td class="right">S/ {{ number_format($total * 0.18, 2) }}</td>
        </tr>
        <tr>
            <td colspan="5" class="right">TOTAL</td>
            <td class="right">S/ {{ number_format($total * 1.18, 2) }}</td>
        </tr>
    </tfoot>
</table>


    <br>

    {{-- FOOTER CON NROS DE CUENTA --}}
    <div class="footer">
        <p><strong>Yarly Bulnes R</strong></p>
        <br>
        <p><strong>CUENTAS DE DEPÓSITO:</strong></p>
        <ul>
            <li><strong>BCP Soles:</strong> 000-00000000-0-00 | CCI: 000-00000000000000000</li>
            <li><strong>Interbank Soles:</strong> 000-0000000000 | CCI: 00000000000000000000</li>
            <li><strong>Banco de la Nación:</strong> 000-000000000</li>
        </ul>
      
    </div>
</body>

</html>