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
            height: 90px;
        }
    </style>
</head>

<body>
  {{-- Cabecera --}}
    <table class="header-table">
        <tr>
            <td style="width: 10%">
                <img src="{{ asset('img/logo.jpg') }}" width="150" class="logo" alt="Logo">
            </td>
            <td class="center">
                <div class="bold" style="font-size:16">TECNI MOTORS DEL PERÚ</div>
                <div>Dir: Mz. A Lt. 7 Urb. San Manuel - Prolongación Bolognesi</div>
                <div>Cel: 941515301 - 986202388</div>
                <div>Email: cynthiab.tecnimotorsdelperu@gmail.com</div>
            </td>
            <td class="right" style="text-align: right; vertical-align: top; padding: 10px;">
    <div class="title" style="margin-bottom: 60px;"></div> {{-- margen para separar bloque superior --}}
    
    <div class="title" style="font-weight: bold; font-size: 14px; margin-bottom: 5px;">
        PRESUPUESTO
    </div>
    
    <div class="number" style="font-size: 14px; margin-bottom: 5px;">
        N° {{ $budgetsheet->number }}
    </div>
    
    <div style="font-size: 12px;">
        Fecha: {{ \Carbon\Carbon::parse($budgetsheet->created_at)->format('d/m/Y') }}
    </div>
</td>

            
        </tr>
    </table>

    {{-- DATOS DEL CLIENTE Y VEHÍCULO --}}
    <table class="info-table">
        <tr>
            <th>CLIENTE</th>
            <td>{{ $budgetsheet?->attention?->vehicle->person->businessName ?? $budgetsheet?->attention?->vehicle->person->full_name }}
            </td>
            <th>FECHA DE ENTRADA</th>
            <td>{{ Carbon::parse($budgetsheet?->attention?->arrivalDate)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th>PLACA</th>
            <td>{{ $budgetsheet?->attention?->vehicle->plate }}</td>
            <th>KM</th>
            <td>{{ $budgetsheet?->attention?->km }}</td>
        </tr>
        <tr>
            <th>MARCA</th>
            <td>{{ $budgetsheet?->attention?->vehicle->vehicleModel->brand->name ?? '' }}</td>
            <th>FECHA DE ENTREGA</th>
            <td>{{ Carbon::parse($budgetsheet?->attention?->deliveryDate)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th>MODELO</th>
            <td>{{ $budgetsheet?->attention?->vehicle->vehicleModel->name ?? '' }}</td>
            <th>AÑO</th>
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
            <li><strong>BCP Soles:</strong> 3052311871039 | CCI: 00230500231187103913</li>
            <li><strong>Scotiabank Soles:</strong> 630-0059018  | CCI: 00963020630005901858</li>
        </ul>
      
    </div>
</body>

</html>