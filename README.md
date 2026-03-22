# Modern Full-Stack Shopping Cart

A premium, high-performance shopping cart application built with a **Laravel 12** backend and a **Next.js 16** frontend (**React 19**). Featuring real-time optimistic UI updates, debounced batch synchronization, and secure Firebase Google Authentication.

## 📂 Project Structure

- `backend/` - Laravel 12 API, MySQL, Sanctum, Firebase Auth.
- `frontend/` - Next.js 16 (App Router), Redux Toolkit, RTK Query, Framer Motion.

---

## 🛠️ Tech Stack

### Backend
- **Framework**: Laravel 12
- **Auth**: Laravel Sanctum + Firebase Admin SDK
- **Database**: MySQL
- **Documentation**: L5-Swagger (OpenAPI 3.0)

### Frontend
- **Framework**: Next.js 16 (App Router)
- **State Management**: Redux Toolkit & RTK Query
- **Authentication**: Firebase Authentication (Google Sign-in)
- **UI Architecture**: React 19 + Vanilla CSS
- **Animations**: Framer Motion
- **Icons**: Lucide React

---

## ⚡ Quick Start

### Prerequisites
- PHP 8.2+ & Composer
- Node.js 20+ & npm
- MySQL

### 1. Backend Setup
```bash
cd backend
composer install
# or use the setup shortcut:
# composer setup
cp .env.example .env
php artisan key:generate
```
**Configure `.env`**:
- `DB_DATABASE=shopping_cart`
- `FIREBASE_PROJECT_ID=your-project-id`
- `FIREBASE_API_KEY=your-api-key`
- `FRONTEND_URL=http://localhost:3000`

**Initialize Database:**
```bash
php artisan migrate --seed
php artisan serve
```
*API available at `http://localhost:8000`. Swagger docs: `http://localhost:8000/api/documentation`*

### 2. Frontend Setup
```bash
cd frontend
npm install
```
**Configure `.env.local`**:
- `NEXT_PUBLIC_API_URL=http://localhost:8000/api`
- `NEXT_PUBLIC_FIREBASE_API_KEY=your_api_key`
- `NEXT_PUBLIC_FIREBASE_AUTH_DOMAIN=your_project.firebaseapp.com`
- `NEXT_PUBLIC_FIREBASE_PROJECT_ID=your_project_id`

**Run Development Server:**
```bash
npm run dev
```
*Frontend available at `http://localhost:3000`.*

---

## 🚀 Key Features & Architecture

### 1. Optimistic UI Updates (Frontend)
All cart interactions (Add, Increment, Decrement, Remove) happen instantly. The Redux state is updated immediately to provide a zero-latency experience.

### 2. Batch Synchronization
Instead of calling the API on every click, the frontend queues changes and synchronizes with the backend using a **Batch API Request** after a 2-second period of inactivity (debounce).

### 3. Server-Side Persistence
The application does NOT use `localStorage` for cart persistence.
- Cart data is stored in the **MySQL database**.
- On application load, the backend API is queried and the Redux state is hydrated.
- This ensures the cart is persistent across devices and sessions securely.

### 4. Firebase Google Sign-in
Seamless authentication using Google. The Firebase ID token is exchanged for a secure Laravel Sanctum token for all subsequent API requests.

---

## 📡 API Endpoints

### Authentication
| Method | URL | Auth | Description |
|--------|-----|------|-------------|
| POST | `/api/auth/login` | No | Firebase Google token login |
| POST | `/api/auth/logout` | Yes | Revoke Sanctum token |
| GET | `/api/auth/me` | Yes | Get authenticated user |

### Products (Public)
| Method | URL | Auth | Description |
|--------|-----|------|-------------|
| GET | `/api/products` | No | List products (paginated) |
| GET | `/api/products/{id}` | No | Get single product |

### Cart (Auth Required)
| Method | URL | Description |
|--------|-----|-------------|
| GET | `/api/cart` | Get all cart items |
| POST | `/api/cart` | Add item to cart |
| PATCH | `/api/cart/{id}` | Update item quantity |
| POST | `/api/cart/batch-sync` | Batch sync (debounced updates) |
| DELETE | `/api/cart` | Clear entire cart |

---

## 🏗️ Backend Internal Architecture
```
app/
├── Http/
│   ├── Controllers/Api/   # Auth, Products, Cart Controllers
│   ├── Requests/          # Validation logic
│   └── Resources/         # JSON response transformation
├── Models/                # User, Product, Cart models
├── Services/              # Firebase & Cart business logic
└── Traits/                # Consistent ApiResponse helper
```
