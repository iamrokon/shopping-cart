# PremiumCart Frontend

A high-performance, premium shopping cart frontend built with Next.js, Redux Toolkit, and Firebase.

## Technology Stack

- **Framework**: [Next.js](https://nextjs.org/) (App Router)
- **State Management**: [Redux Toolkit](https://redux-toolkit.js.org/)
- **API Communication**: [RTK Query](https://redux-toolkit.js.org/rtk-query/overview)
- **Authentication**: [Firebase Authentication](https://firebase.google.com/docs/auth) (Google Sign-in)
- **Animations**: [Framer Motion](https://www.framer.com/motion/)
- **Styling**: Vanilla CSS with Modern Best Practices
- **Icons**: [Lucide React](https://lucide.dev/)

## Key Features

### 1. Optimistic UI Updates
All cart interactions (Add, Increment, Decrement, Remove) happen instantly on the frontend. The Redux state is updated immediately to provide a snappy, zero-latency experience for the user.

### 2. Batch Synchronization
Instead of calling the API on every click, the frontend queues changes and synchronizes with the backend using a **Batch API Request** after a 2-second period of inactivity (debounce). This significantly reduces server load and prevents rate limiting.

### 3. Persistent Cart (No LocalStorage)
The application does NOT use `localStorage` for cart persistence. Instead:
- On application load, the backend API is queried for the current user's cart.
- The Redux state is hydrated with these server values.
- This ensures the cart is truly persistent across devices and sessions without the security risks/limitations of local storage.

### 4. Firebase Google Sign-in
Seamless authentication using Google. The Firebase ID token is exchanged for a secure Laravel Sanctum token for all subsequent API requests.

## Setup Instructions

1.  **Install Dependencies**:
    ```bash
    npm install
    ```

2.  **Environment Variables**:
    Create a `.env.local` file in the `frontend` directory:
    ```env
    NEXT_PUBLIC_API_URL=http://localhost:8000/api
    NEXT_PUBLIC_FIREBASE_API_KEY=your_api_key
    NEXT_PUBLIC_FIREBASE_AUTH_DOMAIN=your_project.firebaseapp.com
    NEXT_PUBLIC_FIREBASE_PROJECT_ID=your_project_id
    NEXT_PUBLIC_FIREBASE_STORAGE_BUCKET=your_project.appspot.com
    NEXT_PUBLIC_FIREBASE_MESSAGING_SENDER_ID=your_id
    NEXT_PUBLIC_FIREBASE_APP_ID=your_app_id
    ```

3.  **Run Development Server**:
    ```bash
    npm run dev
    ```

## Architecture

- `src/lib/redux`: Store configuration and API slice definitions.
- `src/hooks/useCartSync.ts`: Custom hook managing the debounced synchronization logic.
- `src/components/AuthInit.tsx`: Handles session persistence and initial data hydration.
- `src/app/globals.css`: Premium design system implemented in Vanilla CSS.
