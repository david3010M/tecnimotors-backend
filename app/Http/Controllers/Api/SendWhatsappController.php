<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attention;
use App\Models\budgetSheet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWhatsappController extends Controller
{
    // public function enviarWhatsapp($celular, $objSolicitud)
    // {
    //     if (empty($celular)) {
    //         echo "ERROR: Debe ingresar un número de celular";
    //         return false;
    //     }

    //     $parametros = http_build_query(array(
    //         'ruc' => $objSolicitud["doc_cliente"],
    //         'razon_social' => $objSolicitud["nombre_empresa"],
    //         'nombre_comercial' => $objSolicitud["nombre_empresa"],
    //         'cliente' => $objSolicitud["nombre_cliente"],
    //         'celular' => $celular,
    //         'serie' => $objSolicitud['serie'],
    //         'correlativo' => $objSolicitud['correlativo'],
    //         'tipo' => $objSolicitud["tipo_documento"],
    //         'empresa' => $objSolicitud['username_solicitud'],
    //     ));

    //     $url = 'https://sistema.gesrest.net/api/sendBillByWhatsApp';
    //     $headers = 

    //     $curl = curl_init($url);
    //     curl_setopt($curl, CURLOPT_POST, true);
    //     curl_setopt($curl, CURLOPT_POSTFIELDS, $parametros);
    //     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    //     $response = curl_exec($curl);
    //     $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    //     curl_close($curl);

    //     if ($httpcode == 200) {
    //         echo "Enviado correctamente";
    //         return true;
    //     } else {
    //         echo "Error al enviar: $httpcode";
    //         return false;
    //     }
    // }

    //post.
    //     Body:
    // {
    //     "idAttention": "1",
    //      "phone_number": "123456789"
    // }

    /**
     * Send sheet service information via WhatsApp
     * @OA\Post(
     *     path="/tecnimotors-backend/public/api/sendSheetByWhatsapp",
     *     tags={"WhatsApp"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Data for sending sheet service information via WhatsApp",
     *         @OA\JsonContent(
     *             required={"idAttention", "phone_number"},
     *             @OA\Property(property="idAttention", type="integer", example=1, description="The ID of the attention."),
     *             @OA\Property(property="phone_number", type="string", description="The phone number to send the message to.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Message sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="WhatsApp message sent successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Validation failed: The idAttention field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal server error occurred.")
     *         )
     *     )
     * )
     */

    public function sendSheetServiceByWhatsapp(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'idAttention' => 'required|exists:attentions,id',
            'phone_number' => 'required|digits:9',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }
        $attention = Attention::with(['vehicle.person'])->find($request->input('idAttention'));
        $client = $attention->vehicle->person;

        try {
            $url = 'https://sistema.gesrest.net/api/send-document-by-whatsapp';

            $response = Http::withHeaders([
                'Authorization' => config('services.api_key_message_whatsapp'),
            ])->post($url, [
                        "nombre_plantilla" => "tecnimotors",
                        "ruc" => "20487467139",
                        "razon_social" => "TECNI MOTORS DEL PERU E.I.R.L.",
                        "nombre_comercial" => "TECNI MOTORS DEL PERU E.I.R.L.",
                        "cliente" => $client->typeofDocument == 'DNI' ? $client->names . ' ' . $client->fatherSurname : $client->businessName,
                        "celular" => $request->input('phone_number'),
                        "tipo_documento" => "ORDEN DE SERVICIO " . $attention->number,
                        "tipo_documento_url" => "ordenservicio",
                        "documento_id_url" => $attention->id,
                    ]);
            return response()->json(['message' => 'El mensaje de WhatsApp se ha enviado correctamente'], 200);
        } catch (Exception $e) {

            Log::error('Error to send Sheet Service by Whatsapp, ' . 'Id Attention: ' . $request->input('idAttention') . '=> ' . $e->getMessage());
            return response()->json(['error' => 'Hubo un error al enviar el mensaje de WhatsApp'], 500);
        }

    }

    /**
     * Send budget sheet information via WhatsApp
     * @OA\Post(
     *     path="/tecnimotors-backend/public/api/sendBudgetSheetByWhatsapp",
     *     tags={"WhatsApp"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Data for sending budget sheet information via WhatsApp",
     *         @OA\JsonContent(
     *             required={"idAttention", "phone_number"},
     *             @OA\Property(property="idBudgetSheet", type="integer", example=1, description="The ID of the budget sheet."),
     *             @OA\Property(property="phone_number", type="string", description="The phone number to send the message to.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Message sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="WhatsApp message sent successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Validation failed: The idAttention field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal server error occurred.")
     *         )
     *     )
     * )
     */

    public function sendBudgetSheetByWhatsapp(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'idBudgetSheet' => 'required|exists:budget_sheets,id',
            'phone_number' => 'required|digits:9',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }
        $budgetSheet = budgetSheet::getBudgetSheet($request->input('idBudgetSheet'));
        $client = $budgetSheet->attention->vehicle->person;

        try {
            $url = 'https://sistema.gesrest.net/api/send-document-by-whatsapp';

            $response = Http::withHeaders([
                'Authorization' => config('services.api_key_message_whatsapp'),
            ])->post($url, [
                        "nombre_plantilla" => "tecnimotors",
                        "ruc" => "20487467139",
                        "razon_social" => "TECNI MOTORS DEL PERU E.I.R.L.",
                        "nombre_comercial" => "TECNI MOTORS DEL PERU E.I.R.L.",
                        "cliente" => $client->typeofDocument == 'DNI' ? $client->names . ' ' . $client->fatherSurname : $client->businessName,
                        "celular" => $request->input('phone_number'),
                        "tipo_documento" => "PRESUPUESTO DE ORDEN DE SERVICIO " . $budgetSheet->number,
                        "tipo_documento_url" => "presupuesto",
                        "documento_id_url" => $budgetSheet->id,
                    ]);

            return response()->json(['message' => 'El mensaje de WhatsApp se ha enviado correctamente'], 200);

        } catch (Exception $e) {

            Log::error('Error to send Sheet Budget by Whatsapp, ' . 'Id Budget: ' . $request->input('idBudgetSheet') . '=> ' . $e->getMessage());
            return response()->json(['error' => 'Hubo un error al enviar el mensaje de WhatsApp'], 500);
        }

    }


    /**
     * Send sheet service information via WhatsApp
     * @OA\Post(
     *     path="/tecnimotors-backend/public/api/sendEvidenceByWhatsapp",
     *     tags={"WhatsApp"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Evidences by attention via WhatsApp",
     *         @OA\JsonContent(
     *             required={"idAttention", "phone_number"},
     *             @OA\Property(property="idAttention", type="integer", example=1, description="The ID of the attention."),
     *             @OA\Property(property="phone_number", type="string", description="The phone number to send the message to.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Message sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="WhatsApp message sent successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Validation failed: The idAttention field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal server error occurred.")
     *         )
     *     )
     * )
     */
    public function sendEvidenceByWhatsapp(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'idAttention' => 'required|exists:attentions,id',
            'phone_number' => 'required|digits:9',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }
        $attention = Attention::with(['vehicle.person'])->find($request->input('idAttention'));
        $client = $attention->vehicle->person;

        try {
            $url = 'https://sistema.gesrest.net/api/send-document-by-whatsapp';

            $response = Http::withHeaders([
                'Authorization' => config('services.api_key_message_whatsapp'),
            ])->post($url, [
                        "nombre_plantilla" => "tecnimotors",
                        "ruc" => "20487467139",
                        "razon_social" => "TECNI MOTORS DEL PERU E.I.R.L.",
                        "nombre_comercial" => "TECNI MOTORS DEL PERU E.I.R.L.",
                        "cliente" => $client->typeofDocument == 'DNI' ? $client->names . ' ' . $client->fatherSurname : $client->businessName,
                        "celular" => $request->input('phone_number'),
                        "tipo_documento" => "EVIDENCIAS DE LA ORDEN DE SERVICIO " . $attention->number,
                        "tipo_documento_url" => "evidencias",
                        "documento_id_url" => $attention->id,
                    ]);
            return response()->json(['message' => 'El mensaje de WhatsApp se ha enviado correctamente'], 200);
        } catch (Exception $e) {

            Log::error('Error to send Sheet Service by Whatsapp, ' . 'Id Attention: ' . $request->input('idAttention') . '=> ' . $e->getMessage());
            return response()->json(['error' => 'Hubo un error al enviar el mensaje de WhatsApp'], 500);
        }


    }

}
