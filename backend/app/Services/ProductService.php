<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    /**
     * Get paginated list of products.
     */
    public function getAllProducts(int $perPage = 12): LengthAwarePaginator
    {
        return Product::latest()->paginate($perPage);
    }

    /**
     * Get a single product by ID.
     */
    public function getProductById(int $id): ?Product
    {
        return Product::find($id);
    }
}
