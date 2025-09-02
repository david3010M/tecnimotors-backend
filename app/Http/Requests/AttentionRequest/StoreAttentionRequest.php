<?php

namespace App\Http\Requests\BudgetSheetRequest;

use App\Http\Requests\StoreRequest;
use App\Models\Attention;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StoreAttentionRequest extends StoreRequest
{
      public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'correlativo' => [
                'required',
                'numeric',
                Rule::unique('attentions', 'correlativo')->whereNull('deleted_at'),
            ],

            'arrivalDate'   => ['required', 'date'],
            'deliveryDate'  => ['required', 'date', 'after_or_equal:arrivalDate'],
            'observations'  => ['nullable', 'string'],
            'fuelLevel'     => ['required', 'in:0,2,4,6,8,10'],
            'km'            => ['required', 'numeric'],

            'routeImage'    => ['nullable','array'],
            'routeImage.*'  => ['nullable','file','image','mimes:jpeg,png,jpg,gif','max:5120'],

            'vehicle_id'    => ['required','exists:vehicles,id'],
            'worker_id'     => ['required','exists:workers,id'],
            'concession_id' => ['nullable','exists:concessions,id'],
            'driver'        => ['nullable','string'],

            'typeMaintenance' => [
                'required',
                Rule::in([
                    Attention::MAINTENICE_CORRECTIVE,
                    Attention::MAINTENICE_PREVENTIVE,
                ]),
            ],

            'elements'      => ['nullable','array'],
            'elements.*'    => ['integer','exists:elements,id'],

            // details (servicios) — aquí los requerimos (como tu chequeo original)
            'details'       => ['required','array'],
            'details.*.service_id' => ['required_with:details','integer','exists:services,id'],
            'details.*.worker_id'  => ['required_with:details','integer','exists:workers,id'],
            'details.*.period'     => ['nullable','integer','min:0'],
            'details.*.comment'    => ['nullable','string'],
            'details.*.status'     => ['nullable','string'],

            // detailsProducts: validación personalizada (closure) para mensajes claros
            'detailsProducts' => [
                'nullable',
                'array',
                function ($attribute, $value, $fail) {
                    if (!is_array($value)) {
                        $fail('detailsProducts debe ser un array de detalles de producto.');
                        return;
                    }

                    foreach ($value as $index => $item) {
                        // idProduct requerido
                        if (!isset($item['idProduct']) || $item['idProduct'] === '' || $item['idProduct'] === null) {
                            $fail("El campo 'producto' es obligatorio en detailsProducts[{$index}].");
                            continue;
                        }

                        // verifica existencia del producto
                        if (!Product::where('id', $item['idProduct'])->exists()) {
                            $fail("El producto indicado en detailsProducts[{$index}] no existe.");
                        }

                        // cantidad requerida y válida
                        if (!isset($item['quantity']) || $item['quantity'] === '' || $item['quantity'] === null) {
                            $fail("La cantidad es obligatoria en detailsProducts[{$index}].");
                        } elseif (!is_numeric($item['quantity']) || (float)$item['quantity'] <= 0) {
                            $fail("La cantidad debe ser un número mayor que 0 en detailsProducts[{$index}].");
                        }
                    }
                },
            ],

            // campos opcionales dentro de detailsProducts (si quieres validarlos, la closure puede hacerlo)
            'detailsProducts.*.comment' => ['nullable','string'],
            'detailsProducts.*.status'  => ['nullable','string'],
        ];
    }

    public function messages(): array
    {
        return [
            // correlativo
            'correlativo.required' => 'El correlativo es obligatorio.',
            'correlativo.numeric' => 'El correlativo debe ser numérico.',
            'correlativo.unique' => 'El correlativo ya está en uso.',

            // fechas
            'arrivalDate.required' => 'La fecha de llegada (arrivalDate) es obligatoria.',
            'arrivalDate.date' => 'La fecha de llegada debe ser una fecha válida.',
            'deliveryDate.required' => 'La fecha de entrega (deliveryDate) es obligatoria.',
            'deliveryDate.date' => 'La fecha de entrega debe ser una fecha válida.',
            'deliveryDate.after_or_equal' => 'La fecha de entrega no puede ser anterior a la fecha de llegada.',

            // fuel / km
            'fuelLevel.required' => 'El nivel de combustible (fuelLevel) es obligatorio.',
            'fuelLevel.in' => 'El nivel de combustible no es válido. Valores permitidos: 0,2,4,6,8,10.',
            'km.required' => 'El campo km es obligatorio.',
            'km.numeric' => 'El campo km debe ser numérico.',

            // relaciones
            'vehicle_id.required' => 'El vehículo (vehicle_id) es obligatorio.',
            'vehicle_id.exists' => 'El vehículo proporcionado no existe.',
            'worker_id.required' => 'El trabajador (worker_id) es obligatorio.',
            'worker_id.exists' => 'El trabajador proporcionado no existe.',
            'concession_id.exists' => 'La concesión indicada no existe.',

            // typeMaintenance
            'typeMaintenance.required' => 'El tipo de mantenimiento es obligatorio.',
            'typeMaintenance.in' => 'El tipo de mantenimiento no es válido.',

            // elements
            'elements.array' => 'Elements debe ser un array.',
            'elements.*.integer' => 'Cada elemento en elements debe ser un ID entero.',
            'elements.*.exists' => 'Alguno de los elementos indicados no existe.',

            // details (servicios)
            'details.required' => 'Atención sin Servicios. Debes enviar al menos un detalle de servicio.',
            'details.array' => 'Details debe ser un array.',
            'details.*.service_id.required_with' => 'El campo service_id es obligatorio cuando se envían detalles.',
            'details.*.service_id.exists' => 'El servicio indicado en details no existe.',
            'details.*.worker_id.required_with' => 'El campo worker_id es obligatorio cuando se envían detalles.',
            'details.*.worker_id.exists' => 'El trabajador indicado en details no existe.',
            'details.*.period.integer' => 'El periodo debe ser un número entero.',
            'details.*.period.min' => 'El periodo no puede ser negativo.',

            // detailsProducts: mensajes genéricos (la closure genera mensajes por índice)
            'detailsProducts.array' => 'detailsProducts debe ser un array.',
            'detailsProducts.*.comment' => 'El comentario del detalle de producto debe ser texto.',
            'detailsProducts.*.status' => 'El estado del detalle de producto debe ser texto.',

            // routeImage
            'routeImage.array' => 'routeImage debe ser un array de archivos.',
            'routeImage.*.image' => 'Cada archivo de routeImage debe ser una imagen válida (jpeg,png,jpg,gif).',
            'routeImage.*.max' => 'Cada imagen no puede pesar más de 5MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'correlativo' => 'correlativo',
            'arrivalDate' => 'fecha de llegada',
            'deliveryDate' => 'fecha de entrega',
            'observations' => 'observaciones',
            'typeofDocument' => 'tipo de documento',
            'fuelLevel' => 'nivel de combustible',
            'km' => 'kilometraje',
            'routeImage' => 'imágenes de ruta',
            'routeImage.*' => 'imagen de ruta',
            'vehicle_id' => 'vehículo',
            'worker_id' => 'trabajador',
            'concession_id' => 'concesión',
            'driver' => 'chofer / conductor',
            'typeMaintenance' => 'tipo de mantenimiento',
            'elements' => 'elementos',
            'elements.*' => 'elemento',
            'details' => 'detalles (servicios)',
            'details.*.service_id' => 'servicio',
            'details.*.worker_id' => 'trabajador del detalle',
            'detailsProducts' => 'detalles de productos',
            'detailsProducts.*.idProduct' => 'producto',
            'detailsProducts.*.quantity' => 'cantidad del producto',
        ];
    }
}
