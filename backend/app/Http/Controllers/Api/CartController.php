<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\BatchSyncCartRequest;
use App\Http\Requests\Cart\UpdateCartRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Services\CartService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="UserResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", example="john@example.com"),
 *     @OA\Property(property="avatar", type="string", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="CartResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="product_id", type="integer", example=5),
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="product", ref="#/components/schemas/ProductResource"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Get(
 *     path="/api/cart",
 *     summary="Get all cart items for authenticated user",
 *     tags={"Cart"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Cart items",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/CartResource"))
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 *
 * @OA\Post(
 *     path="/api/cart",
 *     summary="Add a product to the cart",
 *     tags={"Cart"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"product_id","quantity"},
 *             @OA\Property(property="product_id", type="integer", example=3),
 *             @OA\Property(property="quantity", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Item added to cart",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", ref="#/components/schemas/CartResource")
 *         )
 *     ),
 *     @OA\Response(response=422, description="Validation error")
 * )
 *
 * @OA\Patch(
 *     path="/api/cart/{id}",
 *     summary="Update quantity of a cart item",
 *     tags={"Cart"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"quantity"},
 *             @OA\Property(property="quantity", type="integer", example=3)
 *         )
 *     ),
 *     @OA\Response(response=200, description="Cart item updated"),
 *     @OA\Response(response=403, description="Forbidden"),
 *     @OA\Response(response=404, description="Cart item not found")
 * )
 *
 * @OA\Post(
 *     path="/api/cart/{id}/increment",
 *     summary="Increment cart item quantity by 1",
 *     tags={"Cart"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Quantity incremented"),
 *     @OA\Response(response=403, description="Forbidden"),
 *     @OA\Response(response=404, description="Cart item not found")
 * )
 *
 * @OA\Post(
 *     path="/api/cart/{id}/decrement",
 *     summary="Decrement cart item quantity by 1 (removes if reaches 0)",
 *     tags={"Cart"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Quantity decremented"),
 *     @OA\Response(response=403, description="Forbidden"),
 *     @OA\Response(response=404, description="Cart item not found")
 * )
 *
 * @OA\Delete(
 *     path="/api/cart/{id}",
 *     summary="Remove a product from the cart",
 *     tags={"Cart"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Item removed"),
 *     @OA\Response(response=403, description="Forbidden"),
 *     @OA\Response(response=404, description="Cart item not found")
 * )
 *
 * @OA\Post(
 *     path="/api/cart/batch-sync",
 *     summary="Batch sync cart items (debounced update from frontend)",
 *     description="Accepts an array of cart items to sync with backend. Items with quantity 0 will be removed.",
 *     tags={"Cart"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"items"},
 *             @OA\Property(
 *                 property="items",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="product_id", type="integer", example=3),
 *                     @OA\Property(property="quantity", type="integer", example=2)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Cart synced successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/CartResource"))
 *         )
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/cart",
 *     summary="Clear all items from the cart",
 *     tags={"Cart"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(response=200, description="Cart cleared"),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 */
class CartController extends BaseController
{
    use ApiResponse;

    public function __construct(
        private readonly CartService $cartService
    ) {}

    /**
     * Get all cart items for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $items = $this->cartService->getCartItems($request->user());

        return $this->successResponse(CartResource::collection($items));
    }

    /**
     * Add a product to the cart.
     */
    public function store(AddToCartRequest $request): JsonResponse
    {
        $cartItem = $this->cartService->addItem(
            $request->user(),
            $request->product_id,
            $request->quantity
        );

        return $this->successResponse(new CartResource($cartItem), 'Item added to cart.', 201);
    }

    /**
     * Update the quantity of a cart item.
     */
    public function update(UpdateCartRequest $request, int $id): JsonResponse
    {
        $cartItem = Cart::find($id);

        if (!$cartItem) {
            return $this->notFoundResponse('Cart item not found.');
        }

        if ($cartItem->user_id !== $request->user()->id) {
            return $this->errorResponse('Forbidden.', 403);
        }

        $updated = $this->cartService->updateItem($cartItem, $request->quantity);

        return $this->successResponse(new CartResource($updated), 'Cart item updated.');
    }

    /**
     * Increment a cart item quantity by 1.
     */
    public function increment(Request $request, int $id): JsonResponse
    {
        $cartItem = Cart::find($id);

        if (!$cartItem) {
            return $this->notFoundResponse('Cart item not found.');
        }

        if ($cartItem->user_id !== $request->user()->id) {
            return $this->errorResponse('Forbidden.', 403);
        }

        $updated = $this->cartService->incrementItem($cartItem);

        return $this->successResponse(new CartResource($updated), 'Quantity incremented.');
    }

    /**
     * Decrement a cart item quantity by 1.
     */
    public function decrement(Request $request, int $id): JsonResponse
    {
        $cartItem = Cart::find($id);

        if (!$cartItem) {
            return $this->notFoundResponse('Cart item not found.');
        }

        if ($cartItem->user_id !== $request->user()->id) {
            return $this->errorResponse('Forbidden.', 403);
        }

        $updated = $this->cartService->decrementItem($cartItem);

        if ($updated === null) {
            return $this->successResponse(null, 'Item removed because quantity reached zero.');
        }

        return $this->successResponse(new CartResource($updated), 'Quantity decremented.');
    }

    /**
     * Remove a specific cart item.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $cartItem = Cart::find($id);

        if (!$cartItem) {
            return $this->notFoundResponse('Cart item not found.');
        }

        if ($cartItem->user_id !== $request->user()->id) {
            return $this->errorResponse('Forbidden.', 403);
        }

        $this->cartService->removeItem($cartItem);

        return $this->successResponse(null, 'Item removed from cart.');
    }

    /**
     * Batch sync cart items from the frontend (debounced).
     */
    public function batchSync(BatchSyncCartRequest $request): JsonResponse
    {
        $items = $this->cartService->batchSync($request->user(), $request->items);

        return $this->successResponse(CartResource::collection($items), 'Cart synced successfully.');
    }

    /**
     * Clear all cart items for the authenticated user.
     */
    public function clear(Request $request): JsonResponse
    {
        $this->cartService->clearCart($request->user());

        return $this->successResponse(null, 'Cart cleared.');
    }
}
