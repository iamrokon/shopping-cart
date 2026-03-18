<?php

namespace App\Swagger\Documentation;

use OpenApi\Annotations as OA;

/**
 * Centralized schema definitions for the Shopping Cart API.
 */
class Schemas
{
    /**
     * @OA\Schema(
     *     schema="UserResource",
     *     type="object",
     *     description="Authenticated user resource",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="John Doe"),
     *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *     @OA\Property(property="avatar", type="string", nullable=true, example="https://lh3.googleusercontent.com/a/photo.jpg"),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T10:30:00Z"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-15T10:30:00Z")
     * )
     *
     * @OA\Schema(
     *     schema="ProductResource",
     *     type="object",
     *     description="Product resource",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="Wireless Headphones"),
     *     @OA\Property(property="description", type="string", example="Premium noise-cancelling headphones"),
     *     @OA\Property(property="price", type="number", format="float", example=99.99),
     *     @OA\Property(
     *         property="image",
     *         type="string",
     *         nullable=true,
     *         example="http://localhost/storage/products/headphones.jpg"
     *     ),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T10:30:00Z"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-15T10:30:00Z")
     * )
     *
     * @OA\Schema(
     *     schema="CartResource",
     *     type="object",
     *     description="Cart item resource",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="user_id", type="integer", example=1),
     *     @OA\Property(property="product_id", type="integer", example=5),
     *     @OA\Property(property="quantity", type="integer", example=2),
     *     @OA\Property(
     *         property="product",
     *         ref="#/components/schemas/ProductResource"
     *     ),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T10:30:00Z"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-15T10:30:00Z")
     * )
     *
     * @OA\Schema(
     *     schema="ErrorResponse",
     *     type="object",
     *     description="Standard error response",
     *     @OA\Property(property="success", type="boolean", example=false),
     *     @OA\Property(property="message", type="string", example="An error occurred")
     * )
     *
     * @OA\Schema(
     *     schema="ValidationErrorResponse",
     *     type="object",
     *     description="Validation error response",
     *     @OA\Property(property="success", type="boolean", example=false),
     *     @OA\Property(property="message", type="string", example="The given data was invalid."),
     *     @OA\Property(
     *         property="errors",
     *         type="object",
     *         @OA\AdditionalProperties(
     *             type="array",
     *             @OA\Items(type="string")
     *         ),
     *         example={"field": {"The field is required."}}
     *     )
     * )
     */
    public function __invoke()
    {
    }
}
