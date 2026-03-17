'use client';

import { useEffect, useRef, useCallback } from 'react';
import { useSelector, useDispatch } from 'react-redux';
import { RootState } from '../lib/redux/store';
import { useBatchSyncCartMutation } from '../lib/redux/api/apiSlice';
import { setSyncing } from '../lib/redux/features/cart/cartSlice';
import debounce from 'lodash/debounce';

export const useCartSync = () => {
    const { items, lastChanged } = useSelector((state: RootState) => state.cart);
    const { isAuthenticated } = useSelector((state: RootState) => state.auth);
    const [batchSyncCart] = useBatchSyncCartMutation();
    const dispatch = useDispatch();

    // Create a stable debounced function
    const debouncedSync = useCallback(
        debounce(async (cartItems: any[]) => {
            try {
                dispatch(setSyncing(true));
                // Prepare data for the backend
                const syncData = cartItems.map(item => ({
                    product_id: item.product_id,
                    quantity: item.quantity
                }));
                await batchSyncCart(syncData).unwrap();
            } catch (error) {
                console.error('Failed to sync cart:', error);
            } finally {
                dispatch(setSyncing(false));
            }
        }, 2000),
        [batchSyncCart, dispatch]
    );

    useEffect(() => {
        if (isAuthenticated && lastChanged !== null) {
            debouncedSync(items);
        }
    }, [lastChanged, items, isAuthenticated, debouncedSync]);

    return { isSyncing: useSelector((state: RootState) => state.cart.isSyncing) };
};
