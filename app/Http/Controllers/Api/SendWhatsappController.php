<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class SendWhatsappController extends Controller
{
    public function enviarWhatsapp($celular, $objSolicitud)
    {
        if (empty($celular)) {
            echo "ERROR: Debe ingresar un número de celular";
            return false;
        }

        $parametros = http_build_query(array(
            'ruc' => $objSolicitud["doc_cliente"],
            'razon_social' => $objSolicitud["nombre_empresa"],
            'nombre_comercial' => $objSolicitud["nombre_empresa"],
            'cliente' => $objSolicitud["nombre_cliente"],
            'celular' => $celular,
            'serie' => $objSolicitud['serie'],
            'correlativo' => $objSolicitud['correlativo'],
            'tipo' => $objSolicitud["tipo_documento"],
            'empresa' => $objSolicitud['username_solicitud'],
        ));

        $url = 'https://sistema.gesrest.net/api/sendBillByWhatsApp';
        $headers = array('Authorization:}*rA3>#pyM<dITk]]DFP2,/wc)1md_Y/');

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $parametros);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpcode == 200) {
            echo "Enviado correctamente";
            return true;
        } else {
            echo "Error al enviar: $httpcode";
            return false;
        }
    }

}
