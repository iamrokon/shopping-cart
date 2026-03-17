'use client';

import { useSelector, useDispatch } from 'react-redux';
import { RootState } from '@/lib/redux/store';
import {
    incrementQuantity,
    decrementQuantity,
    removeFromCart,
    setCart
} from '@/lib/redux/features/cart/cartSlice';
import { useGetCartQuery } from '@/lib/redux/api/apiSlice';
import { X, Minus, Plus, Trash2, ShoppingBag, Loader2, CloudSync, RefreshCw } from 'lucide-react';
import { motion, AnimatePresence } from 'framer-motion';
import { useEffect } from 'react';

export default function CartDrawer({ isOpen, onClose }: { isOpen: boolean; onClose: () => void }) {
    const dispatch = useDispatch();
    const { items, isSyncing } = useSelector((state: RootState) => state.cart);
    const { isAuthenticated } = useSelector((state: RootState) => state.auth);

    const subtotal = items.reduce((total, item) => {
        return total + (item.product?.price || 0) * item.quantity;
    }, 0);

    return (
        <AnimatePresence>
            {isOpen && (
                <>
                    {/* Backdrop */}
                    <motion.div
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        exit={{ opacity: 0 }}
                        onClick={onClose}
                        className="fixed inset-0 bg-black/40 backdrop-blur-sm z-[100]"
                    />

                    {/* Drawer */}
                    <motion.div
                        initial={{ x: '100%' }}
                        animate={{ x: 0 }}
                        exit={{ x: '100%' }}
                        transition={{ type: 'spring', damping: 25, stiffness: 200 }}
                        className="fixed right-0 top-0 bottom-0 w-full max-w-md bg-white z-[101] shadow-2xl flex flex-col"
                    >
                        {/* Header */}
                        <div className="p-6 border-b border-gray-100 flex items-center justify-between">
                            <div className="flex items-center gap-3">
                                <ShoppingBag className="text-indigo-600" />
                                <h2 className="text-xl font-outfit font-bold text-gray-900">Your Cart</h2>
                                {isSyncing && (
                                    <motion.div
                                        initial={{ opacity: 0 }}
                                        animate={{ opacity: 1 }}
                                        className="flex items-center gap-1 text-[10px] font-bold text-indigo-500 uppercase tracking-widest"
                                    >
                                        <RefreshCw size={10} className="animate-spin" />
                                        Syncing
                                    </motion.div>
                                )}
                            </div>
                            <button
                                onClick={onClose}
                                className="p-2 hover:bg-gray-100 rounded-full transition-colors"
                            >
                                <X size={20} className="text-gray-500" />
                            </button>
                        </div>

                        {/* Content */}
                        <div className="flex-1 overflow-y-auto p-6 space-y-6">
                            {!isAuthenticated ? (
                                <div className="h-full flex flex-col items-center justify-center text-center space-y-4">
                                    <div className="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center">
                                        <ShoppingBag size={32} className="text-gray-300" />
                                    </div>
                                    <div>
                                        <p className="font-bold text-gray-900">Please sign in</p>
                                        <p className="text-sm text-gray-500 max-w-[200px] mt-1">You need to be authenticated to manage your cart.</p>
                                    </div>
                                </div>
                            ) : items.length === 0 ? (
                                <div className="h-full flex flex-col items-center justify-center text-center space-y-4">
                                    <div className="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center">
                                        <ShoppingBag size={32} className="text-gray-300" />
                                    </div>
                                    <div>
                                        <p className="font-bold text-gray-900">Your cart is empty</p>
                                        <p className="text-sm text-gray-500 mt-1">Start adding some premium products!</p>
                                    </div>
                                </div>
                            ) : (
                                <div className="space-y-6">
                                    {items.map((item) => (
                                        <motion.div
                                            key={item.product_id}
                                            layout
                                            initial={{ opacity: 0, scale: 0.95 }}
                                            animate={{ opacity: 1, scale: 1 }}
                                            className="flex gap-4"
                                        >
                                            <div className="w-24 h-24 bg-gray-50 rounded-2xl overflow-hidden border border-gray-100 flex-shrink-0">
                                                <img
                                                    src={item.product?.image || '/placeholder-product.png'}
                                                    alt={item.product?.name}
                                                    className="w-full h-full object-cover"
                                                />
                                            </div>
                                            <div className="flex-1 flex flex-col justify-between">
                                                <div>
                                                    <div className="flex justify-between items-start">
                                                        <h4 className="font-bold text-gray-900 leading-tight pr-4">
                                                            {item.product?.name}
                                                        </h4>
                                                        <button
                                                            onClick={() => dispatch(removeFromCart(item.product_id))}
                                                            className="text-gray-300 hover:text-red-500 transition-colors"
                                                        >
                                                            <Trash2 size={16} />
                                                        </button>
                                                    </div>
                                                    <p className="text-lg font-outfit font-black text-indigo-600 mt-1">
                                                        ${((item.product?.price || 0) * item.quantity).toFixed(2)}
                                                    </p>
                                                </div>

                                                <div className="flex items-center gap-3 bg-gray-50 w-fit px-2 py-1 rounded-xl border border-gray-100">
                                                    <button
                                                        onClick={() => dispatch(decrementQuantity(item.product_id))}
                                                        className="p-1 hover:text-indigo-600 transition-colors disabled:opacity-30"
                                                        disabled={item.quantity <= 1}
                                                    >
                                                        <Minus size={14} />
                                                    </button>
                                                    <span className="font-bold text-sm w-4 text-center">{item.quantity}</span>
                                                    <button
                                                        onClick={() => dispatch(incrementQuantity(item.product_id))}
                                                        className="p-1 hover:text-indigo-600 transition-colors"
                                                    >
                                                        <Plus size={14} />
                                                    </button>
                                                </div>
                                            </div>
                                        </motion.div>
                                    ))}
                                </div>
                            )}
                        </div>

                        {/* Footer */}
                        {items.length > 0 && (
                            <div className="p-6 border-t border-gray-100 space-y-4">
                                <div className="flex justify-between items-end">
                                    <span className="text-gray-500 font-medium">Subtotal</span>
                                    <span className="text-3xl font-outfit font-black text-gray-900">${subtotal.toFixed(2)}</span>
                                </div>
                                <p className="text-xs text-gray-400 text-center">
                                    Shipping and taxes calculated at checkout.
                                </p>
                                <button className="w-full bg-gray-900 hover:bg-indigo-600 text-white font-bold py-4 rounded-2xl shadow-xl shadow-gray-200 transition-all hover:-translate-y-1 active:scale-95">
                                    Proceed to Checkout
                                </button>
                            </div>
                        )}
                    </motion.div>
                </>
            )}
        </AnimatePresence>
    );
}
