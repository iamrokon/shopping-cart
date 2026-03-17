'use client';

import { useDispatch } from 'react-redux';
import { addToCart } from '@/lib/redux/features/cart/cartSlice';
import { ShoppingCart, Plus, Eye } from 'lucide-react';
import { motion } from 'framer-motion';

interface Product {
    id: number;
    name: string;
    description: string;
    price: number;
    image: string;
}

export default function ProductCard({ product }: { product: Product }) {
    const dispatch = useDispatch();

    const handleAddToCart = () => {
        dispatch(addToCart({ product }));
    };

    return (
        <motion.div
            whileHover={{ y: -8 }}
            className="group bg-white rounded-3xl overflow-hidden border border-gray-100/60 shadow-xl shadow-gray-200/20 hover:shadow-2xl hover:shadow-indigo-200/30 transition-all"
        >
            <div className="relative aspect-square overflow-hidden bg-gray-50 border-b border-gray-50">
                <img
                    src={product.image || '/placeholder-product.png'}
                    alt={product.name}
                    className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                />
                <div className="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                    <button
                        className="w-10 h-10 rounded-full bg-white text-gray-900 shadow-lg flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all transform hover:scale-110"
                        title="View Details"
                    >
                        <Eye size={18} />
                    </button>
                </div>
                <div className="absolute top-4 left-4">
                    <span className="bg-white/80 backdrop-blur-md px-4 py-2 rounded-2xl text-xs font-bold text-gray-900 shadow-sm">
                        NEW
                    </span>
                </div>
            </div>

            <div className="p-6">
                <div className="flex items-start justify-between min-h-[4rem]">
                    <div>
                        <h3 className="font-outfit font-bold text-lg text-gray-900 group-hover:text-indigo-600 transition-colors leading-tight">
                            {product.name}
                        </h3>
                        <p className="text-sm text-gray-500 line-clamp-2 mt-1">
                            {product.description}
                        </p>
                    </div>
                </div>

                <div className="mt-6 flex items-center justify-between">
                    <div className="flex flex-col">
                        <span className="text-xs text-gray-400 font-medium">Price</span>
                        <span className="text-2xl font-outfit font-black text-gray-900">
                            ${Number(product.price).toFixed(2)}
                        </span>
                    </div>
                    <button
                        onClick={handleAddToCart}
                        className="w-12 h-12 bg-gray-900 hover:bg-indigo-600 text-white rounded-2xl flex items-center justify-center shadow-lg hover:shadow-indigo-200 transition-all active:scale-95"
                    >
                        <Plus size={24} />
                    </button>
                </div>
            </div>
        </motion.div>
    );
}
