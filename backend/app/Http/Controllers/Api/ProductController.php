<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Schema(
 *     schema="ProductResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Wireless Headphones"),
 *     @OA\Property(property="description", type="string", example="Premium noise-cancelling headphones"),
 *     @OA\Property(property="price", type="number", format="float", example=99.99),
 *     @OA\Property(property="image", type="string", nullable=true, example="http://localhost/storage/products/headphones.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Get(
 *     path="/api/products",
 *     summary="Get paginated list of products",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="Number of products per page",
 *         required=false,
 *         @OA\Schema(type="integer", default=12)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Products list",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Success"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ProductResource")),
 *                 @OA\Property(property="current_page", type="integer"),
 *                 @OA\Property(property="last_page", type="integer"),
 *                 @OA\Property(property="total", type="integer")
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/products/{id}",
 *     summary="Get a single product by ID",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product details",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", ref="#/components/schemas/ProductResource")
 *         )
 *     ),
 *     @OA\Response(response=404, description="Product not found")
 * )
 */
class ProductController extends BaseController
{
    use ApiResponse;

    public function __construct(
        private readonly ProductService $productService
    ) {}

    /**
     * Get paginated list of products.
     */
    public function index(): JsonResponse
    {
        $perPage  = (int) request('per_page', 12);
        $products = $this->productService->getAllProducts($perPage);

        return $this->successResponse(
            ProductResource::collection($products)->response()->getData(true)
        );
    }

    /**
     * Get a single product by ID.
     */
    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getProductById($id);

        if (!$product) {
            return $this->notFoundResponse('Product not found.');
        }

        return $this->successResponse(new ProductResource($product));
    }
}
