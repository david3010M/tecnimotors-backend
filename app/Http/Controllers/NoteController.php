<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexRequestNote;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Utils\Constants;
use Illuminate\Support\Facades\Log;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/note",
     *     tags={"Note"},
     *     summary="Listado de ventas",
     *     description="Listado de ventas",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="number", in="query", description="Número de la nota de crédito", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="from", in="query", description="Fecha de inicio", required=false, @OA\Schema(type="date")),
     *     @OA\Parameter(name="to", in="query", description="Fecha de fin", required=false, @OA\Schema(type="date")),
     *     @OA\Parameter(name="sale$number", in="query", description="Número de la venta", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="sale$person_id", in="query", description="ID de la persona", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="all", in="query", description="Listar todos los registros", required=false, @OA\Schema(type="string", enum={"true", "false"})),
     *     @OA\Parameter(name="page", in="query", description="Número de página", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", description="Cantidad de registros por página", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="direction", in="query", description="Orden de los registros", required=false, @OA\Schema(type="string", enum={"asc", "desc"})),
     *     @OA\Response(response="200", description="Listado de notas de crédito", @OA\JsonContent(ref="#/components/schemas/NoteCollection")),
     *     @OA\Response(response="401", description="No autorizado", @OA\JsonContent(ref="#/components/schemas/Unauthenticated"))
     * )
     */
    public function index(IndexRequestNote $request)
    {
        return $this->getFilteredResults(
            Note::class,
            $request,
            Note::filters,
            Note::sorts,
            NoteResource::class
        );
    }

    /**
     * Store a newly created resource in storage.
     * @OA\Post (
     *     path="/tecnimotors-backend/public/api/note",
     *     tags={"Note"},
     *     summary="Crear nota de crédito",
     *     description="Crear una nueva nota de crédito",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(required=true, description="Datos de la nota de crédito", @OA\JsonContent(ref="#/components/schemas/StoreNoteRequest")),
     *     @OA\Response(response="200", description="Nota de crédito creada", @OA\JsonContent(ref="#/components/schemas/NoteResource")),
     *     @OA\Response(response="401", description="No autorizado", @OA\JsonContent(ref="#/components/schemas/Unauthenticated"))
     * )
     */
    public function store(StoreNoteRequest $request)
    {
        $sale = Sale::find($request->sale_id);
        if ($sale->documentType != Constants::SALE_FACTURA && $sale->documentType != Constants::SALE_BOLETA) {
            return response()->json(["message" => "No se puede crear una nota de crédito para una venta de tipo " . $sale->documentType], 422);
        }
        $documentType = $sale->documentType == Constants::SALE_BOLETA ? Constants::SALE_NOTA_CREDITO_BOLETA : Constants::SALE_NOTA_CREDITO_FACTURA;
        logger($documentType);
        $cashId = 1;
        $query = Note::where('documentType', $request->documentType)
            ->where('cash_id', $cashId);

        $data = [
            'number' => $this->nextCorrelativeQuery($query, 'number'),
            'documentType' => $documentType,
            'date' => $request->input('date'),
            'comment' => $request->input('comment'),
            'company' => 'TECNIMOTORS',
            'discount' => $request->input('discount'),
            'totalCreditNote' => $request->input('totalCreditNote'),
            'totalDocumentReference' => $request->input('totalDocumentReference'),
            'note_reason_id' => $request->input('note_reason_id'),
            'sale_id' => $request->input('sale_id'),
            'status' => Constants::CREDIT_NOTE_STATUS_PENDING,
            'user_id' => auth()->id(),
            'cash_id' => $cashId,
        ];

        $note = Note::create($data);

        foreach ($sale->saleDetails as $saleDetail) {
            SaleDetail::create([
                'description' => $saleDetail['description'],
                'unit' => $saleDetail['unit'],
                'quantity' => $saleDetail['quantity'],
                'unitValue' => $saleDetail['unitValue'],
                'unitPrice' => $saleDetail['unitPrice'],
                'discount' => $saleDetail['discount'] ?? 0,
                'subTotal' => $saleDetail['subTotal'],
                'note_id' => $note->id,
            ]);
        }

        $this->updateFullNumber($note);
        $note = Note::find($note->id);
        $sale->update(['status' => Constants::SALE_STATUS_ANULADO]);
        if ($sale->budget_sheet_id) {
            $sale->budgetSheet->update(['status' => Constants::BUDGET_SHEET_PENDIENTE]);
        }
        $this->declararNotaCredito($note->id);
        return response()->json(NoteResource::make($note));
    }

    /**
     * Display the specified resource.
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/note/{id}",
     *     tags={"Note"},
     *     summary="Mostrar nota de crédito",
     *     description="Muestra una nota de crédito específica",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="ID de la nota de crédito", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Nota de crédito", @OA\JsonContent(ref="#/components/schemas/NoteResource")),
     *     @OA\Response(response="401", description="No autorizado", @OA\JsonContent(ref="#/components/schemas/Unauthenticated"))
     * )
     */
    public function show(int $id)
    {
        $note = Note::find($id);
        if (!$note) {
            return response()->json(["message" => "Nota de crédito no encontrada"], 404);
        }

        return response()->json(NoteResource::make($note));
    }

    /**
     * Update the specified resource in storage.
     * @OA\Put (
     *     path="/tecnimotors-backend/public/api/note/{id}",
     *     tags={"Note"},
     *     summary="Actualizar nota de crédito",
     *     description="Actualizar una nota de crédito existente",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="ID de la nota de crédito", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, description="Datos de la nota de crédito", @OA\JsonContent(ref="#/components/schemas/UpdateNoteRequest")),
     *     @OA\Response(response="200", description="Nota de crédito actualizada", @OA\JsonContent(ref="#/components/schemas/NoteResource")),
     *     @OA\Response(response="401", description="No autorizado", @OA\JsonContent(ref="#/components/schemas/Unauthenticated"))
     * )
     */
    public function update(UpdateNoteRequest $request, int $id)
    {
        // Buscar la nota y verificar que exista
        $note = Note::find($id);
        if (!$note) {
            return response()->json(["message" => "Nota de crédito no encontrada."], 404);
        }
        foreach ($note->sale->commitments as $commitment) {
            if ($commitment->amortizations->count() > 0) {
                logger($commitment->amortizations->count());
                logger($commitment->amortizations->toArray());
                return response()->json(["message" => "No se puede crear una nota de crédito para una venta con amortizaciones"], 422);
            }
        }
        // Verificar que la venta asociada sea válida para una nota de crédito
        $sale = Sale::find($request->sale_id);
        if (!$sale || ($sale->documentType != Constants::SALE_FACTURA && $sale->documentType != Constants::SALE_BOLETA)) {
            return response()->json([
                "message" => "No se puede actualizar una nota de crédito para una venta de tipo " . ($sale->documentType ?? 'desconocido'),
            ], 422);
        }

        $documentType = $sale->documentType == Constants::SALE_BOLETA
            ? Constants::SALE_NOTA_CREDITO_BOLETA
            : Constants::SALE_NOTA_CREDITO_FACTURA;

        // Actualizar los datos de la nota
        $note->update([
            'documentType' => $documentType,
            'date' => $request->input('date'),
            'comment' => $request->input('comment'),
            'discount' => $request->input('discount'),
            'totalCreditNote' => $request->input('totalCreditNote'),
            'totalDocumentReference' => $request->input('totalDocumentReference'),
            'note_reason_id' => $request->input('note_reason_id'),
            'sale_id' => $request->input('sale_id'),
            'status' => Constants::CREDIT_NOTE_STATUS_PENDING, // Puedes ajustar este campo si el estado cambia al actualizar
        ]);

        // Actualizar los detalles de la venta asociados a la nota
        $note->saleDetails()->delete(); // Elimina los detalles existentes
        foreach ($sale->saleDetails as $saleDetail) {
            SaleDetail::create([
                'description' => $saleDetail['description'],
                'unit' => $saleDetail['unit'],
                'quantity' => $saleDetail['quantity'],
                'unitValue' => $saleDetail['unitValue'],
                'unitPrice' => $saleDetail['unitPrice'],
                'discount' => $saleDetail['discount'] ?? 0,
                'subTotal' => $saleDetail['subTotal'],
                'note_id' => $note->id,
            ]);
        }

        $this->updateFullNumber($note);

        // Volver a cargar la nota actualizada con sus relaciones
        $note = Note::with('saleDetails')->find($note->id);
        return response()->json(NoteResource::make($note));
    }

    /**
     * Remove the specified resource from storage.
     * @OA\Delete (
     *     path="/tecnimotors-backend/public/api/note/{id}",
     *     tags={"Note"},
     *     summary="Eliminar nota de crédito",
     *     description="Eliminar una nota de crédito existente",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="ID de la nota de crédito", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Nota de crédito eliminada"),
     *     @OA\Response(response="401", description="No autorizado", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     * )
     */
    public function destroy(int $id)
    {
        $note = Note::find($id);
        if (!$note) {
            return response()->json(["message" => "Nota de crédito no encontrada"], 404);
        }

        $note->delete();
        return response()->json(["message" => "Nota de crédito eliminada"]);
    }

    public function updateFullNumber($note): void
    {
        $documentTypePrefixes = [
            Constants::SALE_NOTA_CREDITO_BOLETA => 'BC',
            Constants::SALE_NOTA_CREDITO_FACTURA => 'FC',
        ];
        $series = substr($note->sale->cash->series, -2);
        $fullNumber = $documentTypePrefixes[$note->documentType] . $series . '-' . $note->number;
        $note->update(['fullNumber' => $fullNumber]);
        $note->save();
    }

    public function declararNotaCredito($idventa)
    {
        $funcion = "enviarNotaCredito";
        $empresa_id = 1;

        $notaCredito = Note::find($idventa);

        if (!$notaCredito) {
            return response()->json(['message' => 'NOTA DE CREDITO NO ENCONTRADA'], 422);
        }
        // if ($notaCredito->status_facturado != 'Pendiente') {
        //     return response()->json(['message' => 'NOTA DE CREDITO NO SE ENCUENTRA EN PENDIENTE DE ENVÍO'], 422);
        // }

        // Construir la URL con los parámetros
        $url = "https://develop.garzasoft.com:81/tecnimotors-facturador/controlador/contComprobante.php";
        $params = [
            'funcion' => $funcion,
            'idventa' => $idventa,
            'empresa_id' => $empresa_id,
        ];
        $url .= '?' . http_build_query($params);

        // Log de inicio de la solicitud
        Log::error("Iniciando solicitud para enviar Nota de Crédito. ID venta: $idventa, Empresa ID: $empresa_id, URL: $url");

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
            // Registrar el error en el log
            Log::error("Error en cURL al enviar Nota de Crédito. ID venta: $idventa, Error: $error");
            // echo 'Error en cURL: ' . $error;
        } else {
            // Registrar la respuesta en el log
            Log::error("Respuesta recibida de Nota de Crédito para ID venta: $idventa, Respuesta: $response");
            // Mostrar la respuesta
            // echo 'Respuesta: ' . $response;
        }

        // Cerrar cURL
        curl_close($ch);

        // Log del cierre de la solicitud
        Log::error("Solicitud de Nota de Crédito finalizada para ID venta: $idventa.");

        // $notaCredito->status_facturado = 'Enviado';
        // $notaCredito->save();

    }
}
