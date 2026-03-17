'use client';

import { useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { signInWithPopup, signOut } from 'firebase/auth';
import { auth, googleProvider } from '@/lib/firebase';
import { useLoginWithFirebaseMutation, useLogoutMutation } from '@/lib/redux/api/apiSlice';
import { setCredentials, logout as logoutAction } from '@/lib/redux/features/auth/authSlice';
import { RootState } from '@/lib/redux/store';
import { ShoppingCart, LogIn, LogOut, User, Loader2 } from 'lucide-react';
import { motion, AnimatePresence } from 'framer-motion';

export default function Navbar({ onCartClick }: { onCartClick: () => void }) {
    const dispatch = useDispatch();
    const { user, isAuthenticated } = useSelector((state: RootState) => state.auth);
    const { items } = useSelector((state: RootState) => state.cart);
    const [loginWithFirebase, { isLoading: isLoginLoading }] = useLoginWithFirebaseMutation();
    const [apiLogout] = useLogoutMutation();

    const handleLogin = async () => {
        try {
            const result = await signInWithPopup(auth, googleProvider);
            const idToken = await result.user.getIdToken();

            const response = await loginWithFirebase(idToken).unwrap();
            dispatch(setCredentials({
                user: response.data.user,
                token: response.data.token,
            }));
        } catch (error) {
            console.error('Login failed:', error);
        }
    };

    const handleLogout = async () => {
        try {
            await signOut(auth);
            await apiLogout(null);
            dispatch(logoutAction());
        } catch (error) {
            console.error('Logout failed:', error);
        }
    };

    const cartItemCount = items.reduce((total, item) => total + item.quantity, 0);

    return (
        <nav className="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100 h-20 flex items-center px-6 md:px-12 justify-between">
            <div className="flex items-center gap-2">
                <div className="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-indigo-200">
                    P
                </div>
                <span className="font-outfit font-bold text-2xl bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent">
                    PremiumCart
                </span>
            </div>

            <div className="flex items-center gap-6">
                <button
                    onClick={onCartClick}
                    className="relative p-2 text-gray-600 hover:text-indigo-600 transition-colors"
                >
                    <ShoppingCart size={24} />
                    {cartItemCount > 0 && (
                        <motion.span
                            initial={{ scale: 0 }}
                            animate={{ scale: 1 }}
                            className="absolute -top-1 -right-1 bg-indigo-600 text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full border-2 border-white"
                        >
                            {cartItemCount}
                        </motion.span>
                    )}
                </button>

                <div className="h-8 w-[1px] bg-gray-200" />

                {isAuthenticated ? (
                    <div className="flex items-center gap-4">
                        <div className="hidden md:block text-right">
                            <p className="text-sm font-bold text-gray-900">{user?.name}</p>
                            <p className="text-xs text-gray-500">{user?.email}</p>
                        </div>
                        <button
                            onClick={handleLogout}
                            className="p-2 text-gray-400 hover:text-red-500 transition-colors"
                            title="Logout"
                        >
                            <LogOut size={20} />
                        </button>
                        <div className="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center border border-gray-200 overflow-hidden">
                            {user?.avatar ? (
                                <img src={user.avatar} alt={user.name} className="w-full h-full object-cover" />
                            ) : (
                                <User size={20} className="text-gray-400" />
                            )}
                        </div>
                    </div>
                ) : (
                    <button
                        onClick={handleLogin}
                        disabled={isLoginLoading}
                        className="flex items-center gap-2 bg-gray-900 hover:bg-indigo-600 text-white px-6 py-2.5 rounded-full font-bold transition-all disabled:opacity-50 shadow-md hover:shadow-indigo-100"
                    >
                        {isLoginLoading ? (
                            <Loader2 size={18} className="animate-spin" />
                        ) : (
                            <LogIn size={18} />
                        )}
                        <span>Sign in with Google</span>
                    </button>
                )}
            </div>
        </nav>
    );
}
