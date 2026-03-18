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
