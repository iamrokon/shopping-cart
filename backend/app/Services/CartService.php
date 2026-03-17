<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class CartService
{
    /**
     * Get all cart items for a user (with product relationship).
     */
    public function getCartItems(User $user): Collection
    {
        return $user->cartItems()->with('product')->get();
    }

    /**
     * Add an item to the cart or update quantity if it already exists.
     */
    public function addItem(User $user, int $productId, int $quantity): Cart
    {
        $cartItem = Cart::firstOrNew([
            'user_id'    => $user->id,
            'product_id' => $productId,
        ]);

        $cartItem->quantity = $cartItem->exists
            ? $cartItem->quantity + $quantity
            : $quantity;

        $cartItem->save();

        return $cartItem->load('product');
    }

    /**
     * Update the quantity of a specific cart item.
     */
    public function updateItem(Cart $cartItem, int $quantity): Cart
    {
        $cartItem->update(['quantity' => $quantity]);

        return $cartItem->load('product');
    }

    /**
     * Increment quantity of a cart item by 1.
     */
    public function incrementItem(Cart $cartItem): Cart
    {
        $cartItem->increment('quantity');

        return $cartItem->load('product');
    }

    /**
     * Decrement quantity of a cart item by 1. Removes if it reaches 0.
     */
    public function decrementItem(Cart $cartItem): ?Cart
    {
        if ($cartItem->quantity <= 1) {
            $cartItem->delete();
            return null;
        }

        $cartItem->decrement('quantity');

        return $cartItem->load('product');
    }

    /**
     * Remove a specific cart item.
     */
    public function removeItem(Cart $cartItem): void
    {
        $cartItem->delete();
    }

    /**
     * Clear all cart items for a user.
     */
    public function clearCart(User $user): void
    {
        $user->cartItems()->delete();
    }

    /**
     * Batch sync cart items from frontend debounced updates.
     * Items with quantity 0 are removed.
     *
     * @param  User  $user
     * @param  array $items  [['product_id' => int, 'quantity' => int], ...]
     * @return Collection
     */
    public function batchSync(User $user, array $items): Collection
    {
        foreach ($items as $item) {
            $productId = $item['product_id'];
            $quantity  = (int) $item['quantity'];

            if ($quantity <= 0) {
                // Remove item if quantity is 0
                Cart::where('user_id', $user->id)
                    ->where('product_id', $productId)
                    ->delete();
            } else {
                Cart::updateOrCreate(
                    ['user_id' => $user->id, 'product_id' => $productId],
                    ['quantity' => $quantity]
                );
            }
        }

        return $this->getCartItems($user);
    }
}
