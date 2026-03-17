'use client';

import { useEffect } from 'react';
import { useDispatch } from 'react-redux';
import { useLoginWithFirebaseMutation, useGetCartQuery } from '@/lib/redux/api/apiSlice';
import { setCredentials, logout } from '@/lib/redux/features/auth/authSlice';
import { setCart } from '@/lib/redux/features/cart/cartSlice';
import { auth } from '@/lib/firebase';
import { onAuthStateChanged } from 'firebase/auth';

export default function AuthInit({ children }: { children: React.ReactNode }) {
    const dispatch = useDispatch();
    const [loginWithFirebase] = useLoginWithFirebaseMutation();

    // We don't use the data directly here, but calling it triggers the query
    const { data: cartData } = useGetCartQuery(undefined, {
        skip: false, // We'll handle the skip logic inside the effect or just let it fail if unauth
    });

    useEffect(() => {
        if (cartData?.data) {
            dispatch(setCart(cartData.data));
        }
    }, [cartData, dispatch]);

    useEffect(() => {
        // Check if we have a token in localStorage
        const savedToken = localStorage.getItem('token');

        const unsubscribe = onAuthStateChanged(auth, async (firebaseUser) => {
            if (firebaseUser) {
                try {
                    // If we have a firebase user, exchange for a fresh API token if needed
                    // Or just use the saved token if it exists
                    const idToken = await firebaseUser.getIdToken();
                    const response = await loginWithFirebase(idToken).unwrap();

                    dispatch(setCredentials({
                        user: response.data.user,
                        token: response.data.token,
                    }));
                } catch (error) {
                    console.error('Failed to re-authenticate:', error);
                    dispatch(logout());
                }
            } else {
                dispatch(logout());
            }
        });

        return () => unsubscribe();
    }, [dispatch, loginWithFirebase]);

    return <>{children}</>;
}
