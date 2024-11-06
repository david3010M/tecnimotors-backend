<?php

namespace App\Utils;

class Constants
{

    public const CREDIT_NOTE_STATUS_PENDING = 'PENDIENTE';
    public const CREDIT_NOTE_STATUS_PAID = 'PAGADO';
    public const CREDIT_NOTE_STATUS_EXPIRED = 'VENCIDO';
    public const CREDIT_NOTE_STATUS_CANCELED = 'ANULADO';

    public const AMORTIZATION_PAID = 'PAGADO';
    public const AMORTIZATION_PENDING = 'PENDIENTE';
    public const COMMITMENT_PENDING = 'PENDIENTE';
    public const COMMITMENT_PAID = 'PAGADO';
    public const COMMITMENT_EXPIRED = 'VENCIDO';

    public const COMMITMENT_CONTADO = 'CONTADO';
    public const COMMITMENT_CREDITO = 'CREDITO';
    public const BUDGET_CREDITO = 'Credito';
    public const BUDGET_CONTADO = 'Contado';
    public const SALE_CONTADO = 'CONTADO';
    public const SALE_CREDITO = 'CREDITO';
    public const SALE_NORMAL = 'NORMAL';
    public const SALE_DETRACCION = 'DETRACCION';
    public const SALE_FACTURA = 'FACTURA';
    public const SALE_BOLETA = 'BOLETA';
    public const SALE_TICKET = 'TICKET';
    public const SALE_NOTA_CREDITO_BOLETA = 'NOTA_CREDITO_BOLETA';
    public const SALE_NOTA_CREDITO_FACTURA = 'NOTA_CREDITO_FACTURA';

    public const IGV = 0.18;
    public const SALE_FACTURADO = 'FACTURADO';
    public const COMMITMENT_PAGADO = 'PAGADO';
    public const COMMITMENT_PENDIENTE = 'PENDIENTE';
    public const COMMITMENT_VENCIDO = 'VENCIDO';
    public const SALE_PAGADO = 'PAGADO';
    public const SALE_PENDIENTE = 'PENDIENTE';
    public const SALE_VENCIDO = 'VENCIDO';
    public const BUDGET_SHEET_PAGADO = 'PAGADO';
    public const BUDGET_SHEET_FACTURADO = 'FACTURADO';
    public const BUDGET_SHEET_PENDIENTE = 'PENDIENTE';
    public const BUDGET_SHEET_VENCIDO = 'VENCIDO';

    public const DEFAULT_PER_PAGE = 5;
    public const NOT_REQUESTED_TEXT = "TODOS";

    //REPORTES
    public const REPORTE_CAJA_CLIENTE_EXCEL = 'REPORTE_CAJA_CLIENTE.xlsx';
    public const REPORTE_CAJA_VEHICLE_EXCEL = 'REPORTE_CAJA_VEHICLE.xlsx';
    public const REPORTE_UNIDADES_ATENDIDAS = 'REPORTE_UNIDADES_ATENDIDAS.xlsx';
    public const REPORTE_PRODUCTOS_VENDIDOS = 'REPORTE_PRODUCTOS_VENDIDOS.xlsx';

    public const REPORTE_SERVICIOS_EXCEL = 'REPORTE_SERVICIOS.xlsx';
    public const REPORTE_CAJA_EXCEL = 'REPORTE_CAJA.xlsx';
    public const REPORTE_COMPROMISOS = 'REPORTE_COMPROMISOS.xlsx';

    public const REPORTES = 'reportes';

    const ES_MONTHS = [
        'January' => "Enero",
        'February' => "Febrero",
        'March' => "Marzo",
        'April' => "Abril",
        'May' => "Mayo",
        'June' => "Junio",
        'July' => "Julio",
        'August' => "Agosto",
        'September' => "Septiembre",
        'October' => "Octubre",
        'November' => "Noviembre",
        'December' => "Diciembre"
    ];
}
