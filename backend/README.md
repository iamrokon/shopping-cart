# Shopping Cart API — Backend Documentation

A full-stack shopping cart REST API built with **Laravel 12**, using Firebase Authentication for Google Sign-in.

---

## Tech Stack

| Technology | Purpose |
|---|---|
| Laravel 12 | Backend framework |
| Laravel Sanctum | API token authentication |
| MySQL | Database |
| Firebase Auth | Google Sign-in verification |
| L5-Swagger (darkaonline) | OpenAPI 3.0 documentation |

---

## Architecture Overview

```
app/
├── Http/
│   ├── Controllers/Api/
│   │   ├── BaseController.php      # Swagger @OA\Info and tags
│   │   ├── AuthController.php      # POST /api/auth/login|logout, GET /api/auth/me
│   │   ├── ProductController.php   # GET /api/products, /api/products/{id}
│   │   └── CartController.php      # Cart CRUD + increment/decrement + batch-sync
│   ├── Requests/
│   │   ├── Auth/FirebaseAuthRequest.php
│   │   ├── Cart/AddToCartRequest.php
│   │   ├── Cart/UpdateCartRequest.php
│   │   └── Cart/BatchSyncCartRequest.php
│   └── Resources/
│       ├── UserResource.php
│       ├── ProductResource.php
│       └── CartResource.php
├── Models/
│   ├── User.php          # firebase_uid, avatar fields + cartItems relationship
│   ├── Product.php       # name, description, price, image
│   └── Cart.php          # user_id, product_id, quantity
├── Services/
│   ├── FirebaseAuthService.php   # Token verification + user upsert
│   ├── CartService.php           # Cart CRUD + batchSync
│   └── ProductService.php        # Product queries
├── Traits/
│   └── ApiResponse.php           # Consistent JSON response helpers
└── Swagger/
    └── SwaggerLogger.php         # Silent PSR-3 logger for swagger-php
```

---

## Setup Instructions

### 1. Clone and Install Dependencies

```bash
cd backend
composer install
```

### 2. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and configure:

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shopping_cart
DB_USERNAME=root
DB_PASSWORD=your_password

# Firebase (required)
FIREBASE_PROJECT_ID=your-firebase-project-id
FIREBASE_API_KEY=your-firebase-web-api-key

# API URL (for Swagger)
APP_URL=http://localhost:8000
L5_SWAGGER_CONST_HOST=http://localhost:8000

# Frontend URL for CORS
FRONTEND_URL=http://localhost:3000
```

### 3. Firebase Setup

1. Go to [Firebase Console](https://console.firebase.google.com/)
2. Create a new project (or use existing)
3. Enable **Google Sign-in** under Authentication > Sign-in methods
4. Go to **Project Settings** > **General**
5. Copy your **Project ID** → `FIREBASE_PROJECT_ID`
6. Go to **Project Settings** > **General** > **Your apps** > Web app > **firebaseConfig**
7. Copy **apiKey** → `FIREBASE_API_KEY`

### 4. Database Setup

```bash
# Create database first:
mysql -u root -e "CREATE DATABASE shopping_cart;"

php artisan migrate
php artisan db:seed
```

### 5. Run the Server

```bash
php artisan serve
```

API will be available at `http://localhost:8000`

---

## API Endpoints

### Authentication

| Method | URL | Auth | Description |
|--------|-----|------|-------------|
| POST | `/api/auth/login` | No | Firebase Google token login |
| POST | `/api/auth/logout` | Yes | Revoke Sanctum token |
| GET | `/api/auth/me` | Yes | Get authenticated user |

**Login Request:**
```json
{
  "firebase_token": "eyJhbGci..."
}
```

**Login Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": { "id": 1, "name": "John Doe", "email": "..." },
    "token": "2|abc123..."
  }
}
```

### Products (Public)

| Method | URL | Auth | Description |
|--------|-----|------|-------------|
| GET | `/api/products` | No | List products (paginated) |
| GET | `/api/products/{id}` | No | Get single product |

**Query Parameters:**
- `per_page` - number of products per page (default: 12)

### Cart (Auth Required)

| Method | URL | Description |
|--------|-----|-------------|
| GET | `/api/cart` | Get all cart items |
| POST | `/api/cart` | Add item to cart |
| PATCH | `/api/cart/{id}` | Update item quantity |
| POST | `/api/cart/{id}/increment` | Increment quantity by 1 |
| POST | `/api/cart/{id}/decrement` | Decrement quantity (removes if 0) |
| DELETE | `/api/cart/{id}` | Remove item from cart |
| POST | `/api/cart/batch-sync` | Batch sync (debounced frontend updates) |
| DELETE | `/api/cart` | Clear entire cart |

**Add to Cart:**
```json
{ "product_id": 3, "quantity": 1 }
```

**Batch Sync (for debounced frontend):**
```json
{
  "items": [
    { "product_id": 3, "quantity": 5 },
    { "product_id": 7, "quantity": 0 }
  ]
}
```
> Items with `quantity: 0` are automatically removed.

---

## Swagger Documentation

Access the interactive API docs at:

```
http://localhost:8000/api/documentation
```

Regenerate docs manually:
```bash
php artisan l5-swagger:generate
```

---

## Response Format

All API endpoints return consistent JSON:

**Success:**
```json
{
  "success": true,
  "message": "Success",
  "data": { ... }
}
```

**Error:**
```json
{
  "success": false,
  "message": "Error description",
  "errors": { ... }
}
```

**HTTP Status Codes:**
| Code | Meaning |
|------|---------|
| 200 | OK |
| 201 | Created |
| 401 | Unauthenticated |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |

---

## Frontend Integration Notes

### Firebase Authentication Flow

```javascript
// 1. User clicks "Sign in with Google"
const result = await signInWithPopup(auth, googleProvider);
const idToken = await result.user.getIdToken();

// 2. Send to Laravel API
const response = await fetch('/api/auth/login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ firebase_token: idToken })
});

// 3. Store Sanctum token in Redux state (not localStorage)
const { data: { token } } = await response.json();

// 4. Use token in subsequent requests
fetch('/api/cart', {
  headers: { 'Authorization': `Bearer ${token}` }
});
```

### Batch Sync for Debounced Updates

```javascript
// Frontend: update Redux state INSTANTLY
dispatch(incrementQuantity({ cartItemId }));

// Debounce API call (e.g., 500ms after last change)
const debouncedSync = debounce(async (items) => {
  await fetch('/api/cart/batch-sync', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ items })
  });
}, 500);
```

---

## Cart Persistence

Cart data is stored in the **MySQL database** and loaded from `/api/cart` on application startup. This satisfies the requirement that "cart must persist after page reload" without using localStorage.
