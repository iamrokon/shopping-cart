<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;


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
