<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

/**
 * @OA\Info(
 *     title="Shopping Cart API",
 *     version="1.0.0",
 *     description="Full Stack Shopping Cart API with Firebase Authentication and Google Sign-in",
 *     @OA\Contact(
 *         email="admin@shopping-cart.com"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter your Sanctum Bearer token"
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="Firebase Google Sign-in authentication endpoints"
 * )
 *
 * @OA\Tag(
 *     name="Products",
 *     description="Product listing endpoints (public)"
 * )
 *
 * @OA\Tag(
 *     name="Cart",
 *     description="Cart management endpoints (authentication required)"
 * )
 */
abstract class BaseController extends Controller
{
    //
}
