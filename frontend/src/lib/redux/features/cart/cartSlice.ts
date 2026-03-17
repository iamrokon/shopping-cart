import { createSlice, PayloadAction } from '@reduxjs/toolkit';

export interface CartItem {
    id: number;
    product_id: number;
    quantity: number;
    product?: {
        id: number;
        name: string;
        price: number;
        image: string;
    };
}

interface CartState {
    items: CartItem[];
    isSyncing: boolean;
    lastChanged: number | null;
}

const initialState: CartState = {
    items: [],
    isSyncing: false,
    lastChanged: null,
};

const cartSlice = createSlice({
    name: 'cart',
    initialState,
    reducers: {
        setCart: (state, action: PayloadAction<CartItem[]>) => {
            state.items = action.payload;
            state.lastChanged = null; // Don't trigger sync when setting initial state from server
        },
        addToCart: (state, action: PayloadAction<{ product: any }>) => {
            const { product } = action.payload;
            const existingItem = state.items.find(item => item.product_id === product.id);

            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                state.items.push({
                    id: Date.now(), // Temporary ID for frontend
                    product_id: product.id,
                    quantity: 1,
                    product: product
                });
            }
            state.lastChanged = Date.now();
        },
        updateQuantity: (state, action: PayloadAction<{ product_id: number; quantity: number }>) => {
            const { product_id, quantity } = action.payload;
            const item = state.items.find(i => i.product_id === product_id);
            if (item) {
                if (quantity > 0) {
                    item.quantity = quantity;
                } else {
                    state.items = state.items.filter(i => i.product_id !== product_id);
                }
                state.lastChanged = Date.now();
            }
        },
        incrementQuantity: (state, action: PayloadAction<number>) => {
            const item = state.items.find(i => i.product_id === action.payload);
            if (item) {
                item.quantity += 1;
                state.lastChanged = Date.now();
            }
        },
        decrementQuantity: (state, action: PayloadAction<number>) => {
            const item = state.items.find(i => i.product_id === action.payload);
            if (item && item.quantity > 1) {
                item.quantity -= 1;
                state.lastChanged = Date.now();
            } else if (item && item.quantity === 1) {
                state.items = state.items.filter(i => i.product_id !== action.payload);
                state.lastChanged = Date.now();
            }
        },
        removeFromCart: (state, action: PayloadAction<number>) => {
            state.items = state.items.filter(i => i.product_id !== action.payload);
            state.lastChanged = Date.now();
        },
        clearCart: (state) => {
            state.items = [];
            state.lastChanged = Date.now();
        },
        setSyncing: (state, action: PayloadAction<boolean>) => {
            state.isSyncing = action.payload;
        }
    }
});

export const {
    setCart,
    addToCart,
    updateQuantity,
    incrementQuantity,
    decrementQuantity,
    removeFromCart,
    clearCart,
    setSyncing
} = cartSlice.actions;

export default cartSlice.reducer;
