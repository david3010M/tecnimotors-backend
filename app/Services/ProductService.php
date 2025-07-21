<?php
namespace App\Services;

use App\Models\Product;

class ProductService
{

    protected $routeImagesService;

    public function __construct(RouteImagesService $routeImagesService)
    {
        $this->routeImagesService = $routeImagesService;
    }

    public function getProductById(int $id): ?Product
    {
        return Product::find($id);
    }

    public function createProduct(array $data): Product
    {
        $images = $data['images'] ?? [];
        unset($data['images']); // Elimina 'images' del array para evitar error en create()

        $product = Product::create($data);

        if (!empty($images)) {
            $this->routeImagesService->createImage($images, null, $product->id);
        }

        return $product;
    }
public function updateProduct($product, array $data)
{
    $images = $data['images'] ?? [];
    unset($data['images']);

    // Solo actualiza los campos válidos del producto
    $filteredData = array_intersect_key($data, $product->getAttributes());
    $product->update($filteredData);

    if (!empty($images)) {
        $imageFiles = [];
        $imageIds = [];

        foreach ($images as $image) {
            if (is_array($image) && isset($image['id'])) {
                $imageIds[] = $image['id'];
            }

            // puede venir directamente como archivo, o dentro del array con clave 'file'
            $imageFiles[] = is_array($image) && isset($image['file']) ? $image['file'] : $image;
        }

        $this->routeImagesService->updateImage($imageFiles, ['product_id' => $product->id], $imageIds);
    }

    return $product;
}


    public function destroyById($id)
    {
        $Product = Product::find($id);

        if (!$Product) {
            return false;
        }
        return $Product->delete(); // Devuelve true si la eliminación fue exitosa
    }

}
