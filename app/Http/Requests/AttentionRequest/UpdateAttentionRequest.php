<?php

namespace App\Http\Requests\AttentionRequest;

use App\Http\Requests\StoreRequest;
use App\Models\Attention;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="UpdateAttentionRequest",

 */
class UpdateAttentionRequest extends StoreRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->route('attention')?->id ?? null;

        return [
            'arrivalDate' => ['required', 'date'],
            'deliveryDate' => ['required', 'date', 'after_or_equal:arrivalDate'],

            'correlativo' => [
                'required',
                'numeric',
                Rule::unique('attentions', 'correlativo')
                    ->ignore($id)
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

            // detalles (servicios)
            'details' => ['nullable', 'array'],
            'details.*.idDetail' => ['nullable', 'integer', 'exists:detail_attentions,id'],
            'details.*.service_id' => ['required_with:details', 'integer', 'exists:services,id'],
            'details.*.worker_id' => ['required_with:details', 'integer', 'exists:workers,id'],
            'details.*.period' => ['nullable', 'integer', 'min:0'],
            'details.*.comment' => ['nullable', 'string'],
            'details.*.status' => ['nullable', 'string'],

            // detalles (productos)
            'detailsProducts' => ['nullable', 'array'],
            'detailsProducts.*.idProduct' => ['required_with:detailsProducts', 'integer', 'exists:products,id'],
            'detailsProducts.*.quantity' => ['required_with:detailsProducts', 'numeric', 'min:0.01'],

            // imágenes (opcional)
            'routeImage' => ['nullable', 'array'],
            'routeImage.*' => ['file', 'image', 'max:5120'], // 5MB por archivo
        ];
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
            'concession_id.exists' => 'La concesion indicada no existe.',

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

            // detalles productos
            'detailsProducts.array' => 'detailsProducts debe ser un array.',
            'detailsProducts.*.idProduct.required_with' => 'El idProduct es obligatorio cuando se envían detalles de productos.',
            'detailsProducts.*.idProduct.exists' => 'El producto indicado no existe.',
            'detailsProducts.*.quantity.required_with' => 'La cantidad es obligatoria para cada detalle de producto.',
            'detailsProducts.*.quantity.numeric' => 'La cantidad del producto debe ser numérica.',
            'detailsProducts.*.quantity.min' => 'La cantidad debe ser mayor a 0.',

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
            'arrivalDate' => 'fecha de llegada',
            'deliveryDate' => 'fecha de entrega',
            'correlativo' => 'correlativo',
            'fuelLevel' => 'nivel de combustible',
            'km' => 'kilometraje',
            'vehicle_id' => 'vehículo',
            'worker_id' => 'trabajador',
            'concession_id' => 'concesión',
            'typeMaintenance' => 'tipo de mantenimiento',
            'elements' => 'elementos',
            'details' => 'detalles (servicios)',
            'detailsProducts' => 'detalles de productos',
            'routeImage' => 'imágenes de ruta',
        ];
    }
}
