import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react';

export const apiSlice = createApi({
    reducerPath: 'api',
    baseQuery: fetchBaseQuery({
        baseUrl: process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api',
        prepareHeaders: (headers) => {
            const token = localStorage.getItem('token');
            if (token) {
                headers.set('authorization', `Bearer ${token}`);
            }
            headers.set('Accept', 'application/json');
            return headers;
        },
    }),
    tagTypes: ['Product', 'Cart'],
    endpoints: (builder) => ({
        getProducts: builder.query({
            query: () => '/products',
            providesTags: ['Product'],
        }),
        getCart: builder.query({
            query: () => '/cart',
            providesTags: ['Cart'],
        }),
        batchSyncCart: builder.mutation({
            query: (cartItems) => ({
                url: '/cart/batch-sync',
                method: 'POST',
                body: { items: cartItems },
            }),
            invalidatesTags: ['Cart'],
        }),
        loginWithFirebase: builder.mutation({
            query: (firebaseToken) => ({
                url: '/auth/login',
                method: 'POST',
                body: { firebase_token: firebaseToken },
            }),
        }),
        logout: builder.mutation({
            query: () => ({
                url: '/auth/logout',
                method: 'POST',
            }),
        }),
    }),
});

export const {
    useGetProductsQuery,
    useGetCartQuery,
    useBatchSyncCartMutation,
    useLoginWithFirebaseMutation,
    useLogoutMutation,
} = apiSlice;
