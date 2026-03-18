<?php

namespace App\Swagger\Documentation;

use OpenApi\Annotations as OA;

/**
 * Product-related API documentation.
 */
class ProductDocs
{
    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get paginated list of products",
     *     description="Returns a paginated list of all available products. This endpoint is public.",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of products per page (default: 12)",
     *         required=false,
     *         @OA\Schema(type="integer", default=12, minimum=1, maximum=100, example=12)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated products list",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/ProductResource")
     *                 ),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="per_page", type="integer", example=12),
     *                 @OA\Property(property="total", type="integer", example=58)
     *             )
     *         )
     *     )
     * )
     *
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Get a single product by ID",
     *     description="Returns the details of a specific product. This endpoint is public.",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product details",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="data", ref="#/components/schemas/ProductResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function __invoke()
    {
    }
}
