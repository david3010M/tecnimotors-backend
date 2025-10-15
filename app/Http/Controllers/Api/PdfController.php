<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attention;
use App\Models\budgetSheet;
use App\Models\DetailBudget;
use App\Models\Guide;
use App\Models\Moviment;
use App\Models\Note;
use App\Models\NoteReason;
use App\Models\Person;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class PdfController extends Controller
{

    public function getServiceOrder($id)
    {
        $object = Attention::getAttention($id);

        //        HORIZONTAL
        $pdf = Pdf::loadView('orden-servicio2', [
            'order' => $object,
        ]);
        //        $pdf->setPaper('a3', 'landscape');

        //        return $object;
        return $pdf->stream('orden-servicio.pdf');
        //        return $pdf->download('orden-servicio.pdf');
    }

    public function getBudgetSheet($id)
    {
        $object = budgetSheet::getBudgetSheet($id);
        $details = DetailBudget::with(['service', 'product'])
            ->where('budget_sheet_id', $id)
            ->get();

        $pdf = Pdf::loadView('presupuesto', [
            'budgetsheet' => $object,
            'details' => $details,
        ]);

        //        $pdf->setPaper('a4', 'landscape');

        //        return view not pdf
//        return view('presupuesto', [
//            'budgetsheet' => $object,
//        ]);

        return $pdf->stream('presupuesto' . $object->id . '.pdf');
        //        return $pdf->download('orden-servicio.pdf');
    }

    // public function getServiceOrder2($id)
    // {
    //     $object = Attention::getAttention($id);
    //     $pdf = Pdf::loadView('orden-servicio2', [
    //         'attention' => $object,
    //     ]);
    //     return $pdf->stream('orden-servicio2.pdf');
    // }

    /**
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/reportCaja",
     *     summary="Exportar Reporte Caja",
     *     tags={"Reporte"},
     *     description="Genera y descarga una Reporte Caja Aperturada en formato PDF",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Reporte Caja en formato PDF",
     *         @OA\MediaType(
     *             mediaType="application/pdf",
     *             @OA\Schema(
     *                 type="string",
     *                 format="binary"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error en los datos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error en los datos")
     *         )
     *     )
     * )
     */

    public function reportCaja()
    {
        $movCaja = Moviment::where('status', 'Activa')
            ->where('paymentConcept_id', 1)
            ->first();

        $data = [];
        if ($movCaja) {

            $movCajaAperturada = Moviment::where('id', $movCaja->id)->where('paymentConcept_id', 1)
                ->first();

            if (!$movCajaAperturada) {
                return response()->json([
                    "message" => "Movimiento de Apertura no encontrado",
                ], 404);
            }

            $movCajaCierre = Moviment::where('id', '>', $movCajaAperturada->id)
                ->where('paymentConcept_id', 2)
                ->orderBy('id', 'asc')->first();

            if ($movCajaCierre == null) {
                //CAJA ACTIVA
                $query = Moviment::select(['*', DB::raw('(SELECT obtenerFormaPagoPorCaja(moviments.id)) AS formaPago')])
                    ->where('id', '>=', $movCajaAperturada->id)
                    ->orderBy('id', 'desc')
                    ->with(['paymentConcept', 'person', 'user.worker.person', 'budgetSheet']);

                // Ejecutar la consulta paginada
                $movimientosCaja = $query->paginate(15);

                $movimientosCaja = [
                    'current_page' => $movimientosCaja->currentPage(),
                    'data' => $movimientosCaja->items(), // Los datos paginados
                    'total' => $movimientosCaja->total(), // El total de registros
                    'first_page_url' => $movimientosCaja->url(1),
                    'from' => $movimientosCaja->firstItem(),
                    'next_page_url' => $movimientosCaja->nextPageUrl(),
                    'path' => $movimientosCaja->path(),
                    'per_page' => $movimientosCaja->perPage(),
                    'prev_page_url' => $movimientosCaja->previousPageUrl(),
                    'to' => $movimientosCaja->lastItem(),
                ];

                $resumenCaja = Moviment::selectRaw('
            COALESCE(SUM(CASE WHEN cp.type = "Ingreso" THEN moviments.total ELSE 0 END), 0.00) as total_ingresos,
            COALESCE(SUM(CASE WHEN cp.type = "Egreso" THEN moviments.total ELSE 0 END), 0.00) as total_egresos,
            COALESCE(SUM(CASE WHEN cp.type = "Ingreso" THEN moviments.cash ELSE 0 END), 0.00) as efectivo_ingresos,
            COALESCE(SUM(CASE WHEN cp.type = "Egreso" THEN moviments.cash ELSE 0 END), 0.00) as efectivo_egresos,
            COALESCE(SUM(CASE WHEN cp.type = "Ingreso" THEN moviments.yape ELSE 0 END), 0.00) as yape_ingresos,
            COALESCE(SUM(CASE WHEN cp.type = "Egreso" THEN moviments.yape ELSE 0 END), 0.00) as yape_egresos,
            COALESCE(SUM(CASE WHEN cp.type = "Ingreso" THEN moviments.plin ELSE 0 END), 0.00) as plin_ingresos,
            COALESCE(SUM(CASE WHEN cp.type = "Egreso" THEN moviments.plin ELSE 0 END), 0.00) as plin_egresos,
            COALESCE(SUM(CASE WHEN cp.type = "Ingreso" THEN moviments.card ELSE 0 END), 0.00) as tarjeta_ingresos,
            COALESCE(SUM(CASE WHEN cp.type = "Egreso" THEN moviments.card ELSE 0 END), 0.00) as tarjeta_egresos,
            COALESCE(SUM(CASE WHEN cp.type = "Ingreso" THEN moviments.deposit ELSE 0 END), 0.00) as deposito_ingresos,
            COALESCE(SUM(CASE WHEN cp.type = "Egreso" THEN moviments.deposit ELSE 0 END), 0.00) as deposito_egresos')
                    ->leftJoin('concept_pays as cp', 'moviments.paymentConcept_id', '=', 'cp.id')
                    ->where('moviments.id', '>=', $movCajaAperturada->id)
                    ->first();

                $movCajaCierreArray = null;
            } else {
                $movimientosCaja = Moviment::select(['*', DB::raw('(SELECT obtenerFormaPagoPorCaja(moviments.id)) AS formaPago')])
                    ->where('id', '>=', $movCajaAperturada->id)
                    ->where('branchOffice_id', $movCajaAperturada->branchOffice_id)
                    ->where('id', '<', $movCajaCierre->id)
                    ->orderBy('id', 'desc')
                    ->with(['paymentConcept', 'person', 'user.worker.person', 'budgetSheet'])
                    ->simplePaginate();

                $resumenCaja = Moviment::selectRaw('
                COALESCE(SUM(CASE WHEN cp.type = "Ingreso" THEN moviments.total ELSE 0 END), 0.00) as total_ingresos,
                COALESCE(SUM(CASE WHEN cp.type = "Egreso" THEN moviments.total ELSE 0 END), 0.00) as total_egresos,
                COALESCE(SUM(CASE WHEN cp.type = "Ingreso" THEN moviments.cash ELSE 0 END), 0.00) as efectivo_ingresos,
                COALESCE(SUM(CASE WHEN cp.type = "Egreso" THEN moviments.cash ELSE 0 END), 0.00) as efectivo_egresos,
                COALESCE(SUM(CASE WHEN cp.type = "Ingreso" THEN moviments.yape ELSE 0 END), 0.00) as yape_ingresos,
                COALESCE(SUM(CASE WHEN cp.type = "Egreso" THEN moviments.yape ELSE 0 END), 0.00) as yape_egresos,
                COALESCE(SUM(CASE WHEN cp.type = "Ingreso" THEN moviments.plin ELSE 0 END), 0.00) as plin_ingresos,
                COALESCE(SUM(CASE WHEN cp.type = "Egreso" THEN moviments.plin ELSE 0 END), 0.00) as plin_egresos,
                COALESCE(SUM(CASE WHEN cp.type = "Ingreso" THEN moviments.card ELSE 0 END), 0.00) as tarjeta_ingresos,
                COALESCE(SUM(CASE WHEN cp.type = "Egreso" THEN moviments.card ELSE 0 END), 0.00) as tarjeta_egresos,
                COALESCE(SUM(CASE WHEN cp.type = "Ingreso" THEN moviments.deposit ELSE 0 END), 0.00) as deposito_ingresos,
                COALESCE(SUM(CASE WHEN cp.type = "Egreso" THEN moviments.deposit ELSE 0 END), 0.00) as deposito_egresos')
                    ->leftJoin('concept_pays as cp', 'moviments.paymentConcept_id', '=', 'cp.id')
                    ->where('moviments.id', '>=', $movCajaAperturada->id)
                    ->where('moviments.id', '<', $movCajaCierre->id)
                    ->first();

            }
            $data = [

                'MovCajaApertura' => $movCajaAperturada,
                'MovCajaCierre' => $movCajaCierre,
                'MovCajaInternos' => $movimientosCaja,

                "resumenCaja" => $resumenCaja ?? null,
            ];
        } else {
            abort(404, 'MovCajaCierre not found');
        }

        $html = view('reportCaja', compact('data'))->render();

        // Crear una nueva instancia de Dompdf
        // $html = View::make('guia', $object)->render();

        // Configurar DomPDF
        $dompdf = new Dompdf();
        $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('a4', 'landscape');
        $dompdf->render();

        // Descargar el PDF con un nombre de archivo dinámico basado en el ID
        return $dompdf->stream('ReporteCaja_' . now() . '.pdf');
    }

    public function getBudgetSheetInfo($id)
    {
        $object = budgetSheet::with([
            'attention.worker.person',
            'attention.vehicle.person',
            'attention.vehicle.brand',
            'attention.details',
            'attention.routeImages',
            'attention.elements'
        ])->find($id);
        return $object;
    }

    /**
     * @OA\Get(
     *     path="/tecnimotors-backend/public/evidencias/{id}",
     *     summary="Exportar Evidencias",
     *     tags={"Reporte"},
     *     description="Genera y descarga una Evidencias en formato PDF",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter( name="id", in="path", required=true, description="ID de la atención", @OA\Schema(type="integer")),
     *     @OA\Response( response=200, description="Evidencias en formato PDF", @OA\MediaType(mediaType="application/pdf", @OA\Schema(type="string", format="binary"))),
     *     @OA\Response( response=422, description="Error en los datos", @OA\JsonContent( @OA\Property(property="message", type="string", example="Error en los datos")))
     * )
     */
    public function getEvidenceByAttention($id)
    {
        $object = Attention::with(['routeImagesTask.task', 'routeImagesAttention'])->find($id);
        $pdf = Pdf::loadView('evidencias', [
            'attention' => $object,
        ]);
        return $pdf->stream('presupuesto' . $object->id . '.pdf');
    }

    public function documentoA4(Request $request, $idMov = 0)
    {

        $Movimiento = Sale::with(['budgetSheet', 'commitments', 'saleDetails', 'moviment'])
            ->find($idMov);
        if (!$Movimiento) {
            abort(404, 'Venta No Encontrada');
        }
        $linkRevisarFact = false;
        $productList = [];
        if ($Movimiento) {
            $productList = $Movimiento->saleDetails;
        }
        // Inicializar el array de detalles
        $detalles = [];
        $totalVenta = 0; // inicializamos el acumulador

        if (!empty($productList)) {
            foreach ($productList as $detalle) {
                $pu = isset($detalle->unitPrice) ? round($detalle->unitPrice, 3) : 0;
                $cantidad = $detalle->quantity ?? 0;
                $descuento = $detalle->discount ?? 0;

                $igvRate = 0.18;
                $vu = $pu > 0 ? round($pu / (1 + $igvRate), 3) : 0;

                $valorVentaItem = round(($vu * $cantidad) - $descuento, 2);

                $detalles[] = [
                    "descripcion" => $detalle->description ?? '-',
                    "um" => $detalle->unit ?? '-',
                    "cant" => $cantidad,
                    "pu" => $pu,
                    "vu" => $vu,
                    "dscto" => $descuento,
                    "precioventaunitarioxitem" => $valorVentaItem,
                ];

                // Acumular el total
                $totalVenta += $valorVentaItem;
            }
        }

        // Redondeamos el total general a 2 decimales
        $totalVenta = round($totalVenta, 2);




        $tipoDocumento = '';

        $num = $Movimiento->fullNumber;
        if (strpos($num, 'B') === 0) {
            $tipoDocumento = 'BOLETA ELECTRÓNICA';
            $linkRevisarFact = true;
        } elseif (strpos($num, 'F') === 0) {
            $tipoDocumento = 'FACTURA ELECTRÓNICA';
            $linkRevisarFact = true;
        } elseif (strpos($num, 'T') === 0) {
            $tipoDocumento = 'TICKET ELECTRÓNICO';
            $linkRevisarFact = false;
        } else {
            abort(404);
        }
        $dateTime = Carbon::now()->format('Y-m-d H:i:s');
        $personaCliente = Person::withTrashed()->find($Movimiento->person_id);
        $fechaInicio = $Movimiento->created_at;
        $rucOdni = $personaCliente->documentNumber;
        $direccion = "";
        if (strtoupper($personaCliente->typeofDocument) != 'DNI') {
            $nombreCliente = $personaCliente->businessName;

            $direccion = $personaCliente->address ?? '-';
        } else {
            $nombreCliente = $personaCliente->names . ' ' . $personaCliente->fatherSurname . ' ' . $personaCliente->motherSurname;
            $direccion = $personaCliente->address ?? '-';
        }

        if ($personaCliente->names == 'VARIOS') {
            $nombreCliente = "VARIOS";
            if (strpos($num, 'B') === 0) {
                $rucOdni = '11111111';

            } elseif (strpos($num, 'F') === 0) {
                $rucOdni = '11111111111';

            }
        }

        $dataE = [
            'title' => 'DOCUMENTO DE PAGO',
            'ruc_dni' => $rucOdni,
            'direccion' => $direccion,
            'idMovimiento' => $Movimiento->id,
            'tipoElectronica' => $tipoDocumento,
            'typePayment' => $Movimiento->paymentType === 'CONTADO' ? 'Contado' : ($Movimiento->paymentType === 'CREDITO' ? 'Crédito' : '-'),
            'numeroVenta' => $num,
            'porcentaje' => $Movimiento->detractionPercentage,
            'porcentaje_retencion' => $Movimiento->retencion,
            'fechaemision' => $Movimiento->paymentDate,
            'cliente' => $nombreCliente,
            'detalles' => $detalles,
            'cuentas' => $Movimiento->commitments,
            'vuelto' => '0.00',
            'totalPagado' => $totalVenta * 1.18,



            'presupuesto' => $Movimiento?->budgetSheet?->number ?? '',
            'placa' => $Movimiento?->budgetSheet?->attention?->vehicle?->plate ?? '',
            'modelo' => $Movimiento?->budgetSheet?->attention?->vehicle?->model ?? '',
            'vin' => $Movimiento?->budgetSheet?->attention?->vehicle?->codeBin ?? '',
            'anio' => $Movimiento?->budgetSheet?->attention?->vehicle?->year ?? '',
            'cuentabn' => $Movimiento->cuentabn ?? '',


            'linkRevisarFact' => $linkRevisarFact,
            'formaPago' => $Movimiento->formaPago ?? '-',
            'fechaInicio' => $fechaInicio,

            'typeSale' => $Movimiento->saleType === 'NORMAL'
                ? 'Normal'
                : ($Movimiento->saleType === 'DETRACCION'
                    ? 'Detracción'
                    : ($Movimiento->saleType === 'RETENCION'
                        ? 'Retencion'
                        : '-')),


            'codeDetraction' => $Movimiento->detractionCode ?? '-',
            'detractionPercentage' => $Movimiento->detractionPercentage ?? '0',
            'retencion' => $Movimiento->retencion ?? '0',
        ];
        // Utiliza el método loadView() directamente en la fachada PDF
        $pdf = PDF::loadView('documentoA4', $dataE);
        $canvas = $pdf->getDomPDF()->get_canvas();
        // $contenidoAncho = $canvas->get_width();
        $contenidoAlto = $canvas->get_height();
        if (strpos($num, 'B') === 0) {
            $tipoDocumento = '01'; // Boleta
        } elseif (strpos($num, 'F') === 0) {
            $tipoDocumento = '03'; // Factura
        } elseif (strpos($num, 'T') === 0) {
            $tipoDocumento = '00'; // Ticket
        }

        $fileName = '20487467139-' . $tipoDocumento . '-' . $num . '.pdf'; // Formato del nombre
        $fileName = str_replace(' ', '_', $fileName); // Reemplazar espacios con guiones bajos
        return $pdf->stream($fileName);
    }

    public function creditNote($id)
    {
        $object = Note::find($id);

        $Movimiento = Sale::find($object->sale_id);
        $linkRevisarFact = false;

        $productList = [];
        if ($Movimiento) {
            $productList = $Movimiento->saleDetails;

        }
        // Inicializar el array de detalles
        $detalles = [];
        if ($object->totalCreditNote < $object->totalDocumentReference) {
            $reason = NoteReason::find($object->note_reason_id);
            $detalles[] = [
                "descripcion" => $reason->description ?? '-',
                "um" => 'NIU',
                "cant" => 1 ?? '1',
                "vu" => $detalle->unitValue ?? '-',
                "pu" => $object->totalCreditNote, // Cantidad fija (es un servicio)
                "dscto" => 0,
                // "precioventaunitarioxitem" => $detalle->subTotal ?? 0,
                "precioventaunitarioxitem" => $object->totalCreditNote ?? 0,
            ];
        } else {
            if (($productList) != []) {
                foreach ($productList as $detalle) {
                    $detalles[] = [
                        "descripcion" => $detalle->description ?? '-',
                        "um" => $detalle->unit ?? '-',
                        "cant" => $detalle->quantity ?? '-',
                        "vu" => $detalle->unitValue ?? '-',
                        "pu" => $detalle->unitPrice, // Cantidad fija (es un servicio)
                        "dscto" => $detalle->discount ?? 0,
                        // "precioventaunitarioxitem" => $detalle->subTotal ?? 0,
                        "precioventaunitarioxitem" => $detalle->unitPrice ?? 0,
                    ];
                }
            }
        }
        $tipoDocumento = '';
        $num = $Movimiento->fullNumber;
        if (strpos($num, 'B') === 0) {
            $tipoDocumento = 'BOLETA ELECTRÓNICA';
            $linkRevisarFact = true;
        } elseif (strpos($num, 'F') === 0) {
            $tipoDocumento = 'FACTURA ELECTRÓNICA';
            $linkRevisarFact = true;
        } elseif (strpos($num, 'T') === 0) {
            $tipoDocumento = 'TICKET ELECTRÓNICO';
            $linkRevisarFact = false;
        } else {
            abort(404);
        }
        $dateTime = Carbon::now()->format('Y-m-d H:i:s');
        // $personaCliente = Person::find($Movimiento->person_id);
        $personaCliente = Person::withTrashed()->find($Movimiento->person_id);
        $fechaInicio = $Movimiento->created_at;
        $rucOdni = $personaCliente->documentNumber;
        $direccion = "";
        if (strtoupper($personaCliente->typeofDocument) != 'DNI') {
            $nombreCliente = $personaCliente->businessName;
            $direccion = $personaCliente->address ?? '-';
        } else {
            $nombreCliente = $personaCliente->names . ' ' . $personaCliente->fatherSurname . ' ' . $personaCliente->motherSurname;
            $nombreCliente = $personaCliente->names . ' ' . $personaCliente->fatherSurname . ' ' . $personaCliente->motherSurname;
            $direccion = $personaCliente->address ?? '-';
        }

        if ($personaCliente->names == 'VARIOS') {
            $nombreCliente = "VARIOS";
            if (strpos($num, 'B') === 0) {
                $rucOdni = '11111111';
            } elseif (strpos($num, 'F') === 0) {
                $rucOdni = '11111111111';
            }
        }
        $direccion = $personaCliente->address ?? '-';
        // Generar el código QR
        $dataE = [
            'object' => $object,
            'title' => 'NOTA DE CREDITO',
            'linkRevisarFact' => $linkRevisarFact,
            'ruc_dni' => $rucOdni,
            'direccion' => $direccion,
            'tipoElectronica' => 'NOTA DE CREDITO ELECTRÓNICA',
            'typePayment' => $Movimiento->paymentType ?? '-',
            'nroReferencia' => $Movimiento->fullNumber ?? '-',
            'numeroNotaCredito' => $object->fullNumber ?? '',
            'comment' => $object->comment,
            'numeroVenta' => $num,
            'fechaemision' => $object->created_at->format('Y-m-d'),
            'cliente' => $nombreCliente,
            'detalles' => $detalles,
            'vuelto' => '0.00',
            'totalPagado' => $Movimiento->total,
            'totalNota' => $object->totalCreditNote,
            'idMovimiento' => $object->id,

            'motive' => $object?->noteReason?->description ?? '',
            'formaPago' => $Movimiento->formaPago ?? '-',
            'fechaInicio' => $fechaInicio,

        ];

        $pdf = PDF::loadView('creditNote', $dataE);
        $canvas = $pdf->getDomPDF()->get_canvas();
        // $contenidoAncho = $canvas->get_width();
        $contenidoAlto = $canvas->get_height();

        $tipoDocumento = "07";
        $fileName = '20487467139-' . $tipoDocumento . '-' . $object->number . '.pdf'; // Formato del nombre
        $fileName = str_replace(' ', '_', $fileName); // Reemplazar espacios con guiones bajos
        return $pdf->stream($fileName);

    }

    public function guia(Request $request, $idMov = 0)
    {

        $Movimiento = Guide::with(['user', 'details', 'districtStart', 'districtEnd'])->find($idMov);

        if (!$Movimiento) {
            abort(404, 'Guia No Encontrada');
        }
        $this->generarQrGuia();
        $dataE = [
            'title' => 'GUIA',
            'object' => $Movimiento,
        ];
        $tipoDocumento = "";
        // Utiliza el método loadView() directamente en la fachada PDF
        $pdf = PDF::loadView('guia', $dataE);
        $canvas = $pdf->getDomPDF()->get_canvas();
        $num = $Movimiento->fullNumber;
        // $contenidoAncho = $canvas->get_width();
        $contenidoAlto = $canvas->get_height();
        if (strpos($num, 'T') === 0) {
            $tipoDocumento = '00'; // Ticket
        }

        $fileName = '20487467139-' . $tipoDocumento . '-' . $num . '.pdf'; // Formato del nombre
        $fileName = str_replace(' ', '_', $fileName); // Reemplazar espacios con guiones bajos
        return $pdf->stream($fileName);
    }

    /**
     * @OA\Get(
     *     path="/tecnimotors-backend/public/facturaprevia/{id}",
     *     summary="Exportar Factura Previa",
     *     tags={"Reporte"},
     *     description="Genera y descarga una Factura Previa en formato PDF",
     *     @OA\Parameter( name="id", in="path", required=true, description="ID del presupuesto", @OA\Schema(type="integer")),
     *     @OA\Response( response=200, description="Factura Previa en formato PDF", @OA\MediaType(mediaType="application/pdf", @OA\Schema(type="string", format="binary"))),
     *     @OA\Response( response=422, description="Error en los datos", @OA\JsonContent( @OA\Property(property="message", type="string", example="Error en los datos")))
     * )
     */
    public function facturaprevia(Request $request, $id = 0)
    {
        $object = budgetSheet::getBudgetSheet($id);
        if (!$object->attention->concession_id) {
            abort(404, 'Concesion no encontrada');
        }
        //        return response()->json($object);

        $pdf = Pdf::loadView('facturaprevia', [
            'budgetsheet' => $object,
        ]);

        return $pdf->stream('facturaprevia' . $object->id . '.pdf');
        //        return $pdf->download('orden-servicio.pdf');
    }


    public function generarQrGuia()
    {

        $funcion = "actualizarEstadoServidor2";
        $fechaHoy = Carbon::now()->format('Y-m-d');
        $fechaAyer = Carbon::now()->subDay()->format('Y-m-d');
        // Construir la URL con los parámetros
        $url = "https://develop.garzasoft.com:81/tecnimotors-facturador/controlador/contComprobante.php";
        $params = [
            'funcion' => $funcion,
            'fecini' => $fechaAyer,
            'fecfin' => $fechaHoy,
            'estado' => 1,
            'id_empresa' => 1,
        ];
        $url .= '?' . http_build_query($params);

        // Inicializar cURL
        $ch = curl_init();

        // Configurar opciones cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($ch);

        // Verificar si ocurrió algún error
        if (curl_errno($ch)) {
            $error = curl_error($ch);

        } else {

        }

        // Cerrar cURL
        curl_close($ch);

    }
}
