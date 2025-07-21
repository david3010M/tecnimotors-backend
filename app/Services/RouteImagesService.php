<?php
namespace App\Services;

use App\Models\Image;
use App\Models\RouteImages;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RouteImagesService
{
    public function getImageById(int $id): ?RouteImages
    {
        return RouteImages::find($id);
    }

    public function createImage(array $images, $attention_id = null, $product_id = null, $task_id = null, $concession_id = null): array
    {
        $savedRouteImages = [];

        foreach ($images as $index => $file) {
            $now = now();
            $name = str_replace(' ', '_', $file->getClientOriginalName());
            $filename = ($index + 1) . '-' . $now->format('YmdHis') . '_' . $name;

            $file->storeAs('public/route_images', $filename);
            $url = asset('storage/route_images/' . $filename);

            $data = array_filter([
                'route' => $url,
                'attention_id' => $attention_id,
                'product_id' => $product_id,
                'task_id' => $task_id,
                'concession_id' => $concession_id,
                'created_at' => $now,
            ], fn($value) => !is_null($value));

            $savedRouteImages[] = RouteImages::create($data);
        }

        return $savedRouteImages;
    }



    public function updateImage(
        array $images,
        array $meta = [],
        array $imageIds = []
    ): array {
        $savedRouteImages = [];

        // Filtrar imágenes existentes asociadas al contexto (product_id, etc.)
        $query = RouteImages::query();
        foreach (['attention_id', 'product_id', 'task_id', 'concession_id'] as $key) {
            if (!empty($meta[$key])) {
                $query->where($key, $meta[$key]);
            }
        }
        $existingImages = $query->get();

        // Eliminar imágenes que ya no están en imageIds
        $existingImages->whereNotIn('id', $imageIds)->each(function ($img) {
            $this->deletePhysicalFile($img->route);
            $img->delete();
        });

        // Guardar o actualizar imágenes
        foreach ($images as $index => $file) {
            $now = now();
            $filename = ($index + 1) . '-' . $now->format('YmdHis') . '_' . str_replace(' ', '_', $file->getClientOriginalName());

            $file->storeAs('public/route_images', $filename);
            $url = asset("storage/route_images/$filename");

            $data = array_merge($meta, [
                'route' => $url,
                'updated_at' => $now,
            ]);

            // Si se proporciona un ID válido en la posición actual, actualiza
            if (!empty($imageIds[$index]) && $imageModel = RouteImages::find($imageIds[$index])) {
                $this->deletePhysicalFile($imageModel->route);
                $imageModel->update($data);
                $savedRouteImages[] = $imageModel;
            } else {
                $data['created_at'] = $now;
                $savedRouteImages[] = RouteImages::create($data);
            }
        }

        return $savedRouteImages;
    }

    protected function deletePhysicalFile(string $url): void
    {
        $path = str_replace(asset('storage') . '/', '', $url); // convierte URL pública a ruta relativa
        Storage::disk('public')->delete($path);
    }



    public function destroyById($id)
    {
        $Image = RouteImages::find($id);
        if (!$Image) {
            return false;
        }
        return $Image->delete(); // Devuelve true si la eliminación fue exitosa
    }

}
