<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest\IndexRequestProduct;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use App\Services\RouteImagesService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    protected $productService;
    protected $routeImagesService;

    public function __construct(
        ProductService $productService,
        RouteImagesService $routeImagesService
    ) {
        $this->productService = $productService;
        $this->routeImagesService = $routeImagesService;
    }
    /**
     * Get all products with simple pagination
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/product",
     *     tags={"Product"},
     *     summary="Get all products",
     *     operationId="index",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Return all products",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product")),
     *             @OA\Property(property="first_page_url", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/products?page=1"),
     *             @OA\Property(property="from", type="integer", example=1),
     *             @OA\Property(property="next_page_url", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/products?page=2"),
     *             @OA\Property(property="path", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/products"),
     *             @OA\Property(property="per_page", type="integer", example=15),
     *             @OA\Property(property="prev_page_url", type="string", example="null"),
     *             @OA\Property(property="to", type="integer", example=15)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *        )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Product::with('category', 'unit', 'brand', 'images')->simplePaginate(15));
    }

    public function list(IndexRequestProduct $request)
    {
        return $this->getFilteredResults(
            Product::class,
            $request,
            Product::filters,
            Product::sorts,
            ProductResource::class
        );
    }

    /**
     * Create a new product
     * @OA\Post(
     *     path="/tecnimotors-backend/public/api/product",
     *     tags={"Product"},
     *     summary="Create a new product",
     *     operationId="store",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/ProductRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Return product created",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The name has already been taken.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *        )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique('products')->whereNull('deleted_at'),
            ],
            'purchase_price' => 'required|numeric',
            'percentage' => 'nullable|numeric',
            'sale_price' => 'required|numeric',
            'quantity' => 'nullable|numeric',
            'type' => 'nullable|string',
            'category_id' => 'required|integer|exists:categories,id',
            'unit_id' => 'required|integer|exists:units,id',
            'brand_id' => 'required|integer|exists:brands,id',
            'images' => 'nullable|array',
            'images.*' => 'file|image|mimes:jpg,jpeg,png,webp|max:5120', // Máx 5 MB por imagen
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name'),
            'purchase_price' => $request->input('purchase_price'),
            'percentage' => $request->input('percentage'),
            'sale_price' => $request->input('sale_price'),
            'quantity' => $request->input('quantity',0),
            'stock' => $request->input('quantity'),
            'type' => $request->input('type','Repuesto'),
            'category_id' => $request->input('category_id'),
            'unit_id' => $request->input('unit_id'),
            'brand_id' => $request->input('brand_id'),
            'images' => $request->file('images')
        ];

        $product = $this->productService->createProduct($data);
        $product = Product::with('category', 'unit', 'brand', 'images')->find($product->id);

        return response()->json($product);
    }

    /**
     * Get a product
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/product/{id}",
     *     tags={"Product"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Return a product",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto No Encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Producto No Encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function show(int $id)
    {

        $product = Product::find($id);
        if ($product) {
            return response()->json($product);
        }
        $product = Product::with('category', 'unit', 'brand', 'images')->find($id);
        return response()->json(['message' => 'Producto No Encontrado'], 404);
    }

    /**
     * Update a product
     * @OA\Put (
     *     path="/tecnimotors-backend/public/api/product/{id}",
     *     tags={"Product"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/ProductRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Return product updated",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto No Encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Producto No Encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The name has already been taken.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function update(Request $request, int $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Producto No Encontrado'], 404);
        }

        $validator = validator()->make($request->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique('products')->ignore($id)->whereNull('deleted_at'),
            ],

            'purchase_price' => 'required|numeric',
            'percentage' => 'nullable|numeric',
            'sale_price' => 'required|numeric',
            'quantity' => 'nullable|numeric',
            'type' => 'nullable|string',

            'category_id' => 'required|integer|exists:categories,id',
            'unit_id' => 'required|integer|exists:units,id',
            'brand_id' => 'required|integer|exists:brands,id',

        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name'),
            'purchase_price' => $request->input('purchase_price'),
            'percentage' => $request->input('percentage'),
            'sale_price' => $request->input('sale_price'),
            'quantity' => $request->input('quantity'),
            'stock' => $request->input('quantity'),
            'type' => $request->input('type'),
            'category_id' => $request->input('category_id'),
            'unit_id' => $request->input('unit_id'),
            'brand_id' => $request->input('brand_id'),
            'images' => $request->file('images')
        ];

        $this->productService->updateProduct($product, $data);
        $product = Product::with('category', 'unit', 'brand', 'images')->find($id);

        return response()->json($product);

    }

    /**
     * Delete a product
     * @OA\Delete (
     *     path="/tecnimotors-backend/public/api/product/{id}",
     *     tags={"Product"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Producto Eliminado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Producto Eliminado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto No Encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Producto No Encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Product has stock",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product has stock")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function destroy(int $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Producto No Encontrado'], 404);
        }

        //        if ($product->stock > 0) {
//            return response()->json(['message' => 'Product has stock'], 422);
//        }

        $product->delete();
        return response()->json(['message' => 'Producto Eliminado']);

    }

    public function destroy_image(int $id)
    {
        $route_image = $this->routeImagesService->getImageById($id);
        if (!$route_image) {
            return response()->json(['message' => 'Imagen del Producto No Encontrado'], 404);
        }
        $route_image->delete();
        return response()->json(['message' => 'Imagen del Producto Eliminado']);

    }
}
