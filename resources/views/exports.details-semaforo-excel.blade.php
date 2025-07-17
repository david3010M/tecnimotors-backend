<table>
    <thead>
        <tr>
            <th>Nro° Atención</th>
            <th>Cliente</th>
            <th>Placa</th>
            <th>Servicio</th>
            <th>Fecha Atención</th>
            <th>Días</th>
            <th>Periodo</th>
            <th>Semáforo</th>
        </tr>
    </thead>
    <tbody>
        @foreach($details as $detail)
            @php
                $fecha = \Carbon\Carbon::parse($detail['date_register']);
                $dias = $detail['period_detail']['days_diference'] ?? '-';
                $luces = $detail['period_detail']['lights'] ?? ['gris', 'gris', 'gris'];
                $colorTexto = implode('-', $luces); // por ejemplo "verde-gris-gris"
            @endphp
            <tr>
                <td>ATN-{{ str_pad($detail['id'], 3, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $detail['client_name'] ?? 'No disponible' }}</td>
                <td>{{ $detail['plate'] ?? '-' }}</td>
                <td>{{ $detail['service_name'] ?? 'N/A' }}</td>
                <td>{{ $fecha->format('Y-m-d') }}</td>
                <td>{{ $dias }}</td>
                <td>{{ $detail['period'] }}</td>
                <td>{{ $colorTexto }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
