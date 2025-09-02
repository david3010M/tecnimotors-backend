<?php

namespace App\Http\Requests\AttentionRequest;

use App\Http\Requests\UpdateRequest;
use App\Models\Attention;
use App\Models\Product;
use Illuminate\Validation\Rule;

class UpdateAttentionRequest extends UpdateRequest
{
    protected ?int $routeId = null;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $id = $this->route('id')
            ?? $this->route('attention')?->id
            ?? $this->route('attention_id')
            ?? $this->attention?->id
            ?? null;

        $this->routeId = $id !== null ? (int) $id : null;
    }

    public function rules(): array
    {
        $id = $this->routeId;

        return [
            'arrivalDate' => ['required', 'date'],
            'deliveryDate' => ['required', 'date', 'after_or_equal:arrivalDate'],

            'correlativo' => [
                'required',
                'numeric',
                Rule::unique('attentions', 'correlativo')
                    ->ignore($id ?? 0, 'id')
                    ->whereNull('deleted_at'),
            ],

            'observations' => ['nullable', 'string'],
            'fuelLevel' => ['required', 'numeric'],
            'km' => ['required', 'numeric'],

            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'worker_id' => ['required', 'exists:workers,id'],
            'concession_id' => ['nullable', 'exists:concessions,id'],
            'driver' => ['nullable', 'string'],

            'typeMaintenance' => [
                'nullable',
                Rule::in([
                    Attention::MAINTENICE_CORRECTIVE,
                    Attention::MAINTENICE_PREVENTIVE,
                ]),
            ],

            'elements' => ['nullable', 'array'],
            'elements.*' => ['integer', 'exists:elements,id'],

            'details' => ['nullable', 'array'],
            'details.*.idDetail' => ['nullable', 'integer', 'exists:detail_attentions,id'],
            'details.*.service_id' => ['required_with:details', 'integer', 'exists:services,id'],
            'details.*.worker_id' => ['required_with:details', 'integer', 'exists:workers,id'],
            'details.*.period' => ['nullable', 'integer', 'min:0'],
            'details.*.comment' => ['nullable', 'string'],
            'details.*.status' => ['nullable', 'string'],

            // Validación personalizada para detalles de productos:
            // recorremos el array y validamos idProduct y quantity por cada item
            'detailsProducts' => [
                'nullable',
                'array',
                function ($attribute, $value, $fail) {
                    // $value debe ser un array de items
                    if (!is_array($value)) {
                        $fail('detailsProducts debe ser un array de detalles de producto.');
                        return;
                    }

                    foreach ($value as $index => $item) {
                        // idProduct obligatorio
                        if (!isset($item['idProduct']) || $item['idProduct'] === '' || $item['idProduct'] === null) {
                            $fail("El campo 'producto' es obligatorio en detailsProducts[{$index}].");
                        } else {
                            // verificar que el producto exista (si tienes muchos, podrías optimizar)
                            $exists = Product::where('id', $item['idProduct'])->exists();
                            if (!$exists) {
                                $fail("El producto indicado en detailsProducts[{$index}] no existe.");
                            }
                        }

                        // quantity obligatorio y numérico > 0
                        if (!isset($item['quantity']) || $item['quantity'] === '' || $item['quantity'] === null) {
                            $fail("La cantidad es obligatoria en detailsProducts[{$index}].");
                        } elseif (!is_numeric($item['quantity']) || (float)$item['quantity'] <= 0) {
                            $fail("La cantidad debe ser un número mayor que 0 en detailsProducts[{$index}].");
                        }
                    }
                },
            ],

            // campos opcionales dentro de cada detalle (si quieres validarlos, la closure puede hacerlo)
            // 'detailsProducts.*.comment' y 'detailsProducts.*.status' se permiten y no requieren reglas separadas

            'routeImage' => ['nullable', 'array'],
            'routeImage.*' => ['file', 'image', 'max:5120'],
        ];
    }

    public function withValidator($validator)
    {
        // Comprobación manual del correlativo para evitar falsos positivos
        $validator->after(function ($validator) {
            $correlativo = $this->input('correlativo');
            if ($correlativo === null) {
                return;
            }

            $query = Attention::where('correlativo', $correlativo)
                ->whereNull('deleted_at');

            if ($this->routeId) {
                $query->where('id', '<>', $this->routeId);
            }

            if ($query->exists()) {
                $validator->errors()->add('correlativo', 'El correlativo ya está en uso.');
            }
        });
    }

    public function messages(): array
    {
        return [
            // fechas y generales
            'arrivalDate.required' => 'La fecha de llegada (arrivalDate) es obligatoria.',
            'arrivalDate.date' => 'La fecha de llegada debe tener un formato de fecha válido.',
            'deliveryDate.required' => 'La fecha de entrega (deliveryDate) es obligatoria.',
            'deliveryDate.date' => 'La fecha de entrega debe ser una fecha válida.',
            'deliveryDate.after_or_equal' => 'La fecha de entrega no puede ser anterior a la fecha de llegada.',

            // correlativo
            'correlativo.required' => 'El correlativo es obligatorio.',
            'correlativo.numeric' => 'El correlativo debe ser numérico.',
            'correlativo.unique' => 'El correlativo ya está en uso.',

            // campos numéricos
            'fuelLevel.required' => 'El nivel de combustible (fuelLevel) es obligatorio.',
            'fuelLevel.numeric' => 'El nivel de combustible debe ser numérico.',
            'km.required' => 'El campo km es obligatorio.',
            'km.numeric' => 'El campo km debe ser numérico.',

            // relaciones
            'vehicle_id.required' => 'El vehículo (vehicle_id) es obligatorio.',
            'vehicle_id.exists' => 'El vehículo proporcionado no existe.',
            'worker_id.required' => 'El trabajador (worker_id) es obligatorio.',
            'worker_id.exists' => 'El trabajador proporcionado no existe.',
            'concession_id.exists' => 'La concesión indicada no existe.',

            // typeMaintenance
            'typeMaintenance.in' => 'El tipo de mantenimiento no es válido.',

            // elements
            'elements.array' => 'Elements debe ser un array.',
            'elements.*.integer' => 'Cada elemento en elements debe ser un ID entero.',
            'elements.*.exists' => 'Alguno de los elementos indicados no existe.',

            // detalles (servicios)
            'details.array' => 'Details debe ser un array.',
            'details.*.idDetail.integer' => 'El idDetail debe ser un entero.',
            'details.*.idDetail.exists' => 'El detalle (idDetail) no existe.',
            'details.*.service_id.required_with' => 'El campo service_id es obligatorio cuando se envían detalles.',
            'details.*.service_id.exists' => 'El servicio indicado en details no existe.',
            'details.*.worker_id.required_with' => 'El campo worker_id es obligatorio cuando se envían detalles.',
            'details.*.worker_id.exists' => 'El worker_id indicado en details no existe.',
            'details.*.period.integer' => 'El periodo debe ser un número entero.',
            'details.*.period.min' => 'El periodo no puede ser negativo.',

            // imágenes
            'routeImage.array' => 'routeImage debe ser un array de archivos.',
            'routeImage.*.file' => 'Cada routeImage debe ser un archivo válido.',
            'routeImage.*.image' => 'Cada archivo de routeImage debe ser una imagen.',
            'routeImage.*.max' => 'Cada imagen no puede pesar más de 5MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            // Cabecera
            'arrivalDate' => 'fecha de llegada',
            'deliveryDate' => 'fecha de entrega',
            'correlativo' => 'correlativo',
            'observations' => 'observaciones',
            'typeofDocument' => 'tipo de documento',
            'fuelLevel' => 'nivel de combustible',
            'km' => 'kilometraje',
            'driver' => 'chofer / conductor',

            // Relaciones
            'vehicle_id' => 'vehículo',
            'worker_id' => 'trabajador',
            'concession_id' => 'concesión',
            'typeMaintenance' => 'tipo de mantenimiento',

            // Elements
            'elements' => 'elementos',
            'elements.*' => 'elemento',

            // Details (servicios)
            'details' => 'detalles (servicios)',
            'details.*' => 'detalle (servicio)',
            'details.*.idDetail' => 'ID del detalle',
            'details.*.service_id' => 'servicio',
            'details.*.worker_id' => 'trabajador del detalle',
            'details.*.period' => 'periodo del detalle',
            'details.*.comment' => 'comentario del detalle',
            'details.*.status' => 'estado del detalle',

            // DetailsProducts (productos)
            'detailsProducts' => 'detalles de productos',
            'detailsProducts.*' => 'detalle (producto)',
            'detailsProducts.*.idProduct' => 'producto',
            'detailsProducts.*.quantity' => 'cantidad del producto',
            'detailsProducts.*.comment' => 'comentario del detalle de producto',
            'detailsProducts.*.status' => 'estado del detalle de producto',

            // Imágenes
            'routeImage' => 'imágenes de ruta',
            'routeImage.*' => 'imagen de ruta',
        ];
    }
}
